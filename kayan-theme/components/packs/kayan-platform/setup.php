<?php
/**
 * kayan-platform — SEO Platform Core (Phases 1 → 2.7)
 * ════════════════════════════════════════════════════════════════
 * Phase 1: Country/Language engines, context, settings, locale meta, SEO bridge
 * Phase 2: Country Routing Engine + Content Resolution Engine
 * Phase 2.5: Native Programmatic SEO Engine architecture (no generation yet)
 * Phase 2.5.1: Templates, Blocks, Media, Blueprint versioning, prefer existing CPTs
 * Phase 2.6: Entity Relationship Engine + Dynamic Data Tags
 * Phase 2.7: Query / Cache / Settings / Logger engines + developer docs
 *
 * Constraints:
 * - Single WP install (no Multisite / WPML / Polylang)
 * - Canonical URLs: language-first (/en/sa/…)
 * - Rank Math = only SEO engine (KAYAN extends via filters)
 * - Architecture/APIs only until Admin Platform — no page/content generation, no admin UI
 * ════════════════════════════════════════════════════════════════
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'KAYAN_PLATFORM_LOADED' ) ) {
	return;
}
define( 'KAYAN_PLATFORM_LOADED', true );
define( 'KAYAN_PLATFORM_VERSION', '2.7.0' );
define( 'KAYAN_PLATFORM_DIR', __DIR__ );
define( 'KAYAN_PLATFORM_URL_MODE_LEGACY', 'legacy' );
define( 'KAYAN_PLATFORM_URL_MODE_LANG_FIRST', 'language_first' );

require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-country-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-language-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-context.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-country-settings.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-content-locale.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-url.php';
require_once KAYAN_PLATFORM_DIR . '/includes/infra/class-kayan-cache-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/infra/class-kayan-settings-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/infra/class-kayan-logger.php';
require_once KAYAN_PLATFORM_DIR . '/includes/infra/class-kayan-query-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/infra/class-kayan-docs-generator.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-programmatic-seo.php';
require_once KAYAN_PLATFORM_DIR . '/includes/entities/class-kayan-entity-api.php';
require_once KAYAN_PLATFORM_DIR . '/includes/entities/class-kayan-entity-relationships.php';
require_once KAYAN_PLATFORM_DIR . '/includes/entities/class-kayan-entity-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/entities/class-kayan-dynamic-data-tags.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-blocks.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-templates.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-media.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-patterns.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-rules.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-identity.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-blueprint.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-storage.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-jobs.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-ai.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-generator.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-engine.php';
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
