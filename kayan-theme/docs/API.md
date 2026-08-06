# Public API

## Facades

```php
kayan_platform();          // container
kayan_entity();            // Entity Relationship Engine
kayan_tags();              // Dynamic Data Tags
kayan_pseo();              // Programmatic SEO Engine
kayan_query();             // Query Engine
kayan_cache();             // Cache Engine
kayan_settings();          // Settings Engine
kayan_logger();            // Logger
kayan_platform_url();      // language-first URLs
kayan_platform_setting();  // country setting helper (BC)
```

## Describe contracts

```php
kayan_platform()->entity->describe();
kayan_platform()->pseo->describe();
kayan_platform()->query->describe();
kayan_platform()->cache->describe();
kayan_platform()->settings_engine->describe();
kayan_platform()->logger->describe();
kayan_tags()->describe();
```