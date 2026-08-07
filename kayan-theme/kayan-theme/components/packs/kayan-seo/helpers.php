<?php
/**
 * kayan-seo / helpers.php
 *
 * KAYAN SEO يعمل طالما الخيار kayan_seo_disable فارغ.
 * إذا كان غير فارغ (مثلاً 1 أو on) → يتوقف KAYAN SEO وتعود واجهة Rank Math.
 */

if ( ! function_exists( 'kayan_seo_is_disabled' ) ) {
	/**
	 * هل تم تعطيل KAYAN SEO؟
	 */
	function kayan_seo_is_disabled() {
		$val = function_exists( 'yc_get_option' )
			? yc_get_option( 'kayan_seo_disable', '' )
			: get_option( 'kayan_seo_disable', '' );
		return ! empty( $val );
	}
}

if ( ! function_exists( 'kayan_seo_is_enabled' ) ) {
	/**
	 * هل KAYAN SEO يعمل؟ (الوضع الافتراضي: نعم)
	 */
	function kayan_seo_is_enabled() {
		return ! kayan_seo_is_disabled();
	}
}
