<?php
header("Content-Type: application/json");
ob_start();
$_POST = YC_stripslashes_deep( $_POST );
$json = array();
#
# ═══ حماية v1.2.0: CSRF nonce + مصيدة سبام (honeypot) + تعقيم كامل ═══
$yc_nonce_ok = isset( $_POST['yc_contact_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yc_contact_nonce'] ) ), 'yc_contact_form' );
$yc_honeypot = isset( $_POST['yc_website_url'] ) && '' !== trim( (string) $_POST['yc_website_url'] );

# حد أقصى 5 إرسالات لكل IP كل 10 دقائق
$yc_ip      = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( (string) $_SERVER['REMOTE_ADDR'] ) : '';
$yc_rl_key  = 'yc_cf_rl_' . md5( $yc_ip );
$yc_rl_hits = (int) get_transient( $yc_rl_key );

if ( $yc_rl_hits >= 5 ) {
	$json['alert_output'] = array( 'type' => 'error', 'alert' => 'محاولات كثيرة — يرجى الانتظار قليلاً ثم المحاولة مجدداً' );
} else if ( $yc_honeypot ) {
	# بوت وقع في المصيدة — نرجع نجاحاً صامتاً بدون إرسال أي بريد
	$json['alert_output'] = array( 'type' => '', 'alert' => 'تم إرسال رسالتك بنجاح وسيتم التواصل معك قريبا' );
} else if ( ! $yc_nonce_ok ) {
	$json['alert_output'] = array( 'type' => 'error', 'alert' => 'انتهت صلاحية النموذج — يرجى تحديث الصفحة والمحاولة مرة أخرى' );
} else if ( isset( $_POST['user__name'] ) ) {

	$AdminMail = get_bloginfo('admin_email');
	$SiteName  = get_bloginfo('name');

	# تعقيم كل الحقول قبل أي استخدام
	$user__name      = sanitize_text_field( (string) $_POST['user__name'] );
	$user_mail       = isset( $_POST['user_mail'] ) ? sanitize_email( (string) $_POST['user_mail'] ) : '';
	$phone__number   = isset( $_POST['phone__number'] ) ? sanitize_text_field( (string) $_POST['phone__number'] ) : '';
	$description     = isset( $_POST['description'] ) ? sanitize_textarea_field( (string) $_POST['description'] ) : '';

	if( isset( $_POST['servies__category'] ) ){
		$category = get_term_by( 'id', absint( $_POST['servies__category'] ), 'category' );
		$category_name = ( $category && ! is_wp_error( $category ) ) ? $category->name : '';
		$message_title = "قام {$user__name} بتقديم طلب للحصول على الخدمة {$category_name}";
	}else{
		$category_name = '';
		$message_title = "قام {$user__name} بإرسال طلب للتواصل مع {$SiteName}";
	}

	echo "<div>";
		echo "<ul>";
			echo ( '' !== $user__name )    ? "<li><strong>اسم العميل :</strong><span>".esc_html( $user__name )."</span></li>" : '';
			echo ( '' !== $user_mail )     ? "<li><strong>البريد الالكتروني :</strong><span>".esc_html( $user_mail )."</span></li>" : '';
			echo ( '' !== $phone__number ) ? "<li><strong>رقم الهاتف :</strong><span>".esc_html( $phone__number )."</span></li>" : '';
			if( '' !== $category_name ){
				echo "<li><strong>الخدمة :</strong><span>".esc_html( $category_name )."</span></li>";
			}
			echo ( '' !== $description )   ? "<li><strong>ملاحظات الطلب :</strong><span>".esc_html( $description )."</span></li>" : '';
		echo "</ul>";
	echo "</div>";
	$mail__output = ob_get_clean();

	$headers = ['Content-Type: text/html; charset=UTF-8'];
	wp_mail( $AdminMail, $message_title, $mail__output, $headers );
	set_transient( $yc_rl_key, $yc_rl_hits + 1, 10 * MINUTE_IN_SECONDS );
	#
	$json['alert_output'] = array('type'=>'','alert'=>'تم إرسال رسالتك بنجاح وسيتم التواصل معك قريبا');

}else{
	$json['alert_output'] = 'لم يتم العثور على نموذج ';
}
if ( ob_get_level() > 0 && ! isset( $mail__output ) ) { ob_end_clean(); }
echo json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
