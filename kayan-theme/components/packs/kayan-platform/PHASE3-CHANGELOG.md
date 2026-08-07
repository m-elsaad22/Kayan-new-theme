# KAYAN Platform — Phase 3 Changelog (Complete Admin Platform)

**Theme version:** 2.1.0
**Platform version:** 3.2.0
**Scope:** Complete the Admin Platform in one milestone — Dashboard, Navigation,
Settings Framework, Existing Theme Integration, Countries, Languages,
Entities, Relationships, Permissions, Logs, System Health, Import/Export,
Rank Math Integration.

This changelog documents the full Phase 3 milestone. Earlier sub-phase
changelogs remain for history: `PHASE3.0-CHANGELOG.md` (Admin Platform Core
shells) and `PHASE3.1-CHANGELOG.md` (Existing Theme Integration adapters).

## Explicitly NOT implemented (later phases)

- Templates / Blueprints / Blocks / Programmatic SEO feature UIs (Phase 4)
- AI Platform (all providers, prompt engine, translation, content enhancement) (Phase 5)
- Queue / Scheduler / bulk generation (Phase 4)
- Analytics / Performance / Security dashboards (deferred — not in Phase 3 list)
- Countries/Languages *registries* themselves (still owned by kayan-i18n) — only the platform profile/label layer is edited here
- Full project-wide architecture/performance/security/SEO audit (deferred to after Phase 6, per updated execution strategy)

## New: Generic module save/notice dispatch

- `Kayan_Admin_Module_Registry`: modules may declare a `save` callable
- `Kayan_Admin_Platform::maybe_handle_module_post()` — runs on `admin_init`,
  before headers are sent, dispatching POST to the current module's `save`
  handler (each module still verifies its own nonce)
- `Kayan_Admin_Platform::redirect_module()` / `status_message()` — shared
  redirect-with-status-flag pattern; the shell renders the resulting notice

## New: Feature modules (`includes/admin/modules/`)

| Module id | File | What it does |
|-----------|------|---------------|
| `settings` | `class-kayan-admin-module-settings.php` | Platform-level settings only (routing mode, cache TTL, logger tuning) — does not duplicate Theme Options |
| `countries` | `class-kayan-admin-module-countries.php` | Edits per-country business profile (phone/WhatsApp/email/business/currency/SEO/GTM) via existing Country Settings repository |
| `languages` | `class-kayan-admin-module-languages.php` | Register/remove custom languages, enable/disable — merged via existing `kayan_platform_languages` filter, no second registry |
| `entities` | `class-kayan-admin-module-entities.php` | Read-only entity type inspector + single-entity resolver over the existing Entity API |
| `relationships` | `class-kayan-admin-module-relationships.php` | Allowed relationship matrix + related-entity browser over the existing Entity Relationship Engine |
| `permissions` | `class-kayan-admin-module-permissions.php` | Roles/capabilities view + assigns KAYAN roles to WordPress users via `WP_User::set_role()`/`add_role()` (no second RBAC store) |
| `logs` | `class-kayan-admin-module-logs.php` | Filterable log viewer + clear action over the existing `Kayan_Logger` ring buffer |
| `system_health` | `class-kayan-admin-module-system-health.php` | Read-only checks: Rank Math, routing ownership, permalinks, cache driver, logger, locale counts, adapter status, PHP/WP versions |
| `import` (+ `export` merged) | `class-kayan-admin-module-import-export.php` | Export/import platform-owned data only (global settings, country profiles, custom languages) — never Theme Options/booking/payment/tracking |
| `rankmath` | `class-kayan-admin-module-rankmath.php` | Read-only SEO Bridge status, bridged Rank Math filters, schema adapter state, per-country GTM — links out to Rank Math's own settings |
| Dashboard stats | `class-kayan-admin-dashboard-stats.php` | Real data for the `countries`, `languages`, `rankmath`, `logs` dashboard widget slots registered in Phase 3.0 |

Loaded by a new thin aggregator, `Kayan_Admin_Feature_Modules`
(`includes/admin/class-kayan-admin-feature-modules.php`), wired into
`Kayan_Admin_Platform::register()` alongside `Kayan_Admin_Core_Modules`.

## Updated: Theme Integration adapter

- `Kayan_Adapter_Admin_Bridges` no longer bridges `countries`, `languages`,
  or `rankmath` to external screens — those are now real Admin Platform
  modules. `analytics` (→ KAYAN Track) and `tools` (→ Theme Options) remain
  thin bridges (out of Phase 3 scope).
- Compatibility report entries for those systems updated to reflect the new
  functional modules (no new full audit performed — see "No new UI" note below).

## Small engine additions (reuse, not duplication)

- `Kayan_Query_Engine::set_default_ttl()` — lets the Settings module tune
  the existing default cache TTL instead of adding a second cache config
- `kayan_platform_url_mode`, `kayan_logger_enabled`, `kayan_logger_max_entries`
  filters are now fed from the Settings module's stored global values —
  no new config path, just wiring the platform's own existing filters

## Testing

- `php -l` across all new/changed files
- Functional smoke harness booting the real `Kayan_Platform` with a minimal
  WordPress hook/option shim: renders every module screen (list + detail
  views) and exercises every `save` handler's success/redirect path,
  verifies the custom-language merge, language enable/disable toggle, and
  that the Settings module's stored `routing_mode` reaches the existing
  `kayan_platform_url_mode` filter

## Docs regenerated

`kayan_platform()->docs->generate()` — `Architecture.md`, `API.md`,
`AdminPlatform.md`, `AdminModules.md`, `AdminPermissions.md`,
`AdminDashboard.md`, `Countries.md`, `Languages.md`, `Compatibility.md`,
plus all other existing docs (regenerated, not restructured).

## Execution strategy note

Per updated instructions, this and subsequent phases are delivered as large
complete milestones rather than micro-phases, and a single project-wide
architecture/performance/security/SEO audit will be produced once after
Phase 6 instead of after every phase.
