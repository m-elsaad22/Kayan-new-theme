<?php 
if ( ! defined( 'KAYAN_TRACK_VERSION' ) ) {
	define( 'KAYAN_TRACK_VERSION', '1.1.0' );
}

if ( ! function_exists( 'kayan_track_pack_url' ) ) {
	function kayan_track_pack_url() {
		return trailingslashit( get_template_directory_uri() . '/components/packs/kayan-track' );
	}
}

if ( ! function_exists( 'kayan_track_pack_path' ) ) {
	function kayan_track_pack_path() {
		return trailingslashit( get_template_directory() . '/components/packs/kayan-track' );
	}
}

if ( ! function_exists( 'kayan_track_is_enabled' ) ) {
	function kayan_track_is_enabled() {
		return empty( yc_get_option( 'kayan_track_disable' ) );
	}
}

if ( ! function_exists( 'kayan_track_get_option' ) ) {
	function kayan_track_get_option( $key, $default = false ) {
		return yc_get_option( $key, $default );
	}
}

if ( ! function_exists( 'kayan_track_update_option' ) ) {
	function kayan_track_update_option( $key, $value ) {
		return yc_update_option( $key, $value );
	}
}

if ( ! function_exists( 'kayan_track_table' ) ) {
	function kayan_track_table( $name ) {
		global $wpdb;
		return $wpdb->prefix . 'kayan_' . $name;
	}
}

if ( ! function_exists( 'kayan_track_date_range' ) ) {
	function kayan_track_date_range( $preset = '7d', $from = '', $to = '' ) {
		$now = current_time( 'timestamp' );
		$end = date( 'Y-m-d 23:59:59', $now );

		if ( $preset === 'today' ) {
			$start = date( 'Y-m-d 00:00:00', $now );
		} elseif ( $preset === 'yesterday' ) {
			$start = date( 'Y-m-d 00:00:00', strtotime( '-1 day', $now ) );
			$end   = date( 'Y-m-d 23:59:59', strtotime( '-1 day', $now ) );
		} elseif ( $preset === '7d' ) {
			$start = date( 'Y-m-d 00:00:00', strtotime( '-6 days', $now ) );
		} elseif ( $preset === '30d' ) {
			$start = date( 'Y-m-d 00:00:00', strtotime( '-29 days', $now ) );
		} elseif ( $preset === '3m' || $preset === '90d' ) {
			$start = date( 'Y-m-d 00:00:00', strtotime( '-89 days', $now ) );
		} elseif ( $preset === 'month' ) {
			$start = date( 'Y-m-01 00:00:00', $now );
		} elseif ( $preset === 'custom' && $from && $to ) {
			$start = sanitize_text_field( $from ) . ' 00:00:00';
			$end   = sanitize_text_field( $to ) . ' 23:59:59';
		} else {
			# افتراضي: آخر 30 يوماً
			$start = date( 'Y-m-d 00:00:00', strtotime( '-29 days', $now ) );
		}

		return array(
			'from' => $start,
			'to'   => $end,
		);
	}
}

if ( ! function_exists( 'kayan_track_is_ip_blocked' ) ) {
	function kayan_track_is_ip_blocked( $ip ) {
		global $wpdb;
		if ( empty( $ip ) ) {
			return false;
		}
		$table = kayan_track_table( 'blacklist' );
		$found = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM {$table} WHERE ip = %s LIMIT 1",
				$ip
			)
		);
		return ! empty( $found );
	}
}

if ( ! function_exists( 'kayan_track_get_page_service' ) ) {
	function kayan_track_get_page_service( $post_id = 0 ) {
		if ( ! $post_id ) {
			$post_id = get_queried_object_id();
		}
		if ( ! $post_id ) {
			return '';
		}
		$service = get_post_meta( $post_id, '_kayan_service_name', true );
		if ( ! empty( $service ) ) {
			return sanitize_text_field( $service );
		}
		if ( is_singular( 'post' ) ) {
			$terms = get_the_terms( $post_id, 'category' );
			if ( is_array( $terms ) && ! empty( $terms[0]->name ) ) {
				return sanitize_text_field( $terms[0]->name );
			}
		}
		return '';
	}
}
