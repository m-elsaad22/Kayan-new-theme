# Theme Integration (Phase 3.1)

Enterprise evolution of the **existing** KAYAN Theme — not a new theme.

Adapters connect packs built before the platform without replacing them.

## Facade

```php
kayan_integration();
kayan_integration()->describe();
kayan_theme_option( 'phonenumber' );
Kayan_Theme_Integration::profile_field( 'phone' );
Kayan_Theme_Integration::rank_math_active();
```

## Adapters

`schema`, `rukn_contact`, `booking`, `payment`, `track`, `i18n_switcher`, `legacy_city`, `theme_options`, `admin_bridges`, `cpt`, `query`

## Rules

- Zero breaking changes for existing sites
- Prefer wrap / extend / filter over rewrite
- Admin option bridges skip `is_admin()` (non-AJAX) so Theme Options editors see stored values
- Country profile reads inside option filters use `profile_field()` (raw option) to avoid recursion with Country Settings dual-read

## Compatibility report

```php
kayan_integration()->report->generate();
kayan_integration()->report->to_markdown();
kayan_integration()->report->write_files();
```

See [Compatibility.md](./Compatibility.md).
