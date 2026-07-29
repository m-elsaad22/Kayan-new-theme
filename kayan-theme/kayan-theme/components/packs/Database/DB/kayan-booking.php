<?php
/**
 * kayan-booking DB Schema (v1.0.0)
 * ════════════════════════════════════════════════════════════════
 * جداول نظام الحجز متعدد الخطوات — تُنشأ عبر dbDelta الآمن.
 * لا يوجد Composer/Namespaces، بنفس نمط تحميل ملفات Database/DB الحالي.
 * ════════════════════════════════════════════════════════════════
 */

if ( ! function_exists( 'kayan_booking_install_tables' ) ) {

	function kayan_booking_install_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$bookings_table  = $wpdb->prefix . 'kayan_bookings';
		$items_table     = $wpdb->prefix . 'kayan_booking_items';
		$activity_table  = $wpdb->prefix . 'kayan_booking_activity';

		$sql = "CREATE TABLE {$bookings_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			booking_ref VARCHAR(20) NOT NULL,
			customer_name VARCHAR(190) NOT NULL DEFAULT '',
			customer_phone VARCHAR(30) NOT NULL DEFAULT '',
			customer_whatsapp VARCHAR(30) NOT NULL DEFAULT '',
			customer_email VARCHAR(190) NOT NULL DEFAULT '',
			country VARCHAR(60) NOT NULL DEFAULT '',
			emirate VARCHAR(60) NOT NULL DEFAULT '',
			city VARCHAR(60) NOT NULL DEFAULT '',
			district VARCHAR(120) NOT NULL DEFAULT '',
			address TEXT NULL,
			building_name VARCHAR(190) NOT NULL DEFAULT '',
			unit_number VARCHAR(60) NOT NULL DEFAULT '',
			floor VARCHAR(30) NOT NULL DEFAULT '',
			lat DECIMAL(10,7) NULL,
			lng DECIMAL(10,7) NULL,
			booking_date DATE NULL,
			booking_time TIME NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
			tax DECIMAL(12,2) NOT NULL DEFAULT 0,
			discount DECIMAL(12,2) NOT NULL DEFAULT 0,
			total DECIMAL(12,2) NOT NULL DEFAULT 0,
			currency VARCHAR(10) NOT NULL DEFAULT 'AED',
			payment_method VARCHAR(30) NOT NULL DEFAULT '',
			payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid',
			notes TEXT NULL,
			source_post_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			ip_address VARCHAR(45) NOT NULL DEFAULT '',
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY booking_ref (booking_ref),
			KEY status (status),
			KEY booking_date (booking_date),
			KEY customer_phone (customer_phone)
		) {$charset_collate};";
		dbDelta( $sql );

		$sql_items = "CREATE TABLE {$items_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			booking_id BIGINT UNSIGNED NOT NULL,
			service_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			service_title VARCHAR(190) NOT NULL DEFAULT '',
			unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
			qty SMALLINT UNSIGNED NOT NULL DEFAULT 1,
			fields_data LONGTEXT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY booking_id (booking_id),
			KEY service_id (service_id)
		) {$charset_collate};";
		dbDelta( $sql_items );

		$sql_activity = "CREATE TABLE {$activity_table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			booking_id BIGINT UNSIGNED NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT '',
			note TEXT NULL,
			actor VARCHAR(60) NOT NULL DEFAULT '',
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY booking_id (booking_id)
		) {$charset_collate};";
		dbDelta( $sql_activity );

		update_option( 'kayan_booking_db_version', '1.0.0' );
	}
	add_action( 'after_switch_theme', 'kayan_booking_install_tables' );

	# تأكد من وجود الجداول تلقائياً بعد أي تحديث للقالب (بدون تفعيل يدوي)
	function kayan_booking_maybe_install_tables() {
		if ( get_option( 'kayan_booking_db_version' ) !== '1.0.0' ) {
			kayan_booking_install_tables();
		}
	}
	add_action( 'init', 'kayan_booking_maybe_install_tables', 1 );
}
