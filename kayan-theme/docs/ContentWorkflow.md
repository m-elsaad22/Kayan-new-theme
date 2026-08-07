# Content Workflow (Phase 5)

Every PSEO-generated page has an explicit lifecycle state, stored in
`kayan_workflow_state` post meta:

`draft` → `ai_draft` → `human_review` → `approved` → `scheduled` / `published`
plus `needs_update`, `needs_regeneration`, `archived`, `failed`.

```php
kayan_workflow()->get_state( $post_id );
kayan_workflow()->transition( $post_id, Kayan_Content_Workflow::PUBLISHED );
kayan_workflow()->history( $post_id );
kayan_workflow()->transition_map();
```

Publishing/scheduling is gated by the Quality Engine unless
`array( 'force' => true )` is passed. A failed gate routes the page to
`human_review` instead of silently failing. Transitions sync the
underlying WordPress `post_status` — a single source of truth (the
Generator never sets `post_status` directly for anything but the initial
safe `draft` write).