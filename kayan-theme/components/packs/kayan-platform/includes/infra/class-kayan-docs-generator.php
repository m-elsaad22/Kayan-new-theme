<?php
/**
 * KAYAN Docs Generator — regenerates developer documentation from live contracts.
 *
 * Writes markdown into the theme /docs directory. Architecture tooling only.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Docs_Generator {

	/** @var string */
	private $docs_dir;

	/**
	 * @param string $docs_dir Absolute docs directory.
	 */
	public function __construct( $docs_dir = '' ) {
		if ( ! $docs_dir ) {
			if ( defined( 'KAYAN_PLATFORM_DIR' ) ) {
				// kayan-platform → packs → components → kayan-theme
				$docs_dir = dirname( KAYAN_PLATFORM_DIR, 3 ) . '/docs';
			} else {
				// includes/infra → includes → kayan-platform → packs → components → kayan-theme
				$docs_dir = dirname( __DIR__, 5 ) . '/docs';
			}
		}
		$this->docs_dir = rtrim( $docs_dir, '/' );
	}

	/**
	 * @return string
	 */
	public function docs_dir() {
		return $this->docs_dir;
	}

	/**
	 * Generate all documentation files.
	 *
	 * @param Kayan_Platform|null $platform Platform.
	 * @return array{ok:bool,files:string[],dir:string,errors?:string[]}
	 */
	public function generate( $platform = null ) {
		if ( ! $platform && function_exists( 'kayan_platform' ) ) {
			$platform = kayan_platform();
		}
		if ( ! $platform ) {
			return array(
				'ok'     => false,
				'files'  => array(),
				'dir'    => $this->docs_dir,
				'errors' => array( 'platform_unavailable' ),
			);
		}

		if ( ! is_dir( $this->docs_dir ) ) {
			wp_mkdir_p( $this->docs_dir );
		}

		$files = array(
			'Architecture.md'     => $this->doc_architecture( $platform ),
			'API.md'              => $this->doc_api( $platform ),
			'Entities.md'         => $this->doc_entities( $platform ),
			'Relationships.md'    => $this->doc_relationships( $platform ),
			'DynamicTags.md'      => $this->doc_tags( $platform ),
			'Templates.md'        => $this->doc_templates( $platform ),
			'Blueprints.md'       => $this->doc_blueprints( $platform ),
			'ProgrammaticSEO.md'  => $this->doc_pseo( $platform ),
			'Countries.md'        => $this->doc_countries( $platform ),
			'Languages.md'        => $this->doc_languages( $platform ),
			'RankMath.md'         => $this->doc_rankmath(),
			'DeveloperGuide.md'   => $this->doc_developer_guide( $platform ),
			'QueryEngine.md'      => $this->doc_query( $platform ),
			'CacheEngine.md'      => $this->doc_cache( $platform ),
			'SettingsEngine.md'   => $this->doc_settings( $platform ),
			'Logger.md'           => $this->doc_logger( $platform ),
			'MigrationEngine.md'  => $this->doc_migration_engine( $platform ),
			'AIPlatform.md'       => $this->doc_ai_platform( $platform ),
			'ContentWorkflow.md'  => $this->doc_content_workflow( $platform ),
			'QualityEngine.md'    => $this->doc_quality_engine( $platform ),
			'DependencyGraph.md'  => $this->doc_dependency_graph( $platform ),
			'AdminPlatform.md'    => $this->doc_admin_platform( $platform ),
			'AdminModules.md'     => $this->doc_admin_modules( $platform ),
			'AdminPermissions.md' => $this->doc_admin_permissions( $platform ),
			'AdminUI.md'          => $this->doc_admin_ui( $platform ),
			'AdminDashboard.md'   => $this->doc_admin_dashboard( $platform ),
			'ThemeIntegration.md' => $this->doc_theme_integration( $platform ),
			'Compatibility.md'    => $this->doc_compatibility( $platform ),
		);

		$written = array();
		foreach ( $files as $name => $body ) {
			$path = $this->docs_dir . '/' . $name;
			if ( false !== file_put_contents( $path, $body ) ) {
				$written[] = $name;
			}
		}

		$index = $this->doc_index( array_keys( $files ) );
		file_put_contents( $this->docs_dir . '/README.md', $index );
		$written[] = 'README.md';

		return array(
			'ok'    => true,
			'files' => $written,
			'dir'   => $this->docs_dir,
		);
	}

	/**
	 * @param string[] $files Files.
	 * @return string
	 */
	private function doc_index( array $files ) {
		$lines = array(
			'# KAYAN Platform — Developer Documentation',
			'',
			'Auto-generated from live platform contracts. Regenerate via:',
			'',
			'```php',
			'kayan_platform()->docs->generate();',
			'```',
			'',
			'## Contents',
			'',
		);
		foreach ( $files as $file ) {
			$lines[] = '- [' . $file . '](./' . $file . ')';
		}
		$lines[] = '';
		$lines[] = 'Platform version: `' . ( defined( 'KAYAN_PLATFORM_VERSION' ) ? KAYAN_PLATFORM_VERSION : '' ) . '`';
		$lines[] = '';
		return implode( "\n", $lines );
	}

	private function doc_architecture( $platform ) {
		$v = defined( 'KAYAN_PLATFORM_VERSION' ) ? KAYAN_PLATFORM_VERSION : '';
		return <<<MD
# Architecture

**Platform version:** {$v}

KAYAN is a single-install WordPress SEO platform (no Multisite, no WPML/Polylang).

## Layers

1. **Country / Language / Context** — request locale
2. **Routing + Content Resolution** — language-first URLs
3. **Entity Relationship Engine** — reusable entities + edges
4. **Dynamic Data Tags** — `{{tag}}` tokens for templates/blocks/AI
5. **Programmatic SEO Platform** — patterns, templates, blocks, blueprints, generator, queue, scheduler (Phase 4 — generation is live)
6. **AI Platform** — interchangeable provider registry (OpenAI/Claude/Gemini/Mistral/future) — Phase 5
7. **Content Workflow + Quality Engine + Dependency Graph** — lifecycle, pre-publish validation, targeted regeneration — Phase 5
8. **Core Infrastructure** — Query, Cache, Settings, Logger, Migration & Version Engine
9. **Admin Platform** — Module Registry, Permissions, UI Framework, Dashboard, functional feature modules
10. **Theme Integration** — Adapters connecting existing KAYAN Theme packs (Phase 3.1)
11. **SEO Bridge** — Rank Math only (filters, never competing head tags)

## Design rules

- Prefer existing WP content types; `kayan_pseo` hosts true multi-entity combinations only
- Modules must use Query / Cache / Settings / Logger / Entity APIs
- Admin features register through `kayan_admin()` — no isolated admin pages
- Do not call `WP_Query`, `get_posts`, `get_option`, or scatter transients in app code
- Rank Math remains the only SEO engine — the Generator only writes RM's own postmeta fields
- Reuse / extend / wrap existing theme packs — never duplicate implementations
- Schema/DB changes go through the Migration Engine — never a manual upgrade step
- Application code never talks to a concrete AI vendor — always `kayan_ai()`
- Publishing a generated page always goes through `kayan_workflow()->transition()` — never set `post_status` directly on a PSEO-managed post

## Boot order

Content Locale → Programmatic entities → Entity Engine → Data Tags → Cache → Settings Engine → Logger → Query → Migration Engine → AI Platform → Quality Engine → Content Workflow → Dependency Graph → PSEO → Router → Resolver → SEO Bridge → Admin Platform → Theme Integration
MD;
	}

	private function doc_api( $platform ) {
		return <<<MD
# Public API

## Facades

```php
kayan_platform();          // container
kayan_entity();            // Entity Relationship Engine
kayan_tags();              // Dynamic Data Tags
kayan_pseo();              // Programmatic SEO Engine
kayan_query();             // Query Engine
kayan_cache();             // Cache Engine
kayan_settings();          // Settings Engine
kayan_logger();            // Logger
kayan_migrations();        // Migration & Version Engine
kayan_ai();                // AI Platform (interchangeable providers)
kayan_workflow();          // Content Workflow
kayan_quality();           // Quality Engine
kayan_dependencies();      // Dependency Graph
kayan_admin();             // Admin Platform
kayan_integration();       // Theme Integration (adapters)
kayan_theme_option();      // Theme option + country profile bridge
kayan_platform_url();      // language-first URLs
kayan_platform_setting();  // country setting helper (BC)
```

## Describe contracts

```php
kayan_platform()->entity->describe();
kayan_platform()->pseo->describe();
kayan_platform()->query->describe();
kayan_platform()->cache->describe();
kayan_platform()->settings_engine->describe();
kayan_platform()->logger->describe();
kayan_admin()->describe();
kayan_integration()->describe();
kayan_tags()->describe();
```
MD;
	}

	private function doc_entities( $platform ) {
		$types = array();
		if ( isset( $platform->entity ) ) {
			$types = array_keys( $platform->entity->api->types() );
		}
		$list = implode( ', ', array_map( static function ( $t ) {
			return '`' . $t . '`';
		}, $types ) );
		return <<<MD
# Entities

Canonical entity types:

{$list}

## API

```php
kayan_entity()->get( 'service', \$ref );
kayan_entity()->name( 'city', \$ref );
kayan_entity()->field( 'service', \$ref, 'price_from' );
kayan_entity()->api->query( 'service', array( 'number' => 10 ) );
kayan_entity()->api->get_media( 'service', \$ref );
```

Prefer Entity API / Query Engine over `get_post_meta()`.
MD;
	}

	private function doc_relationships( $platform ) {
		return <<<MD
# Relationships

Meta contract: `kayan_entity_relationships`

```php
kayan_entity()->relate( 'service', \$sid, 'city', \$cid, 'serves', array( 'bidirectional' => true ) );
kayan_entity()->related( 'service', \$sid, 'city' );
kayan_entity()->relationships->has( 'service', \$sid, 'city', \$cid );
```

Edge shape: `{ type, ref, rel, meta }`

Legacy bridge: city term meta `kayan_country` → `located_in` country (read path).
MD;
	}

	private function doc_tags( $platform ) {
		$tags = array();
		if ( isset( $platform->tags ) ) {
			$tags = array_keys( $platform->tags->all() );
		}
		$list = implode( "\n", array_map( static function ( $t ) {
			return '- `{{' . $t . '}}`';
		}, $tags ) );
		return <<<MD
# Dynamic Data Tags

Syntax: `{{tag_name}}`

## Registered tags

{$list}

## Resolve

```php
kayan_tags()->resolve( 'Book {{service_name}} in {{city_name}} — {{phone}}', array(
  'country'  => 'ae',
  'language' => 'ar',
  'entities' => array( 'service' => '123', 'city' => '45' ),
) );
```

		Templates and future AI must consume tags instead of hardcoded values.
The Renderer resolves any `{{tag}}` found in block data at render time, so
values like phone/WhatsApp always stay fresh without regenerating content.
MD;
	}

	private function doc_templates( $platform ) {
		$templates = array();
		if ( isset( $platform->pseo->templates ) ) {
			$templates = array_keys( $platform->pseo->templates->all() );
		}
		$list = implode( ', ', array_map( static function ( $t ) {
			return '`' . $t . '`';
		}, $templates ) );
		return <<<MD
# Templates

PSEO Template Engine assigns page structure via blocks.

Templates: {$list}

```php
kayan_pseo()->templates->get( 'tpl_service_city' );
kayan_pseo()->templates->build_block_instances( 'tpl_service_city' );
```

Patterns reference `template_id`. Templates own block order/defaults; the
Renderer turns them into front-end HTML at request time. No admin builder UI
for authoring new templates — extend via `kayan_pseo_register_templates`.
MD;
	}

	private function doc_blueprints( $platform ) {
		return <<<MD
# Blueprints

Schema version 2 — block-based, versioned, lock-safe.

```php
kayan_pseo()->blueprint->build_skeleton( \$pattern, \$entities, \$country, \$lang );
kayan_pseo()->blueprint->upgrade_template( \$blueprint, \$template_id );
kayan_pseo()->blueprint->replace_block( \$blueprint, 'faq', \$data, 'ai' );
kayan_pseo()->blueprint->set_block_lock( \$blueprint, 'hero', true );
```

Manual/locked blocks survive template upgrades. Media is first-class via Media Engine.
`materialize()`/`regenerate()` (Phase 4) sanitize + persist blueprints via
this engine — locked blocks are never overwritten.
MD;
	}

	private function doc_pseo( $platform ) {
		unset( $platform );
		return <<<MD
# Programmatic SEO Platform (Phase 4 — complete)

Generation is live: preview → draft/publish/scheduled materialize → queue-backed
bulk generation → regeneration. Rank Math stays the only SEO engine — the
platform only writes its own postmeta fields (title/description/focus
keyword/robots) when a blueprint supplies them.

```php
kayan_pseo()->patterns->all();
kayan_pseo()->rules->save( \$rule );                          // create/update a generation rule
kayan_pseo()->rules->preview_combinations( \$rule_id );        // real cartesian expansion (Query Engine)
kayan_pseo()->generator->preview( \$pattern, \$entities, \$country, \$lang, \$tokens );
kayan_pseo()->generator->materialize( \$preview, array( 'post_status' => 'draft' ) );
kayan_pseo()->generator->regenerate( \$post_id, array( 'mode' => 'content_only' ) );
kayan_pseo()->bulk_generate( \$rule_id, array( 'post_status' => 'draft' ) ); // enqueues a Queue job
kayan_pseo()->regenerate_bulk( \$post_ids );
kayan_pseo()->scheduler->process_now();                       // manual batch trigger (also automatic)
kayan_pseo()->generator->translate_post( \$post_id, 'en' );     // AI translation (Phase 5), linked via translation_group
kayan_pseo()->generator->regenerate_block( \$post_id, 'hero', array() ); // via the AI Platform bridge — never a vendor call here
```

Prefer existing CPTs (`services`, `faqs`, `pricing`, …); `kayan_pseo` hosts
true multi-entity combination pages. Every write is keyed by a stable
fingerprint — regenerating never changes a post's URL. AI-authored block
content (hero copy, FAQ items) goes through the AI Platform bridge
(`Kayan_PSEO_AI_Bridge_Provider`) — configure a provider in the AI admin
module. `regenerate()` always refreshes derived data (contact info,
related entities, breadcrumb); it also calls the bridge for text/list
blocks when a provider is available. Locked blocks are never touched by
either path.

Every `materialize()` call assigns a Content Workflow state (quality-gated
for publish/scheduled) and records Dependency Graph rows so future source
changes flag only this page for regeneration.

See [MigrationEngine.md](./MigrationEngine.md) for the Queue's storage,
[AIPlatform.md](./AIPlatform.md) for providers, [ContentWorkflow.md](./ContentWorkflow.md),
[QualityEngine.md](./QualityEngine.md), [DependencyGraph.md](./DependencyGraph.md), and
[Countries.md](./Countries.md)/[Languages.md](./Languages.md) for locale integration.
MD;
	}

	private function doc_migration_engine( $platform ) {
		unset( $platform );
		return <<<MD
# Migration & Version Engine

Generic upgrade infrastructure — no manual upgrade step is ever required.
Runs automatically (`after_switch_theme` + a cheap cached-version check on
every `boot()`) and is idempotent, incremental, and logged.

```php
kayan_migrations()->register_migration( 'my_pack_v1', array(
  'version'          => 10,
  'type'             => 'table', // schema|table|option|meta|taxonomy|rewrite
  'description'      => 'Create my_pack table',
  'rollback_options' => array( 'my_pack_option' ), // auto-snapshotted before `up` runs
  'up'               => function( \$engine ) {
    return \$engine->create_or_upgrade_table( 'my_pack_table', 'id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY (id)' );
  },
) );

kayan_migrations()->run();               // safe to call anytime; skips already-applied migrations
kayan_migrations()->rollback( 'my_pack_v1' );
kayan_migrations()->history();           // paginated history table (also visible in System Health)
kayan_migrations()->current_version();
kayan_migrations()->target_version();
```

Hook: `kayan_migrations_register` — other packs (including existing
booking/payment/track, which keep their own working version checks) MAY
register through this engine later; nothing is forced.

Backs the PSEO Queue: the `kayan_pseo_queue` table is created by the
`pseo_queue_table_v1` core migration, not by application code.
MD;
	}

	private function doc_countries( $platform ) {
		return <<<MD
# Countries

```php
kayan_platform()->countries->all();
kayan_platform()->countries->get_default();
kayan_platform()->countries->normalize( \$code );
kayan_settings()->get_country( 'phone', 'ae' );
kayan_platform_setting( 'whatsapp', 'ae' ); // BC helper
```

Country profiles live in Country Settings repository; Settings Engine is the preferred API.

## Admin UI (Phase 3)

The `countries` Admin Platform module edits the business profile
(phone/WhatsApp/currency/SEO/GTM) per existing country — it does not add
or remove countries (those come from kayan-i18n).
MD;
	}

	private function doc_languages( $platform ) {
		return <<<MD
# Languages

```php
kayan_platform()->languages; // Language Engine
kayan_platform_language();
kayan_settings()->get_language( \$key, 'en' );
kayan_settings()->set_language( \$key, \$value, 'ar' );
```

Canonical URLs are language-first: `/`, `/sa/`, `/en/`, `/en/sa/`.

## Admin UI (Phase 3)

The `languages` Admin Platform module can register additional languages
(stored in `kayan_platform_custom_languages`, merged via the existing
`kayan_platform_languages` filter) and toggle languages on/off
(`kayan_platform_disabled_languages`). Arabic cannot be disabled.

```php
Kayan_Admin_Module_Languages::enabled_languages();
```
MD;
	}

	private function doc_rankmath() {
		return <<<MD
# Rank Math

Rank Math is the **only** SEO engine.

KAYAN:

- Does **not** print competing title/meta/canonical/schema/OG/Twitter/sitemap tags
- Extends Rank Math via filters only (SEO Bridge)
- Exposes source values for future use (`{{meta_title}}`, Entity API SEO fields)

Never bypass Rank Math for on-page SEO output.
MD;
	}

	private function doc_developer_guide( $platform ) {
		unset( $platform );
		return <<<MD
# Developer Guide

## Do

- Use `kayan_query()` for posts/terms/users/meta
- Use `kayan_cache()` for caching
- Use `kayan_settings()` for options
- Use `kayan_logger()` for logs
- Use `kayan_entity()` + `kayan_tags()` for entities and tokens
- Use `kayan_theme_option()` / `kayan_integration()` when bridging Theme Options
- Register admin features via `kayan_admin()->modules->register_module()`
- Keep Rank Math as the SEO engine
- Reuse existing packs via adapters — never fork a second implementation
- Register schema/table/option changes via `kayan_migrations()->register_migration()` — never a manual SQL/upgrade step
- Use `kayan_pseo()->generator` for any post created/updated by the platform — never a second write path
- Use `kayan_ai()` for any AI text/translation call — never a vendor SDK/HTTP call outside `includes/ai/`
- Use `kayan_workflow()->transition()` to change a generated page's lifecycle — never `wp_update_post( ['post_status' => …] )` directly on one
- Respect `kayan_pseo_manual_override` post meta — the Generator refuses to overwrite a protected post without `force`

## Do not

- Call `WP_Query` / `get_posts` / `get_post_meta` / `get_terms` / `get_users` from modules
- Call `get_option()` / scatter `set_transient()` in modules
- Create isolated `add_menu_page()` admin screens outside the Admin Platform
- Emit SEO head tags outside Rank Math
- Implement generation/AI/statistics until those phases are approved
- Create Countries / Languages / Templates / AI UIs until those phases are approved

## Theme integration

See [ThemeIntegration.md](./ThemeIntegration.md) and [Compatibility.md](./Compatibility.md).

## Regenerate docs

```php
kayan_platform()->docs->generate();
```
MD;
	}

	private function doc_query( $platform ) {
		$resources = isset( $platform->query ) ? implode( ', ', array_map( static function ( $r ) {
			return '`' . $r . '`';
		}, array_keys( $platform->query->resources() ) ) ) : '';
		return <<<MD
# Query Engine

Centralized data access. Resources: {$resources}

```php
kayan_query()->services( array( 'number' => 10 ) );
kayan_query()->get( 'service', \$slug_or_id );
kayan_query()->meta( \$post_id, 'kayan_public_slug' );
kayan_query()->cities();
kayan_query()->programmatic_pages();
kayan_query()->flush();
```

Cache-ready via Cache Engine group `query`.
MD;
	}

	private function doc_cache( $platform ) {
		return <<<MD
# Cache Engine

Unified API — object cache, transients, future Redis/Memcached drivers.

```php
kayan_cache()->get( \$key, \$group );
kayan_cache()->set( \$key, \$value, \$ttl, \$group );
kayan_cache()->remember( \$key, function() { return \$expensive; }, 300, 'query' );
kayan_cache()->delete( \$key, \$group );
kayan_cache()->flush_group( 'query' );
```

Register custom drivers on `kayan_cache_register_drivers` without changing app code.
MD;
	}

	private function doc_settings( $platform ) {
		return <<<MD
# Settings Engine

Scopes: **global**, **country**, **language**, **module**.

```php
kayan_settings()->get_global( 'feature.enabled' );
kayan_settings()->get_country( 'phone', 'ae' );
kayan_settings()->get_language( 'label', 'en' );
kayan_settings()->get_module( 'pseo', 'batch_size' );
kayan_settings()->set( 'phone', '+971…', array( 'scope' => 'country', 'country' => 'ae' ) );
```

Do not call `get_option()` from application modules.
MD;
	}

	private function doc_logger( $platform ) {
		return <<<MD
# Logger

Channels: `ai`, `generator`, `queue`, `seo`, `errors`, `performance`, `security`, `general`.

```php
kayan_logger()->info( 'seo', 'bridge ready' );
kayan_logger()->error( 'errors', 'resolve failed', array( 'slug' => \$slug ) );
kayan_logger()->ai( 'prompt built', array( 'block' => 'faq' ) );
kayan_logger()->generator( 'preview ok' );
kayan_logger()->queue( 'job enqueued', array( 'id' => \$id ) );
kayan_logger()->performance( 'query slow', array( 'duration_ms' => 120 ) );
kayan_logger()->security( 'capability denied' );
kayan_logger()->time( 'heavy', function() { /* … */ } );
```
MD;
	}

	private function doc_admin_platform( $platform ) {
		$version = isset( $platform->admin ) && method_exists( $platform->admin, 'describe' )
			? (string) ( $platform->admin->describe()['version'] ?? '3.2.0' )
			: '3.2.0';
		return <<<MD
# Admin Platform

**Phase:** 3 (complete) · **Admin version contract:** {$version}

Functional modules: Dashboard, Settings, Countries, Languages, Entities,
Relationships, Permissions, Logs, System Health, Import/Export, Rank Math
Integration (Phase 3); Templates, Blueprints, Blocks, Programmatic SEO,
Queue (Phase 4 — real Generator + DB-backed Scheduler); AI (Phase 5 —
interchangeable provider configuration). Media, Analytics, Performance,
Security remain architecture shells.

Centralized administration shell. Future modules register through one API — no isolated admin pages.

```php
kayan_admin()->describe();
kayan_admin()->modules->register_module( 'my_module', array(
  'label' => 'My Module',
  'capability' => 'kayan_manage_tools',
  'position' => 120,
  'screen' => function( \$module, \$context ) {
    echo kayan_admin()->ui->empty_state( array(
      'title' => \$module['label'],
      'description' => 'Hello',
    ) );
  },
) );
```

Hook: `kayan_admin_register_modules`

Menu slug: `kayan-platform` (+ per-module `kayan-platform-{id}`).
MD;
	}

	private function doc_admin_modules( $platform ) {
		$modules = array();
		if ( isset( $platform->admin->modules ) ) {
			$modules = array_keys( $platform->admin->modules->all() );
		}
		$list = $modules ? implode( ', ', array_map( static function ( $m ) {
			return '`' . $m . '`';
		}, $modules ) ) : '_(register at runtime)_';
		return <<<MD
# Admin Modules

Registered modules:

{$list}

## Functional (Phase 3)

`dashboard` · `settings` · `countries` · `languages` · `entities` · `relationships` · `permissions` · `logs` · `system_health` · `import` (Import/Export) · `rankmath`

## Functional (Phase 4)

`templates` · `blueprints` (+ workflow state, quality score, translate action) · `blocks` · `pseo` (rules, preview, bulk generate) · `queue` (real DB-backed jobs + scheduler)

## Functional (Phase 5)

`ai` (interchangeable provider configuration)

## Architecture shells (later phases)

`media` · `analytics` · `performance` · `security` · `tools` (bridges to Theme Options) · `export` (merged into `import` screen)

## Registration buckets

Each module may declare: `nav`, `widgets`, `settings`, `tables`, `forms`, `cards`, `actions`, `notifications`, `permissions`, `screen`, `save`.

The `save` callable handles POST for that module on `admin_init` (before
headers are sent) — see `Kayan_Admin_Platform::maybe_handle_module_post()`.

```php
kayan_admin()->modules->register_item( 'pseo', 'actions', 'preview', array(
  'label' => 'Preview',
  'callback' => \$fn,
) );
```
MD;
	}

	private function doc_admin_permissions( $platform ) {
		return <<<MD
# Admin Permissions

Access capability: `kayan_access_admin`

## Roles

- Administrator
- SEO Manager (`kayan_seo_manager`)
- Content Manager (`kayan_content_manager`)
- Editor (caps granted to WP `editor`)
- Translator (`kayan_translator`)
- Marketing (`kayan_marketing`)
- Developer (`kayan_developer`)
- Custom roles via `register_role()`

```php
kayan_admin()->permissions->can( 'kayan_manage_pseo' );
kayan_admin()->permissions->register_capability( 'kayan_custom', array( 'label' => 'Custom' ) );
kayan_admin()->permissions->register_role( 'kayan_custom_role', array(
  'label' => 'Custom Role',
  'create_wp_role' => true,
  'capabilities' => array( 'kayan_access_admin', 'kayan_custom' ),
) );
```

The `permissions` Admin Platform module (Phase 3) lists roles/capabilities
and lets users with `promote_users` assign a KAYAN role to a WordPress
user via the standard `WP_User::set_role()` / `add_role()` API — no second
RBAC store.
MD;
	}

	private function doc_admin_ui( $platform ) {
		return <<<MD
# Admin UI Framework

Reusable components (no duplicated markup):

`card` · `table` · `form` · `field` · `tabs` · `panel` · `dialog` · `drawer` · `notice` · `progress` · `status` · `filters` · `bulk_actions` · `search` · `pagination` · `empty_state`

```php
echo kayan_admin()->ui->card( array(
  'title' => 'Hello',
  'content' => '<p>Body</p>',
  'status' => 'ready',
) );

echo kayan_admin()->ui->table( array(
  'columns' => array( 'name' => 'Name' ),
  'rows' => array(),
) );
```
MD;
	}

	private function doc_admin_dashboard( $platform ) {
		$widgets = array();
		if ( isset( $platform->admin->dashboard ) ) {
			$widgets = array_keys( $platform->admin->dashboard->widgets() );
		}
		$list = $widgets ? implode( ', ', array_map( static function ( $w ) {
			return '`' . $w . '`';
		}, $widgets ) ) : '';
		return <<<MD
# Admin Dashboard

Widget slots: {$list}

## Live data (Phase 3)

`countries`, `languages`, `rankmath`, and `logs` widgets render real
counts/status from the Country/Language engines, `Kayan_Theme_Integration`,
and `Kayan_Logger` (see `Kayan_Admin_Dashboard_Stats`).

`pseo`, `queue`, `ai`, `performance`, `analytics` remain placeholders —
those systems are architecture-only until Phases 4–5.

```php
kayan_admin()->dashboard->register_widget( 'custom', array(
  'title' => 'Custom',
  'capability' => 'kayan_access_admin',
  'position' => 110,
  'callback' => function( \$widget ) { return '<p>Live content</p>'; },
) );
```
MD;
	}

	private function doc_theme_integration( $platform ) {
		$adapters = '';
		if ( isset( $platform->integration ) && method_exists( $platform->integration, 'describe' ) ) {
			$desc = $platform->integration->describe();
			if ( ! empty( $desc['adapters'] ) && is_array( $desc['adapters'] ) ) {
				$adapters = implode( ', ', array_map( static function ( $id ) {
					return '`' . $id . '`';
				}, array_keys( $desc['adapters'] ) ) );
			}
		}
		if ( ! $adapters ) {
			$adapters = '`schema`, `rukn_contact`, `booking`, `payment`, `track`, `i18n_switcher`, `legacy_city`, `theme_options`, `admin_bridges`, `cpt`, `query`';
		}
		return <<<MD
# Theme Integration (Phase 3.1)

Enterprise evolution of the **existing** KAYAN Theme — not a new theme.

Adapters connect packs built before the platform without replacing them.

## Facade

```php
kayan_integration();
kayan_integration()->describe();
kayan_theme_option( 'phonenumber' );
Kayan_Theme_Integration::profile_field( 'phone' );
Kayan_Theme_Integration::rank_math_active();
```

## Adapters

{$adapters}

## Rules

- Zero breaking changes for existing sites
- Prefer wrap / extend / filter over rewrite
- Admin option bridges skip `is_admin()` (non-AJAX) so Theme Options editors see stored values
- Country profile reads inside option filters use `profile_field()` (raw option) to avoid recursion with Country Settings dual-read

## Compatibility report

```php
kayan_integration()->report->generate();
kayan_integration()->report->to_markdown();
kayan_integration()->report->write_files();
```

See [Compatibility.md](./Compatibility.md).
MD;
	}

	private function doc_ai_platform( $platform ) {
		$providers = isset( $platform->ai ) ? array_keys( $platform->ai->providers() ) : array();
		$list = implode( ', ', array_map( static function ( $p ) { return '`' . $p . '`'; }, $providers ) );
		return <<<MD
# AI Platform (Phase 5)

Interchangeable AI provider registry — application code (PSEO block
regeneration, translation) only ever calls `kayan_ai()`, never a concrete
vendor class. Swapping providers requires zero code changes.

Registered providers: {$list}

```php
kayan_ai()->complete( array( 'prompt' => 'Write a headline for {service} in {city}.' ) );
kayan_ai()->translate( \$text, 'ar', 'en' );
kayan_ai()->default_provider_id();
kayan_ai()->is_any_available();
kayan_ai()->register_provider( \$my_provider ); // implements Kayan_AI_Provider_Interface
```

API keys/model live in the existing Settings Engine (module scope
`ai_{provider_id}`) — configured via the AI admin module. No new options
table.

## PSEO bridge

`Kayan_PSEO_AI` (the existing block/blueprint-shaped contract from Phase
2.5) now defaults to `Kayan_PSEO_AI_Bridge_Provider`, which translates
block regeneration requests into `kayan_ai()->complete()` calls and maps
the response back into each block's data shape. Locked blocks are never
sent for regeneration.
MD;
	}

	private function doc_content_workflow( $platform ) {
		unset( $platform );
		return <<<MD
# Content Workflow (Phase 5)

Every PSEO-generated page has an explicit lifecycle state, stored in
`kayan_workflow_state` post meta:

`draft` → `ai_draft` → `human_review` → `approved` → `scheduled` / `published`
plus `needs_update`, `needs_regeneration`, `archived`, `failed`.

```php
kayan_workflow()->get_state( \$post_id );
kayan_workflow()->transition( \$post_id, Kayan_Content_Workflow::PUBLISHED );
kayan_workflow()->history( \$post_id );
kayan_workflow()->transition_map();
```

Publishing/scheduling is gated by the Quality Engine unless
`array( 'force' => true )` is passed. A failed gate routes the page to
`human_review` instead of silently failing. Transitions sync the
underlying WordPress `post_status` — a single source of truth (the
Generator never sets `post_status` directly for anything but the initial
safe `draft` write).
MD;
	}

	private function doc_quality_engine( $platform ) {
		unset( $platform );
		return <<<MD
# Quality Engine (Phase 5)

Validates a generated page before it is allowed to publish. Checks:
content length, heading structure, duplicate detection, internal/external
links, image ALT coverage, schema source completeness, dynamic tag
resolution, country/language consistency, blueprint completeness, broken
relationships, missing entities, missing CTA/FAQ/Reviews/Pricing (only
when the assigned template requires them), and SEO completeness.

```php
kayan_quality()->validate( \$post_id );
// => array( 'ok' => bool, 'score' => 0..1, 'checks' => array(...), 'blockers' => array(...) )
```

Posts without a PSEO blueprint always pass (`not_applicable`) — this never
affects existing manually-authored content.
MD;
	}

	private function doc_dependency_graph( $platform ) {
		unset( $platform );
		return <<<MD
# Dependency Graph (Phase 5)

Tracks which generated pages depend on which source entities (service,
city, faq, pricing, portfolio, review, article, …) via the
`kayan_pseo_dependencies` table (Migration Engine). When a source post is
saved or a source term is edited, only the pages that actually depend on
it are flagged `needs_regeneration` — never a full-site sweep.

```php
kayan_dependencies()->record( \$post_id, \$entities );      // written by materialize()
kayan_dependencies()->find_affected( 'service', 'plumbing' );
kayan_dependencies()->mark_affected( 'service', 'plumbing' );
```

Hooked automatically on `save_post` and `edited_term` for every post
type/taxonomy already registered in the Programmatic SEO entity registry —
no per-pack wiring required.
MD;
	}

	private function doc_compatibility( $platform ) {
		if ( isset( $platform->integration->report ) && method_exists( $platform->integration->report, 'to_markdown' ) ) {
			return $platform->integration->report->to_markdown();
		}
		return <<<MD
# Compatibility

Generate at runtime:

```php
echo kayan_integration()->report->to_markdown();
```

Or write files:

```php
kayan_integration()->report->write_files();
```

Static copy also ships as `PHASE3.1-COMPATIBILITY-REPORT.md` in the platform pack.
MD;
	}
}
