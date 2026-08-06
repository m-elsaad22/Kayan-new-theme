# Developer Guide

## Do

- Use `kayan_query()` for posts/terms/users/meta
- Use `kayan_cache()` for caching
- Use `kayan_settings()` for options
- Use `kayan_logger()` for logs
- Use `kayan_entity()` + `kayan_tags()` for entities and tokens
- Use `kayan_theme_option()` / `kayan_integration()` when bridging Theme Options
- Register admin features via `kayan_admin()->modules->register_module()`
- Keep Rank Math as the SEO engine
- Reuse existing packs via adapters — never fork a second implementation
- Register schema/table/option changes via `kayan_migrations()->register_migration()` — never a manual SQL/upgrade step
- Use `kayan_pseo()->generator` for any post created/updated by the platform — never a second write path

## Do not

- Call `WP_Query` / `get_posts` / `get_post_meta` / `get_terms` / `get_users` from modules
- Call `get_option()` / scatter `set_transient()` in modules
- Create isolated `add_menu_page()` admin screens outside the Admin Platform
- Emit SEO head tags outside Rank Math
- Implement generation/AI/statistics until those phases are approved
- Create Countries / Languages / Templates / AI UIs until those phases are approved

## Theme integration

See [ThemeIntegration.md](./ThemeIntegration.md) and [Compatibility.md](./Compatibility.md).

## Regenerate docs

```php
kayan_platform()->docs->generate();
```