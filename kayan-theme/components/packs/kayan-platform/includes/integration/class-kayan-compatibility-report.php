<?php
/**
 * Compatibility Report — Phase 3.1.
 *
 * Programmatic inventory of existing theme systems vs platform integration status.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Compatibility_Report {

	/** @var Kayan_Theme_Integration */
	private $integration;

	public function __construct( Kayan_Theme_Integration $integration ) {
		$this->integration = $integration;
	}

	/**
	 * Full structured report.
	 *
	 * @return array{generated_at:string,theme_version:string,platform_version:string,systems:array<int,array<string,mixed>>}
	 */
	public function generate() {
		return array(
			'generated_at'      => gmdate( 'c' ),
			'theme_version'     => $this->theme_version(),
			'platform_version'  => defined( 'KAYAN_PLATFORM_VERSION' ) ? KAYAN_PLATFORM_VERSION : '',
			'phase'             => '3.1',
			'systems'           => $this->systems(),
			'adapters'          => $this->integration->describe(),
		);
	}

	/**
	 * Markdown report body.
	 *
	 * @return string
	 */
	public function to_markdown() {
		$data = $this->generate();
		$lines = array(
			'# KAYAN Theme ↔ Platform Compatibility Report',
			'',
			'**Phase:** 3.1 — Existing Theme Integration',
			'**Theme version:** `' . $data['theme_version'] . '`',
			'**Platform version:** `' . $data['platform_version'] . '`',
			'**Generated:** `' . $data['generated_at'] . '`',
			'',
			'This report maps every major existing KAYAN Theme system to the platform.',
			'Adapters wrap/extend existing packs — they do not replace them.',
			'',
			'## Status legend',
			'',
			'| Status | Meaning |',
			'|--------|---------|',
			'| Already compatible | Works with platform as-is |',
			'| Needs Adapter | Connected via adapter in Phase 3.1 |',
			'| Needs Extension | Light filter/hook extension applied |',
			'| Needs Refactoring | Deeper change deferred; documented risk |',
			'| Deprecated | Kept for BC; prefer replacement |',
			'| Can be removed later | Safe to remove after migration window |',
			'',
			'## Systems',
			'',
		);

		foreach ( $data['systems'] as $system ) {
			$lines[] = '### ' . $system['name'];
			$lines[] = '';
			$lines[] = '| Field | Value |';
			$lines[] = '|-------|-------|';
			$lines[] = '| Status | ' . $system['status'] . ' |';
			$lines[] = '| Risk | ' . $system['risk'] . ' |';
			$lines[] = '| Adapter | ' . ( $system['adapter'] ? '`' . $system['adapter'] . '`' : '—' ) . ' |';
			$lines[] = '| Path | `' . $system['path'] . '` |';
			$lines[] = '';
			$lines[] = $system['summary'];
			$lines[] = '';
			$lines[] = '**Migration strategy:** ' . $system['migration'];
			$lines[] = '';
			if ( ! empty( $system['notes'] ) ) {
				$lines[] = $system['notes'];
				$lines[] = '';
			}
		}

		$lines[] = '## Adapter inventory';
		$lines[] = '';
		foreach ( $this->integration->adapters() as $id => $adapter ) {
			$st = method_exists( $adapter, 'status' ) ? $adapter->status() : array();
			$state = isset( $st['state'] ) ? $st['state'] : 'unknown';
			$notes = isset( $st['notes'] ) ? $st['notes'] : '';
			$lines[] = '- `' . $id . '` — **' . $state . '**' . ( $notes ? ' — ' . $notes : '' );
		}
		$lines[] = '';
		$lines[] = '## Explicit non-goals (Phase 3.1)';
		$lines[] = '';
		$lines[] = '- No new Countries / Languages / Templates / AI / Dashboard Widgets UI';
		$lines[] = '- No second Theme Options, booking, payment, tracking, or SEO stack';
		$lines[] = '- No data migration of legacy `city` terms';
		$lines[] = '- Zero breaking changes for existing sites';
		$lines[] = '';

		return implode( "\n", $lines );
	}

	/**
	 * Write report markdown under platform pack + docs.
	 *
	 * @return array{ok:bool,files:string[]}
	 */
	public function write_files() {
		$md    = $this->to_markdown();
		$files = array();
		$targets = array();
		if ( defined( 'KAYAN_PLATFORM_DIR' ) ) {
			$targets[] = KAYAN_PLATFORM_DIR . '/PHASE3.1-COMPATIBILITY-REPORT.md';
			$docs = dirname( KAYAN_PLATFORM_DIR, 3 ) . '/docs/Compatibility.md';
			$targets[] = $docs;
		}
		foreach ( $targets as $path ) {
			$dir = dirname( $path );
			if ( ! is_dir( $dir ) ) {
				wp_mkdir_p( $dir );
			}
			if ( false !== file_put_contents( $path, $md ) ) {
				$files[] = $path;
			}
		}
		return array(
			'ok'    => ! empty( $files ),
			'files' => $files,
		);
	}

	/**
	 * @return string
	 */
	private function theme_version() {
		$theme = wp_get_theme();
		return $theme ? (string) $theme->get( 'Version' ) : '';
	}

	/**
	 * @return array<int,array<string,mixed>>
	 */
	private function systems() {
		$rm = Kayan_Theme_Integration::rank_math_active();
		return array(
			array(
				'name'      => 'Theme Options (YTS / FieldsMachine)',
				'path'      => 'components/packs/FieldsMachine/',
				'status'    => 'Needs Adapter',
				'risk'      => 'Low',
				'adapter'   => 'theme_options',
				'summary'   => 'Existing Theme Options remain the write UI. Frontend `get_option` for contact keys prefers country profile values via option bridges.',
				'migration' => 'Keep writing via YTS. Populate country profiles gradually; empty profile fields continue to fall back to Theme Options (Country Settings dual-read).',
				'notes'     => 'Admin screens skip bridges so editors still see stored option values.',
			),
			array(
				'name'      => 'Custom Post Types (kayan-cpt)',
				'path'      => 'components/packs/kayan-cpt/',
				'status'    => 'Needs Extension',
				'risk'      => 'Low',
				'adapter'   => 'cpt',
				'summary'   => 'Canonical CPT pack reused. Content Locale post types + rewrite map extended to include services, reviews, faqs, pricing, portfolio, before_after.',
				'migration' => 'None — continue using kayan-cpt. Do not revive post-types stub pack.',
				'notes'     => '',
			),
			array(
				'name'      => 'Taxonomies — cities (canonical)',
				'path'      => 'components/packs/kayan-cpt/setup.php',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => 'legacy_city',
				'summary'   => '`cities` taxonomy is canonical for Query Engine, Entity Engine, and Country Router rewrite map.',
				'migration' => 'Use `cities` + term meta `kayan_country`.',
				'notes'     => '',
			),
			array(
				'name'      => 'Taxonomies — legacy city',
				'path'      => 'components/packs/taxonomies/setup.php',
				'status'    => 'Deprecated',
				'risk'      => 'Medium',
				'adapter'   => 'legacy_city',
				'summary'   => 'Legacy non-hierarchical `city` taxonomy may clash with `cities` rewrite slug. Empty legacy taxonomy is unregistered; non-empty kept for BC.',
				'migration' => 'If terms exist, plan a later term migration to `cities` then remove taxonomies pack registration. Can be removed later when empty.',
				'notes'     => 'Status may become “Can be removed later” once term count is zero.',
			),
			array(
				'name'      => 'Widgets (YourColorWidgets)',
				'path'      => 'components/packs/YourColorWidgets/',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => '',
				'summary'   => 'Widget machine unchanged. Contact widgets benefit from Theme Options phone bridges on the frontend.',
				'migration' => 'Reuse as-is. Prefer `Kayan_Theme_Integration::theme_option()` in new widget code.',
				'notes'     => '',
			),
			array(
				'name'      => 'Blocks (PSEO content blocks)',
				'path'      => 'components/packs/kayan-platform/includes/pseo/',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => '',
				'summary'   => 'PSEO blocks registry already platform-native. Not Gutenberg blocks.',
				'migration' => 'Continue registering via `kayan_pseo_register_blocks`.',
				'notes'     => 'No Blocks UI in Phase 3.1.',
			),
			array(
				'name'      => 'Templates (theme + PSEO)',
				'path'      => 'components/packs/@* + pseo/templates',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => '',
				'summary'   => 'Existing theme templates continue to load. PSEO templates engine remains architecture-only.',
				'migration' => 'No template migration. No Templates UI.',
				'notes'     => '',
			),
			array(
				'name'      => 'Shortcodes',
				'path'      => 'components/packs/shortcodes/',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => '',
				'summary'   => 'Existing shortcodes unchanged; contact-related output inherits option bridges.',
				'migration' => 'Reuse tags as-is.',
				'notes'     => '',
			),
			array(
				'name'      => 'SEO (theme-seo + Rank Math)',
				'path'      => 'components/packs/theme-seo/ + SEO Bridge',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => 'admin_bridges',
				'summary'   => 'SEO Bridge already extends Rank Math only. theme-seo defers title when Rank Math present.',
				'migration' => 'Keep Rank Math as sole SEO engine. Admin Platform rankmath module bridges to Rank Math admin.',
				'notes'     => $rm ? 'Rank Math detected active.' : 'Rank Math not detected in this runtime — install/activate on production sites.',
			),
			array(
				'name'      => 'Schema (YourColor__Schema)',
				'path'      => 'components/packs/schema/',
				'status'    => 'Needs Adapter',
				'risk'      => 'Medium',
				'adapter'   => 'schema',
				'summary'   => 'Theme JSON-LD can compete with Rank Math. Adapter forces existing `validate__schema` kill switch when Rank Math is active.',
				'migration' => 'Leave pack in place. Rely on kill switch + Rank Math schema. Optional later: remove pack after all sites on Rank Math.',
				'notes'     => 'Known typo risk: LocalBusiness may read `YourColoe_Schema_business`.',
			),
			array(
				'name'      => 'Breadcrumb()',
				'path'      => 'components/packs/Breadcrumb/',
				'status'    => 'Needs Refactoring',
				'risk'      => 'Medium',
				'adapter'   => '',
				'summary'   => 'Emits BreadcrumbList JSON-LD + HTML; limited taxonomy coverage (no `cities`); home URL not language-first.',
				'migration' => 'Deferred — avoid template edits in 3.1. Prefer Rank Math breadcrumbs long-term; wrap Breadcrumb() in a later phase if needed.',
				'notes'     => 'Documented risk only; no behavior change in Phase 3.1 to preserve exact frontend markup.',
			),
			array(
				'name'      => 'Booking (kayan-booking)',
				'path'      => 'components/packs/kayan-booking/',
				'status'    => 'Needs Adapter',
				'risk'      => 'Low',
				'adapter'   => 'booking',
				'summary'   => 'Booking pack remains the booking system. Currency prefers country profile; WhatsApp confirm uses Theme Options bridge.',
				'migration' => 'Continue using kayan-bookings admin. Set currency on country profile when multi-country.',
				'notes'     => '',
			),
			array(
				'name'      => 'Payments (kayan-payment)',
				'path'      => 'components/packs/kayan-payment/',
				'status'    => 'Needs Adapter',
				'risk'      => 'Low',
				'adapter'   => 'payment',
				'summary'   => 'Invoice company name prefers country `business_name`. Payment methods unchanged.',
				'migration' => 'Reuse payment pack; no second gateway layer.',
				'notes'     => '',
			),
			array(
				'name'      => 'Tracking / DNI (kayan-track)',
				'path'      => 'components/packs/kayan-track/',
				'status'    => 'Needs Adapter',
				'risk'      => 'Low',
				'adapter'   => 'track',
				'summary'   => 'DNI default phone used `contact_number` (often empty). Bridged to country phone / `phonenumber`.',
				'migration' => 'Keep KAYAN Track admin. Optionally set `contact_number` or rely on bridge.',
				'notes'     => '',
			),
			array(
				'name'      => 'Contact System (RuknContact)',
				'path'      => 'components/packs/RuknContact/',
				'status'    => 'Needs Extension',
				'risk'      => 'Low',
				'adapter'   => 'rukn_contact',
				'summary'   => 'Extends `rukn_cs_post_types` with kayan-cpt types. Global numbers via Theme Options bridges.',
				'migration' => 'Reuse RuknContact resolve API. No second contact system.',
				'notes'     => '',
			),
			array(
				'name'      => 'Cities / Countries / Languages engines',
				'path'      => 'components/packs/kayan-platform/ + kayan-i18n/',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => '',
				'summary'   => 'Platform Country/Language engines + kayan-i18n routing ownership already integrated. Phase 3 added real Countries/Languages Admin Platform modules editing the platform profile layer (not the i18n registry itself).',
				'migration' => 'Manage per-country business profile and custom languages via the Admin Platform. Theme Options (YTS) remains the source for raw contact fields when a profile is empty.',
				'notes'     => '',
			),
			array(
				'name'      => 'Theme Settings / Country Profiles',
				'path'      => 'class-kayan-country-settings.php + Settings Engine',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => 'theme_options',
				'summary'   => 'Country Settings already dual-read Theme Options into empty profile fields. Phase 3.1 adds reverse frontend bridges.',
				'migration' => 'Use `kayan_settings()` / `kayan_platform_setting()` in new code.',
				'notes'     => '',
			),
			array(
				'name'      => 'Rewrite Rules',
				'path'      => 'Country Router + kayan-i18n + AjaxCenter + kayan-cpt',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => 'cpt',
				'summary'   => 'When `kayan_platform_owns_routing()` is true, i18n skips duplicate rewrites. CPT rewrite map extended.',
				'migration' => 'Flush permalinks after deploy if rewrite maps change. Do not register competing rules.',
				'notes'     => '',
			),
			array(
				'name'      => 'Admin Pages (existing)',
				'path'      => 'YTS, kayan-bookings, kayan-track-pro, Rank Math',
				'status'    => 'Needs Adapter',
				'risk'      => 'Low',
				'adapter'   => 'admin_bridges',
				'summary'   => 'Existing menus remain. Analytics/Tools modules still link to KAYAN Track / Theme Options. Countries, Languages, and Rank Math Integration are now real Admin Platform screens (Phase 3) that edit platform-owned data alongside the existing menus.',
				'migration' => 'Operators keep using existing menus for booking/tracking/theme-wide options; use the Admin Platform for country profiles, languages, and platform settings.',
				'notes'     => '',
			),
			array(
				'name'      => 'Menus (WP + header switcher)',
				'path'      => '#header/part.php + kayan-i18n/switcher.php',
				'status'    => 'Needs Adapter',
				'risk'      => 'Low',
				'adapter'   => 'i18n_switcher',
				'summary'   => '`rukn_v3_lang_switcher` action had no listener. Wired to `kayan_i18n_render_switcher()`.',
				'migration' => 'None — existing switcher renderer reused.',
				'notes'     => '',
			),
			array(
				'name'      => 'Existing Helpers / APIs',
				'path'      => 'functions.php + kayan-platform/includes/helpers.php',
				'status'    => 'Needs Extension',
				'risk'      => 'Low',
				'adapter'   => '',
				'summary'   => 'Added `kayan_integration()` / `kayan_theme_option()` helpers. Existing `yc_get_option`, `kayan_platform_*` unchanged.',
				'migration' => 'Prefer platform helpers for new modules; keep `yc_get_option` for legacy packs.',
				'notes'     => '',
			),
			array(
				'name'      => 'Query / Cache / Settings / Logger',
				'path'      => 'includes/infra/',
				'status'    => 'Needs Extension',
				'risk'      => 'Low',
				'adapter'   => 'query',
				'summary'   => 'Query Engine resources ensured for kayan-cpt; optional `legacy_city` resource when taxonomy remains.',
				'migration' => 'New code must use `kayan_query()` / `kayan_cache()` / `kayan_settings()` / `kayan_logger()`.',
				'notes'     => '',
			),
			array(
				'name'      => 'Entity Relationship Engine + Dynamic Tags',
				'path'      => 'includes/entities/',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => '',
				'summary'   => 'Already maps city→country via term meta. No duplicate relationship layer.',
				'migration' => 'Use `kayan_entity()` / `kayan_tags()`.',
				'notes'     => '',
			),
			array(
				'name'      => 'PSEO architecture',
				'path'      => 'includes/pseo/',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => '',
				'summary'   => 'Architecture-only; no generation. Prefer existing CPTs.',
				'migration' => 'No change until generation phase approved.',
				'notes'     => 'No AI / Templates UI in 3.1.',
			),
			array(
				'name'      => 'Admin Platform Core',
				'path'      => 'includes/admin/',
				'status'    => 'Already compatible',
				'risk'      => 'Low',
				'adapter'   => 'admin_bridges',
				'summary'   => 'Phase 3 completed Dashboard, Navigation, Settings, Countries, Languages, Entities, Relationships, Permissions, Logs, System Health, Import/Export, and Rank Math Integration. Templates/Blueprints/Blocks/PSEO/AI/Queue/Analytics/Performance/Security remain placeholder shells for later phases.',
				'migration' => 'Register future UIs only through `kayan_admin()` when approved.',
				'notes'     => '',
			),
		);
	}
}
