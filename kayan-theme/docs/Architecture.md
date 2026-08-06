# Architecture

**Platform version:** 3.1.0

KAYAN is a single-install WordPress SEO platform (no Multisite, no WPML/Polylang).

## Layers

1. **Country / Language / Context** — request locale
2. **Routing + Content Resolution** — language-first URLs
3. **Entity Relationship Engine** — reusable entities + edges
4. **Dynamic Data Tags** — `{{tag}}` tokens for templates/AI
5. **Programmatic SEO** — patterns, templates, blocks, blueprints (no generation yet)
6. **Core Infrastructure** — Query, Cache, Settings, Logger
7. **Admin Platform** — Module Registry, Permissions, UI Framework, Dashboard foundation
8. **Theme Integration** — Adapters connecting existing KAYAN Theme packs (Phase 3.1)
9. **SEO Bridge** — Rank Math only (filters, never competing head tags)

## Design rules

- Prefer existing WP content types; `kayan_pseo` is fallback only
- Modules must use Query / Cache / Settings / Logger / Entity APIs
- Admin features register through `kayan_admin()` — no isolated admin pages
- Do not call `WP_Query`, `get_posts`, `get_option`, or scatter transients in app code
- Rank Math remains the only SEO engine
- Reuse / extend / wrap existing theme packs — never duplicate implementations

## Boot order

Content Locale → Programmatic entities → Entity Engine → Data Tags → Cache → Settings Engine → Logger → Query → PSEO → Router → Resolver → SEO Bridge → Admin Platform → Theme Integration
