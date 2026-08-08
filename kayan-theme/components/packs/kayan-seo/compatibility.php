<?php
/**
 * kayan-seo / compatibility.php
 *
 * تعطيل إخراج Rank Math في الواجهة فقط — الإضافة تظل Active.
 * يعمل فقط بينما KAYAN SEO مفعّل (kayan_seo_disable فارغ).
 *
 * لاستعادة واجهة Rank Math: فعّل خيار «تعطيل KAYAN SEO»
 * أو اجعل option: kayan_seo_disable = 1
 */

if ( ! function_exists( 'kayan_seo_disable_rank_math_frontend' ) ) {

	/**
	 * هل إضافة Rank Math مثبتة/مفعّلة؟ (للتخزين — لا يعني إخراج الواجهة)
	 */
	function kayan_seo_rank_math_plugin_active() {
		return class_exists( 'RankMath' )
			|| class_exists( 'RankMath\\RankMath' )
			|| defined( 'RANK_MATH_VERSION' )
			|| function_exists( 'rank_math' );
	}

	/**
	 * تعطيل واجهة Rank Math بالكامل على الفرونت.
	 * لا يعمل إذا كان KAYAN SEO معطّلاً عبر kayan_seo_disable.
	 */
	function kayan_seo_disable_rank_math_frontend() {
		# الخيار الأفضل لاستعادة Rank Math: kayan_seo_disable غير فارغ
		if ( function_exists( 'kayan_seo_is_disabled' ) && kayan_seo_is_disabled() ) {
			return;
		}
		if ( is_admin() || ! kayan_seo_rank_math_plugin_active() ) {
			return;
		}

		# 1) الفلاتر الرسمية
		add_filter( 'rank_math/frontend/disable', '__return_true', 999 );
		add_filter( 'rank_math/json_ld', '__return_false', 999 );
		add_filter( 'rank_math/opengraph/facebook/enable', '__return_false', 999 );
		add_filter( 'rank_math/opengraph/twitter/enable', '__return_false', 999 );

		# 2) إزالة أكشنات الرأس / JSON-LD / OpenGraph
		remove_all_actions( 'rank_math/head' );
		remove_all_actions( 'rank_math/json_ld' );
		remove_all_actions( 'rank_math/opengraph/facebook' );
		remove_all_actions( 'rank_math/opengraph/twitter' );

		# 3) إزالة Head::head من wp_head إن كانت مسجّلة
		if ( class_exists( 'RankMath\\Frontend\\Head' ) ) {
			remove_action( 'wp_head', array( 'RankMath\\Frontend\\Head', 'head' ), 1 );
			global $wp_filter;
			if ( isset( $wp_filter['wp_head'] ) ) {
				foreach ( (array) $wp_filter['wp_head']->callbacks as $priority => $callbacks ) {
					foreach ( (array) $callbacks as $id => $cb ) {
						$fn = isset( $cb['function'] ) ? $cb['function'] : null;
						if ( is_array( $fn ) && isset( $fn[0] ) && is_object( $fn[0] ) && is_a( $fn[0], 'RankMath\\Frontend\\Head' ) ) {
							remove_action( 'wp_head', $fn, (int) $priority );
						}
						if ( is_array( $fn ) && isset( $fn[0] ) && $fn[0] === 'RankMath\\Frontend\\Head' ) {
							remove_action( 'wp_head', $fn, (int) $priority );
						}
					}
				}
			}
		}
	}

	# مطابق لـ ServicesTheme: أولوية 0 على هذه الخطافات
	add_action( 'plugins_loaded', 'kayan_seo_disable_rank_math_frontend', 0 );
	add_action( 'init', 'kayan_seo_disable_rank_math_frontend', 0 );
	add_action( 'wp', 'kayan_seo_disable_rank_math_frontend', 0 );
	add_action( 'wp_head', 'kayan_seo_disable_rank_math_frontend', 0 );
}
