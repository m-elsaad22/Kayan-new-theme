<?php 
if ( ! class_exists( 'Kayan_Tracker_REST' ) ) {

	class Kayan_Tracker_REST {

		public function __construct() {
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}

		public function register_routes() {
			register_rest_route(
				'kayan/v1',
				'/track',
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'handle_track' ),
					'permission_callback' => '__return_true',
				)
			);
			register_rest_route(
				'kayan/v1',
				'/dni',
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'handle_dni' ),
					'permission_callback' => '__return_true',
				)
			);
		}

		public function handle_track( WP_REST_Request $request ) {
			$params = $this->get_params( $request );
			$ip     = $this->get_real_ip();

			if ( kayan_track_is_ip_blocked( $ip ) ) {
				return new WP_REST_Response( array( 'ok' => false, 'msg' => 'blocked' ), 403 );
			}

			$rate_key = 'kayan_rate_' . md5( $ip );
			$rate     = (int) get_transient( $rate_key );
			if ( $rate > 20 ) {
				return new WP_REST_Response( array( 'ok' => false, 'msg' => 'rate_limit' ), 429 );
			}
			set_transient( $rate_key, $rate + 1, 60 );

			$event = isset( $params['event'] ) ? sanitize_key( $params['event'] ) : '';

			if ( $event === 'conversion' ) {
				return $this->save_conversion( $params, $ip );
			}
			if ( $event === 'visit' ) {
				return $this->save_visit( $params, $ip );
			}
			if ( $event === 'session_end' ) {
				return $this->end_session( $params );
			}
			if ( $event === 'heatmap' ) {
				return $this->save_heatmap( $params );
			}

			return new WP_REST_Response( array( 'ok' => true ), 200 );
		}

		public function handle_dni( WP_REST_Request $request ) {
			$src  = sanitize_key( $request->get_param( 'src' ) ? $request->get_param( 'src' ) : 'direct' );
			$usrc = sanitize_text_field( $request->get_param( 'utm_source' ) ? $request->get_param( 'utm_source' ) : '' );
			$umed = sanitize_text_field( $request->get_param( 'utm_medium' ) ? $request->get_param( 'utm_medium' ) : '' );
			$ucmp = sanitize_text_field( $request->get_param( 'utm_campaign' ) ? $request->get_param( 'utm_campaign' ) : '' );

			$data = Kayan_DNI::resolve( $src, $usrc, $umed, $ucmp );
			return new WP_REST_Response( $data, 200 );
		}

		private function get_params( WP_REST_Request $request ) {
			$json = $request->get_json_params();
			if ( is_array( $json ) && ! empty( $json ) ) {
				return $json;
			}
			return $request->get_params();
		}

		private function get_real_ip() {
			$keys = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
			foreach ( $keys as $k ) {
				if ( ! empty( $_SERVER[ $k ] ) ) {
					$parts = explode( ',', $_SERVER[ $k ] );
					$ip    = trim( $parts[0] );
					if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
						return $ip;
					}
				}
			}
			return '0.0.0.0';
		}

		private function get_geo( $ip ) {
			if ( in_array( $ip, array( '127.0.0.1', '::1', '0.0.0.0' ), true ) ) {
				return array( 'country' => 'Local', 'city' => 'Local' );
			}
			$cached = get_transient( 'kayan_geo_' . md5( $ip ) );
			if ( $cached ) {
				return $cached;
			}
			$res = wp_remote_get(
				'http://ip-api.com/json/' . rawurlencode( $ip ) . '?fields=country,city,status',
				array( 'timeout' => 3, 'sslverify' => false )
			);
			if ( is_wp_error( $res ) ) {
				return array( 'country' => '', 'city' => '' );
			}
			$data = json_decode( wp_remote_retrieve_body( $res ), true );
			$geo  = array(
				'country' => sanitize_text_field( isset( $data['country'] ) ? $data['country'] : '' ),
				'city'    => sanitize_text_field( isset( $data['city'] ) ? $data['city'] : '' ),
			);
			set_transient( 'kayan_geo_' . md5( $ip ), $geo, DAY_IN_SECONDS );
			return $geo;
		}

		private function save_conversion( $params, $ip ) {
			global $wpdb;
			$table = kayan_track_table( 'conversions' );
			$fp    = sanitize_text_field( isset( $params['fp'] ) ? $params['fp'] : '' );
			$phone = sanitize_text_field( isset( $params['phone'] ) ? $params['phone'] : '' );
			$cool  = (int) kayan_track_get_option( 'kayan_cooldown_mins', 30 );
			$since = date( 'Y-m-d H:i:s', time() - $cool * 60 );

			$dup = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM {$table} WHERE fingerprint = %s AND phone_raw = %s AND created_at >= %s LIMIT 1",
					$fp,
					$phone,
					$since
				)
			);
			if ( $dup ) {
				return new WP_REST_Response( array( 'ok' => true, 'duplicate' => true ), 200 );
			}

			$cnt24 = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$table} WHERE ip = %s AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)",
					$ip
				)
			);
			$threshold = (int) kayan_track_get_option( 'kayan_fraud_threshold', 3 );
			$is_sus    = ( $cnt24 >= $threshold ) ? 1 : 0;
			$geo       = $this->get_geo( $ip );

			$number_id = null;
			if ( $phone ) {
				$clean = preg_replace( '/[^0-9]/', '', $phone );
				$num   = $wpdb->get_row(
					$wpdb->prepare(
						'SELECT id FROM ' . kayan_track_table( 'numbers' ) . ' WHERE (phone = %s OR wa_number = %s) AND active = 1 LIMIT 1',
						$clean,
						$clean
					)
				);
				if ( $num ) {
					$number_id = (int) $num->id;
				}
			}

			$wpdb->insert(
				$table,
				array(
					'number_id'    => $number_id,
					'phone_raw'    => $phone,
					'session_id'   => sanitize_text_field( isset( $params['sid'] ) ? $params['sid'] : '' ),
					'fingerprint'  => $fp,
					'ip'           => $ip,
					'ip_hash'      => hash( 'sha256', $ip . wp_salt() ),
					'country'      => $geo['country'],
					'city'         => $geo['city'],
					'device_type'  => sanitize_text_field( isset( $params['device'] ) ? $params['device'] : '' ),
					'browser'      => sanitize_text_field( isset( $params['browser'] ) ? $params['browser'] : '' ),
					'os'           => sanitize_text_field( isset( $params['os'] ) ? $params['os'] : '' ),
					'click_type'   => sanitize_key( isset( $params['click_type'] ) ? $params['click_type'] : '' ),
					'page_url'     => esc_url_raw( isset( $params['page_url'] ) ? $params['page_url'] : '' ),
					'page_title'   => sanitize_text_field( isset( $params['page_title'] ) ? $params['page_title'] : '' ),
					'page_service' => sanitize_text_field( isset( $params['page_service'] ) ? $params['page_service'] : '' ),
					'referrer'     => esc_url_raw( isset( $params['referrer'] ) ? $params['referrer'] : '' ),
					'utm_source'   => sanitize_text_field( isset( $params['utm_source'] ) ? $params['utm_source'] : '' ),
					'utm_medium'   => sanitize_text_field( isset( $params['utm_medium'] ) ? $params['utm_medium'] : '' ),
					'utm_campaign' => sanitize_text_field( isset( $params['utm_campaign'] ) ? $params['utm_campaign'] : '' ),
					'traffic_src'  => sanitize_key( isset( $params['traffic_src'] ) ? $params['traffic_src'] : 'direct' ),
					'gclid'        => sanitize_text_field( isset( $params['gclid'] ) ? $params['gclid'] : '' ),
					'scroll_depth' => (int) ( isset( $params['scroll'] ) ? $params['scroll'] : 0 ),
					'time_on_page' => (int) ( isset( $params['time'] ) ? $params['time'] : 0 ),
					'is_suspicious'=> $is_sus,
					'is_duplicate' => 0,
					'created_at'   => current_time( 'mysql' ),
				)
			);

			if ( kayan_track_get_option( 'kayan_telegram_on' ) && ! $is_sus ) {
				$this->send_telegram(
					kayan_track_get_option( 'kayan_telegram_token' ),
					kayan_track_get_option( 'kayan_telegram_chat' ),
					"🔔 تحويل جديد\n" .
					'📞 ' . ( ( isset( $params['click_type'] ) && $params['click_type'] === 'call' ) ? 'اتصال' : 'واتساب' ) . "\n" .
					'🌍 ' . $geo['city'] . ' / ' . $geo['country'] . "\n" .
					'📱 ' . sanitize_text_field( isset( $params['device'] ) ? $params['device'] : '' ) . "\n" .
					'📄 ' . sanitize_text_field( isset( $params['page_title'] ) ? $params['page_title'] : '' )
				);
			}

			$auto_bl = (int) kayan_track_get_option( 'kayan_auto_blacklist', 5 );
			if ( $cnt24 + 1 >= $auto_bl ) {
				$wpdb->query(
					$wpdb->prepare(
						'INSERT IGNORE INTO ' . kayan_track_table( 'blacklist' ) . ' (ip, reason, click_count, blocked_by, created_at) VALUES (%s, %s, %d, %s, %s)',
						$ip,
						'auto',
						$cnt24 + 1,
						'system',
						current_time( 'mysql' )
					)
				);
			}

			return new WP_REST_Response( array( 'ok' => true, 'sus' => $is_sus ), 200 );
		}

		private function save_visit( $params, $ip ) {
			global $wpdb;
			$fp  = sanitize_text_field( isset( $params['fp'] ) ? $params['fp'] : '' );
			$sid = sanitize_text_field( isset( $params['sid'] ) ? $params['sid'] : '' );
			if ( ! $fp ) {
				return new WP_REST_Response( array( 'ok' => true ), 200 );
			}

			$geo = $this->get_geo( $ip );
			$v_table = kayan_track_table( 'visitors' );
			$existing = $wpdb->get_row(
				$wpdb->prepare( "SELECT id, visit_count FROM {$v_table} WHERE fingerprint = %s LIMIT 1", $fp ),
				ARRAY_A
			);

			$now = current_time( 'mysql' );
			$row = array(
				'session_id'   => $sid,
				'ip'           => $ip,
				'country'      => $geo['country'],
				'city'         => $geo['city'],
				'device_type'  => sanitize_text_field( isset( $params['device'] ) ? $params['device'] : '' ),
				'browser'      => sanitize_text_field( isset( $params['browser'] ) ? $params['browser'] : '' ),
				'os'           => sanitize_text_field( isset( $params['os'] ) ? $params['os'] : '' ),
				'screen_res'   => sanitize_text_field( isset( $params['screen'] ) ? $params['screen'] : '' ),
				'language'     => sanitize_text_field( isset( $params['lang'] ) ? $params['lang'] : '' ),
				'referrer'     => esc_url_raw( isset( $params['referrer'] ) ? $params['referrer'] : '' ),
				'utm_source'   => sanitize_text_field( isset( $params['utm_source'] ) ? $params['utm_source'] : '' ),
				'utm_medium'   => sanitize_text_field( isset( $params['utm_medium'] ) ? $params['utm_medium'] : '' ),
				'utm_campaign' => sanitize_text_field( isset( $params['utm_campaign'] ) ? $params['utm_campaign'] : '' ),
				'traffic_src'  => sanitize_key( isset( $params['traffic_src'] ) ? $params['traffic_src'] : 'direct' ),
				'last_visit'   => $now,
			);

			if ( $existing ) {
				$row['visit_count'] = (int) $existing['visit_count'] + 1;
				$row['is_new']      = 0;
				$wpdb->update( $v_table, $row, array( 'id' => (int) $existing['id'] ) );
			} else {
				$row['fingerprint']  = $fp;
				$row['visit_count']  = 1;
				$row['is_new']       = 1;
				$row['first_visit']  = $now;
				$wpdb->insert( $v_table, $row );
			}

			$s_table = kayan_track_table( 'sessions' );
			$session = $wpdb->get_row(
				$wpdb->prepare( "SELECT id, page_count, scroll_max FROM {$s_table} WHERE session_id = %s LIMIT 1", $sid ),
				ARRAY_A
			);
			$scroll = (int) ( isset( $params['scroll'] ) ? $params['scroll'] : 0 );
			if ( $session ) {
				$wpdb->update(
					$s_table,
					array(
						'page_count' => (int) $session['page_count'] + 1,
						'scroll_max' => max( (int) $session['scroll_max'], $scroll ),
					),
					array( 'id' => (int) $session['id'] )
				);
			} else {
				$wpdb->insert(
					$s_table,
					array(
						'session_id'  => $sid,
						'fingerprint' => $fp,
						'ip'          => $ip,
						'start_time'  => $now,
						'page_count'  => 1,
						'scroll_max'  => $scroll,
						'is_new'      => $existing ? 0 : 1,
					)
				);
			}

			return new WP_REST_Response( array( 'ok' => true ), 200 );
		}

		private function end_session( $params ) {
			global $wpdb;
			$sid = sanitize_text_field( isset( $params['sid'] ) ? $params['sid'] : '' );
			if ( ! $sid ) {
				return new WP_REST_Response( array( 'ok' => true ), 200 );
			}
			$duration = (int) ( isset( $params['duration'] ) ? $params['duration'] : 0 );
			$scroll   = (int) ( isset( $params['scroll'] ) ? $params['scroll'] : 0 );
			$table    = kayan_track_table( 'sessions' );

			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$table} SET end_time = %s, duration = %d, scroll_max = GREATEST(scroll_max, %d) WHERE session_id = %s",
					current_time( 'mysql' ),
					$duration,
					$scroll,
					$sid
				)
			);

			return new WP_REST_Response( array( 'ok' => true ), 200 );
		}

		private function save_heatmap( $params ) {
			global $wpdb;
			$sid   = sanitize_text_field( isset( $params['sid'] ) ? $params['sid'] : '' );
			$url   = esc_url_raw( isset( $params['page_url'] ) ? $params['page_url'] : '' );
			$table = kayan_track_table( 'heatmap' );
			$points = array();

			if ( ! empty( $params['points'] ) ) {
				if ( is_string( $params['points'] ) ) {
					$points = json_decode( $params['points'], true );
				} elseif ( is_array( $params['points'] ) ) {
					$points = $params['points'];
				}
			}

			if ( ! is_array( $points ) ) {
				return new WP_REST_Response( array( 'ok' => true ), 200 );
			}

			foreach ( $points as $pt ) {
				if ( ! is_array( $pt ) ) {
					continue;
				}
				$wpdb->insert(
					$table,
					array(
						'session_id' => $sid,
						'page_url'   => $url,
						'x_pct'      => (int) ( isset( $pt['x'] ) ? $pt['x'] : 0 ),
						'y_pct'      => (int) ( isset( $pt['y'] ) ? $pt['y'] : 0 ),
						'element'    => sanitize_text_field( isset( $pt['el'] ) ? $pt['el'] : '' ),
						'created_at' => current_time( 'mysql' ),
					)
				);
			}

			return new WP_REST_Response( array( 'ok' => true ), 200 );
		}

		private function send_telegram( $token, $chat, $msg ) {
			if ( ! $token || ! $chat ) {
				return;
			}
			wp_remote_post(
				'https://api.telegram.org/bot' . $token . '/sendMessage',
				array(
					'body'     => array(
						'chat_id' => $chat,
						'text'    => $msg,
					),
					'timeout'  => 4,
					'blocking' => false,
				)
			);
		}
	}
}
