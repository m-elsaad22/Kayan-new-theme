<?php 
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/switcher.php';

if ( ! function_exists( 'kayan_i18n_register_query_var' ) ) {
	function kayan_i18n_register_query_var( $vars ) {
		$vars[] = 'kayan_lang';
		$vars[] = 'kayan_country';
		return $vars;
	}
}
add_filter( 'query_vars', 'kayan_i18n_register_query_var' );

if ( ! function_exists( 'kayan_i18n_register_rewrites' ) ) {
	function kayan_i18n_register_rewrites() {
		if ( ! kayan_i18n_is_enabled() ) {
			return;
		}

		foreach ( kayan_i18n_get_countries() as $code => $data ) {
			$prefix = isset( $data['path'] ) ? trim( (string) $data['path'], '/' ) : '';

			if ( $prefix === '' ) {
				add_rewrite_rule( '^en/?$', 'index.php?kayan_lang=en', 'top' );
				add_rewrite_rule( '^en/([^/]+)/?$', 'index.php?kayan_lang=en&name=$matches[1]', 'top' );
				add_rewrite_rule( '^en/([^/]+)/page/([0-9]+)/?$', 'index.php?kayan_lang=en&name=$matches[1]&paged=$matches[2]', 'top' );
				continue;
			}

			add_rewrite_rule( '^' . $prefix . '/?$', 'index.php?kayan_country=' . $code, 'top' );
			add_rewrite_rule( '^' . $prefix . '/en/?$', 'index.php?kayan_country=' . $code . '&kayan_lang=en', 'top' );
			add_rewrite_rule( '^' . $prefix . '/en/([^/]+)/?$', 'index.php?kayan_country=' . $code . '&kayan_lang=en&name=$matches[1]', 'top' );
			add_rewrite_rule( '^' . $prefix . '/en/([^/]+)/page/([0-9]+)/?$', 'index.php?kayan_country=' . $code . '&kayan_lang=en&name=$matches[1]&paged=$matches[2]', 'top' );
			add_rewrite_rule( '^' . $prefix . '/([^/]+)/?$', 'index.php?kayan_country=' . $code . '&name=$matches[1]', 'top' );
			add_rewrite_rule( '^' . $prefix . '/([^/]+)/page/([0-9]+)/?$', 'index.php?kayan_country=' . $code . '&name=$matches[1]&paged=$matches[2]', 'top' );
		}
	}
}
add_action( 'init', 'kayan_i18n_register_rewrites', 5 );

if ( ! function_exists( 'kayan_i18n_flush_rewrites_once' ) ) {
	function kayan_i18n_flush_rewrites_once() {
		if ( get_option( 'kayan_i18n_rewrite_version' ) === '1.0.5' ) {
			return;
		}
		flush_rewrite_rules( false );
		update_option( 'kayan_i18n_rewrite_version', '1.0.5', false );
	}
}
add_action( 'init', 'kayan_i18n_flush_rewrites_once', 99 );

if ( ! function_exists( 'kayan_i18n_resolve_localized_request' ) ) {
	function kayan_i18n_resolve_localized_request( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$lang    = get_query_var( 'kayan_lang' );
		$country = get_query_var( 'kayan_country' );
		$name    = get_query_var( 'name' );

		if ( empty( $lang ) && empty( $country ) && empty( $name ) ) {
			return;
		}

		if ( empty( $name ) ) {
			if ( 'en' === $lang || ! empty( $country ) ) {
				$query->is_home     = true;
				$query->is_front_page = true;
			}
			return;
		}

		$query->set( 'post_type', 'post' );
		$query->set( 'name', $name );
	}
}
add_action( 'pre_get_posts', 'kayan_i18n_resolve_localized_request', 1 );

if ( ! function_exists( 'kayan_i18n_enqueue_assets' ) ) {
	function kayan_i18n_enqueue_assets() {
		if ( ! kayan_i18n_is_enabled() ) {
			return;
		}
		$css = get_template_directory_uri() . '/components/packs/kayan-i18n/assets/kayan-locale.css';
		wp_enqueue_style( 'kayan-locale', $css, array(), '1.0.5' );
	}
}
add_action( 'wp_enqueue_scripts', 'kayan_i18n_enqueue_assets', 6 );

add_filter( 'kayan_seo_resolved_title', 'kayan_i18n_filter_seo_title', 10, 1 );
add_filter( 'kayan_seo_resolved_description', 'kayan_i18n_filter_seo_description', 10, 1 );
add_filter( 'language_attributes', 'kayan_i18n_filter_language_attributes', 20 );
