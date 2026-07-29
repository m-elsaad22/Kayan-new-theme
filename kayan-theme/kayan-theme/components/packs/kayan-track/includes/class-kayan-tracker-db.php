<?php 
if ( ! class_exists( 'Kayan_Tracker_DB' ) ) {

	class Kayan_Tracker_DB {

		public static function install() {
			global $wpdb;
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$charset = $wpdb->get_charset_collate();
			$p       = $wpdb->prefix;

			$sql = array();

			$sql[] = "CREATE TABLE {$p}kayan_conversions (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				number_id BIGINT UNSIGNED DEFAULT NULL,
				phone_raw VARCHAR(30) NOT NULL DEFAULT '',
				session_id VARCHAR(64) NOT NULL DEFAULT '',
				fingerprint VARCHAR(64) NOT NULL DEFAULT '',
				ip VARCHAR(45) NOT NULL DEFAULT '',
				ip_hash VARCHAR(64) NOT NULL DEFAULT '',
				country VARCHAR(100) NOT NULL DEFAULT '',
				city VARCHAR(100) NOT NULL DEFAULT '',
				device_type VARCHAR(20) NOT NULL DEFAULT '',
				browser VARCHAR(60) NOT NULL DEFAULT '',
				os VARCHAR(60) NOT NULL DEFAULT '',
				click_type VARCHAR(20) NOT NULL DEFAULT '',
				page_url TEXT NOT NULL,
				page_title VARCHAR(255) NOT NULL DEFAULT '',
				page_service VARCHAR(255) NOT NULL DEFAULT '',
				referrer TEXT,
				utm_source VARCHAR(100) NOT NULL DEFAULT '',
				utm_medium VARCHAR(100) NOT NULL DEFAULT '',
				utm_campaign VARCHAR(200) NOT NULL DEFAULT '',
				traffic_src VARCHAR(40) NOT NULL DEFAULT 'direct',
				gclid VARCHAR(100) NOT NULL DEFAULT '',
				scroll_depth TINYINT NOT NULL DEFAULT 0,
				time_on_page SMALLINT NOT NULL DEFAULT 0,
				is_suspicious TINYINT(1) NOT NULL DEFAULT 0,
				is_duplicate TINYINT(1) NOT NULL DEFAULT 0,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY fingerprint (fingerprint),
				KEY click_type (click_type),
				KEY ip (ip),
				KEY page_url (page_url(191)),
				KEY page_service (page_service),
				KEY created_at (created_at),
				KEY city (city),
				KEY country (country)
			) {$charset};";

			$sql[] = "CREATE TABLE {$p}kayan_numbers (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				label VARCHAR(100) NOT NULL DEFAULT '',
				phone VARCHAR(30) NOT NULL DEFAULT '',
				wa_number VARCHAR(30) NOT NULL DEFAULT '',
				color VARCHAR(7) NOT NULL DEFAULT '#0056b3',
				description VARCHAR(255) NOT NULL DEFAULT '',
				active TINYINT(1) NOT NULL DEFAULT 1,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			) {$charset};";

			$sql[] = "CREATE TABLE {$p}kayan_dni_rules (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				number_id BIGINT UNSIGNED NOT NULL,
				source_type VARCHAR(30) NOT NULL DEFAULT 'direct',
				utm_source VARCHAR(100) NOT NULL DEFAULT '',
				utm_medium VARCHAR(100) NOT NULL DEFAULT '',
				utm_campaign VARCHAR(200) NOT NULL DEFAULT '',
				priority TINYINT NOT NULL DEFAULT 0,
				active TINYINT(1) NOT NULL DEFAULT 1,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY number_id (number_id),
				KEY source_type (source_type)
			) {$charset};";

			$sql[] = "CREATE TABLE {$p}kayan_visitors (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				fingerprint VARCHAR(64) NOT NULL DEFAULT '',
				session_id VARCHAR(64) NOT NULL DEFAULT '',
				ip VARCHAR(45) NOT NULL DEFAULT '',
				country VARCHAR(100) NOT NULL DEFAULT '',
				city VARCHAR(100) NOT NULL DEFAULT '',
				device_type VARCHAR(20) NOT NULL DEFAULT '',
				browser VARCHAR(60) NOT NULL DEFAULT '',
				os VARCHAR(60) NOT NULL DEFAULT '',
				screen_res VARCHAR(20) NOT NULL DEFAULT '',
				language VARCHAR(10) NOT NULL DEFAULT '',
				referrer TEXT,
				utm_source VARCHAR(100) NOT NULL DEFAULT '',
				utm_medium VARCHAR(100) NOT NULL DEFAULT '',
				utm_campaign VARCHAR(200) NOT NULL DEFAULT '',
				traffic_src VARCHAR(40) NOT NULL DEFAULT 'direct',
				visit_count SMALLINT NOT NULL DEFAULT 1,
				is_new TINYINT(1) NOT NULL DEFAULT 1,
				first_visit DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				last_visit DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY fingerprint (fingerprint),
				KEY ip (ip),
				KEY last_visit (last_visit)
			) {$charset};";

			$sql[] = "CREATE TABLE {$p}kayan_sessions (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				session_id VARCHAR(64) NOT NULL DEFAULT '',
				fingerprint VARCHAR(64) NOT NULL DEFAULT '',
				ip VARCHAR(45) NOT NULL DEFAULT '',
				start_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				end_time DATETIME DEFAULT NULL,
				duration SMALLINT NOT NULL DEFAULT 0,
				page_count TINYINT NOT NULL DEFAULT 1,
				scroll_max TINYINT NOT NULL DEFAULT 0,
				is_new TINYINT(1) NOT NULL DEFAULT 1,
				PRIMARY KEY (id),
				KEY session_id (session_id),
				KEY fingerprint (fingerprint),
				KEY start_time (start_time)
			) {$charset};";

			$sql[] = "CREATE TABLE {$p}kayan_heatmap (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				session_id VARCHAR(64) NOT NULL DEFAULT '',
				page_url TEXT NOT NULL,
				x_pct TINYINT NOT NULL DEFAULT 0,
				y_pct TINYINT NOT NULL DEFAULT 0,
				element VARCHAR(255) NOT NULL DEFAULT '',
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY session_id (session_id),
				KEY created_at (created_at)
			) {$charset};";

			$sql[] = "CREATE TABLE {$p}kayan_blacklist (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				ip VARCHAR(45) NOT NULL DEFAULT '',
				reason VARCHAR(100) NOT NULL DEFAULT 'manual',
				click_count SMALLINT NOT NULL DEFAULT 0,
				blocked_by VARCHAR(20) NOT NULL DEFAULT 'admin',
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY ip (ip)
			) {$charset};";

			$sql[] = "CREATE TABLE {$p}kayan_reports (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				token VARCHAR(64) NOT NULL DEFAULT '',
				title VARCHAR(255) NOT NULL DEFAULT '',
				filters_json TEXT NOT NULL,
				data_json LONGTEXT NOT NULL,
				created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
				view_count SMALLINT NOT NULL DEFAULT 0,
				expires_at DATETIME NOT NULL,
				created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				UNIQUE KEY token (token)
			) {$charset};";

			foreach ( $sql as $query ) {
				dbDelta( $query );
			}

			self::maybe_seed_default_number();
			self::register_cron();
		}

		public static function maybe_seed_default_number() {
			global $wpdb;
			$table = kayan_track_table( 'numbers' );
			$count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
			if ( $count > 0 ) {
				return;
			}
			$phone = yc_get_option( 'contact_number' );
			if ( empty( $phone ) ) {
				return;
			}
			$clean = preg_replace( '/[^0-9]/', '', $phone );
			$wpdb->insert(
				$table,
				array(
					'label'       => 'الرقم الرئيسي',
					'phone'       => $clean,
					'wa_number'   => $clean,
					'color'       => '#0056b3',
					'description' => 'من إعدادات القالب',
					'active'      => 1,
					'created_at'  => current_time( 'mysql' ),
				)
			);
		}

		public static function register_cron() {
			if ( ! wp_next_scheduled( 'kayan_track_daily_cleanup' ) ) {
				wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'kayan_track_daily_cleanup' );
			}
		}

		public static function cleanup_old_data() {
			global $wpdb;
			$days   = (int) kayan_track_get_option( 'kayan_tracking_retention', 90 );
			$cutoff = date( 'Y-m-d H:i:s', strtotime( '-' . $days . ' days' ) );

			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM " . kayan_track_table( 'conversions' ) . " WHERE created_at < %s",
					$cutoff
				)
			);
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM " . kayan_track_table( 'heatmap' ) . " WHERE created_at < %s",
					$cutoff
				)
			);
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM " . kayan_track_table( 'sessions' ) . " WHERE start_time < %s",
					$cutoff
				)
			);
		}

		public static function get_table_sizes() {
			global $wpdb;
			$tables = array(
				'conversions' => kayan_track_table( 'conversions' ),
				'visitors'    => kayan_track_table( 'visitors' ),
				'sessions'    => kayan_track_table( 'sessions' ),
				'heatmap'     => kayan_track_table( 'heatmap' ),
				'numbers'     => kayan_track_table( 'numbers' ),
				'blacklist'   => kayan_track_table( 'blacklist' ),
				'reports'     => kayan_track_table( 'reports' ),
			);
			$sizes  = array();
			foreach ( $tables as $key => $table ) {
				$row = $wpdb->get_row(
					$wpdb->prepare(
						'SELECT ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb FROM information_schema.TABLES WHERE table_schema = %s AND table_name = %s',
						DB_NAME,
						$table
					),
					ARRAY_A
				);
				$sizes[ $key ] = isset( $row['size_mb'] ) ? (float) $row['size_mb'] : 0;
			}
			return $sizes;
		}
	}
}
