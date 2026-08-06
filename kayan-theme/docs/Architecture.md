# Architecture

**Platform version:** 2.7.0

KAYAN is a single-install WordPress SEO platform (no Multisite, no WPML/Polylang).

## Layers

1. **Country / Language / Context** — request locale
2. **Routing + Content Resolution** — language-first URLs
3. **Entity Relationship Engine** — reusable entities + edges
4. **Dynamic Data Tags** — `{{tag}}` tokens for templates/AI
5. **Programmatic SEO** — patterns, templates, blocks, blueprints (no generation yet)
6. **Core Infrastructure** — Query, Cache, Settings, Logger
7. **SEO Bridge** — Rank Math only (filters, never competing head tags)

## Design rules

- Prefer existing WP content types; `kayan_pseo` is fallback only
- Modules must use Query / Cache / Settings / Logger / Entity APIs
- Do not call `WP_Query`, `get_posts`, `get_option`, or scatter transients in app code
- Rank Math remains the only SEO engine

## Boot order

Content Locale → Programmatic entities → Entity Engine → Data Tags → Cache → Settings Engine → Logger → Query → PSEO → Router → Resolver → SEO Bridge