# Admin Modules

Registered modules:

`dashboard`, `settings`, `countries`, `languages`, `entities`, `relationships`, `templates`, `blueprints`, `blocks`, `pseo`, `ai`, `export`, `media`, `queue`, `logs`, `analytics`, `performance`, `security`, `import`, `tools`, `system_health`, `permissions`, `rankmath`

## Functional (Phase 3)

`dashboard` · `settings` · `countries` · `languages` · `entities` · `relationships` · `permissions` · `logs` · `system_health` · `import` (Import/Export) · `rankmath`

## Architecture shells (later phases)

`templates` · `blueprints` · `blocks` · `pseo` · `ai` · `media` · `queue` · `analytics` · `performance` · `security` · `tools` (bridges to Theme Options) · `export` (merged into `import` screen)

## Registration buckets

Each module may declare: `nav`, `widgets`, `settings`, `tables`, `forms`, `cards`, `actions`, `notifications`, `permissions`, `screen`, `save`.

The `save` callable handles POST for that module on `admin_init` (before
headers are sent) — see `Kayan_Admin_Platform::maybe_handle_module_post()`.

```php
kayan_admin()->modules->register_item( 'pseo', 'actions', 'preview', array(
  'label' => 'Preview',
  'callback' => $fn,
) );
```