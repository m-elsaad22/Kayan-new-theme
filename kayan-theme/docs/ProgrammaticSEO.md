# Programmatic SEO Platform (Phase 4 — complete)

Generation is live: preview → draft/publish/scheduled materialize → queue-backed
bulk generation → regeneration. Rank Math stays the only SEO engine — the
platform only writes its own postmeta fields (title/description/focus
keyword/robots) when a blueprint supplies them.

```php
kayan_pseo()->patterns->all();
kayan_pseo()->rules->save( $rule );                          // create/update a generation rule
kayan_pseo()->rules->preview_combinations( $rule_id );        // real cartesian expansion (Query Engine)
kayan_pseo()->generator->preview( $pattern, $entities, $country, $lang, $tokens );
kayan_pseo()->generator->materialize( $preview, array( 'post_status' => 'draft' ) );
kayan_pseo()->generator->regenerate( $post_id, array( 'mode' => 'content_only' ) );
kayan_pseo()->bulk_generate( $rule_id, array( 'post_status' => 'draft' ) ); // enqueues a Queue job
kayan_pseo()->regenerate_bulk( $post_ids );
kayan_pseo()->scheduler->process_now();                       // manual batch trigger (also automatic)
```

Prefer existing CPTs (`services`, `faqs`, `pricing`, …); `kayan_pseo` hosts
true multi-entity combination pages. Every write is keyed by a stable
fingerprint — regenerating never changes a post's URL. AI-authored block
content (hero copy, FAQ text, etc.) remains a null-provider stub until
Phase 5; `regenerate()` refreshes derived data (contact info, related
entities, breadcrumb) without inventing text.

See [MigrationEngine.md](./MigrationEngine.md) for the Queue's storage and
[Countries.md](./Countries.md)/[Languages.md](./Languages.md) for locale
integration.