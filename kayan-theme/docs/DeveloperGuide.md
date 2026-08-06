# Developer Guide

## Do

- Use `kayan_query()` for posts/terms/users/meta
- Use `kayan_cache()` for caching
- Use `kayan_settings()` for options
- Use `kayan_logger()` for logs
- Use `kayan_entity()` + `kayan_tags()` for entities and tokens
- Keep Rank Math as the SEO engine

## Do not

- Call `WP_Query` / `get_posts` / `get_post_meta` / `get_terms` / `get_users` from modules
- Call `get_option()` / scatter `set_transient()` in modules
- Emit SEO head tags outside Rank Math
- Implement generation/AI/admin UI until those phases are approved

## Regenerate docs

```php
kayan_platform()->docs->generate();
```