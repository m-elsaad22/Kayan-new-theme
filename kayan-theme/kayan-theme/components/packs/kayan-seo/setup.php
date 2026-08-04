<?php
/**
 * kayan-seo — طبقة SEO الخاصة بـ KAYAN
 *
 * السياسة الافتراضية:
 * - Rank Math Active للتخزين.
 * - واجهة Rank Math معطّلة بينما KAYAN SEO يعمل.
 * - لاستعادة واجهة Rank Math: فعّل kayan_seo_disable (لا تشغّل الاثنين معاً على الواجهة).
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/compatibility.php';
require_once __DIR__ . '/rank-math-bridge.php';

if ( ! function_exists( 'kayan_seo_bootstrap' ) ) {
	function kayan_seo_bootstrap() {
		add_theme_support( 'title-tag' );

		# إذا KAYAN SEO معطّل → لا نطبع عنوان/وصف من القالب (Rank Math يتولى الواجهة)
		if ( kayan_seo_is_disabled() ) {
			return;
		}

		add_filter( 'pre_get_document_title', 'kayan_seo_filter_document_title', 20 );
		add_action( 'wp_head', 'kayan_seo_print_meta_description', 1 );
	}
	add_action( 'after_setup_theme', 'kayan_seo_bootstrap', 2 );
}

if ( ! function_exists( 'kayan_seo_filter_document_title' ) ) {
	function kayan_seo_filter_document_title( $title ) {
		if ( kayan_seo_is_disabled() ) {
			return $title;
		}
		$rm_title = kayan_seo_get_rank_math_title();
		if ( '' !== $rm_title ) {
			return $rm_title;
		}
		return $title;
	}
}

if ( ! function_exists( 'kayan_seo_print_meta_description' ) ) {
	function kayan_seo_print_meta_description() {
		if ( is_admin() || kayan_seo_is_disabled() ) {
			return;
		}
		$hide = get_option( 'hide__description_show' );
		if ( ! empty( $hide ) ) {
			return;
		}
		$desc = kayan_seo_get_rank_math_description();
		if ( '' === $desc ) {
			$desc = is_singular() ? wp_strip_all_tags( get_the_excerpt() ) : get_bloginfo( 'description' );
		}
		if ( '' === $desc ) {
			return;
		}
		echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
	}
}
