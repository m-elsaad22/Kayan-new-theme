# KAYAN Platform — Production Checklist

Use this before flipping a site live, and again periodically afterward.

## Pre-launch

### Environment
- [ ] PHP 7.4+ confirmed (8.1+ recommended)
- [ ] MySQL/MariaDB supports `dbDelta()` (any WP-supported version)
- [ ] Database user can `CREATE TABLE`
- [ ] WP-Cron enabled, or a real system cron configured
- [ ] Object cache configured (recommended, not required)
- [ ] Backups configured (files + database)

### SEO
- [ ] Rank Math installed and activated
- [ ] **KAYAN → System Health** shows "Rank Math: Active"
- [ ] No other SEO plugin active (avoid conflicting sitemaps/schema/meta)
- [ ] Permalinks set to a pretty structure (checked automatically in
      System Health — plain permalinks break language-first routing)

### Platform
- [ ] **KAYAN → System Health** shows "Schema migrations: current == target"
- [ ] Countries/Languages reviewed at **KAYAN → Countries / Languages**
- [ ] Country business profile (phone/WhatsApp/currency/SEO defaults)
      filled in for every active country
- [ ] AI providers configured at **KAYAN → AI** if Programmatic SEO
      generation or translation will be used (optional — safe to skip)

### Content
- [ ] Existing Theme Options (YTS), booking, payment, and tracking
      settings verified unchanged after the upgrade (they are never
      touched by the platform, but verify as part of your own QA)
- [ ] Any Programmatic SEO rules reviewed at **KAYAN → Programmatic SEO**
      before running **Bulk Generate** in production — always **Preview**
      a rule first to see the real combination count
- [ ] Generated pages intended to go live have been reviewed via
      **KAYAN → Blueprints** (Quality score, Workflow state) and
      transitioned to `Published` deliberately — nothing publishes itself
      without either passing the Quality gate or an explicit `force`

## Launch

- [ ] Final backup taken immediately before go-live
- [ ] Deploy during a low-traffic window if the site has substantial
      existing content (routine WordPress practice, not platform-specific)
- [ ] Load the homepage and one page per active country/language
      combination to confirm routing
- [ ] Load one generated (Programmatic SEO) page per pattern in use, if any
- [ ] Confirm Rank Math's sitemap includes expected post types
      (`services`, `faqs`, `pricing`, `kayan_pseo`, etc. as applicable)

## Post-launch monitoring

### Daily / weekly
- [ ] **KAYAN → Queue** — 0 (or a small, explained number of) failed jobs
- [ ] **KAYAN → Logs** — no unexpected `error`/`critical` entries in the
      `errors`, `security`, or `migration` channels
- [ ] **KAYAN → System Health** — still green across the board

### Monthly
- [ ] Review **KAYAN → Blueprints** for pages stuck in `needs_regeneration`
      or `human_review` for longer than expected
- [ ] Review AI provider usage/cost if translation or block regeneration
      is in active use
- [ ] Confirm no new PHP deprecation notices after any WordPress or PHP
      version bump on the host

### On every future theme update
- [ ] Follow the [Upgrade Guide](./UpgradeGuide.md) — no manual DB steps
      are expected, but re-verify System Health after deploying

## Rollback readiness

- [ ] You know how to revert theme files (Git tag, backup zip, etc.)
- [ ] You understand that reverting files does **not** drop the
      `kayan_migrations` / `kayan_pseo_queue` / `kayan_pseo_dependencies`
      tables — this is intentional (see [Upgrade Guide](./UpgradeGuide.md))
- [ ] You know the `kayan_platform_url_mode` filter exists as an emergency
      routing rollback if language-first URLs ever need to be disabled
