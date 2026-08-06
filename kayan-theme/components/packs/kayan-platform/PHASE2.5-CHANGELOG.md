# KAYAN Platform — Phase 2.5 Changelog

**Theme version:** 1.7.0  
**Platform version:** 2.5.0  
**Scope:** Native Programmatic SEO Engine **architecture + APIs only**.

## Explicitly NOT implemented

- Page generation / materialization
- Content / AI generation
- Admin UI redesign
- Bulk workers / cron runners that create posts

## Added — `includes/pseo/`

| File | Role |
|------|------|
| `class-kayan-pseo-engine.php` | Facade + multi-segment pattern rewrite readiness |
| `class-kayan-pseo-patterns.php` | Combination patterns (Service×City, FAQ×Service, …) |
| `class-kayan-pseo-rules.php` | Generation rules storage/validation/preview API |
| `class-kayan-pseo-identity.php` | Fingerprints + stable URL identity |
| `class-kayan-pseo-blueprint.php` | Per-page slots (hero, CTA, FAQ, schema source, Rank Math fields, …) |
| `class-kayan-pseo-storage.php` | `kayan_pseo` page-like CPT + meta (menu hidden) |
| `class-kayan-pseo-jobs.php` | Draft / schedule / bulk / regenerate job queue API |
| `class-kayan-pseo-ai.php` | AI provider interface + null provider |
| `class-kayan-pseo-generator.php` | Dry-run `preview()`; `materialize()` disabled |
| `PHASE2.5-CHANGELOG.md` | This file |

## Modified

| File | Why |
|------|-----|
| `setup.php` | Load PSEO modules; platform `2.5.0` |
| `includes/class-kayan-platform.php` | Wire `$pseo` engine |
| `includes/class-kayan-programmatic-seo.php` | Entities: neighborhood, brand, building |
| `includes/class-kayan-content-resolver.php` | Resolve `kayan_pseo` + multi-segment public slugs |
| `includes/class-kayan-country-router.php` | Rewrite version `2.5.0` |
| `includes/helpers.php` | `kayan_pseo()` helper |
| `style.css` / `readme.txt` | Version `1.7.0` |

## Public APIs

```php
kayan_pseo()->describe();
kayan_pseo()->patterns->all();
kayan_pseo()->rules->save( $rule );
kayan_pseo()->rules->preview_combinations( $rule_id );
kayan_pseo()->generator->preview( $pattern, $entities, $country, $lang, $tokens );
kayan_pseo()->jobs->enqueue( 'bulk', array( 'rule_id' => '…' ) );
kayan_pseo()->identity->fingerprint( $pattern, $entities, $country, $lang );
kayan_pseo()->generator->materialize( $preview ); // returns not_implemented
```

## Storage contract

- CPT: `kayan_pseo` (page capabilities, revisions, REST, Rank Math ready)
- `show_in_menu = false` (no admin redesign)
- Locale meta via existing Content Locale (translations + country variants)
- Fingerprint meta prevents URL breakage on regenerate
