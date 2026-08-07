# Blueprints

Schema version 2 — block-based, versioned, lock-safe.

```php
kayan_pseo()->blueprint->build_skeleton( $pattern, $entities, $country, $lang );
kayan_pseo()->blueprint->upgrade_template( $blueprint, $template_id );
kayan_pseo()->blueprint->replace_block( $blueprint, 'faq', $data, 'ai' );
kayan_pseo()->blueprint->set_block_lock( $blueprint, 'hero', true );
```

Manual/locked blocks survive template upgrades. Media is first-class via Media Engine.
`materialize()`/`regenerate()` (Phase 4) sanitize + persist blueprints via
this engine — locked blocks are never overwritten.