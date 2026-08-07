# Quality Engine (Phase 5)

Validates a generated page before it is allowed to publish. Checks:
content length, heading structure, duplicate detection, internal/external
links, image ALT coverage, schema source completeness, dynamic tag
resolution, country/language consistency, blueprint completeness, broken
relationships, missing entities, missing CTA/FAQ/Reviews/Pricing (only
when the assigned template requires them), and SEO completeness.

```php
kayan_quality()->validate( $post_id );
// => array( 'ok' => bool, 'score' => 0..1, 'checks' => array(...), 'blockers' => array(...) )
```

Posts without a PSEO blueprint always pass (`not_applicable`) — this never
affects existing manually-authored content.