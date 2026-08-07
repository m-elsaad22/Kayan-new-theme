# Dependency Graph (Phase 5)

Tracks which generated pages depend on which source entities (service,
city, faq, pricing, portfolio, review, article, …) via the
`kayan_pseo_dependencies` table (Migration Engine). When a source post is
saved or a source term is edited, only the pages that actually depend on
it are flagged `needs_regeneration` — never a full-site sweep.

```php
kayan_dependencies()->record( $post_id, $entities );      // written by materialize()
kayan_dependencies()->find_affected( 'service', 'plumbing' );
kayan_dependencies()->mark_affected( 'service', 'plumbing' );
```

Hooked automatically on `save_post` and `edited_term` for every post
type/taxonomy already registered in the Programmatic SEO entity registry —
no per-pack wiring required.