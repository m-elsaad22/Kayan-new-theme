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

if ( ! function_exists( 'kayan_migrations' ) ) {
	/**
	 * Migration & Version Engine facade.
	 *
	 * @return Kayan_Migration_Engine
	 */
	function kayan_migrations() {
		return kayan_platform()->migrations;
	}
}

if ( ! function_exists( 'kayan_ai' ) ) {
	/**
	 * AI Platform facade — the only entry point application code should use
	 * for AI text completion / translation (never a concrete provider class).
	 *
	 * @return Kayan_AI_Platform
	 */
	function kayan_ai() {
		return kayan_platform()->ai;
	}
}

if ( ! function_exists( 'kayan_workflow' ) ) {
	/**
	 * Content Workflow facade — draft/review/approve/publish/regenerate lifecycle.
	 *
	 * @return Kayan_Content_Workflow
	 */
	function kayan_workflow() {
		return kayan_platform()->workflow;
	}
}

if ( ! function_exists( 'kayan_quality' ) ) {
	/**
	 * Quality Engine facade — pre-publish validation for generated pages.
	 *
	 * @return Kayan_Quality_Engine
	 */
	function kayan_quality() {
		return kayan_platform()->quality;
	}
}

if ( ! function_exists( 'kayan_dependencies' ) ) {
	/**
	 * Dependency Graph facade — marks affected generated pages for regeneration.
	 *
	 * @return Kayan_Dependency_Graph
	 */
	function kayan_dependencies() {
		return kayan_platform()->dependencies;
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

if ( ! function_exists( 'kayan_integration' ) ) {
	/**
	 * Theme ↔ Platform integration facade (Phase 3.1 adapters).
	 *
	 * @return Kayan_Theme_Integration
	 */
	function kayan_integration() {
		return kayan_platform()->integration;
	}
}

if ( ! function_exists( 'kayan_theme_option' ) ) {
	/**
	 * Theme option reader with country-profile preference for mapped keys.
	 *
	 * @param string $key     Option key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	function kayan_theme_option( $key, $default = '' ) {
		return Kayan_Theme_Integration::theme_option( $key, $default );
	}
}
