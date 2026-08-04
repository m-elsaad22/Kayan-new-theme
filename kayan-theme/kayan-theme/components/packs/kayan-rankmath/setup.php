<?php
/**
 * kayan-rankmath — إعادة تفعيل Rank Math بالكامل (v1.4.7)
 *
 * سبب التعطيل السابق (من kayan-stabilization / lockdown):
 * - كانت الدالة disable_rank_math_frontend() تُلغي مخرجات Rank Math من الواجهة
 *   ليبقى القالب مصدر SEO الوحيد (قرار قديم في Phase 1.9).
 * - كذلك disable_all_scripts() كانت تزيل أصول Rank Math من الطابور.
 *
 * طريقة إعادة التفعيل (هذا الملف):
 * 1) لا نستدعي أي disable لـ Rank Math.
 * 2) نحمي أصول rank-math من dequeue.
 * 3) نضمن title-tag + عدم طباعة <title> يدوي يتعارض مع Rank Math.
 * 4) نمنع حذف rank-math-schema من HTML عبر lockdown.
 */

if ( ! function_exists( 'kayan_rankmath_bootstrap' ) ) {

	function kayan_rankmath_is_active() {
		if ( function_exists( 'kayan_is_rank_math_active' ) ) {
			return kayan_is_rank_math_active();
		}
		return class_exists( 'RankMath' )
			|| class_exists( 'RankMath\\RankMath' )
			|| defined( 'RANK_MATH_VERSION' )
			|| function_exists( 'rank_math' );
	}

	/**
	 * إزالة أي فلاتر/أكشن معروفة كانت تُعطّل Rank Math (توافق مع نسخ قديمة)
	 */
	function kayan_rankmath_remove_legacy_disablers() {
		$hooks = array(
			array( 'wp_head', 'disable_rank_math_frontend', 1 ),
			array( 'wp', 'disable_rank_math_frontend', 1 ),
			array( 'init', 'disable_rank_math_frontend', 1 ),
			array( 'plugins_loaded', 'disable_rank_math_frontend', 1 ),
			array( 'rank_math/frontend/disable', '__return_true', 10 ),
			array( 'rank_math/json_ld', '__return_empty_array', 99 ),
		);
		foreach ( $hooks as $h ) {
			if ( function_exists( 'remove_action' ) ) {
				remove_action( $h[0], $h[1], isset( $h[2] ) ? $h[2] : 10 );
			}
			if ( function_exists( 'remove_filter' ) ) {
				remove_filter( $h[0], $h[1], isset( $h[2] ) ? $h[2] : 10 );
			}
		}
		# Rank Math filter: لا تعطّل الواجهة
		add_filter( 'rank_math/frontend/disable', '__return_false', 999 );
		add_filter( 'rank_math/opengraph/facebook/enable', '__return_true', 999 );
		add_filter( 'rank_math/opengraph/twitter/enable', '__return_true', 999 );
	}

	function kayan_rankmath_bootstrap() {
		kayan_rankmath_remove_legacy_disablers();

		# title-tag لدعم Rank Math
		add_theme_support( 'title-tag' );

		# لا تحذف schema من الـ HTML
		add_filter( 'kayan_lockdown_strip_rank_math', '__return_false' );
	}

	add_action( 'after_setup_theme', 'kayan_rankmath_bootstrap', 1 );
	add_action( 'plugins_loaded', 'kayan_rankmath_remove_legacy_disablers', 1 );
	add_action( 'init', 'kayan_rankmath_remove_legacy_disablers', 1 );
	add_action( 'wp', 'kayan_rankmath_remove_legacy_disablers', 20 );
}
