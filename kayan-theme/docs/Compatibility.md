# KAYAN Theme ↔ Platform Compatibility Report

**Phase:** 3.1 — Existing Theme Integration
**Theme version:** `2.1.0`
**Platform version:** `6.0.0`
**Generated:** `2026-08-07T01:34:39+00:00`

This report maps every major existing KAYAN Theme system to the platform.
Adapters wrap/extend existing packs — they do not replace them.

## Status legend

| Status | Meaning |
|--------|---------|
| Already compatible | Works with platform as-is |
| Needs Adapter | Connected via adapter in Phase 3.1 |
| Needs Extension | Light filter/hook extension applied |
| Needs Refactoring | Deeper change deferred; documented risk |
| Deprecated | Kept for BC; prefer replacement |
| Can be removed later | Safe to remove after migration window |

## Systems

### Theme Options (YTS / FieldsMachine)

| Field | Value |
|-------|-------|
| Status | Needs Adapter |
| Risk | Low |
| Adapter | `theme_options` |
| Path | `components/packs/FieldsMachine/` |

Existing Theme Options remain the write UI. Frontend `get_option` for contact keys prefers country profile values via option bridges.

**Migration strategy:** Keep writing via YTS. Populate country profiles gradually; empty profile fields continue to fall back to Theme Options (Country Settings dual-read).

Admin screens skip bridges so editors still see stored option values.

### Custom Post Types (kayan-cpt)

| Field | Value |
|-------|-------|
| Status | Needs Extension |
| Risk | Low |
| Adapter | `cpt` |
| Path | `components/packs/kayan-cpt/` |

Canonical CPT pack reused. Content Locale post types + rewrite map extended to include services, reviews, faqs, pricing, portfolio, before_after.

**Migration strategy:** None — continue using kayan-cpt. Do not revive post-types stub pack.

### Taxonomies — cities (canonical)

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | `legacy_city` |
| Path | `components/packs/kayan-cpt/setup.php` |

`cities` taxonomy is canonical for Query Engine, Entity Engine, and Country Router rewrite map.

**Migration strategy:** Use `cities` + term meta `kayan_country`.

### Taxonomies — legacy city

| Field | Value |
|-------|-------|
| Status | Deprecated |
| Risk | Medium |
| Adapter | `legacy_city` |
| Path | `components/packs/taxonomies/setup.php` |

Legacy non-hierarchical `city` taxonomy may clash with `cities` rewrite slug. Empty legacy taxonomy is unregistered; non-empty kept for BC.

**Migration strategy:** If terms exist, plan a later term migration to `cities` then remove taxonomies pack registration. Can be removed later when empty.

Status may become “Can be removed later” once term count is zero.

### Widgets (YourColorWidgets)

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | — |
| Path | `components/packs/YourColorWidgets/` |

Widget machine unchanged. Contact widgets benefit from Theme Options phone bridges on the frontend.

**Migration strategy:** Reuse as-is. Prefer `Kayan_Theme_Integration::theme_option()` in new widget code.

### Blocks (PSEO content blocks)

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | — |
| Path | `components/packs/kayan-platform/includes/pseo/` |

PSEO blocks registry already platform-native. Not Gutenberg blocks.

**Migration strategy:** Continue registering via `kayan_pseo_register_blocks`.

No Blocks UI in Phase 3.1.

### Templates (theme + PSEO)

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | — |
| Path | `components/packs/@* + pseo/templates` |

Existing theme templates continue to load. PSEO templates engine remains architecture-only.

**Migration strategy:** No template migration. No Templates UI.

### Shortcodes

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | — |
| Path | `components/packs/shortcodes/` |

Existing shortcodes unchanged; contact-related output inherits option bridges.

**Migration strategy:** Reuse tags as-is.

### SEO (theme-seo + Rank Math)

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | `admin_bridges` |
| Path | `components/packs/theme-seo/ + SEO Bridge` |

SEO Bridge already extends Rank Math only. theme-seo defers title when Rank Math present.

**Migration strategy:** Keep Rank Math as sole SEO engine. Admin Platform rankmath module bridges to Rank Math admin.

Rank Math not detected in this runtime — install/activate on production sites.

### Schema (YourColor__Schema)

| Field | Value |
|-------|-------|
| Status | Needs Adapter |
| Risk | Medium |
| Adapter | `schema` |
| Path | `components/packs/schema/` |

Theme JSON-LD can compete with Rank Math. Adapter forces existing `validate__schema` kill switch when Rank Math is active.

**Migration strategy:** Leave pack in place. Rely on kill switch + Rank Math schema. Optional later: remove pack after all sites on Rank Math.

