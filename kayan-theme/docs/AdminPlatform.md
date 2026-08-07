# Admin Platform

**Phase:** 3 (complete) · **Admin version contract:** 5.0.0

Functional modules: Dashboard, Settings, Countries, Languages, Entities,
Relationships, Permissions, Logs, System Health, Import/Export, Rank Math
Integration (Phase 3); Templates, Blueprints, Blocks, Programmatic SEO,
Queue (Phase 4 — real Generator + DB-backed Scheduler); AI (Phase 5 —
interchangeable provider configuration). Media, Analytics, Performance,
Security remain architecture shells.

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