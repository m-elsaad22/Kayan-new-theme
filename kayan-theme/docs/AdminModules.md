# Admin Modules

Core registered modules:

`dashboard`, `countries`, `languages`, `entities`, `relationships`, `templates`, `blueprints`, `blocks`, `pseo`, `ai`, `media`, `queue`, `logs`, `analytics`, `performance`, `security`, `import`, `export`, `tools`, `system_health`, `rankmath`

## Registration buckets

Each module may declare: `nav`, `widgets`, `settings`, `tables`, `forms`, `cards`, `actions`, `notifications`, `permissions`, `screen`.

```php
kayan_admin()->modules->register_item( 'pseo', 'actions', 'preview', array(
  'label' => 'Preview',
  'callback' => $fn,
) );
```