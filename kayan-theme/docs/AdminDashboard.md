# Admin Dashboard

Foundation only — **no statistics** in Phase 3.0.

Widget slots: `seo`, `countries`, `languages`, `programmatic_seo`, `queue`, `ai`, `performance`, `analytics`, `rankmath`, `logs`

```php
kayan_admin()->dashboard->register_widget( 'custom', array(
  'title' => 'Custom',
  'capability' => 'kayan_access_admin',
  'position' => 110,
) );
```

Widgets render as placeholders until a later phase supplies data callbacks.