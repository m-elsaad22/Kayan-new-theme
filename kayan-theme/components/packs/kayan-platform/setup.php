<?php
/**
 * kayan-platform — SEO Platform Core (Phase 1 + Phase 2)
 * ════════════════════════════════════════════════════════════════
 * Phase 1: Country/Language engines, context, settings, locale meta, SEO bridge
 * Phase 2: Country Routing Engine + Content Resolution Engine
 *          + Programmatic SEO entity registry (architecture only)
 *
 * Constraints:
 * - Single WP install (no Multisite / WPML / Polylang)
 * - Canonical URLs: language-first (/en/sa/…)
 * - Legacy /{country}/en/… → 301
 * - Rank Math = only SEO engine (KAYAN extends via filters)
 * - No frontend/admin redesign, no data migration in Phase 2
 * ════════════════════════════════════════════════════════════════
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'KAYAN_PLATFORM_LOADED' ) ) {
	return;
}
define( 'KAYAN_PLATFORM_LOADED', true );
define( 'KAYAN_PLATFORM_VERSION', '2.0.0' );
define( 'KAYAN_PLATFORM_DIR', __DIR__ );
define( 'KAYAN_PLATFORM_URL_MODE_LEGACY', 'legacy' );
define( 'KAYAN_PLATFORM_URL_MODE_LANG_FIRST', 'language_first' );

require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-country-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-language-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-context.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-country-settings.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-content-locale.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-url.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-programmatic-seo.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-country-router.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-content-resolver.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-seo-bridge.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-platform.php';
require_once KAYAN_PLATFORM_DIR . '/includes/helpers.php';

if ( ! function_exists( 'kayan_platform' ) ) {
	function kayan_platform() {
		return Kayan_Platform::instance();
	}
}

add_action(
	'after_setup_theme',
	static function () {
		kayan_platform()->boot();
	},
	1
);
