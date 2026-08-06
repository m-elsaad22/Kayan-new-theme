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
	 * Country setting helper (BC). Prefer kayan_settings() for new code.
	 *
	 * @param string      $key     Setting key / dot path.
	 * @param string|null $country Country code.
	 * @param mixed       $default Default.
	 * @return mixed
	 */
	function kayan_platform_setting( $key, $country = null, $default = '' ) {
		if ( null === $country ) {
			$country = kayan_platform_country();
		}
		if ( isset( kayan_platform()->settings_engine ) ) {
			return kayan_platform()->settings_engine->get_country( $key, $country, $default );
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

if ( ! function_exists( 'kayan_pseo' ) ) {
	/**
	 * @return Kayan_PSEO_Engine
	 */
	function kayan_pseo() {
		return kayan_platform()->pseo;
	}
}

if ( ! function_exists( 'kayan_entity' ) ) {
	/**
	 * Entity Relationship Engine facade.
	 *
	 * @return Kayan_Entity_Engine
	 */
	function kayan_entity() {
		return kayan_platform()->entity;
	}
}

if ( ! function_exists( 'kayan_tags' ) ) {
	/**
	 * Dynamic Data Tags registry / resolver.
	 *
	 * @return Kayan_Dynamic_Data_Tags
	 */
	function kayan_tags() {
		return kayan_platform()->tags;
	}
}

if ( ! function_exists( 'kayan_query' ) ) {
	/**
	 * @return Kayan_Query_Engine
	 */
	function kayan_query() {
		return kayan_platform()->query;
	}
}

if ( ! function_exists( 'kayan_cache' ) ) {
	/**
	 * @return Kayan_Cache_Engine
	 */
	function kayan_cache() {
		return kayan_platform()->cache;
	}
}

if ( ! function_exists( 'kayan_settings' ) ) {
	/**
	 * Unified Settings Engine (global / country / language / module).
	 *
	 * @return Kayan_Settings_Engine
	 */
	function kayan_settings() {
		return kayan_platform()->settings_engine;
	}
}

if ( ! function_exists( 'kayan_logger' ) ) {
	/**
	 * @return Kayan_Logger
	 */
	function kayan_logger() {
		return kayan_platform()->logger;
	}
}

if ( ! function_exists( 'kayan_admin' ) ) {
	/**
	 * KAYAN Admin Platform facade.
	 *
	 * @return Kayan_Admin_Platform
	 */
	function kayan_admin() {
		return kayan_platform()->admin;
	}
}
