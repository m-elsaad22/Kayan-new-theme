# KAYAN Platform — Phase 2.5.1 Architecture Extensions

**Theme version:** 1.7.1  
**Platform version:** 2.5.1  
**Scope:** Extend Programmatic SEO architecture only. Still **no generation**, **no admin UI**.

## Requirements implemented

1. **Template Engine** — every pattern assigns a `template_id`; templates define page structure.
2. **Block Engine** — templates are composed of reusable blocks (Hero, CTA, Gallery, FAQ, Pricing, Reviews, Map, Areas, Related*, Internal Links, Videos, Schema Source, Breadcrumb).
3. **AI Block Architecture** — each block has its own AI prompt; `regenerate_block()` regenerates one block without touching the rest.
4. **Blueprint Versioning** — schema v2 blueprints with `blueprint_version`, `template_version`, locks, history; `upgrade_template()` preserves locked/manual blocks.
5. **Media Engine** — featured, OG, gallery, video, icons, ALT, caption, future AI image contract.
6. **Prefer existing content types** — patterns declare `preferred_post_type` / `fallback_post_type`; `kayan_pseo` only when no existing CPT fits. Rank Math compatible.

## Added

| File | Role |
|------|------|
| `class-kayan-pseo-blocks.php` | Block registry + per-block AI prompt contracts |
| `class-kayan-pseo-templates.php` | Template registry (Service×City, Service×Area, FAQ×Service, Pricing×Service, Country×Service×City, …) |
| `class-kayan-pseo-media.php` | Media schema + sanitize + AI image stub |

## Extended

| File | Why |
|------|-----|
| `class-kayan-pseo-blueprint.php` | Schema v2: blocks, media, locks, history, `upgrade_template()`, `replace_block()` |
| `class-kayan-pseo-patterns.php` | `template_id`, preferred/fallback post types |
| `class-kayan-pseo-storage.php` | Host meta on existing CPTs; `resolve_post_type()` |
| `class-kayan-pseo-ai.php` | `regenerate_block()` on interface + manager |
| `class-kayan-pseo-generator.php` | Preview includes template/blocks/media/resolved CPT; block regen + template upgrade preview |
| `class-kayan-pseo-engine.php` | Wires blocks/templates/media |
| `class-kayan-pseo-identity.php` | Fingerprint lookup across host CPT list |
| `setup.php` | Load new modules; platform `2.5.1` |

## Explicitly NOT implemented

- Page / content / AI generation
- Admin UI
- Media generation
- Phase 3

## Public APIs (architecture)

```php
kayan_pseo()->templates->all();
kayan_pseo()->blocks->all();
kayan_pseo()->blocks->resolve_prompt( 'hero', $tokens );
kayan_pseo()->media->schema();
kayan_pseo()->blueprint->build_skeleton( $pattern, $entities, $country, $lang );
kayan_pseo()->blueprint->upgrade_template( $blueprint, $template_id );
kayan_pseo()->blueprint->replace_block( $blueprint, 'faq', $data, 'ai' );
kayan_pseo()->blueprint->set_block_lock( $blueprint, 'hero', true );
kayan_pseo()->storage->resolve_post_type( 'service_country' ); // → services when CPT exists
kayan_pseo()->generator->preview( … ); // includes template + blocks + media + resolved CPT
kayan_pseo()->generator->regenerate_block( $post_id, 'faq', $args ); // stub
kayan_pseo()->generator->preview_template_upgrade( $post_id );
kayan_pseo()->generator->materialize( $preview ); // still disabled
```
