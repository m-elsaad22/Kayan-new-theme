<?php 
if ( ! class_exists( 'Kayan_DNI' ) ) {

	class Kayan_DNI {

		public static function resolve( $traffic_src = 'direct', $utm_source = '', $utm_medium = '', $utm_campaign = '' ) {
			global $wpdb;

			if ( ! kayan_track_get_option( 'kayan_dni_enabled', false ) ) {
				return self::default_number();
			}

			$table = kayan_track_table( 'dni_rules' );
			$rules = $wpdb->get_results(
				"SELECT * FROM {$table} WHERE active = 1 ORDER BY priority DESC, id ASC",
				ARRAY_A
			);

			if ( empty( $rules ) ) {
				return self::default_number();
			}

			$traffic_src  = sanitize_key( $traffic_src );
			$utm_source   = sanitize_text_field( $utm_source );
			$utm_medium   = sanitize_text_field( $utm_medium );
			$utm_campaign = sanitize_text_field( $utm_campaign );

			foreach ( $rules as $rule ) {
				if ( self::rule_matches( $rule, $traffic_src, $utm_source, $utm_medium, $utm_campaign ) ) {
					return self::number_by_id( (int) $rule['number_id'] );
				}
			}

			return self::default_number();
		}

		private static function rule_matches( $rule, $traffic_src, $utm_source, $utm_medium, $utm_campaign ) {
			if ( ! empty( $rule['source_type'] ) && $rule['source_type'] !== 'any' && $rule['source_type'] !== $traffic_src ) {
				return false;
			}
			if ( ! empty( $rule['utm_source'] ) && $rule['utm_source'] !== $utm_source ) {
				return false;
			}
			if ( ! empty( $rule['utm_medium'] ) && $rule['utm_medium'] !== $utm_medium ) {
				return false;
			}
			if ( ! empty( $rule['utm_campaign'] ) && $rule['utm_campaign'] !== $utm_campaign ) {
				return false;
			}
			return true;
		}

		private static function number_by_id( $id ) {
			global $wpdb;
			if ( ! $id ) {
				return self::default_number();
			}
			$row = $wpdb->get_row(
				$wpdb->prepare(
					'SELECT phone, wa_number FROM ' . kayan_track_table( 'numbers' ) . ' WHERE id = %d AND active = 1 LIMIT 1',
					$id
				),
				ARRAY_A
			);
			if ( empty( $row ) ) {
				return self::default_number();
			}
			return array(
				'phone'     => preg_replace( '/[^0-9]/', '', $row['phone'] ),
				'wa_number' => preg_replace( '/[^0-9]/', '', $row['wa_number'] ),
			);
		}

		private static function default_number() {
			global $wpdb;
			$row = $wpdb->get_row(
				'SELECT phone, wa_number FROM ' . kayan_track_table( 'numbers' ) . ' WHERE active = 1 ORDER BY id ASC LIMIT 1',
				ARRAY_A
			);
			if ( empty( $row ) ) {
				$phone = yc_get_option( 'contact_number' );
				$clean = preg_replace( '/[^0-9]/', '', (string) $phone );
				return array(
					'phone'     => $clean,
					'wa_number' => $clean,
				);
			}
			return array(
				'phone'     => preg_replace( '/[^0-9]/', '', $row['phone'] ),
				'wa_number' => preg_replace( '/[^0-9]/', '', $row['wa_number'] ),
			);
		}
	}
}
