# KAYAN Platform — Upgrade Guide

This guide covers upgrading a site from any earlier KAYAN Theme/Platform
version to the current one (Theme `2.3.x` / Platform `5.x`, Phase 6
production-ready). **No manual upgrade step is required** — this is a
core design guarantee of the Migration & Version Engine (Phase 4).

## TL;DR

1. Replace the theme files (FTP/SFTP, Git pull, or your normal deploy
   process).
2. Load any page — front-end or wp-admin. That's it.
3. Optionally flush permalinks (`Settings → Permalinks → Save`) if you
   changed URL-related settings during the deploy (not required for a
   routine version bump).

There is no database export/import step, no WP-CLI command to run, no
"click here to upgrade" admin notice to dismiss.

## What happens automatically on upgrade

1. **Version detection** — `Kayan_Migration_Engine::boot_check()` runs on
   every `Kayan_Platform::boot()` call. It compares a cached schema
   version (`kayan_platform_schema_version` option) against the highest
   version among all registered migrations.
2. **Fast path (steady state)** — if the site is already up to date, this
   is a single `get_option()` read. No database table is touched, no
   `dbDelta()` call happens. This is true on every single page load,
   front-end or admin, so there is no meaningful performance cost to
   having the engine present on an up-to-date site.
3. **Upgrade path (version changed)** — the moment the cached version
   falls behind the code's registered migrations (i.e. right after you
   deploy new theme files), the **very next request of any kind**
   (front-end visitor, admin page, WP-Cron, WP-CLI) triggers:
   - `kayan_migrations` history table creation/verification (idempotent
     `dbDelta()`),
   - every migration not yet marked `success` in that table runs, in
     order, exactly once,
   - a snapshot of any options a migration declares in
     `rollback_options` is taken automatically first,
   - the cached version option is updated once all migrations succeed.
4. **Idempotent and incremental** — migrations are tracked by a stable id,
   not a single counter. Re-running never re-applies an already-successful
   migration, and adding a new migration in a future release only ever
   runs that one new migration — never the old ones again.

## Upgrading from specific versions

| From (Theme / Platform) | To | What changes for you |
|---|---|---|
| Pre-2.0 / no platform | 2.3.x / 5.x | The whole platform (Phases 1–5) activates on first load. Existing content, Theme Options, bookings, payments, and tracking data are **completely untouched** — nothing is migrated or rewritten. New tables (`kayan_migrations`, `kayan_pseo_queue`, `kayan_pseo_dependencies`) are created automatically. |
| 2.0.x–2.1.x (Phase 3) | 2.3.x / 5.x | Admin Platform modules for Templates/Blueprints/Blocks/Programmatic SEO/Queue/AI become functional (were placeholders). No data changes. |
| 2.2.x (Phase 4) | 2.3.x / 5.x | Existing generated pages (if any) gain a Workflow state (default `draft` until you explicitly transition them) and a Quality score becomes visible in the Blueprints admin screen. Publishing/scheduling from that screen is now quality-gated (`force` still available). No content is altered or unpublished automatically. |

## Rollback safety

- **Migrations**: `kayan_migrations()->rollback( $migration_id )` restores
  any options a migration snapshotted via `rollback_options`, or calls the
  migration's own `down` callback if one was registered. Rollback status
  is recorded in the same history table as forward migrations.
- **Custom tables are never dropped automatically** — rolling back a
  migration does not delete `kayan_pseo_queue` or `kayan_pseo_dependencies`
  data, even if you also revert the theme's PHP files. This is intentional:
  losing queue/dependency history is never done silently.
- **Emergency URL rollback**: if language-first routing needs to be
  reverted, apply the `kayan_platform_url_mode` filter to return
  `KAYAN_PLATFORM_URL_MODE_LEGACY` — this is a supported, tested code
  path (`Kayan_URL::build_legacy()`), not a hack.
- **Theme file rollback**: because the platform never migrates or
  destructively rewrites existing data, reverting the theme's files to a
  previous release is always safe from a data standpoint. The only
  irreversible-by-file-rollback state is anything written to the new
  Phase 4/5 tables (queue jobs, dependency rows, workflow history) —
  none of which existing pre-Phase-4 functionality (booking, payment,
  tracking, Theme Options, manually authored content) depends on.

## Before you deploy (recommended, not required)

- Take a normal database + files backup as you would for any WordPress
  update — this is standard practice, not something the Migration Engine
  requires.
- If you use a staging environment, deploy there first and load a few
  pages (front-end + `/wp-admin/`) to let migrations run in a
  non-production database.
- Confirm PHP ≥ 7.4 (checked automatically in **System Health**) and that
  your database user can `CREATE TABLE`/`ALTER TABLE` (required once, for
  the automatic migrations — the same permission every WordPress site
  already needs for its own core tables).

## After you deploy

- Open **KAYAN → System Health** and confirm:
  - "Schema migrations" shows `current == target`.
  - "PSEO queue" shows 0 failed jobs.
  - "Rank Math" shows Active (if you use it — recommended).
- If you changed rewrite-related settings, re-save Permalinks once.
- Nothing else is required.
