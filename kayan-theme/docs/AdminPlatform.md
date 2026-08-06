# Admin Platform

**Phase:** 3.0 · **Admin version contract:** 3.0.0

Centralized administration shell. Future modules register through one API — no isolated admin pages.

```php
kayan_admin()->describe();
kayan_admin()->modules->register_module( 'my_module', array(
  'label' => 'My Module',
  'capability' => 'kayan_manage_tools',
  'position' => 120,
  'screen' => function( $module, $context ) {
    echo kayan_admin()->ui->empty_state( array(
      'title' => $module['label'],
      'description' => 'Hello',
    ) );
  },
) );
```

Hook: `kayan_admin_register_modules`

Menu slug: `kayan-platform` (+ per-module `kayan-platform-{id}`).