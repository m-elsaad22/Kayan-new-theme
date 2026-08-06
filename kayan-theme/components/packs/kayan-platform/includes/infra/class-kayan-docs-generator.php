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
4. **Dynamic Data Tags** — `{{tag}}` tokens for templates/AI
5. **Programmatic SEO** — patterns, templates, blocks, blueprints (no generation yet)
6. **Core Infrastructure** — Query, Cache, Settings, Logger
7. **SEO Bridge** — Rank Math only (filters, never competing head tags)

## Design rules

- Prefer existing WP content types; `kayan_pseo` is fallback only
- Modules must use Query / Cache / Settings / Logger / Entity APIs
- Do not call `WP_Query`, `get_posts`, `get_option`, or scatter transients in app code
- Rank Math remains the only SEO engine

## Boot order

Content Locale → Programmatic entities → Entity Engine → Data Tags → Cache → Settings Engine → Logger → Query → PSEO → Router → Resolver → SEO Bridge
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

Patterns reference `template_id`. No frontend template redesign in this phase.
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
MD;
	}

	private function doc_pseo( $platform ) {
		return <<<MD
# Programmatic SEO

Architecture + APIs only — generation disabled.

```php
kayan_pseo()->patterns->all();
kayan_pseo()->generator->preview( \$pattern, \$entities, \$country, \$lang, \$tokens );
kayan_pseo()->generator->materialize( \$preview ); // disabled
kayan_pseo()->generator->regenerate_block( \$post_id, 'faq', \$args ); // stub
```

Prefer existing CPTs (`services`, `faqs`, `pricing`, …); `kayan_pseo` is fallback.
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
		return <<<MD
# Developer Guide

## Do

- Use `kayan_query()` for posts/terms/users/meta
- Use `kayan_cache()` for caching
- Use `kayan_settings()` for options
- Use `kayan_logger()` for logs
- Use `kayan_entity()` + `kayan_tags()` for entities and tokens
- Keep Rank Math as the SEO engine

## Do not

- Call `WP_Query` / `get_posts` / `get_post_meta` / `get_terms` / `get_users` from modules
- Call `get_option()` / scatter `set_transient()` in modules
- Emit SEO head tags outside Rank Math
- Implement generation/AI/admin UI until those phases are approved

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
}