Known typo risk: LocalBusiness may read `YourColoe_Schema_business`.

### Breadcrumb()

| Field | Value |
|-------|-------|
| Status | Needs Refactoring |
| Risk | Medium |
| Adapter | — |
| Path | `components/packs/Breadcrumb/` |

Emits BreadcrumbList JSON-LD + HTML; limited taxonomy coverage (no `cities`); home URL not language-first.

**Migration strategy:** Deferred — avoid template edits in 3.1. Prefer Rank Math breadcrumbs long-term; wrap Breadcrumb() in a later phase if needed.

Documented risk only; no behavior change in Phase 3.1 to preserve exact frontend markup.

### Booking (kayan-booking)

| Field | Value |
|-------|-------|
| Status | Needs Adapter |
| Risk | Low |
| Adapter | `booking` |
| Path | `components/packs/kayan-booking/` |

Booking pack remains the booking system. Currency prefers country profile; WhatsApp confirm uses Theme Options bridge.

**Migration strategy:** Continue using kayan-bookings admin. Set currency on country profile when multi-country.

### Payments (kayan-payment)

| Field | Value |
|-------|-------|
| Status | Needs Adapter |
| Risk | Low |
| Adapter | `payment` |
| Path | `components/packs/kayan-payment/` |

Invoice company name prefers country `business_name`. Payment methods unchanged.

**Migration strategy:** Reuse payment pack; no second gateway layer.

### Tracking / DNI (kayan-track)

| Field | Value |
|-------|-------|
| Status | Needs Adapter |
| Risk | Low |
| Adapter | `track` |
| Path | `components/packs/kayan-track/` |

DNI default phone used `contact_number` (often empty). Bridged to country phone / `phonenumber`.

**Migration strategy:** Keep KAYAN Track admin. Optionally set `contact_number` or rely on bridge.

### Contact System (RuknContact)

| Field | Value |
|-------|-------|
| Status | Needs Extension |
| Risk | Low |
| Adapter | `rukn_contact` |
| Path | `components/packs/RuknContact/` |

Extends `rukn_cs_post_types` with kayan-cpt types. Global numbers via Theme Options bridges.

**Migration strategy:** Reuse RuknContact resolve API. No second contact system.

### Cities / Countries / Languages engines

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | — |
| Path | `components/packs/kayan-platform/ + kayan-i18n/` |

Platform Country/Language engines + kayan-i18n routing ownership already integrated. Phase 3 added real Countries/Languages Admin Platform modules editing the platform profile layer (not the i18n registry itself).

**Migration strategy:** Manage per-country business profile and custom languages via the Admin Platform. Theme Options (YTS) remains the source for raw contact fields when a profile is empty.

### Theme Settings / Country Profiles

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | `theme_options` |
| Path | `class-kayan-country-settings.php + Settings Engine` |

Country Settings already dual-read Theme Options into empty profile fields. Phase 3.1 adds reverse frontend bridges.

**Migration strategy:** Use `kayan_settings()` / `kayan_platform_setting()` in new code.

### Rewrite Rules

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | `cpt` |
| Path | `Country Router + kayan-i18n + AjaxCenter + kayan-cpt` |

When `kayan_platform_owns_routing()` is true, i18n skips duplicate rewrites. CPT rewrite map extended.

**Migration strategy:** Flush permalinks after deploy if rewrite maps change. Do not register competing rules.

### Admin Pages (existing)

| Field | Value |
|-------|-------|
| Status | Needs Adapter |
| Risk | Low |
| Adapter | `admin_bridges` |
| Path | `YTS, kayan-bookings, kayan-track-pro, Rank Math` |

Existing menus remain. Analytics/Tools modules still link to KAYAN Track / Theme Options. Countries, Languages, and Rank Math Integration are now real Admin Platform screens (Phase 3) that edit platform-owned data alongside the existing menus.

**Migration strategy:** Operators keep using existing menus for booking/tracking/theme-wide options; use the Admin Platform for country profiles, languages, and platform settings.

### Menus (WP + header switcher)

| Field | Value |
|-------|-------|
| Status | Needs Adapter |
| Risk | Low |
| Adapter | `i18n_switcher` |
| Path | `#header/part.php + kayan-i18n/switcher.php` |

`rukn_v3_lang_switcher` action had no listener. Wired to `kayan_i18n_render_switcher()`.

**Migration strategy:** None — existing switcher renderer reused.

### Existing Helpers / APIs

