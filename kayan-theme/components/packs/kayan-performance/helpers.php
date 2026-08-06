<?php 
if ( ! function_exists( 'kayan_perf_is_enabled' ) ) {
	function kayan_perf_is_enabled() {
		if ( function_exists( 'kayan_seo_is_enabled' ) && ! kayan_seo_is_enabled() ) {
			return false;
		}
		return empty( yc_get_option( 'kayan_perf_disable' ) );
	}
}

if ( ! function_exists( 'kayan_perf_get_lcp_image_url' ) ) {
	function kayan_perf_get_lcp_image_url() {
		if ( ( is_singular() || is_page() ) && has_post_thumbnail() ) {
			$thumb_id = get_post_thumbnail_id( get_queried_object_id() );
			if ( $thumb_id ) {
				$url = wp_get_attachment_image_url( $thumb_id, 'full' );
				if ( $url ) {
					return esc_url( $url );
				}
			}
		}

		if ( ( is_front_page() || is_home() ) && function_exists( 'kayan_homepage_v3_active_request' ) && kayan_homepage_v3_active_request() ) {
			return '';
		}

		if ( is_front_page() || is_home() ) {
			$logo_data = yc_get_option( 'logo__data' );
			if ( is_array( $logo_data ) && isset( $logo_data['logo__mode'] ) && 'Image' === $logo_data['logo__mode'] ) {
				$logo_id = $logo_data['Image']['image_logo_id'] ?? 0;
				if ( $logo_id ) {
					$url = wp_get_attachment_image_url( (int) $logo_id, 'full' );
					if ( $url ) {
						return esc_url( $url );
					}
				}
			}
		}

		if ( is_tax( 'city' ) ) {
			$term = get_queried_object();
			if ( $term && ! empty( $term->term_id ) && function_exists( 'kayan_seo_get_term_image_url' ) ) {
				$url = kayan_seo_get_term_image_url( $term->term_id );
				if ( $url ) {
					return $url;
				}
			}
		}

		return '';
	}
}

if ( ! function_exists( 'kayan_perf_render_resource_hints' ) ) {
	function kayan_perf_render_resource_hints() {
		static $rendered = false;
		if ( $rendered ) {
			return;
		}
		if ( ! kayan_perf_is_enabled() || ( function_exists( 'IsSpeed' ) && IsSpeed() ) ) {
			return;
		}
		$rendered = true;

		echo '<link rel="dns-prefetch" href="//cdnjs.cloudflare.com" />' . "\n";
		echo '<link rel="dns-prefetch" href="//fonts.gstatic.com" />' . "\n";
		echo '<link rel="preconnect" href="https://fonts.googleapis.com" />' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />' . "\n";
		echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />' . "\n";

		if ( empty( yc_get_option( 'kayan_perf_disable_preload' ) ) ) {
			$lcp = kayan_perf_get_lcp_image_url();
			if ( ! empty( $lcp ) ) {
				echo '<link rel="preload" as="image" href="' . esc_url( $lcp ) . '" fetchpriority="high" />' . "\n";
			}
		}
	}
}

if ( ! function_exists( 'kayan_perf_attachment_image_attributes' ) ) {
	function kayan_perf_attachment_image_attributes( $attr, $attachment, $size ) {
		if ( ! kayan_perf_is_enabled() || ( function_exists( 'IsSpeed' ) && IsSpeed() ) ) {
			return $attr;
		}

		if ( is_singular() && (int) get_post_thumbnail_id() === (int) $attachment->ID ) {
			$attr['fetchpriority'] = 'high';
			$attr['loading'] = 'eager';
			$attr['decoding'] = 'async';
		} elseif ( empty( $attr['loading'] ) ) {
			$attr['loading'] = 'lazy';
			$attr['decoding'] = 'async';
		}

		return $attr;
	}
}
