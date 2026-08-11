<?php 
if ( ! class_exists( 'Kayan_Tracker_Admin' ) ) {

	class Kayan_Tracker_Admin {

		private $slug = 'kayan-track-pro';

		public function __construct() {
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );

			$actions = array(
				'kayan_track_overview',
				'kayan_track_conversions',
				'kayan_track_pages',
				'kayan_track_analytics',
				'kayan_track_numbers_list',
				'kayan_track_number_save',
				'kayan_track_number_delete',
				'kayan_track_visitors',
				'kayan_track_blacklist',
				'kayan_track_block_ip',
				'kayan_track_unblock_ip',
				'kayan_track_dni_list',
				'kayan_track_dni_save',
				'kayan_track_dni_delete',
				'kayan_track_settings_get',
				'kayan_track_settings_save',
				'kayan_track_export_csv',
				'kayan_track_generate_report',
			);

			foreach ( $actions as $action ) {
				add_action( 'wp_ajax_' . $action, array( $this, 'ajax_' . str_replace( 'kayan_track_', '', $action ) ) );
			}
		}

		public function register_menu() {
			add_menu_page(
				'تتبع كيان',
				'تتبع كيان',
				'manage_options',
				$this->slug,
				array( $this, 'render_shell' ),
				'dashicons-chart-area',
				26
			);
		}

		public function enqueue_assets( $hook ) {
			if ( strpos( $hook, $this->slug ) === false ) {
				return;
			}

			$base = kayan_track_pack_url();
			$path = kayan_track_pack_path();

			wp_enqueue_style(
				'kayan-track-admin',
				$base . 'admin/css/track-admin.css',
				array(),
				file_exists( $path . 'admin/css/track-admin.css' ) ? (string) filemtime( $path . 'admin/css/track-admin.css' ) : KAYAN_TRACK_VERSION
			);
			wp_enqueue_script(
				'chartjs',
				'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js',
				array(),
				'4',
				true
			);
			wp_enqueue_script(
				'kayan-track-admin',
				$base . 'admin/js/track-admin.js',
				array( 'chartjs' ),
				file_exists( $path . 'admin/js/track-admin.js' ) ? (string) filemtime( $path . 'admin/js/track-admin.js' ) : KAYAN_TRACK_VERSION,
				true
			);

			wp_localize_script(
				'kayan-track-admin',
				'KayanTrackAdmin',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'kayan_track_nonce' ),
					'homeUrl' => home_url( '/' ),
				)
			);
		}

		public function render_shell() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			echo '<div class="wrap kayan-track-wrap" id="kayan-track-app">';
			echo '<iframe id="kayan-track-frame" src="' . esc_url( admin_url( 'admin.php?page=' . $this->slug . '&kt_view=app' ) ) . '" title="تتبع كيان"></iframe>';
			echo '</div>';
		}

		private function verify() {
			check_ajax_referer( 'kayan_track_nonce', 'nonce' );
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
			}
		}

		private function filters_from_request() {
			$preset = sanitize_key( isset( $_POST['preset'] ) ? $_POST['preset'] : '30d' );
			$from   = sanitize_text_field( isset( $_POST['date_from'] ) ? $_POST['date_from'] : '' );
			$to     = sanitize_text_field( isset( $_POST['date_to'] ) ? $_POST['date_to'] : '' );
			return kayan_track_date_range( $preset, $from, $to );
		}

		private function where_clause( $range, $alias = '' ) {
			$pre = $alias ? $alias . '.' : '';
			return array(
				'sql'  => " AND {$pre}created_at >= %s AND {$pre}created_at <= %s",
				'args' => array( $range['from'], $range['to'] ),
			);
		}

		public function ajax_overview() {
			$this->verify();
			global $wpdb;
			$range = $this->filters_from_request();
			$table = kayan_track_table( 'conversions' );
			$w     = $this->where_clause( $range );

			$stats = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT
						COUNT(*) AS total,
						SUM(click_type = 'call') AS calls,
						SUM(click_type = 'whatsapp') AS whatsapp,
						COUNT(DISTINCT fingerprint) AS unique_fp,
						SUM(is_suspicious = 1) AS suspicious
					FROM {$table}
					WHERE is_duplicate = 0 {$w['sql']}",
					$w['args']
				),
				ARRAY_A
			);

			$daily = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT DATE(created_at) AS day,
						SUM(click_type = 'call') AS calls,
						SUM(click_type = 'whatsapp') AS whatsapp
					FROM {$table}
					WHERE is_duplicate = 0 {$w['sql']}
					GROUP BY DATE(created_at)
					ORDER BY day ASC",
					$w['args']
				),
				ARRAY_A
			);

			$top_pages = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT page_title, page_url, COUNT(*) AS total
					FROM {$table}
					WHERE is_suspicious = 0 {$w['sql']}
					GROUP BY page_url
					ORDER BY total DESC
					LIMIT 5",
					$w['args']
				),
				ARRAY_A
			);

			$top_cities = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT city, COUNT(*) AS total
					FROM {$table}
					WHERE is_suspicious = 0 AND city != '' {$w['sql']}
					GROUP BY city
					ORDER BY total DESC
					LIMIT 5",
					$w['args']
				),
				ARRAY_A
			);

			$sus_ips = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ip, COUNT(*) AS cnt
					FROM {$table}
					WHERE is_suspicious = 1 {$w['sql']}
					GROUP BY ip
					ORDER BY cnt DESC
					LIMIT 10",
					$w['args']
				),
				ARRAY_A
			);

			wp_send_json_success(
				array(
					'stats'      => $stats,
					'daily'      => $daily,
					'top_pages'  => $top_pages,
					'top_cities' => $top_cities,
					'sus_ips'    => $sus_ips,
					'range'      => $range,
				)
			);
		}

		public function ajax_conversions() {
			$this->verify();
			global $wpdb;
			$range   = $this->filters_from_request();
			$table   = kayan_track_table( 'conversions' );
			$page    = max( 1, (int) ( isset( $_POST['page_num'] ) ? $_POST['page_num'] : 1 ) );
			$per     = 50;
			$offset  = ( $page - 1 ) * $per;
			$sort    = sanitize_key( isset( $_POST['sort'] ) ? $_POST['sort'] : 'created_at' );
			$order   = strtoupper( sanitize_text_field( isset( $_POST['order'] ) ? $_POST['order'] : 'DESC' ) ) === 'ASC' ? 'ASC' : 'DESC';
			$search  = sanitize_text_field( isset( $_POST['search'] ) ? $_POST['search'] : '' );
			$type    = sanitize_key( isset( $_POST['click_type'] ) ? $_POST['click_type'] : '' );
			$device  = sanitize_key( isset( $_POST['device'] ) ? $_POST['device'] : '' );
			$sus     = sanitize_key( isset( $_POST['suspicious'] ) ? $_POST['suspicious'] : '' );

			$allowed_sort = array( 'created_at', 'page_title', 'city', 'ip' );
			if ( ! in_array( $sort, $allowed_sort, true ) ) {
				$sort = 'created_at';
			}

			$where  = 'WHERE is_duplicate = 0 AND created_at >= %s AND created_at <= %s';
			$args   = array( $range['from'], $range['to'] );

			if ( $type ) {
				$where .= ' AND click_type = %s';
				$args[] = $type;
			}
			if ( $device ) {
				$where .= ' AND device_type = %s';
				$args[] = $device;
			}
			if ( $sus === 'yes' ) {
				$where .= ' AND is_suspicious = 1';
			} elseif ( $sus === 'no' ) {
				$where .= ' AND is_suspicious = 0';
			}
			if ( $search ) {
				$like    = '%' . $wpdb->esc_like( $search ) . '%';
				$where  .= ' AND (ip LIKE %s OR phone_raw LIKE %s OR city LIKE %s OR page_title LIKE %s OR page_url LIKE %s OR traffic_src LIKE %s OR browser LIKE %s)';
				$args[]  = $like;
				$args[]  = $like;
				$args[]  = $like;
				$args[]  = $like;
				$args[]  = $like;
				$args[]  = $like;
				$args[]  = $like;
			}

			$total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} {$where}", $args ) );

			$list_args   = $args;
			$list_args[] = $per;
			$list_args[] = $offset;

			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$table} {$where} ORDER BY {$sort} {$order} LIMIT %d OFFSET %d",
					$list_args
				),
				ARRAY_A
			);

			wp_send_json_success(
				array(
					'rows'  => $rows,
					'total' => $total,
					'page'  => $page,
					'pages' => (int) ceil( $total / $per ),
				)
			);
		}

		public function ajax_pages() {
			$this->verify();
			global $wpdb;
			$range = $this->filters_from_request();
			$table = kayan_track_table( 'conversions' );

			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT page_title, page_url, page_service,
						COUNT(*) AS total,
						SUM(click_type = 'call') AS calls,
						SUM(click_type = 'whatsapp') AS whatsapp,
						COUNT(DISTINCT fingerprint) AS unique_visitors
					FROM {$table}
					WHERE is_suspicious = 0 AND created_at >= %s AND created_at <= %s
					GROUP BY page_url
					ORDER BY total DESC
					LIMIT 100",
					$range['from'],
					$range['to']
				),
				ARRAY_A
			);

			wp_send_json_success( array( 'rows' => $rows, 'range' => $range ) );
		}

		public function ajax_analytics() {
			$this->verify();
			global $wpdb;
			$range = $this->filters_from_request();
			$table = kayan_track_table( 'conversions' );

			$services = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT page_service AS name, COUNT(*) AS total
					FROM {$table}
					WHERE is_suspicious = 0 AND created_at >= %s AND created_at <= %s AND page_service != ''
					GROUP BY page_service ORDER BY total DESC LIMIT 10",
					$range['from'],
					$range['to']
				),
				ARRAY_A
			);

			$cities = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT city, country, COUNT(*) AS total,
						SUM(click_type = 'call') AS calls,
						SUM(click_type = 'whatsapp') AS whatsapp
					FROM {$table}
					WHERE is_suspicious = 0 AND created_at >= %s AND created_at <= %s AND city != ''
					GROUP BY city ORDER BY total DESC LIMIT 10",
					$range['from'],
					$range['to']
				),
				ARRAY_A
			);

			$devices = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT device_type AS name, COUNT(*) AS total
					FROM {$table}
					WHERE is_suspicious = 0 AND created_at >= %s AND created_at <= %s
					GROUP BY device_type ORDER BY total DESC",
					$range['from'],
					$range['to']
				),
				ARRAY_A
			);

			$sources = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT traffic_src AS name, COUNT(*) AS total
					FROM {$table}
					WHERE is_suspicious = 0 AND created_at >= %s AND created_at <= %s
					GROUP BY traffic_src ORDER BY total DESC",
					$range['from'],
					$range['to']
				),
				ARRAY_A
			);

			$channel = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT SUM(click_type = 'call') AS calls, SUM(click_type = 'whatsapp') AS whatsapp
					FROM {$table}
					WHERE is_suspicious = 0 AND created_at >= %s AND created_at <= %s",
					$range['from'],
					$range['to']
				),
				ARRAY_A
			);

			wp_send_json_success(
				array(
					'services' => $services,
					'cities'   => $cities,
					'devices'  => $devices,
					'sources'  => $sources,
					'channel'  => $channel,
					'range'    => $range,
				)
			);
		}

		public function ajax_numbers_list() {
			$this->verify();
			global $wpdb;
			$range = $this->filters_from_request();
			$table = kayan_track_table( 'numbers' );
			$conv  = kayan_track_table( 'conversions' );

			$numbers = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id ASC", ARRAY_A );
			foreach ( $numbers as $i => $num ) {
				$stats = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT SUM(click_type = 'call') AS calls, SUM(click_type = 'whatsapp') AS whatsapp,
							COUNT(DISTINCT fingerprint) AS unique_fp
						FROM {$conv}
						WHERE number_id = %d AND created_at >= %s AND created_at <= %s",
						(int) $num['id'],
						$range['from'],
						$range['to']
					),
					ARRAY_A
				);
				$numbers[ $i ]['stats'] = $stats;
			}

			wp_send_json_success( array( 'numbers' => $numbers ) );
		}

		public function ajax_number_save() {
			$this->verify();
			global $wpdb;
			$id    = (int) ( isset( $_POST['id'] ) ? $_POST['id'] : 0 );
			$data  = array(
				'label'       => sanitize_text_field( isset( $_POST['label'] ) ? $_POST['label'] : '' ),
				'phone'       => preg_replace( '/[^0-9]/', '', isset( $_POST['phone'] ) ? $_POST['phone'] : '' ),
				'wa_number'   => preg_replace( '/[^0-9]/', '', isset( $_POST['wa_number'] ) ? $_POST['wa_number'] : '' ),
				'color'       => sanitize_hex_color( isset( $_POST['color'] ) ? $_POST['color'] : '#0056b3' ),
				'description' => sanitize_text_field( isset( $_POST['description'] ) ? $_POST['description'] : '' ),
				'active'      => empty( $_POST['active'] ) ? 0 : 1,
			);
			$table = kayan_track_table( 'numbers' );

			if ( $id ) {
				$wpdb->update( $table, $data, array( 'id' => $id ) );
			} else {
				$data['created_at'] = current_time( 'mysql' );
				$wpdb->insert( $table, $data );
				$id = (int) $wpdb->insert_id;
			}

			wp_send_json_success( array( 'id' => $id ) );
		}

		public function ajax_number_delete() {
			$this->verify();
			global $wpdb;
			$id = (int) ( isset( $_POST['id'] ) ? $_POST['id'] : 0 );
			if ( $id ) {
				$wpdb->delete( kayan_track_table( 'numbers' ), array( 'id' => $id ) );
			}
			wp_send_json_success();
		}

		public function ajax_visitors() {
			$this->verify();
			global $wpdb;
			$table = kayan_track_table( 'visitors' );
			$rows  = $wpdb->get_results(
				"SELECT * FROM {$table} ORDER BY last_visit DESC LIMIT 100",
				ARRAY_A
			);
			wp_send_json_success( array( 'rows' => $rows ) );
		}

		public function ajax_blacklist() {
			$this->verify();
			global $wpdb;
			$rows = $wpdb->get_results(
				'SELECT * FROM ' . kayan_track_table( 'blacklist' ) . ' ORDER BY created_at DESC LIMIT 200',
				ARRAY_A
			);
			wp_send_json_success( array( 'rows' => $rows ) );
		}

		public function ajax_block_ip() {
			$this->verify();
			global $wpdb;
			$ip = sanitize_text_field( isset( $_POST['ip'] ) ? $_POST['ip'] : '' );
			if ( ! $ip ) {
				wp_send_json_error( array( 'message' => 'missing ip' ) );
			}
			$wpdb->query(
				$wpdb->prepare(
					'INSERT IGNORE INTO ' . kayan_track_table( 'blacklist' ) . ' (ip, reason, click_count, blocked_by, created_at) VALUES (%s, %s, %d, %s, %s)',
					$ip,
					'manual',
					0,
					'admin',
					current_time( 'mysql' )
				)
			);
			wp_send_json_success();
		}

		public function ajax_unblock_ip() {
			$this->verify();
			global $wpdb;
			$ip = sanitize_text_field( isset( $_POST['ip'] ) ? $_POST['ip'] : '' );
			if ( $ip ) {
				$wpdb->delete( kayan_track_table( 'blacklist' ), array( 'ip' => $ip ) );
			}
			wp_send_json_success();
		}

		public function ajax_dni_list() {
			$this->verify();
			global $wpdb;
			$rules = $wpdb->get_results(
				'SELECT r.*, n.label AS number_label FROM ' . kayan_track_table( 'dni_rules' ) . ' r
				LEFT JOIN ' . kayan_track_table( 'numbers' ) . ' n ON n.id = r.number_id
				ORDER BY r.priority DESC, r.id ASC',
				ARRAY_A
			);
			$numbers = $wpdb->get_results( 'SELECT id, label, phone FROM ' . kayan_track_table( 'numbers' ) . ' WHERE active = 1', ARRAY_A );
			wp_send_json_success( array( 'rules' => $rules, 'numbers' => $numbers ) );
		}

		public function ajax_dni_save() {
			$this->verify();
			global $wpdb;
			$id = (int) ( isset( $_POST['id'] ) ? $_POST['id'] : 0 );
			$data = array(
				'number_id'    => (int) ( isset( $_POST['number_id'] ) ? $_POST['number_id'] : 0 ),
				'source_type'  => sanitize_key( isset( $_POST['source_type'] ) ? $_POST['source_type'] : 'direct' ),
				'utm_source'   => sanitize_text_field( isset( $_POST['utm_source'] ) ? $_POST['utm_source'] : '' ),
				'utm_medium'   => sanitize_text_field( isset( $_POST['utm_medium'] ) ? $_POST['utm_medium'] : '' ),
				'utm_campaign' => sanitize_text_field( isset( $_POST['utm_campaign'] ) ? $_POST['utm_campaign'] : '' ),
				'priority'     => (int) ( isset( $_POST['priority'] ) ? $_POST['priority'] : 0 ),
				'active'       => empty( $_POST['active'] ) ? 0 : 1,
			);
			$table = kayan_track_table( 'dni_rules' );
			if ( $id ) {
				$wpdb->update( $table, $data, array( 'id' => $id ) );
			} else {
				$data['created_at'] = current_time( 'mysql' );
				$wpdb->insert( $table, $data );
				$id = (int) $wpdb->insert_id;
			}
			wp_send_json_success( array( 'id' => $id ) );
		}

		public function ajax_dni_delete() {
			$this->verify();
			global $wpdb;
			$id = (int) ( isset( $_POST['id'] ) ? $_POST['id'] : 0 );
			if ( $id ) {
				$wpdb->delete( kayan_track_table( 'dni_rules' ), array( 'id' => $id ) );
			}
			wp_send_json_success();
		}

		public function ajax_settings_get() {
			$this->verify();
			wp_send_json_success(
				array(
					'kayan_track_disable'        => kayan_track_get_option( 'kayan_track_disable', '' ),
					'kayan_tracking_retention'   => (int) kayan_track_get_option( 'kayan_tracking_retention', 90 ),
					'kayan_cooldown_mins'        => (int) kayan_track_get_option( 'kayan_cooldown_mins', 30 ),
					'kayan_fraud_threshold'      => (int) kayan_track_get_option( 'kayan_fraud_threshold', 3 ),
					'kayan_auto_blacklist'       => (int) kayan_track_get_option( 'kayan_auto_blacklist', 5 ),
					'kayan_telegram_on'          => kayan_track_get_option( 'kayan_telegram_on', '' ),
					'kayan_telegram_token'       => kayan_track_get_option( 'kayan_telegram_token', '' ),
					'kayan_telegram_chat'        => kayan_track_get_option( 'kayan_telegram_chat', '' ),
					'kayan_dni_enabled'          => kayan_track_get_option( 'kayan_dni_enabled', '' ),
					'kayan_report_expiry_days'   => (int) kayan_track_get_option( 'kayan_report_expiry_days', 30 ),
					'table_sizes'                => Kayan_Tracker_DB::get_table_sizes(),
				)
			);
		}

		public function ajax_settings_save() {
			$this->verify();
			$keys = array(
				'kayan_track_disable',
				'kayan_tracking_retention',
				'kayan_cooldown_mins',
				'kayan_fraud_threshold',
				'kayan_auto_blacklist',
				'kayan_telegram_on',
				'kayan_telegram_token',
				'kayan_telegram_chat',
				'kayan_dni_enabled',
				'kayan_report_expiry_days',
			);
			foreach ( $keys as $key ) {
				if ( isset( $_POST[ $key ] ) ) {
					$val = is_array( $_POST[ $key ] ) ? '' : wp_unslash( $_POST[ $key ] );
					if ( in_array( $key, array( 'kayan_tracking_retention', 'kayan_cooldown_mins', 'kayan_fraud_threshold', 'kayan_auto_blacklist', 'kayan_report_expiry_days' ), true ) ) {
						$val = (int) $val;
					} else {
						$val = sanitize_text_field( $val );
					}
					kayan_track_update_option( $key, $val );
				}
			}
			wp_send_json_success();
		}

		public function ajax_export_csv() {
			$this->verify();
			global $wpdb;
			$range  = $this->filters_from_request();
			$table  = kayan_track_table( 'conversions' );
			$type   = sanitize_key( isset( $_POST['click_type'] ) ? $_POST['click_type'] : '' );
			$device = sanitize_key( isset( $_POST['device'] ) ? $_POST['device'] : '' );
			$search = sanitize_text_field( isset( $_POST['search'] ) ? $_POST['search'] : '' );

			$where = 'WHERE is_duplicate = 0 AND created_at >= %s AND created_at <= %s';
			$args  = array( $range['from'], $range['to'] );

			if ( $type ) {
				$where .= ' AND click_type = %s';
				$args[] = $type;
			}
			if ( $device ) {
				$where .= ' AND device_type = %s';
				$args[] = $device;
			}
			if ( $search ) {
				$like    = '%' . $wpdb->esc_like( $search ) . '%';
				$where  .= ' AND (ip LIKE %s OR phone_raw LIKE %s OR city LIKE %s OR page_title LIKE %s OR page_url LIKE %s)';
				$args[]  = $like;
				$args[]  = $like;
				$args[]  = $like;
				$args[]  = $like;
				$args[]  = $like;
			}

			$list_args   = $args;
			$list_args[] = 5000;

			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d",
					$list_args
				),
				ARRAY_A
			);

			$csv = "id,click_type,phone,ip,country,city,device,browser,page_title,page_url,traffic_src,created_at\n";
			foreach ( $rows as $row ) {
				$csv .= implode(
					',',
					array(
						$row['id'],
						$row['click_type'],
						'"' . str_replace( '"', '""', $row['phone_raw'] ) . '"',
						$row['ip'],
						'"' . str_replace( '"', '""', $row['country'] ) . '"',
						'"' . str_replace( '"', '""', $row['city'] ) . '"',
						$row['device_type'],
						isset( $row['browser'] ) ? $row['browser'] : '',
						'"' . str_replace( '"', '""', $row['page_title'] ) . '"',
						'"' . str_replace( '"', '""', $row['page_url'] ) . '"',
						$row['traffic_src'],
						$row['created_at'],
					)
				) . "\n";
			}

			wp_send_json_success( array( 'csv' => $csv ) );
		}

		public function ajax_generate_report() {
			$this->verify();
			$title  = sanitize_text_field( isset( $_POST['title'] ) ? $_POST['title'] : 'تقرير التحويلات' );
			$range  = $this->filters_from_request();
			global $wpdb;
			$table = kayan_track_table( 'conversions' );

			$totals = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT COUNT(*) AS total,
						SUM(click_type = 'call') AS calls,
						SUM(click_type = 'whatsapp') AS whatsapp
					FROM {$table}
					WHERE is_suspicious = 0 AND created_at >= %s AND created_at <= %s",
					$range['from'],
					$range['to']
				),
				ARRAY_A
			);

			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT page_title, page_url,
						COUNT(*) AS total,
						SUM(click_type = 'call') AS calls,
						SUM(click_type = 'whatsapp') AS whatsapp
					FROM {$table}
					WHERE is_suspicious = 0 AND created_at >= %s AND created_at <= %s
					GROUP BY page_url ORDER BY total DESC LIMIT 50",
					$range['from'],
					$range['to']
				),
				ARRAY_A
			);

			$result = Kayan_Report_Share::create_report(
				$title,
				array(
					'date_from' => $range['from'],
					'date_to'   => $range['to'],
				),
				array(
					'totals' => $totals,
					'rows'   => $rows,
				),
				get_current_user_id()
			);

			wp_send_json_success( $result );
		}
	}

	if ( is_admin() && isset( $_GET['page'] ) && $_GET['page'] === 'kayan-track-pro' && isset( $_GET['kt_view'] ) && $_GET['kt_view'] === 'app' ) {
		add_action(
			'admin_init',
			function () {
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}
				require kayan_track_pack_path() . 'admin/views/dashboard-app.php';
				exit;
			}
		);
	}
}
