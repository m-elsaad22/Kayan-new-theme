<?php
header( "Content-Type: application/json" );
$_POST = YC_stripslashes_deep( $_POST );
$json  = array( 'success' => false );

$kb_nonce_ok = isset( $_POST['kb_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kb_nonce'] ) ), 'kayan_booking_form' );

if ( ! $kb_nonce_ok ) {

	$json['message'] = 'انتهت صلاحية الجلسة — يرجى تحديث الصفحة';

} else {

	global $wpdb;
	$booking_id = isset( $_POST['booking_id'] ) ? absint( $_POST['booking_id'] ) : 0;
	$booking    = Kayan_Payment::get_booking( $booking_id );

	if ( ! $booking ) {

		$json['message'] = 'الحجز غير موجود';

	} else {

		$now            = current_time( 'mysql' );
		$invoice_number = Kayan_Payment::generate_invoice_number();
		$txn_ref        = Kayan_Payment::generate_txn_ref();

		$wpdb->insert(
			$wpdb->prefix . 'kayan_payments',
			array(
				'booking_id'     => $booking_id,
				'txn_ref'        => $txn_ref,
				'method'         => 'cash',
				'amount'         => $booking->total,
				'currency'       => $booking->currency,
				'status'         => 'cash_pending',
				'invoice_number' => $invoice_number,
				'created_at'     => $now,
				'updated_at'     => $now,
			),
			array( '%d', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s' )
		);

		$wpdb->update(
			$wpdb->prefix . 'kayan_bookings',
			array( 'payment_status' => 'unpaid', 'payment_method' => 'cash', 'status' => 'confirmed', 'updated_at' => $now ),
			array( 'id' => $booking_id ),
			array( '%s', '%s', '%s', '%s' ),
			array( '%d' )
		);

		Kayan_Payment::log_activity( $booking_id, 'confirmed', "تم تأكيد الحجز — الدفع عند الاستلام — فاتورة {$invoice_number}" );

		$json['success']        = true;
		$json['invoice_number'] = $invoice_number;
		$json['booking_ref']    = $booking->booking_ref;
		$json['total']          = $booking->total;
		$json['currency']       = $booking->currency;
		$json['invoice_url']    = trailingslashit( home_url( '/AjaxCenter/kayan_invoice_view' ) ) . '?ref=' . rawurlencode( $booking->booking_ref );
		$json['message']        = 'تم تأكيد حجزك — سيتم الدفع عند وصول الفني';
	}
}

echo json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
