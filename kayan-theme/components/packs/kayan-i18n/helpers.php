<?php 
require_once __DIR__ . '/countries.php';
require_once __DIR__ . '/strings.php';

if ( ! function_exists( 'kayan_i18n_is_enabled' ) ) {
	function kayan_i18n_is_enabled() {
		return empty( yc_get_option( 'kayan_i18n_disable' ) );
	}
}

if ( ! function_exists( 'kayan_i18n_get_request_path' ) ) {
	function kayan_i18n_get_request_path() {
		$path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH );
		return is_string( $path ) ? $path : '/';
	}
}

if ( ! function_exists( 'kayan_i18n_detect_country_from_path' ) ) {
	function kayan_i18n_detect_country_from_path( $path = null ) {
		$path      = null === $path ? kayan_i18n_get_request_path() : $path;
		$countries = kayan_i18n_get_countries();
		$matches   = array();

		// Language-first canonical: /en/{country}/…
		if ( preg_match( '#^/en/([a-z]{2})(/|$)#', $path, $m ) ) {
			$code = $m[1];
			if ( isset( $countries[ $code ] ) && ! empty( $countries[ $code ]['path'] ) ) {
				return $code;
			}
		}

		foreach ( $countries as $code => $data ) {
			$prefix = isset( $data['path'] ) ? trim( (string) $data['path'], '/' ) : '';
			if ( $prefix === '' ) {
				continue;
			}
			if ( $path === '/' . $prefix || strpos( $path, '/' . $prefix . '/' ) === 0 ) {
				$matches[ strlen( $prefix ) ] = $code;
			}
		}

		if ( empty( $matches ) ) {
			$default = yc_get_option( 'kayan_i18n_default_country' );
			if ( ! empty( $default ) && isset( $countries[ $default ] ) ) {
				return $default;
			}
			return 'ae';
		}

		ksort( $matches );
		return end( $matches );
	}
}

if ( ! function_exists( 'kayan_i18n_get_country' ) ) {
	function kayan_i18n_get_country() {
		$countries = kayan_i18n_get_countries();
		$country   = get_query_var( 'kayan_country' );
		if ( ! empty( $country ) && isset( $countries[ $country ] ) ) {
			return $country;
		}
		return kayan_i18n_detect_country_from_path();
	}
}

if ( ! function_exists( 'kayan_i18n_get_country_data' ) ) {
	function kayan_i18n_get_country_data( $country = null ) {
		if ( null === $country ) {
			$country = kayan_i18n_get_country();
		}
		$countries = kayan_i18n_get_countries();
		return isset( $countries[ $country ] ) ? $countries[ $country ] : $countries['ae'];
	}
}

