# KAYAN Platform — Phase 2.6 Changelog

**Theme version:** 1.8.0  
**Platform version:** 2.6.0  
**Scope:** Native **Entity Relationship Engine** + **Dynamic Data Tag** architecture only.

## Explicitly NOT implemented

- Page / content / AI generation
- Admin UI redesign
- Frontend / theme template modifications
- Phase 3

## Goals

The platform is built around reusable entities instead of isolated post meta access.

1. Centralized **Entity API** for all entity types
2. **Relationships** between entities
3. **Dynamic Data Tags** (`{{tag}}`) for templates and future AI
4. Full compatibility with Rank Math and Phases 1–2.5.1

## Added — `includes/entities/`

| File | Role |
|------|------|
| `class-kayan-entity-api.php` | Centralized Entity API — `get()`, `query()`, `get_field()`, `get_media()`, `get_locale()`, Rank Math source readers |
| `class-kayan-entity-relationships.php` | Relationship engine — `relate()`, `unrelate()`, `get_related()`, allowed matrix, meta contract `kayan_entity_relationships` |
| `class-kayan-entity-engine.php` | Facade — `kayan_entity()` |
| `class-kayan-dynamic-data-tags.php` | Tag registry + `resolve()` / `resolve_tag()` / `resolve_mixed()` — `kayan_tags()` |
| `PHASE2.6-CHANGELOG.md` | This file |

## Entity types (API surface)

Country · City · Area · District · Neighborhood · Landmark · Service · Category · FAQ · Pricing · Review · Portfolio · Gallery · Video · Article  

(Plus existing programmatic types: brand, building, before_after.)

- **Existing WP types preferred:** services, faqs, pricing, reviews, portfolio, cities, service_categories, `post` (article)
- **Virtual/reserved until storage exists:** area, district, neighborhood, landmark, gallery, video (and other generated sources)
- **Country** resolves via Country Engine + Country Settings (phone, whatsapp, …)

## Relationship contract

- Meta key: `kayan_entity_relationships` (posts + terms)
- Edge shape: `{ type, ref, rel, meta }`
- Default matrix covers geo hierarchy + service graph (city/area/faq/pricing/review/portfolio/gallery/video/article/…)
- Bidirectional support for `related`, `serves`, `located_in`/`contains`, `in_category`
- Legacy bridge: city term meta `kayan_country` → `located_in` country edge (read path)
- Consumers must use `kayan_entity()->relate()` / `related()` — not raw meta

## Dynamic Data Tags

Syntax: `{{tag_name}}`

| Tag | Source |
|-----|--------|
| `{{service_name}}` | Entity API |
| `{{city_name}}` | Entity API |
| `{{country_name}}` | Entity API |
| `{{faq}}` | Related FAQs / FAQ entity |
| `{{gallery}}` | Blueprint media / entity media |
| `{{price_from}}` | Pricing / service fields |
| `{{featured_image}}` | Entity / blueprint media |
| `{{phone}}` | Country settings |
| `{{whatsapp}}` | Country settings |
| `{{average_rating}}` | Related reviews |
| `{{review_count}}` | Related reviews |
| `{{related_services}}` | Relationship engine |
| `{{related_articles}}` | Relationship engine |
| `{{cta_title}}` | Blueprint CTA block |
| `{{hero_title}}` | Blueprint Hero block |
| `{{meta_title}}` | Blueprint Rank Math source / Rank Math post meta (read-only) |

`resolve_mixed()` also maps legacy `{service}` / `{city}` prompt tokens into tags for PSEO AI prompts.

## Modified

| File | Why |
|------|-----|
| `setup.php` | Load entity modules; platform `2.6.0` |
| `includes/class-kayan-platform.php` | Wire `$entity` + `$tags`; boot after programmatic, before PSEO |
| `includes/helpers.php` | `kayan_entity()`, `kayan_tags()` |
| `includes/pseo/class-kayan-pseo-blocks.php` | Prompt resolver consumes Dynamic Data Tags |
| `includes/pseo/class-kayan-pseo-generator.php` | Preview includes sample tag resolutions |
| `includes/pseo/class-kayan-pseo-engine.php` | `describe()` exposes entity engine + tags |
| `includes/class-kayan-country-router.php` | Rewrite version `2.6.0` |
| `style.css` / `readme.txt` | Theme `1.8.0` |

## Public APIs

```php
// Entity API
kayan_entity()->describe();
kayan_entity()->api->types();
kayan_entity()->get( 'service', $ref );
kayan_entity()->name( 'city', $ref );
kayan_entity()->field( 'service', $ref, 'price_from' );
kayan_entity()->api->query( 'service', array( 'number' => 10 ) );
kayan_entity()->api->get_media( 'service', $ref );
kayan_entity()->api->get_rank_math_field( $post_id, 'title' ); // read-only source

// Relationships
kayan_entity()->relate( 'service', $sid, 'city', $cid, 'serves', array( 'bidirectional' => true ) );
kayan_entity()->related( 'service', $sid, 'city' );
kayan_entity()->relationships->get_related( 'service', $sid, 'faq' );
kayan_entity()->relationships->has( 'service', $sid, 'city', $cid );

// Dynamic Data Tags
kayan_tags()->all();
kayan_tags()->resolve( 'Book {{service_name}} in {{city_name}} — {{phone}}', $context );
kayan_tags()->resolve_tag( 'meta_title', $context );
kayan_tags()->resolve_mixed( 'Write FAQ for {service} in {city}', $context );
kayan_tags()->register_tag( 'custom_tag', array( 'callback' => $fn ) );
```

### Tag context contract

```php
array(
  'country'   => 'ae',
  'language'  => 'ar',
  'entities'  => array( 'service' => '123', 'city' => '45' ),
  'post_id'   => 0,
  'blueprint' => null, // optional PSEO blueprint
  'tokens'    => array(), // optional overrides
);
```

## Rank Math compatibility

- No competing title/meta/canonical/schema/OG/Twitter/sitemap output
- `{{meta_title}}` and Entity API SEO fields are **source values only**
- Rank Math remains the only SEO engine

## Compatibility with previous phases

- Phase 1 locale meta / country settings unchanged
- Phase 2 routing / resolver unchanged in behavior
- Phase 2.5 / 2.5.1 PSEO patterns, templates, blocks, media, blueprints unchanged in structure
- Entity engine boots **after** programmatic registry and **before** PSEO so patterns/AI can consume entities + tags