| Field | Value |
|-------|-------|
| Status | Needs Extension |
| Risk | Low |
| Adapter | — |
| Path | `functions.php + kayan-platform/includes/helpers.php` |

Added `kayan_integration()` / `kayan_theme_option()` helpers. Existing `yc_get_option`, `kayan_platform_*` unchanged.

**Migration strategy:** Prefer platform helpers for new modules; keep `yc_get_option` for legacy packs.

### Query / Cache / Settings / Logger

| Field | Value |
|-------|-------|
| Status | Needs Extension |
| Risk | Low |
| Adapter | `query` |
| Path | `includes/infra/` |

Query Engine resources ensured for kayan-cpt; optional `legacy_city` resource when taxonomy remains.

**Migration strategy:** New code must use `kayan_query()` / `kayan_cache()` / `kayan_settings()` / `kayan_logger()`.

### Entity Relationship Engine + Dynamic Tags

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | — |
| Path | `includes/entities/` |

Already maps city→country via term meta. No duplicate relationship layer.

**Migration strategy:** Use `kayan_entity()` / `kayan_tags()`.

### Programmatic SEO Platform (complete)

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | — |
| Path | `includes/pseo/` |

Phase 4 completed generation (Generator/Queue/Scheduler/Renderer). Prefers existing CPTs (services/faqs/pricing); only true multi-entity combinations use the kayan_pseo host type. Every write is fingerprint-keyed — never changes an existing post's URL.

**Migration strategy:** No action required. Bulk-generate only via reviewed Rules; always Preview before Bulk Generate in production.

Rank Math remains the only SEO engine — the Generator only writes RM's own postmeta fields.

### Admin Platform Core (complete)

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | `admin_bridges` |
| Path | `includes/admin/` |

Phases 3–5 completed Dashboard, Navigation, Settings, Countries, Languages, Entities, Relationships, Permissions, Logs, System Health, Import/Export, Rank Math Integration, Templates, Blueprints, Blocks, Programmatic SEO, Queue, and AI. Media/Analytics/Performance/Security remain placeholder shells (out of the approved roadmap).

**Migration strategy:** Register future UIs only through `kayan_admin()` when approved.

### Migration & Version Engine

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | — |
| Path | `includes/migration/` |

New in Phase 4. Additive only — existing booking/payment/track ad-hoc version checks are untouched and continue to work exactly as before.

**Migration strategy:** No action required. Other packs may adopt `kayan_migrations_register` later; nothing is forced.

### AI Platform / Content Workflow / Quality Engine / Dependency Graph

| Field | Value |
|-------|-------|
| Status | Already compatible |
| Risk | Low |
| Adapter | — |
| Path | `includes/ai/, includes/workflow/, includes/quality/, includes/dependency/` |

New in Phase 5. Only affect posts carrying a PSEO fingerprint/blueprint (i.e. platform-generated pages). Manually authored content, and every pre-existing pack, is completely unaffected — verified by automated tests confirming a manually created post has no workflow/quality/dependency footprint.

**Migration strategy:** Configure an AI provider (optional) at KAYAN → AI to enable block regeneration/translation. Safe to leave unconfigured.

## Adapter inventory

- `schema` — **compatible_idle** — Theme schema silenced when Rank Math active; pack code unchanged.
- `rukn_contact` — **idle** — Extends rukn_cs_post_types; contact numbers via Theme Options adapter.
- `booking` — **idle** — No second booking stack. WhatsApp via theme_options adapter.
- `payment` — **idle** — Payment pack unchanged; company name prefers country profile.
- `track` — **idle** — Fixes DNI key mismatch without changing track pack storage.
- `i18n_switcher` — **idle** — No new switcher UI — connects existing header action to existing i18n renderer.
- `legacy_city` — **compatible** — Canonical cities taxonomy from kayan-cpt. Empty legacy city unregistered; non-empty kept for BC.
- `theme_options` — **adapter** — Frontend dual-read from country profiles; Theme Options remains write source.
- `admin_bridges` — **adapter** — Links remaining placeholder modules (analytics, tools) to existing Track / Theme Options screens. Countries, Languages, and Rank Math are now real Admin Platform modules (Phase 3).
- `cpt` — **extension** — Reuses kayan-cpt; extends locale post types + rewrite map only.
- `query` — **extension** — Ensures Query Engine resources for kayan-cpt; legacy_city resource when taxonomy remains.

## Explicit non-goals (Phase 3.1)

- No new Countries / Languages / Templates / AI / Dashboard Widgets UI
- No second Theme Options, booking, payment, tracking, or SEO stack
- No data migration of legacy `city` terms
- Zero breaking changes for existing sites
