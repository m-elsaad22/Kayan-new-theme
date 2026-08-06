# KAYAN Platform — Phase 2.7 Changelog

**Theme version:** 1.9.0  
**Platform version:** 2.7.0  
**Scope:** Core infrastructure engines + developer documentation. **No** generation, admin UI, AI, or template/frontend redesign.

## Explicitly NOT implemented

- Admin Platform (Phase 3)
- Page / content / AI generation
- Frontend redesign
- Admin UI redesign
- Template modifications

## Added — Core Infrastructure (`includes/infra/`)

| File | Engine |
|------|--------|
| `class-kayan-query-engine.php` | **Query Engine** — centralized data access |
| `class-kayan-cache-engine.php` | **Cache Engine** — object / transient / Redis·Memcached stubs |
| `class-kayan-settings-engine.php` | **Settings Engine** — global · country · language · module |
| `class-kayan-logger.php` | **Logger** — ai, generator, queue, seo, errors, performance, security |
| `class-kayan-docs-generator.php` | Regenerates `/docs` markdown from live contracts |

## Query Engine

Replaces direct use of `WP_Query`, `get_posts()`, `get_post()`, `get_post_meta()`, `get_terms()`, `get_users()` in future modules.

Resources: service, article, city, country, area, district, faq, review, portfolio, pricing, programmatic_page, category, user, page, before_after (+ extensible).

```php
kayan_query()->services( array( 'number' => 10 ) );
kayan_query()->get( 'service', $ref );
kayan_query()->meta( $post_id, $key );
kayan_query()->cities();
kayan_query()->programmatic_pages();
kayan_query()->flush();
```

Cache-ready via Cache Engine group `query`.

## Cache Engine

Unified API — do not scatter transients.

Drivers: `object`, `transient`, future `redis` / `memcached` (stubs registerable without app code changes).

```php
kayan_cache()->get( $key, $group );
kayan_cache()->set( $key, $value, $ttl, $group );
kayan_cache()->remember( $key, $callback, $ttl, $group );
kayan_cache()->flush_group( 'query' );
```

## Settings Engine

Future code should not call `get_option()` directly.

Scopes: **global**, **country** (wraps Country Settings BC), **language**, **module**.

```php
kayan_settings()->get_global( 'feature.enabled' );
kayan_settings()->get_country( 'phone', 'ae' );
kayan_settings()->get_language( 'label', 'en' );
kayan_settings()->get_module( 'pseo', 'batch_size' );
```

`kayan_platform_setting()` now delegates to Settings Engine (BC preserved).

## Logger

Channels: `ai`, `generator`, `queue`, `seo`, `errors`, `performance`, `security`, `general`.

```php
kayan_logger()->info( 'seo', 'bridge ready' );
kayan_logger()->error( 'errors', 'failed', $ctx );
kayan_logger()->ai( $message );
kayan_logger()->generator( $message );
kayan_logger()->queue( $message );
kayan_logger()->performance( $message );
kayan_logger()->security( $message );
kayan_logger()->time( 'label', $callback );
```

## Developer Documentation

Directory: `kayan-theme/docs/`

| File |
|------|
| Architecture.md |
| API.md |
| Entities.md |
| Relationships.md |
| DynamicTags.md |
| Templates.md |
| Blueprints.md |
| ProgrammaticSEO.md |
| Countries.md |
| Languages.md |
| RankMath.md |
| DeveloperGuide.md |
| QueryEngine.md |
| CacheEngine.md |
| SettingsEngine.md |
| Logger.md |
| README.md |

Regenerate:

```php
kayan_platform()->docs->generate();
```

## Modified

| File | Why |
|------|-----|
| `setup.php` | Load infra; platform `2.7.0` |
| `includes/class-kayan-platform.php` | Wire cache, settings_engine, logger, query, docs |
| `includes/helpers.php` | `kayan_query()`, `kayan_cache()`, `kayan_settings()`, `kayan_logger()` |
| `includes/class-kayan-country-router.php` | Rewrite version `2.7.0` |
| `style.css` / `readme.txt` | Theme `1.9.0` |

## Rank Math

Unchanged: Rank Math remains the only SEO engine. Infrastructure does not emit SEO head tags.

## Compatibility

Phases 1–2.6 APIs preserved. Country Settings repository kept as `$platform->settings` for BC; new code uses `$platform->settings_engine` / `kayan_settings()`.
