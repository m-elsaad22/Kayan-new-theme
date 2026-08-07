# Languages

```php
kayan_platform()->languages; // Language Engine
kayan_platform_language();
kayan_settings()->get_language( $key, 'en' );
kayan_settings()->set_language( $key, $value, 'ar' );
```

Canonical URLs are language-first: `/`, `/sa/`, `/en/`, `/en/sa/`.

## Admin UI (Phase 3)

The `languages` Admin Platform module can register additional languages
(stored in `kayan_platform_custom_languages`, merged via the existing
`kayan_platform_languages` filter) and toggle languages on/off
(`kayan_platform_disabled_languages`). Arabic cannot be disabled.

```php
Kayan_Admin_Module_Languages::enabled_languages();
```