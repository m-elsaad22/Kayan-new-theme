<?php
header( "Content-Type: application/json" );
$_POST = YC_stripslashes_deep( $_POST );
$json  = array( 'success' => false );

$kb_nonce_ok = isset( $_POST['kb_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kb_nonce'] ) ), 'kayan_booking_form' );

if ( ! $kb_nonce_ok ) {

	$json['message'] = 'انتهت صلاحية الجلسة — يرجى تحديث الصفحة';

} else {

	global $wpdb;

	$booking_id   = isset( $_POST['booking_id'] ) ? absint( $_POST['booking_id'] ) : 0;
	$card_number  = isset( $_POST['card_number'] ) ? preg_replace( '/\D/', '', (string) $_POST['card_number'] ) : '';
	$card_name    = isset( $_POST['card_name'] ) ? sanitize_text_field( $_POST['card_name'] ) : '';
	$card_expiry  = isset( $_POST['card_expiry'] ) ? sanitize_text_field( $_POST['card_expiry'] ) : '';
	$card_cvv     = isset( $_POST['card_cvv'] ) ? preg_replace( '/\D/', '', (string) $_POST['card_cvv'] ) : '';

	$booking = Kayan_Payment::get_booking( $booking_id );

	if ( ! $booking ) {
		$json['message'] = 'الحجز غير موجود';
	} else if ( 'paid' === $booking->payment_status ) {
		$json['message'] = 'تم دفع هذا الحجز بالفعل';
	} else if ( strlen( $card_number ) < 12 || strlen( $card_number ) > 19 ) {
		$json['message'] = 'رقم البطاقة غير صالح';
	} else if ( '' === $card_name ) {
		$json['message'] = 'اسم حامل البطاقة مطلوب';
	} else if ( ! preg_match( '/^(0[1-9]|1[0-2])\/?([0-9]{2})$/', $card_expiry, $m ) ) {
		$json['message'] = 'تاريخ انتهاء البطاقة غير صالح (MM/YY)';
	} else if ( strlen( $card_cvv ) < 3 || strlen( $card_cvv ) > 4 ) {
		$json['message'] = 'رمز CVV غير صالح';
	} else {

		$exp_month = (int) $m[1];
		$exp_year  = 2000 + (int) $m[2];
		$now       = current_time( 'timestamp' );
		if ( mktime( 0, 0, 0, $exp_month + 1, 1, $exp_year ) < $now ) {

			$json['message'] = 'بطاقة منتهية الصلاحية';

		} else {

			$txn_ref = Kayan_Payment::generate_txn_ref();
			$last4   = substr( $card_number, -4 );
			$brand   = Kayan_Payment::card_brand( $card_number );

			$wpdb->insert(
				$wpdb->prefix . 'kayan_payments',
				array(
					'booking_id'   => $booking_id,
					'txn_ref'      => $txn_ref,
					'method'       => 'card',
					'amount'       => $booking->total,
					'currency'     => $booking->currency,
					'status'       => 'otp_pending',
					'card_brand'   => $brand,
					'card_last4'   => $last4,
					'created_at'   => current_time( 'mysql' ),
					'updated_at'   => current_time( 'mysql' ),
				),
				array( '%d', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s' )
			);

			Kayan_Payment::log_activity( $booking_id, 'pending', "تمت مبادرة دفع ببطاقة {$brand} تنتهي بـ {$last4} — بانتظار رمز التحقق" );

			$json['success']   = true;
			$json['txn_ref']   = $txn_ref;
			$json['card_last4']= $last4;
			$json['card_brand']= $brand;
			# ═══ Demo Gateway فقط: عرض رمز OTP التجريبي مباشرة لأغراض العرض — لا يوجد بوابة SMS حقيقية ═══
			$json['demo_otp']  = Kayan_Payment::DEMO_OTP;
			$json['message']   = 'تم إرسال رمز التحقق (تجريبي)';
		}
	}
}

echo json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
