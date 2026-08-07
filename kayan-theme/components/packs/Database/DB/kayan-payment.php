<?php
/**
 * kayan-payment DB Schema (v1.0.0)
 * ════════════════════════════════════════════════════════════════
 * جدول سجل عمليات الدفع (Demo Gateway) — منفصل عن wp_kayan_bookings
 * لتتبّع كل محاولة دفع (نجاح/فشل/OTP) بشكل مستقل قابل للتدقيق.
 * ════════════════════════════════════════════════════════════════
 */

if ( ! function_exists( 'kayan_payment_install_tables' ) ) {

	function kayan_payment_install_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$table = $wpdb->prefix . 'kayan_payments';

		$sql = "CREATE TABLE {$table} (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			booking_id BIGINT UNSIGNED NOT NULL,
			txn_ref VARCHAR(30) NOT NULL,
			method VARCHAR(20) NOT NULL DEFAULT '',
			amount DECIMAL(12,2) NOT NULL DEFAULT 0,
			currency VARCHAR(10) NOT NULL DEFAULT 'AED',
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			card_brand VARCHAR(20) NOT NULL DEFAULT '',
			card_last4 VARCHAR(4) NOT NULL DEFAULT '',
			otp_attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
			invoice_number VARCHAR(30) NOT NULL DEFAULT '',
			created_at DATETIME NOT NULL,
			updated_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY txn_ref (txn_ref),
			KEY booking_id (booking_id),
			KEY status (status)
		) {$charset_collate};";
		dbDelta( $sql );

		update_option( 'kayan_payment_db_version', '1.0.0' );
	}
	add_action( 'after_switch_theme', 'kayan_payment_install_tables' );

	function kayan_payment_maybe_install_tables() {
		if ( get_option( 'kayan_payment_db_version' ) !== '1.0.0' ) {
			kayan_payment_install_tables();
		}
	}
	add_action( 'init', 'kayan_payment_maybe_install_tables', 1 );
}
