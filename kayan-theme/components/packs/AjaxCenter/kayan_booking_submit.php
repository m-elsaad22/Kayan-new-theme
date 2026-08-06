<?php
header( "Content-Type: application/json" );
ob_start();
$_POST = YC_stripslashes_deep( $_POST );
$json  = array();

# ═══ حماية: CSRF nonce + Honeypot + Rate Limit (نفس نمط contact__form.php) ═══
$kb_nonce_ok = isset( $_POST['kb_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kb_nonce'] ) ), 'kayan_booking_form' );
$kb_honeypot = isset( $_POST['kb_website'] ) && '' !== trim( (string) $_POST['kb_website'] );

$kb_ip     = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( (string) $_SERVER['REMOTE_ADDR'] ) : '';
$kb_rl_key = 'kb_rl_' . md5( $kb_ip );
$kb_rl_hits = (int) get_transient( $kb_rl_key );

if ( $kb_rl_hits >= 5 ) {

	$json['success'] = false;
	$json['message'] = 'محاولات كثيرة — يرجى الانتظار قليلاً ثم المحاولة مجدداً';

} else if ( $kb_honeypot ) {

	# بوت وقع في المصيدة — رد نجاح صامت بدون أي كتابة فعلية في قاعدة البيانات
	$json['success']     = true;
	$json['booking_ref'] = 'KYN-000000';

} else if ( ! $kb_nonce_ok ) {

	$json['success'] = false;
	$json['message'] = 'انتهت صلاحية النموذج — يرجى تحديث الصفحة والمحاولة مرة أخرى';

} else {

	global $wpdb;

	# ═══ 1) تعقيم بيانات العميل والموقع ═══
	$customer_name     = isset( $_POST['customer_name'] ) ? sanitize_text_field( $_POST['customer_name'] ) : '';
	$customer_phone    = isset( $_POST['customer_phone'] ) ? sanitize_text_field( $_POST['customer_phone'] ) : '';
	$customer_whatsapp = isset( $_POST['customer_whatsapp'] ) ? sanitize_text_field( $_POST['customer_whatsapp'] ) : $customer_phone;
	$customer_email    = isset( $_POST['customer_email'] ) ? sanitize_email( $_POST['customer_email'] ) : '';
	$country           = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
	$emirate           = isset( $_POST['emirate'] ) ? sanitize_text_field( $_POST['emirate'] ) : '';
	$city              = isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
	$district          = isset( $_POST['district'] ) ? sanitize_text_field( $_POST['district'] ) : '';
	$address           = isset( $_POST['address'] ) ? sanitize_textarea_field( $_POST['address'] ) : '';
	$building_name     = isset( $_POST['building_name'] ) ? sanitize_text_field( $_POST['building_name'] ) : '';
	$unit_number       = isset( $_POST['unit_number'] ) ? sanitize_text_field( $_POST['unit_number'] ) : '';
	$floor             = isset( $_POST['floor'] ) ? sanitize_text_field( $_POST['floor'] ) : '';
	$lat               = isset( $_POST['lat'] ) && is_numeric( $_POST['lat'] ) ? (float) $_POST['lat'] : null;
	$lng               = isset( $_POST['lng'] ) && is_numeric( $_POST['lng'] ) ? (float) $_POST['lng'] : null;
	$booking_date      = isset( $_POST['booking_date'] ) ? sanitize_text_field( $_POST['booking_date'] ) : '';
	$booking_time      = isset( $_POST['booking_time'] ) ? sanitize_text_field( $_POST['booking_time'] ) : '';
	$notes             = isset( $_POST['notes'] ) ? sanitize_textarea_field( $_POST['notes'] ) : '';
	$source_post_id    = isset( $_POST['source_post_id'] ) ? absint( $_POST['source_post_id'] ) : 0;

	$services_raw = isset( $_POST['services'] ) ? json_decode( wp_unslash( $_POST['services'] ), true ) : array();
	$services_raw = is_array( $services_raw ) ? $services_raw : array();

	# ═══ 2) التحقق من صحة البيانات الأساسية ═══
	$errors = array();
	if ( '' === $customer_name )                 $errors[] = 'الاسم مطلوب';
	if ( '' === $customer_phone )                 $errors[] = 'رقم الهاتف مطلوب';
	if ( empty( $services_raw ) )                 $errors[] = 'يرجى اختيار خدمة واحدة على الأقل';
	if ( '' === $booking_date || '' === $booking_time ) $errors[] = 'يرجى تحديد التاريخ والوقت';

	if ( '' !== $booking_date ) {
		$d = DateTime::createFromFormat( 'Y-m-d', $booking_date );
		$today = new DateTime( 'today' );
		if ( ! $d || $d < $today ) $errors[] = 'التاريخ المحدد غير صالح';
	}

	if ( ! empty( $errors ) ) {

		$json['success'] = false;
		$json['message'] = implode( ' — ', $errors );

	} else {

		# ═══ 3) بناء عناصر الحجز واحتساب السعر ═══
		$subtotal     = 0;
		$items        = array();
		$service_titles = array();

		foreach ( $services_raw as $srv ) {

			$service_id = isset( $srv['id'] ) ? absint( $srv['id'] ) : 0;
			$post       = $service_id ? get_post( $service_id ) : null;
			if ( ! $post || 'services' !== $post->post_type ) continue;

			$price_raw = get_post_meta( $service_id, 'service_price', true );
			$price     = is_numeric( $price_raw ) ? (float) $price_raw : 0;

			$fields_data = isset( $srv['fields'] ) && is_array( $srv['fields'] ) ? $srv['fields'] : array();

			# تعقيم كل قيم الحقول الديناميكية قبل التخزين
			$clean_fields = array();
			foreach ( $fields_data as $fk => $fv ) {
				$fk = sanitize_key( $fk );
				if ( is_array( $fv ) ) {
					$clean_fields[ $fk ] = array_map( 'sanitize_text_field', $fv );
				} else {
					$clean_fields[ $fk ] = sanitize_text_field( (string) $fv );
				}
			}

			# ═══ رفع الصور المرفقة لهذه الخدمة (إن وجدت) ═══
			if ( ! empty( $_FILES ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
				require_once ABSPATH . 'wp-admin/includes/image.php';
				require_once ABSPATH . 'wp-admin/includes/media.php';

				foreach ( $_FILES as $file_key => $file_val ) {
					$prefix = 'kb_file_' . $service_id . '_';
					if ( 0 !== strpos( $file_key, $prefix ) || empty( $file_val['name'] ) ) continue;

					$field_id   = sanitize_key( substr( $file_key, strlen( $prefix ) ) );
					$attach_id  = media_handle_upload( $file_key, 0 );
					if ( ! is_wp_error( $attach_id ) ) {
						$clean_fields[ $field_id ] = wp_get_attachment_url( $attach_id );
					}
				}
			}

			$subtotal          += $price;
			$service_titles[]   = get_the_title( $post );
			$items[] = array(
				'service_id'    => $service_id,
				'service_title' => get_the_title( $post ),
				'unit_price'    => $price,
				'qty'           => 1,
				'fields_data'   => wp_json_encode( $clean_fields ),
			);
		}

		if ( empty( $items ) ) {

			$json['success'] = false;
			$json['message'] = 'تعذّر التعرف على الخدمات المختارة';

		} else {

			$tax_rate = Kayan_Booking::tax_rate();
			$tax      = round( $subtotal * ( $tax_rate / 100 ), 2 );
			$discount = 0;
			$total    = round( $subtotal + $tax - $discount, 2 );
			$currency = Kayan_Booking::currency();

			# ═══ 4) توليد رقم حجز فريد ═══
			$bookings_table = $wpdb->prefix . 'kayan_bookings';
			do {
				$booking_ref = 'KYN-' . strtoupper( substr( md5( uniqid( '', true ) ), 0, 6 ) );
				$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$bookings_table} WHERE booking_ref = %s", $booking_ref ) );
			} while ( $exists );

			$now = current_time( 'mysql' );

			$wpdb->insert(
				$bookings_table,
				array(
					'booking_ref'       => $booking_ref,
					'customer_name'     => $customer_name,
					'customer_phone'    => $customer_phone,
					'customer_whatsapp' => $customer_whatsapp,
					'customer_email'    => $customer_email,
					'country'           => $country,
					'emirate'           => $emirate,
					'city'              => $city,
					'district'          => $district,
					'address'           => $address,
					'building_name'     => $building_name,
					'unit_number'       => $unit_number,
					'floor'             => $floor,
					'lat'               => $lat,
					'lng'               => $lng,
					'booking_date'      => $booking_date,
					'booking_time'      => $booking_time,
					'status'            => 'pending',
					'subtotal'          => $subtotal,
					'tax'               => $tax,
					'discount'          => $discount,
					'total'             => $total,
					'currency'          => $currency,
					'payment_method'    => '',
					'payment_status'    => 'unpaid',
					'notes'             => $notes,
					'source_post_id'    => $source_post_id,
					'ip_address'        => $kb_ip,
					'created_at'        => $now,
					'updated_at'        => $now,
				),
				array( '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%f','%f','%s','%s','%s','%f','%f','%f','%f','%s','%s','%s','%s','%d','%s','%s','%s' )
			);

			$booking_id = (int) $wpdb->insert_id;

			$items_table = $wpdb->prefix . 'kayan_booking_items';
			foreach ( $items as $item ) {
				$wpdb->insert(
					$items_table,
					array(
						'booking_id'    => $booking_id,
						'service_id'    => $item['service_id'],
						'service_title' => $item['service_title'],
						'unit_price'    => $item['unit_price'],
						'qty'           => $item['qty'],
						'fields_data'   => $item['fields_data'],
						'created_at'    => $now,
					),
					array( '%d','%d','%s','%f','%d','%s','%s' )
				);
			}

			$activity_table = $wpdb->prefix . 'kayan_booking_activity';
			$wpdb->insert(
				$activity_table,
				array(
					'booking_id' => $booking_id,
					'status'     => 'pending',
					'note'       => 'تم إنشاء الحجز من نموذج الحجز التلقائي',
					'actor'      => 'customer',
					'created_at' => $now,
				),
				array( '%d','%s','%s','%s','%s' )
			);

			# ═══ 5) إشعار البريد الإلكتروني للإدارة ═══
			$notify_email = Kayan_Booking::option( 'kayan_booking_notify_email', get_bloginfo( 'admin_email' ) );
			$site_name    = get_bloginfo( 'name' );

			echo '<div><ul>';
			echo '<li><strong>رقم الحجز :</strong><span>' . esc_html( $booking_ref ) . '</span></li>';
			echo '<li><strong>الخدمات :</strong><span>' . esc_html( implode( '، ', $service_titles ) ) . '</span></li>';
			echo '<li><strong>العميل :</strong><span>' . esc_html( $customer_name ) . '</span></li>';
			echo '<li><strong>الهاتف :</strong><span>' . esc_html( $customer_phone ) . '</span></li>';
			echo '<li><strong>الموقع :</strong><span>' . esc_html( trim( "$emirate، $city، $district، $address" ) ) . '</span></li>';
			echo '<li><strong>الموعد :</strong><span>' . esc_html( "$booking_date $booking_time" ) . '</span></li>';
			echo '<li><strong>الإجمالي :</strong><span>' . esc_html( $total . ' ' . $currency ) . '</span></li>';
			echo '</ul></div>';
			$mail_output = ob_get_clean();

			wp_mail(
				$notify_email,
				"حجز جديد ({$booking_ref}) عبر {$site_name}",
				$mail_output,
				array( 'Content-Type: text/html; charset=UTF-8' )
			);

			# ═══ 6) رابط تأكيد واتساب (اختياري) ═══
			$whatsapp_number = get_option( 'whatsapp_number' );
			$whatsapp_url    = '';
			if ( ! empty( $whatsapp_number ) ) {
				$msg = "طلب حجز جديد {$booking_ref}%0aالخدمات: " . implode( '، ', $service_titles ) . "%0aالعميل: {$customer_name}%0aالهاتف: {$customer_phone}%0aالموعد: {$booking_date} {$booking_time}%0aالإجمالي: {$total} {$currency}";
				$whatsapp_url = 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $whatsapp_number ) . '?text=' . rawurlencode( $msg );
			}

			set_transient( $kb_rl_key, $kb_rl_hits + 1, 10 * MINUTE_IN_SECONDS );

			$json['success']     = true;
			$json['booking_id']  = $booking_id;
			$json['booking_ref'] = $booking_ref;
			$json['subtotal']    = $subtotal;
			$json['tax']         = $tax;
			$json['total']       = $total;
			$json['currency']    = $currency;
			$json['whatsapp_url']= $whatsapp_url;
			$json['message']     = Kayan_Booking::option( 'kayan_booking_success_message', 'تم استلام طلب حجزك بنجاح.' );
		}
	}
}

if ( ob_get_level() > 0 && ! isset( $mail_output ) ) { ob_end_clean(); }
echo json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
