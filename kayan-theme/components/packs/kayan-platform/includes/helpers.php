<?php
/**
 * Procedural helpers for kayan-platform (single definitions, no duplication).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'kayan_platform_owns_routing' ) ) {
	/**
	 * When true, kayan-platform owns rewrite rules + content resolution.
	 * kayan-i18n must not register duplicate rewrites/resolvers.
	 *
	 * @return bool
	 */
	function kayan_platform_owns_routing() {
		/**
		 * @param bool $owns Owns routing.
		 */
		return (bool) apply_filters( 'kayan_platform_owns_routing', true );
	}
}

if ( ! function_exists( 'kayan_platform_context' ) ) {
	/**
	 * @return array{country:string,language:string}
	 */
	function kayan_platform_context() {
		return kayan_platform()->context->get();
	}
}

if ( ! function_exists( 'kayan_platform_country' ) ) {
	/**
	 * @return string
	 */
	function kayan_platform_country() {
		return kayan_platform()->context->country();
	}
}

if ( ! function_exists( 'kayan_platform_language' ) ) {
	/**
	 * @return string
	 */
	function kayan_platform_language() {
		return kayan_platform()->context->language();
	}
}

if ( ! function_exists( 'kayan_platform_setting' ) ) {
	/**
	 * @param string      $key     Setting key / dot path.
	 * @param string|null $country Country code.
	 * @param mixed       $default Default.
	 * @return mixed
	 */
	function kayan_platform_setting( $key, $country = null, $default = '' ) {
		if ( null === $country ) {
			$country = kayan_platform_country();
		}
		return kayan_platform()->settings->get( $key, $country, $default );
	}
}

if ( ! function_exists( 'kayan_platform_url' ) ) {
	/**
	 * @param string      $slug    Path slug.
	 * @param string|null $country Country.
	 * @param string|null $lang    Language.
	 * @return string
	 */
	function kayan_platform_url( $slug = '/', $country = null, $lang = null ) {
		$platform = kayan_platform();
		if ( null === $country ) {
			$country = $platform->context->country();
		}
		if ( null === $lang ) {
			$lang = $platform->context->language();
		}
		return $platform->urls->build( $country, $lang, $slug );
	}
}
