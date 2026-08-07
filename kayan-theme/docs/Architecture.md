# Architecture

**Platform version:** 5.0.0

KAYAN is a single-install WordPress SEO platform (no Multisite, no WPML/Polylang).

## Layers

1. **Country / Language / Context** — request locale
2. **Routing + Content Resolution** — language-first URLs
3. **Entity Relationship Engine** — reusable entities + edges
4. **Dynamic Data Tags** — `{{tag}}` tokens for templates/blocks/AI
5. **Programmatic SEO Platform** — patterns, templates, blocks, blueprints, generator, queue, scheduler (Phase 4 — generation is live)
6. **AI Platform** — interchangeable provider registry (OpenAI/Claude/Gemini/Mistral/future) — Phase 5
7. **Content Workflow + Quality Engine + Dependency Graph** — lifecycle, pre-publish validation, targeted regeneration — Phase 5
8. **Core Infrastructure** — Query, Cache, Settings, Logger, Migration & Version Engine
9. **Admin Platform** — Module Registry, Permissions, UI Framework, Dashboard, functional feature modules
10. **Theme Integration** — Adapters connecting existing KAYAN Theme packs (Phase 3.1)
11. **SEO Bridge** — Rank Math only (filters, never competing head tags)

## Design rules

- Prefer existing WP content types; `kayan_pseo` hosts true multi-entity combinations only
- Modules must use Query / Cache / Settings / Logger / Entity APIs
- Admin features register through `kayan_admin()` — no isolated admin pages
- Do not call `WP_Query`, `get_posts`, `get_option`, or scatter transients in app code
- Rank Math remains the only SEO engine — the Generator only writes RM's own postmeta fields
- Reuse / extend / wrap existing theme packs — never duplicate implementations
- Schema/DB changes go through the Migration Engine — never a manual upgrade step
- Application code never talks to a concrete AI vendor — always `kayan_ai()`
- Publishing a generated page always goes through `kayan_workflow()->transition()` — never set `post_status` directly on a PSEO-managed post

## Boot order

Content Locale → Programmatic entities → Entity Engine → Data Tags → Cache → Settings Engine → Logger → Query → Migration Engine → AI Platform → Quality Engine → Content Workflow → Dependency Graph → PSEO → Router → Resolver → SEO Bridge → Admin Platform → Theme Integration