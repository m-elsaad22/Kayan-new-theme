# KAYAN Platform — Phase 1 Changelog (Core Architecture)

**Theme version:** 1.5.0  
**Platform version:** 1.0.0  
**Scope:** Core architecture only — no frontend redesign, no URL changes, no data migration.

## Added

### Pack: `components/packs/kayan-platform/`

| File | Purpose |
|------|---------|
| `setup.php` | Bootstrap, constants, boot hook |
| `includes/class-kayan-platform.php` | Service container / facade |
| `includes/class-kayan-country-engine.php` | Extensible country registry (reuses `kayan_i18n_get_countries`) |
| `includes/class-kayan-language-engine.php` | Extensible language registry (ar default, en built-in) |
| `includes/class-kayan-context.php` | Per-request country + language context |
| `includes/class-kayan-country-settings.php` | Per-country profile repository + legacy dual-read |
| `includes/class-kayan-content-locale.php` | Content locale meta contracts (`register_post_meta`) |
| `includes/class-kayan-url.php` | URL helper (active=legacy; target language-first documented) |
| `includes/class-kayan-seo-bridge.php` | Rank Math–compatible SEO bridge + safe hreflang |
| `includes/helpers.php` | `kayan_platform_*` procedural helpers |
| `PHASE1-CHANGELOG.md` | This file |

### Content meta (registered, unused by queries yet)

- `kayan_content_lang`
- `kayan_content_countries`
- `kayan_translation_group`
- `kayan_variant_group`
- `kayan_public_slug`
- `kayan_content_visibility`
- `kayan_locale_seo`
- Term meta `cities` → `kayan_country`

### Country profile option keys

- `kayan_country_profile_{code}` (ae, sa, qa, kw, bh, om, eg, …)

## Modified

| File | Why |
|------|-----|
| `style.css` | Version 1.5.0 + platform description note |
| `readme.txt` | Stable tag 1.5.0 + changelog entry |

## Explicitly NOT changed

- Rewrite rules / permalinks
- `kayan-i18n` rewrite registration
- Templates, widgets, CSS, JS UI
- Database tables / migrations
- Theme Options field IDs
- CPT / taxonomy registration
- Rank Math ownership of title/canonical/OG/Twitter/sitemaps

## SEO impact (Phase 1)

- Adds safe `hreflang` + `x-default` for home and blog posts only
- Avoids emitting CPT/country alternates that would 404 today
- Rank Math remains primary for title/canonical/OG/Twitter
- Country GTM/GSC tags output only when profile values are set (default empty = no HTML change)

## Scalability impact

- Unlimited countries via filters (`kayan_i18n_countries` / `kayan_platform_countries`)
- Unlimited languages via `kayan_platform_languages`
- Per-country settings schema ready without Multisite
- Content locale contracts ready for shared + variant + translation models
- Module is extractable to a plugin later without rewriting domain logic

## Backward compatibility

- Legacy posts with no locale meta remain globally visible / Arabic
- Country settings dual-read existing Theme Options (`phonenumber`, `whatsapp_number`, …)
- Active URLs still built by `kayan_i18n_build_url` (`/{country}/en/…`)
- Target `/en/{country}/…` architecture is documented but hard-guarded off
