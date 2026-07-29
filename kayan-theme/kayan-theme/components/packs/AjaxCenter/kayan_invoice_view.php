<?php
header( "Content-Type: text/html; charset=UTF-8" );
nocache_headers();

global $wpdb;
$ref = isset( $_GET['ref'] ) ? sanitize_text_field( $_GET['ref'] ) : '';

$booking = $ref ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}kayan_bookings WHERE booking_ref = %s", $ref ) ) : null;

if ( ! $booking ) {
	echo '<!doctype html><html lang="ar" dir="rtl"><head><meta charset="UTF-8"><title>الفاتورة غير موجودة</title></head><body style="font-family:sans-serif;text-align:center;padding:60px;"><h2>لم يتم العثور على هذه الفاتورة</h2></body></html>';
	exit;
}

$items   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}kayan_booking_items WHERE booking_id = %d", $booking->id ) );
$payment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}kayan_payments WHERE booking_id = %d ORDER BY id DESC LIMIT 1", $booking->id ) );

$company    = Kayan_Payment::invoice_company_name();
$footer     = Kayan_Payment::option( 'kayan_invoice_footer_note', '' );
$invoice_no = $payment ? $payment->invoice_number : '—';
$paid       = 'paid' === $booking->payment_status;
$qr_data    = rawurlencode( $booking->booking_ref );
$qr_img     = 'https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=' . $qr_data;

?><!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>فاتورة <?php echo esc_html( $booking->booking_ref ); ?></title>
<style>
	body { font-family: Tahoma, Arial, sans-serif; background:#f3f4f6; margin:0; padding:30px; color:#111827; }
	.invoice { max-width: 680px; margin: 0 auto; background:#fff; border-radius:14px; padding:36px; box-shadow:0 6px 24px rgba(0,0,0,.06); }
	.invoice-head { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #e5e7eb; padding-bottom:20px; margin-bottom:20px; }
	.invoice-head h1 { font-size:20px; margin:0 0 4px; }
	.badge { display:inline-block; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700; }
	.badge.paid { background:#dcfce7; color:#166534; }
	.badge.unpaid { background:#fef9c3; color:#854d0e; }
	table { width:100%; border-collapse:collapse; margin:16px 0; }
	th, td { text-align:right; padding:10px; border-bottom:1px solid #f0f0f0; font-size:13px; }
	th { color:#6b7280; font-weight:700; }
	.totals { margin-top:10px; width:260px; margin-inline-start:auto; }
	.totals div { display:flex; justify-content:space-between; padding:4px 0; font-size:13px; color:#374151; }
	.totals .final { font-weight:800; font-size:16px; color:#111827; border-top:1px solid #e5e7eb; padding-top:8px; margin-top:6px; }
	.qr-box { text-align:center; margin-top:20px; }
	.print-btn { display:inline-block; margin-top:20px; background:#2563eb; color:#fff; border:none; padding:12px 24px; border-radius:10px; cursor:pointer; font-size:14px; }
	footer { text-align:center; color:#9ca3af; font-size:12px; margin-top:24px; }
	@media print { .print-btn { display:none; } body { background:#fff; padding:0; } .invoice { box-shadow:none; } }
</style>
</head>
<body>
	<div class="invoice">
		<div class="invoice-head">
			<div>
				<h1><?php echo esc_html( $company ); ?></h1>
				<div>رقم الفاتورة: <strong><?php echo esc_html( $invoice_no ); ?></strong></div>
				<div>رقم الحجز: <strong><?php echo esc_html( $booking->booking_ref ); ?></strong></div>
				<div><?php echo esc_html( $booking->created_at ); ?></div>
			</div>
			<span class="badge <?php echo $paid ? 'paid' : 'unpaid'; ?>"><?php echo $paid ? 'مدفوعة' : 'غير مدفوعة (عند الاستلام)'; ?></span>
		</div>

		<p><strong>العميل:</strong> <?php echo esc_html( $booking->customer_name ); ?> — <?php echo esc_html( $booking->customer_phone ); ?></p>
		<p><strong>الموقع:</strong> <?php echo esc_html( trim( "{$booking->emirate} {$booking->city} {$booking->district} {$booking->address}" ) ); ?></p>
		<p><strong>الموعد:</strong> <?php echo esc_html( $booking->booking_date . ' ' . $booking->booking_time ); ?></p>

		<table>
			<thead><tr><th>الخدمة</th><th>السعر</th></tr></thead>
			<tbody>
			<?php foreach ( $items as $item ) : ?>
				<tr><td><?php echo esc_html( $item->service_title ); ?></td><td><?php echo esc_html( number_format( (float) $item->unit_price, 2 ) ); ?> <?php echo esc_html( $booking->currency ); ?></td></tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<div class="totals">
			<div><span>الإجمالي الفرعي</span><span><?php echo esc_html( number_format( (float) $booking->subtotal, 2 ) ); ?> <?php echo esc_html( $booking->currency ); ?></span></div>
			<div><span>الضريبة</span><span><?php echo esc_html( number_format( (float) $booking->tax, 2 ) ); ?> <?php echo esc_html( $booking->currency ); ?></span></div>
			<div class="final"><span>الإجمالي</span><span><?php echo esc_html( number_format( (float) $booking->total, 2 ) ); ?> <?php echo esc_html( $booking->currency ); ?></span></div>
		</div>

		<div class="qr-box">
			<img src="<?php echo esc_url( $qr_img ); ?>" alt="QR" width="140" height="140">
			<div style="font-size:11px;color:#9ca3af;margin-top:6px;">امسح الكود للتحقق من رقم الحجز</div>
		</div>

		<div style="text-align:center;">
			<button class="print-btn" onclick="window.print()">طباعة / حفظ PDF</button>
		</div>

		<?php if ( $footer ) : ?>
			<footer><?php echo esc_html( $footer ); ?></footer>
		<?php endif; ?>
	</div>
</body>
</html>
