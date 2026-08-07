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
kayan_pseo()->generator->translate_post( $post_id, 'en' );     // AI translation (Phase 5), linked via translation_group
kayan_pseo()->generator->regenerate_block( $post_id, 'hero', array() ); // via the AI Platform bridge — never a vendor call here
```

Prefer existing CPTs (`services`, `faqs`, `pricing`, …); `kayan_pseo` hosts
true multi-entity combination pages. Every write is keyed by a stable
fingerprint — regenerating never changes a post's URL. AI-authored block
content (hero copy, FAQ items) goes through the AI Platform bridge
(`Kayan_PSEO_AI_Bridge_Provider`) — configure a provider in the AI admin
module. `regenerate()` always refreshes derived data (contact info,
related entities, breadcrumb); it also calls the bridge for text/list
blocks when a provider is available. Locked blocks are never touched by
either path.

Every `materialize()` call assigns a Content Workflow state (quality-gated
for publish/scheduled) and records Dependency Graph rows so future source
changes flag only this page for regeneration.

See [MigrationEngine.md](./MigrationEngine.md) for the Queue's storage,
[AIPlatform.md](./AIPlatform.md) for providers, [ContentWorkflow.md](./ContentWorkflow.md),
[QualityEngine.md](./QualityEngine.md), [DependencyGraph.md](./DependencyGraph.md), and
[Countries.md](./Countries.md)/[Languages.md](./Languages.md) for locale integration.