# KAYAN Platform — Phase 3.1 Changelog

**Theme version:** 2.0.1  
**Platform version:** 3.1.0  
**Scope:** Existing Theme Integration — connect packs built so far to the platform via adapters.

## Explicitly NOT implemented

- New Countries / Languages / Templates / AI / Dashboard Widgets UI
- New Theme Options / settings pages
- Data migration of legacy `city` terms
- Breadcrumb refactor / template edits
- Programmatic page generation
- Second booking, payment, tracking, contact, or SEO stacks
- Frontend redesign

## Goal

Enterprise evolution of the **existing** KAYAN Theme (not a new theme).  
Reuse · extend · wrap. Zero breaking changes for sites already using the theme.

## Added — Theme Integration (`includes/integration/`)

| File | Role |
|------|------|
| `class-kayan-theme-integration.php` | Integration facade + `theme_option()` / `profile_field()` |
| `class-kayan-compatibility-report.php` | Programmatic + markdown compatibility report |
| `adapters/class-kayan-adapter-theme-options.php` | Frontend Theme Options ↔ country profile bridges |
| `adapters/class-kayan-adapter-schema.php` | Silence theme schema when Rank Math active |
| `adapters/class-kayan-adapter-rukn-contact.php` | Extend RuknContact post types |
| `adapters/class-kayan-adapter-booking.php` | Country currency → booking |
| `adapters/class-kayan-adapter-payment.php` | Country business name → invoices |
| `adapters/class-kayan-adapter-track.php` | `contact_number` → phone / phonenumber |
| `adapters/class-kayan-adapter-i18n-switcher.php` | Wire `rukn_v3_lang_switcher` → i18n switcher |
| `adapters/class-kayan-adapter-legacy-city.php` | Prefer `cities`; unregister empty legacy `city` |
| `adapters/class-kayan-adapter-admin-bridges.php` | Admin shells → existing YTS / Track / Rank Math / Bookings |
| `adapters/class-kayan-adapter-cpt.php` | Locale types + rewrite map for kayan-cpt |
| `adapters/class-kayan-adapter-query.php` | Ensure Query Engine CPT / city resources |
| `PHASE3.1-COMPATIBILITY-REPORT.md` | Full system-by-system report |
| `PHASE3.1-CHANGELOG.md` | This file |

## Helpers

```php
kayan_integration();
kayan_theme_option( 'phonenumber' );
kayan_integration()->report->generate();
```

## Boot order change

Admin Platform → **Theme Integration** → `kayan_platform_booted`

## Compatibility highlights

| System | Status |
|--------|--------|
| Theme Options (YTS) | Adapter (frontend dual-read) |
| kayan-cpt | Extension |
| Legacy `city` taxonomy | Deprecated (unregister if empty) |
| Schema pack | Adapter (RM kill switch) |
| RuknContact | Extension |
| Booking / Payment / Track | Adapter |
| i18n header switcher | Adapter |
| SEO Bridge / Rank Math | Already compatible |
| Breadcrumb() | Needs refactoring (deferred) |
| Widgets / Shortcodes / Templates | Already compatible |

## Docs

- `docs/ThemeIntegration.md`
- `docs/Compatibility.md`
- Updated Architecture, API, Developer Guide

## Upgrade notes

1. Deploy theme 2.0.1 / platform 3.1.0.
2. Existing sites keep working with no content or option changes.
3. Optional: flush permalinks if rewrite maps were customized.
4. When Rank Math is active, theme JSON-LD is disabled via existing `validate__schema` switch.
5. Populate country profile phone/currency when ready — empty fields still fall back to Theme Options.
