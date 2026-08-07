# KAYAN Theme — Deployment Guide

## Requirements

| Component | Minimum | Recommended | Notes |
|---|---|---|---|
| PHP | 7.4 | 8.1+ | Verified: no PHP 8-only syntax is used anywhere in the platform, so 7.4 genuinely works — 8.1+ is recommended purely for performance (JIT/opcache improvements), not a requirement. |
| WordPress | 5.9+ | Latest | `register_post_meta()`/`register_term_meta()` with `auth_callback`, `get_current_screen()`-free admin UI, and `wp_remote_post()` are all long-stable APIs. |
| MySQL / MariaDB | 5.7 / 10.3 | 8.0 / 10.6 | Required for `dbDelta()`-managed tables (`kayan_migrations`, `kayan_pseo_queue`, `kayan_pseo_dependencies`) — standard InnoDB, utf8mb4, no vendor-specific SQL. |
| Rank Math | Any recent version | Latest | The **only** supported SEO plugin. The theme never prints competing meta/schema/canonical/sitemap output — install and activate Rank Math for full SEO Bridge behavior (title/description/canonical/hreflang extensions, Programmatic SEO postmeta write-through). Running without Rank Math is safe (the bridge simply has nothing to extend) but not the intended production configuration. |
| WP-Cron | Enabled (default) | Enabled, or a real system cron hitting `wp-cron.php` | Drives the PSEO Scheduler (~every 5 minutes). A light `admin_init` fallback also nudges the queue on normal admin traffic, so a fully disabled cron degrades gracefully (queue still progresses on any admin page view) rather than stalling completely. |
| Object cache (Redis/Memcached) | Not required | Recommended for high-traffic PSEO sites | The Cache Engine already prefers the WordPress object cache API when available and falls back to transients automatically — no configuration needed either way. |

## Pre-deployment checklist

- [ ] PHP version confirmed (`php -v` on the target server, or check
      **KAYAN → System Health** after first deploy).
- [ ] Database user has `CREATE TABLE` / `ALTER TABLE` privileges (needed
      once, automatically, for the Migration Engine's tables).
- [ ] Rank Math installed and activated (recommended).
- [ ] Backup taken (files + database) — standard practice for any deploy.
- [ ] If deploying to a new environment: `wp_generate_uuid4()` and
      `wp_remote_post()` are available (both are WordPress core since long
      before any supported WP version — no action needed on a normal WP
      install).

## Deployment steps

1. **Deploy theme files** via your normal process (Git pull, SFTP, CI/CD
   pipeline, WordPress admin theme upload). No build step, no Composer/npm
   install is required for the theme to function — everything is plain
   PHP with no external dependencies.
2. **Load the site once** (front-end or `/wp-admin/`) — this triggers the
   Migration Engine's one-time upgrade path automatically (see the
   [Upgrade Guide](./UpgradeGuide.md)).
3. **Verify** via **KAYAN → System Health**:
   - Schema migrations up to date
   - Cache driver detected
   - Routing ownership = Platform
   - Rank Math = Active (if used)
4. **Configure AI providers** (optional) at **KAYAN → AI** if you plan to
   use Programmatic SEO block regeneration or translation — enter an API
   key for OpenAI, Claude, Gemini, and/or Mistral. Leaving all providers
   unconfigured is fully supported; those features simply report
   `ai_not_configured` until a key is added.
5. **Re-save Permalinks** (`Settings → Permalinks → Save Changes`) only if
   this deploy changed rewrite-affecting settings (new country/language,
   custom rewrite filters). Not required for a routine code deploy.

## Environment-specific notes

### Multisite

Not supported by design (see `docs/Architecture.md` constraints) — this
theme targets a single WordPress install. Do not activate on a Multisite
network.

### Staging → Production promotion

Because the platform never writes destructive migrations against existing
content, promoting a staging environment to production (via a database
copy or file sync) is as safe as any standard WordPress deploy. The
Migration Engine will simply confirm (cheaply) that production is already
at the same schema version if staging already ran the same migrations.

### Multiple web servers / load balancers

The Migration Engine uses a short-lived transient lock
(`kayan_migrations_lock`, 5 minutes) to prevent two concurrent requests
from running the same migration batch simultaneously. This is safe under
normal multi-server setups where transients are shared (via the object
cache or the shared database) — if your environment uses **per-server,
non-shared** object caches, the lock is best-effort only; the underlying
migrations are still idempotent (checked against the shared database
history table), so a rare double-attempt is a no-op, not data corruption.

### WP-Cron alternatives (disabled `DISABLE_WP_CRON`)

If you disable WordPress's pseudo-cron and rely on a real system cron
hitting `wp-cron.php`, ensure it runs at least every 5 minutes for timely
PSEO Queue processing. If you run neither, the queue still progresses via
the `admin_init` fallback whenever an administrator visits wp-admin, just
less predictably — acceptable for low-volume sites, not recommended for
active bulk-generation workloads.

## Rollback

See the [Upgrade Guide](./UpgradeGuide.md#rollback-safety) — reverting
theme files is always safe from a data-integrity standpoint; new Phase
4/5 tables are never dropped automatically.
