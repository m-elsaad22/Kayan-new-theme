<?php
header( "Content-Type: application/json" );
$_POST = YC_stripslashes_deep( $_POST );
$json  = array( 'success' => false );

global $wpdb;
$payments_table = $wpdb->prefix . 'kayan_payments';
$bookings_table = $wpdb->prefix . 'kayan_bookings';

$txn_ref = isset( $_POST['txn_ref'] ) ? sanitize_text_field( $_POST['txn_ref'] ) : '';
$otp     = isset( $_POST['otp'] ) ? preg_replace( '/\D/', '', (string) $_POST['otp'] ) : '';

$payment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$payments_table} WHERE txn_ref = %s", $txn_ref ) );

if ( ! $payment ) {

	$json['message'] = 'عملية الدفع غير موجودة';

} else if ( 'otp_pending' !== $payment->status ) {

	$json['message'] = 'انتهت صلاحية عملية الدفع هذه';

} else if ( $payment->otp_attempts >= 5 ) {

	$json['message'] = 'تم تجاوز عدد المحاولات المسموح — يرجى بدء الدفع من جديد';
	$wpdb->update( $payments_table, array( 'status' => 'failed', 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $payment->id ), array( '%s', '%s' ), array( '%d' ) );

} else if ( 6 !== strlen( $otp ) ) {

	$wpdb->update( $payments_table, array( 'otp_attempts' => $payment->otp_attempts + 1 ), array( 'id' => $payment->id ), array( '%d' ), array( '%d' ) );
	$json['message'] = 'رمز التحقق غير صحيح';

} else {

	# ═══ Demo Gateway: أي رمز مكوّن من 6 أرقام يُقبل (يحاكي نجاح OTP الحقيقي) ═══
	$invoice_number = Kayan_Payment::generate_invoice_number();
	$now            = current_time( 'mysql' );

	$wpdb->update(
		$payments_table,
		array( 'status' => 'paid', 'invoice_number' => $invoice_number, 'updated_at' => $now ),
		array( 'id' => $payment->id ),
		array( '%s', '%s', '%s' ),
		array( '%d' )
	);

	$wpdb->update(
		$bookings_table,
		array( 'payment_status' => 'paid', 'payment_method' => 'card', 'status' => 'confirmed', 'updated_at' => $now ),
		array( 'id' => $payment->booking_id ),
		array( '%s', '%s', '%s', '%s' ),
		array( '%d' )
	);

	Kayan_Payment::log_activity( $payment->booking_id, 'confirmed', "تم الدفع بنجاح — بطاقة {$payment->card_brand} تنتهي بـ {$payment->card_last4} — فاتورة {$invoice_number}" );

	$booking = Kayan_Payment::get_booking( $payment->booking_id );

	$json['success']        = true;
	$json['invoice_number'] = $invoice_number;
	$json['booking_ref']    = $booking ? $booking->booking_ref : '';
	$json['total']          = $payment->amount;
	$json['currency']       = $payment->currency;
	$json['invoice_url']    = trailingslashit( home_url( '/AjaxCenter/kayan_invoice_view' ) ) . '?ref=' . rawurlencode( $booking ? $booking->booking_ref : '' );
	$json['message']        = 'تم الدفع بنجاح';
}

echo json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