if ( ! function_exists( 'kayan_i18n_get_country_path' ) ) {
	function kayan_i18n_get_country_path( $country = null ) {
		$data = kayan_i18n_get_country_data( $country );
		return isset( $data['path'] ) ? (string) $data['path'] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_detect_lang_from_path' ) ) {
	function kayan_i18n_detect_lang_from_path( $path = null ) {
		$path = null === $path ? kayan_i18n_get_request_path() : $path;

		// Canonical language-first: /en/…
		if ( $path === '/en' || $path === '/en/' || strpos( $path, '/en/' ) === 0 ) {
			return 'en';
		}

		// Legacy /{country}/en/… (still detectable for 301 source paths)
		$country = kayan_i18n_detect_country_from_path( $path );
		$base    = kayan_i18n_get_country_path( $country );
		$rest    = $path;

		if ( $base !== '' ) {
			$rest = substr( $path, strlen( $base ) );
			if ( $rest === false || $rest === '' ) {
				$rest = '/';
			}
		}

		if ( $rest === '/en' || $rest === '/en/' || strpos( $rest, '/en/' ) === 0 ) {
			return 'en';
		}
		return 'ar';
	}
}

if ( ! function_exists( 'kayan_i18n_get_lang' ) ) {
	function kayan_i18n_get_lang() {
		if ( ! kayan_i18n_is_enabled() ) {
			return 'ar';
		}
		$lang = get_query_var( 'kayan_lang' );
		if ( 'en' === $lang ) {
			return 'en';
		}
		return kayan_i18n_detect_lang_from_path();
	}
}

if ( ! function_exists( 'kayan_i18n_is_english' ) ) {
	function kayan_i18n_is_english() {
		return 'en' === kayan_i18n_get_lang();
	}
}

if ( ! function_exists( 'kayan_i18n_get_html_attrs' ) ) {
	function kayan_i18n_get_html_attrs() {
		if ( kayan_i18n_is_english() ) {
			return 'lang="en" dir="ltr"';
		}
		return 'lang="ar" dir="rtl"';
	}
}

if ( ! function_exists( 'kayan_i18n_country_label' ) ) {
	function kayan_i18n_country_label( $country = null, $lang = null ) {
		$data = kayan_i18n_get_country_data( $country );
		if ( null === $lang ) {
			$lang = kayan_i18n_get_lang();
		}
		$key = 'en' === $lang ? 'label_en' : 'label_ar';
		return isset( $data[ $key ] ) ? $data[ $key ] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_country_in_phrase' ) ) {
	function kayan_i18n_country_in_phrase( $country = null, $lang = null ) {
		$data = kayan_i18n_get_country_data( $country );
		if ( null === $lang ) {
			$lang = kayan_i18n_get_lang();
		}
		$key = 'en' === $lang ? 'in_en' : 'in_ar';
		return isset( $data[ $key ] ) ? $data[ $key ] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_country_regions' ) ) {
	function kayan_i18n_country_regions( $country = null, $lang = null ) {
		$data = kayan_i18n_get_country_data( $country );
		if ( null === $lang ) {
			$lang = kayan_i18n_get_lang();
		}
		$key = 'en' === $lang ? 'regions_en' : 'regions_ar';
		return isset( $data[ $key ] ) ? $data[ $key ] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_country_address' ) ) {
	function kayan_i18n_country_address( $country = null, $lang = null ) {
		$data = kayan_i18n_get_country_data( $country );
		if ( null === $lang ) {
			$lang = kayan_i18n_get_lang();
		}
		$key = 'en' === $lang ? 'address_en' : 'address_ar';
		return isset( $data[ $key ] ) ? $data[ $key ] : '';
	}
}

if ( ! function_exists( 'kayan_i18n_get_switcher_config' ) ) {
	function kayan_i18n_get_switcher_config() {
		$country_paths = array();
		$flags         = array();
		foreach ( kayan_i18n_get_countries() as $code => $data ) {
			$country_paths[ $code ] = isset( $data['path'] ) ? (string) $data['path'] : '';
			$flags[ $code ]         = isset( $data['flag'] ) ? (string) $data['flag'] : '🌐';
		}

		$base_domain = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
		if ( empty( $base_domain ) ) {
			$base_domain = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		}

		return array(
			'baseDomain'   => $base_domain,
			'countryPaths' => $country_paths,
			'flags'        => $flags,
			'storageKey'   => 'kayan_geo_pref',
		);
	}
}

if ( ! function_exists( 'kayan_i18n_build_url' ) ) {
	function kayan_i18n_build_url( $country, $lang, $slug = '/' ) {
		// Delegate to platform canonical builder (language-first) — no duplicated URL logic.
		if ( function_exists( 'kayan_platform' ) && defined( 'KAYAN_PLATFORM_LOADED' ) ) {
			return kayan_platform()->urls->build( $country, $lang, $slug );
		}

		$base         = trailingslashit( home_url() );
		$country_path = kayan_i18n_get_country_path( $country );
		$slug         = ( $slug && $slug !== '/' ) ? trim( (string) $slug, '/' ) : '';
		$parts        = array();

		if ( 'en' === $lang ) {
			$parts[] = 'en';
		}
		$country_path = trim( (string) $country_path, '/' );
		if ( $country_path !== '' ) {
			$parts[] = $country_path;
		}
		if ( $slug !== '' ) {
			$parts[] = $slug;
		}

		return user_trailingslashit( $base . implode( '/', $parts ) );
	}
}

if ( ! function_exists( 'kayan_i18n_get_post_en_meta' ) ) {
	function kayan_i18n_get_post_en_meta( $post_id, $key ) {
		$value = get_post_meta( $post_id, 'kayan_en_' . $key, true );
		return ! empty( $value ) ? trim( wp_strip_all_tags( (string) $value ) ) : '';
	}
}

if ( ! function_exists( 'kayan_i18n_get_localized_url' ) ) {
	function kayan_i18n_get_localized_url( $lang = 'ar', $post_id = 0 ) {
		$country = kayan_i18n_get_country();
		if ( ! $post_id ) {
			$post_id = get_queried_object_id();
		}

		if ( $post_id ) {
			$post = get_post( $post_id );
			if ( $post ) {
				$slug = $post->post_name;
				return kayan_i18n_build_url( $country, $lang, $slug );
			}
		}

		if ( is_front_page() || is_home() ) {
			return kayan_i18n_build_url( $country, $lang, '/' );
		}

		if ( function_exists( 'kayan_seo_get_current_url' ) ) {
			$url  = kayan_seo_get_current_url();
			$path = wp_parse_url( $url, PHP_URL_PATH );
			$path = is_string( $path ) ? $path : '/';
			$base = kayan_i18n_get_country_path( $country );
			if ( $base !== '' && strpos( $path, $base ) === 0 ) {
				$path = substr( $path, strlen( $base ) );
			}
			if ( 'en' === $lang ) {
				$path = preg_replace( '#^/en/?#', '/en/', $path );
				if ( strpos( $path, '/en' ) !== 0 ) {
					$path = '/en' . ( $path === '/' ? '' : $path );
				}
			} else {
				$path = preg_replace( '#^/en/?#', '/', $path );
			}
			return user_trailingslashit( home_url( trim( $base . $path, '/' ) ) );
		}

		return kayan_i18n_build_url( $country, $lang, '/' );
	}
}

if ( ! function_exists( 'kayan_i18n_filter_seo_title' ) ) {
	function kayan_i18n_filter_seo_title( $title ) {
		if ( ! kayan_i18n_is_english() || ! is_singular() ) {
			return $title;
		}
		$en = kayan_i18n_get_post_en_meta( get_queried_object_id(), 'title' );
		return $en ? $en : $title;
	}
}

if ( ! function_exists( 'kayan_i18n_filter_seo_description' ) ) {
	function kayan_i18n_filter_seo_description( $description ) {
		if ( ! kayan_i18n_is_english() || ! is_singular() ) {
			return $description;
		}
		$en = kayan_i18n_get_post_en_meta( get_queried_object_id(), 'description' );
		return $en ? $en : $description;
	}
}

if ( ! function_exists( 'kayan_i18n_get_schema_language' ) ) {
	function kayan_i18n_get_schema_language() {
		return kayan_i18n_is_english() ? 'en' : 'ar';
	}
}

if ( ! function_exists( 'kayan_i18n_render_hreflang' ) ) {
	function kayan_i18n_render_hreflang() {
		if ( ! kayan_i18n_is_enabled() ) {
			return;
		}
		$ar_url = kayan_i18n_get_localized_url( 'ar' );
		$en_url = kayan_i18n_get_localized_url( 'en' );
		if ( empty( $ar_url ) || empty( $en_url ) || $ar_url === $en_url ) {
			return;
		}
		echo '<link rel="alternate" hreflang="ar" href="' . esc_url( $ar_url ) . '" />' . "\n";
		echo '<link rel="alternate" hreflang="en" href="' . esc_url( $en_url ) . '" />' . "\n";
		echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $ar_url ) . '" />' . "\n";
	}
}

if ( ! function_exists( 'kayan_i18n_filter_language_attributes' ) ) {
	function kayan_i18n_filter_language_attributes( $output ) {
		if ( ! kayan_i18n_is_enabled() ) {
			return $output;
		}
		return kayan_i18n_get_html_attrs();
	}
}
