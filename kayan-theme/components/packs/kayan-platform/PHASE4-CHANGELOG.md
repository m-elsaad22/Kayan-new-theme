# KAYAN Platform — Phase 4 Changelog

**Theme version:** 2.2.0
**Platform version:** 4.0.0
**Scope:** Migration & Version Engine (Phase 4 prerequisite) + the complete
Programmatic SEO Platform, delivered as one milestone per the updated
execution strategy.

## Explicitly NOT implemented (later phases)

- AI content providers (hero copy, FAQ text, image generation) — the null
  provider stub from Phase 2.5 is unchanged; Phase 5 adds real providers
- A visual template/blueprint builder UI (admin screens are list/detail +
  regenerate, not a drag-and-drop editor)
- Analytics / Performance / Security dashboards (not in the Phase 4 list)
- A new full project-wide audit (deferred to after Phase 6, per the updated
  execution strategy)
- Any competing SEO engine — Rank Math remains the only one; the Generator
  only writes Rank Math's own postmeta fields when a blueprint supplies them

## New: Migration & Version Engine (`includes/migration/`)

`Kayan_Migration_Engine` (facade: `kayan_migrations()`):

- **Automatic** — runs on `after_switch_theme` and via a cheap cached
  schema-version check on every `boot()`; no wp-admin button, WP-CLI
  command, or manual SQL is ever required
- **Idempotent** — a migration's `up` callback never runs twice; tracked by
  id in a `kayan_migrations` history table (not by a single version counter)
- **Incremental** — adding a new migration later only runs that one; old
  migrations are never re-executed
- **Rollback preparation** — `rollback_options` auto-snapshots option values
  before `up` runs; `rollback( $id )` restores them (or calls an explicit
  `down` callback) and records the outcome
- **Generic table helper** — `create_or_upgrade_table()` wraps `dbDelta()`
  for any pack's schema/table migrations
- **History + logging** — every run is recorded in the `kayan_migrations`
  table and the existing Logger (new `migration` channel)
- Additive only: existing ad-hoc version checks in booking/payment/track
  packs are untouched. Other packs MAY register through
  `kayan_migrations_register` later.

First consumer: `pseo_queue_table_v1` creates the `kayan_pseo_queue` table
backing the new Queue.

## Programmatic SEO Platform — from architecture to complete

Phase 2.5–2.7 built the full architecture (registries, schemas, identity,
storage contracts) with generation intentionally disabled. Phase 4 turns it
on:

| Component | What changed |
|---|---|
| **Rules** (`class-kayan-pseo-rules.php`) | `preview_combinations()` now does a real cartesian expansion over the existing Query Engine (services/cities/categories/faqs/pricing/reviews), honoring rule filters and country-tagged cities. Capped + early-stop for very large catalogs. |
| **Generator** (`class-kayan-pseo-generator.php`) | `materialize()` creates/updates real WP posts (draft/publish/scheduled) keyed by the existing stable fingerprint — URLs never change on update. `regenerate()` refreshes derived block data (contact info, related entities, breadcrumb) without inventing AI copy. Rank Math postmeta write-through when a blueprint supplies title/description/focus keyword/robots. |
| **Renderer** (new `class-kayan-pseo-renderer.php`) | Turns a post's blueprint blocks into front-end HTML via `the_content`, resolving `{{tags}}` at render time. Only touches posts that carry a `kayan_pseo_blueprint` meta value — every existing post is unaffected. |
| **Queue** (`class-kayan-pseo-jobs.php`) | Re-platformed from a single `wp_options` row onto the new `kayan_pseo_queue` DB table (via the Migration Engine) — bulk jobs can now hold thousands of combinations. Same public API (`enqueue`/`get`/`all`/`update_status`), plus `run()` is now implemented (chunked, resumable), `retry()`, `cancel()`, `due_job_ids()`. |
| **Scheduler** (new `class-kayan-pseo-scheduler.php`) | Drives the Queue automatically via WP-Cron (5-minute interval) plus a light `admin_init` fallback for sites with unreliable cron. `process_now()` is exposed in the admin UI as a convenience, not a requirement. |
| **Bulk / Draft / Publish / Preview / Regeneration** | `Kayan_PSEO_Engine::bulk_generate()` and `::regenerate_bulk()` are the single entry points — they expand a rule and enqueue one Queue job; preview stays a pure dry-run. |

## Admin Platform — Templates/Blueprints/Blocks/PSEO/Queue now functional

New modules under `includes/admin/modules/` (same registration pattern as
Phase 3): `Kayan_Admin_Module_Templates`, `_Blocks`, `_Blueprints`, `_Pseo`
(rules CRUD + preview + bulk generate), `_Queue` (jobs list + process
now/retry/cancel). Dashboard widgets for `pseo` and `queue` now show live
counts. System Health gained migration + queue status cards.

## Existing system integrations

- **Services / FAQs / Pricing** — `service_country`, `faq_service`,
  `pricing_service` patterns materialize directly into their existing CPTs
  (never a second CPT)
- **Articles** (`post`) — already a host post type; unaffected unless a
  blueprint is attached
- **Portfolio** — entity/query resource reused as-is
- **Cities / Countries / Languages** — combinations pull from the existing
  Country Engine, Language Engine, and `cities` taxonomy; generated posts
  get the real `cities` term assigned and existing Content Locale meta
  (`kayan_content_lang`, `kayan_content_countries`, `kayan_public_slug`)
- **Rank Math** — write-through only; sitemap/schema/meta tags remain
  entirely Rank Math's responsibility

## Testing

No local WordPress/MySQL install is available in this environment, so
correctness was verified with two purpose-built functional harnesses (not
just `php -l`):

1. **Migration engine** — 17 assertions: idempotency, incremental runs,
   rollback (with option restore), failure handling that halts a batch,
   `describe()`.
2. **PSEO end-to-end** — boots the real `Kayan_Platform` against a minimal
   WordPress hook/option/`$wpdb` shim with seeded services/cities: rule
   save → real combination expansion (2×2=4) → `preview()` → `materialize()`
   (create, then update-not-duplicate on the same fingerprint, locked slug
   stability) → `bulk_generate()` → `Scheduler::process_batch()` completes
   the job and materializes all 4 combinations → `regenerate()` → block
   rendering → confirms an unrelated manually-created post is completely
   untouched (zero breaking changes) → every new/changed admin module
   screen renders without a fatal error.

Both harnesses live outside the repo (this environment's `/tmp`) since they
exist purely to validate this change; they are not shipped as part of the
theme.

## Docs regenerated

`kayan_platform()->docs->generate()` — new `MigrationEngine.md`; updated
`Architecture.md`, `API.md`, `ProgrammaticSEO.md`, `Templates.md`,
`Blueprints.md`, `DynamicTags.md`, `AdminPlatform.md`, `AdminModules.md`,
`DeveloperGuide.md`, `README.md`.

## Execution strategy note

Per the updated instructions, Phase 4 (Migration Engine + complete
Programmatic SEO Platform) is delivered as one milestone. No new full
compatibility/architecture audit was performed — that is deferred until
after Phase 6.
