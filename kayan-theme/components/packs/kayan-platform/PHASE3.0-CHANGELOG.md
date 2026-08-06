# KAYAN Platform — Phase 3.0 Changelog

**Theme version:** 2.0.0  
**Platform version:** 3.0.0  
**Scope:** Admin Platform Core — modular architecture, permissions, UI framework, dashboard foundation.

## Explicitly NOT implemented

- Statistics / reports / analytics data
- AI generation
- Programmatic page generation
- Generation jobs
- Frontend redesign
- Visual redesign of existing theme templates
- Phase 3.1+ module feature UIs

## Added — Admin Platform (`includes/admin/`)

| File | Role |
|------|------|
| `class-kayan-admin-platform.php` | Central Admin Platform facade + shell |
| `class-kayan-admin-module-registry.php` | Module Registry (single registration API) |
| `class-kayan-admin-permissions.php` | Roles & capabilities |
| `class-kayan-admin-ui.php` | Reusable Admin UI Framework |
| `class-kayan-admin-dashboard.php` | Dashboard foundation (widget slots only) |
| `class-kayan-admin-core-modules.php` | Core module registrations (architecture shells) |
| `assets/admin/kayan-admin.css` | Structural admin framework styles |
| `assets/admin/kayan-admin.js` | Minimal tabs/dialog/drawer behaviors |
| `PHASE3.0-CHANGELOG.md` | This file |

## Module Registry

One API — no isolated admin pages:

```php
kayan_admin()->modules->register_module( 'my_module', array(
  'label' => 'My Module',
  'capability' => 'kayan_manage_tools',
  'position' => 120,
  'nav' => true,
  'screen' => $callback,
  'widgets' => array(),
  'settings' => array(),
  'tables' => array(),
  'forms' => array(),
  'cards' => array(),
  'actions' => array(),
  'notifications' => array(),
  'permissions' => array(),
) );
```

Hook: `kayan_admin_register_modules`

### Core modules registered

Dashboard · Countries · Languages · Entities · Relationships · Templates · Blueprints · Blocks · Programmatic SEO · AI · Media · Queue · Logs · Analytics · Performance · Security · Import · Export · Tools · System Health · Rank Math Integration

## Permissions

Roles: Administrator · SEO Manager · Content Manager · Editor · Translator · Marketing · Developer · custom via API.

Access cap: `kayan_access_admin`  
Feature caps: `kayan_manage_*`, `kayan_view_*`, `kayan_translate`, …

```php
kayan_admin()->permissions->can( 'kayan_manage_pseo' );
```

## UI Framework

Reusable: Cards, Tables, Forms, Tabs, Panels, Dialogs, Drawers, Notifications, Progress, Status, Filters, Bulk Actions, Search, Pagination, Empty State.

```php
kayan_admin()->ui->card( $args );
kayan_admin()->ui->table( $args );
kayan_admin()->ui->form( $args );
```

## Dashboard Foundation

Widget slots prepared (no statistics):

SEO · Countries · Languages · Programmatic SEO · Queue · AI · Performance · Analytics · Rank Math · Logs

```php
kayan_admin()->dashboard->register_widget( 'custom', $args );
```

## Developer documentation

Updated / regenerated under `kayan-theme/docs/`:

- AdminPlatform.md
- AdminModules.md
- AdminPermissions.md
- AdminUI.md
- AdminDashboard.md
- Architecture.md / API.md / DeveloperGuide.md (updated)

Regenerate: `kayan_platform()->docs->generate()`

## Modified

| File | Why |
|------|-----|
| `setup.php` | Load admin modules; platform `3.0.0` |
| `includes/class-kayan-platform.php` | Wire `$admin` |
| `includes/helpers.php` | `kayan_admin()` |
| `includes/infra/class-kayan-docs-generator.php` | Admin docs |
| `includes/class-kayan-country-router.php` | Rewrite version `3.0.0` |
| `style.css` / `readme.txt` | Theme `2.0.0` |

## Rank Math

Fully compatible. Rank Math remains the only SEO engine. Admin module `rankmath` is an integration placeholder only.

## Public API

```php
kayan_admin()->describe();
kayan_admin()->modules->register_module( $id, $args );
kayan_admin()->modules->register_item( $module, $bucket, $id, $args );
kayan_admin()->permissions->can( $cap );
kayan_admin()->ui->card( $args );
kayan_admin()->dashboard->register_widget( $id, $args );
```
