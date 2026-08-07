# Admin Dashboard

Widget slots: `seo`, `countries`, `languages`, `programmatic_seo`, `queue`, `ai`, `performance`, `analytics`, `rankmath`, `logs`

## Live data (Phase 3)

`countries`, `languages`, `rankmath`, and `logs` widgets render real
counts/status from the Country/Language engines, `Kayan_Theme_Integration`,
and `Kayan_Logger` (see `Kayan_Admin_Dashboard_Stats`).

`pseo`, `queue`, `ai`, `performance`, `analytics` remain placeholders —
those systems are architecture-only until Phases 4–5.

```php
kayan_admin()->dashboard->register_widget( 'custom', array(
  'title' => 'Custom',
  'capability' => 'kayan_access_admin',
  'position' => 110,
  'callback' => function( $widget ) { return '<p>Live content</p>'; },
) );
```