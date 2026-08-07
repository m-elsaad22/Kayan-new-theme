<?php
/**
 * kayan-platform — SEO Platform Core (Phases 1 → 5)
 * ════════════════════════════════════════════════════════════════
 * Phase 1: Country/Language engines, context, settings, locale meta, SEO bridge
 * Phase 2: Country Routing Engine + Content Resolution Engine
 * Phase 2.5–2.7: PSEO architecture, Entities, Tags, Query/Cache/Settings/Logger
 * Phase 3.0: Admin Platform Core (module registry, permissions, UI framework, dashboard foundation)
 * Phase 3.1: Existing Theme Integration (adapters — no new feature UIs)
 * Phase 3 (complete): Dashboard, Navigation, Settings Framework, Countries,
 *   Languages, Entities, Relationships, Permissions, Logs, System Health,
 *   Import/Export, Rank Math Integration — all functional inside the
 *   Admin Platform.
 * Phase 4: Migration & Version Engine (automatic, idempotent, incremental
 *   upgrades — no manual step, ever) + the complete Programmatic SEO
 *   Platform: real Template/Blueprint/Block engines, a Generator that
 *   creates/updates real WP posts (preview, draft, publish, scheduled,
 *   bulk, regeneration), a DB-backed Queue, and a Scheduler that drives it
 *   automatically.
 * Phase 5: AI, Workflow & Quality Platform — an interchangeable AI provider
 *   registry (OpenAI/Claude/Gemini/Mistral/future, never leaking
 *   vendor-specific logic into application code), a Content Workflow with
 *   10 explicit lifecycle states, a Quality Engine gating publish, a
 *   Dependency Graph that targets ONLY affected pages for regeneration, AI
 *   translation linked via the existing Content Locale contract, and
 *   safety guards (locked blocks, manual-override protection, confirm-
 *   required full regeneration of approved/published pages). Analytics/
 *   Performance/Security remain shells — out of scope for this phase.
 *
 * Constraints:
 * - Single WP install (no Multisite / WPML / Polylang)
 * - Canonical URLs: language-first (/en/sa/…)
 * - Rank Math = only SEO engine (KAYAN extends via filters)
 * - Reuse / extend / wrap existing theme packs — never duplicate
 * - Zero breaking changes for existing sites
 * ════════════════════════════════════════════════════════════════
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'KAYAN_PLATFORM_LOADED' ) ) {
	return;
}
define( 'KAYAN_PLATFORM_LOADED', true );
define( 'KAYAN_PLATFORM_VERSION', '5.0.0' );
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
require_once KAYAN_PLATFORM_DIR . '/includes/migration/class-kayan-migration-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/ai/class-kayan-ai-provider-interface.php';
require_once KAYAN_PLATFORM_DIR . '/includes/ai/class-kayan-ai-provider-base.php';
require_once KAYAN_PLATFORM_DIR . '/includes/ai/class-kayan-ai-provider-null.php';
require_once KAYAN_PLATFORM_DIR . '/includes/ai/class-kayan-ai-provider-openai.php';
require_once KAYAN_PLATFORM_DIR . '/includes/ai/class-kayan-ai-provider-claude.php';
require_once KAYAN_PLATFORM_DIR . '/includes/ai/class-kayan-ai-provider-gemini.php';
require_once KAYAN_PLATFORM_DIR . '/includes/ai/class-kayan-ai-provider-mistral.php';
require_once KAYAN_PLATFORM_DIR . '/includes/ai/class-kayan-ai-platform.php';
require_once KAYAN_PLATFORM_DIR . '/includes/class-kayan-programmatic-seo.php';
require_once KAYAN_PLATFORM_DIR . '/includes/quality/class-kayan-quality-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/workflow/class-kayan-content-workflow.php';
require_once KAYAN_PLATFORM_DIR . '/includes/dependency/class-kayan-dependency-graph.php';
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
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-ai-bridge-provider.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-generator.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-renderer.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-scheduler.php';
require_once KAYAN_PLATFORM_DIR . '/includes/pseo/class-kayan-pseo-engine.php';
require_once KAYAN_PLATFORM_DIR . '/includes/admin/class-kayan-admin-permissions.php';
require_once KAYAN_PLATFORM_DIR . '/includes/admin/class-kayan-admin-module-registry.php';
require_once KAYAN_PLATFORM_DIR . '/includes/admin/class-kayan-admin-ui.php';
require_once KAYAN_PLATFORM_DIR . '/includes/admin/class-kayan-admin-dashboard.php';
require_once KAYAN_PLATFORM_DIR . '/includes/admin/class-kayan-admin-core-modules.php';
require_once KAYAN_PLATFORM_DIR . '/includes/admin/class-kayan-admin-feature-modules.php';
require_once KAYAN_PLATFORM_DIR . '/includes/admin/class-kayan-admin-platform.php';
require_once KAYAN_PLATFORM_DIR . '/includes/integration/class-kayan-theme-integration.php';
require_once KAYAN_PLATFORM_DIR . '/includes/integration/class-kayan-compatibility-report.php';
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
