<?php
/**
 * kayan-platform — Phase 1 Core Architecture
 * ════════════════════════════════════════════════════════════════
 * Long-term SEO platform foundation for a SINGLE WordPress install:
 * Country Engine + Language Engine + Request Context +
 * Country Settings repository + Content Locale contracts +
 * SEO Bridge (Rank Math compatible).
 *
 * Phase 1 constraints (enforced):
 * - No frontend / UI changes
 * - No URL / rewrite changes (delegates to existing kayan-i18n)
 * - No data migration
 * - No duplicated countries, rewrites, settings, or queries
 * - Extractable later to a plugin without rewriting domain logic
 * ════════════════════════════════════════════════════════════════
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'KAYAN_PLATFORM_LOADED' ) ) {
	return;
}
define( 'KAYAN_PLATFORM_LOADED', true );
define( 'KAYAN_PLATFORM_VERSION', '1.0.0' );
define( 'KAYAN_PLATFORM_DIR', __DIR__ );
define( 'KAYAN_PLATFORM_URL_MODE_LEGACY', 'legacy' );
define( 'KAYAN_PLATFORM_URL_MODE_LANG_FIRST', 'language_first' );

require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-country-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-language-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-context.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-country-settings.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-content-locale.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-url.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-seo-bridge.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-platform.php';
require_once KAYAN_PLATFORM_DIR . '/includes/helpers.php';

/**
 * Bootstrap once. Depends on kayan-i18n helpers when present (glob loads i18n first).
 */
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
