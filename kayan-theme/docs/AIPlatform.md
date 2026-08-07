# AI Platform (Phase 5)

Interchangeable AI provider registry — application code (PSEO block
regeneration, translation) only ever calls `kayan_ai()`, never a concrete
vendor class. Swapping providers requires zero code changes.

Registered providers: `openai`, `claude`, `gemini`, `mistral`, `null`

```php
kayan_ai()->complete( array( 'prompt' => 'Write a headline for {service} in {city}.' ) );
kayan_ai()->translate( $text, 'ar', 'en' );
kayan_ai()->default_provider_id();
kayan_ai()->is_any_available();
kayan_ai()->register_provider( $my_provider ); // implements Kayan_AI_Provider_Interface
```

API keys/model live in the existing Settings Engine (module scope
`ai_{provider_id}`) — configured via the AI admin module. No new options
table.

## PSEO bridge

`Kayan_PSEO_AI` (the existing block/blueprint-shaped contract from Phase
2.5) now defaults to `Kayan_PSEO_AI_Bridge_Provider`, which translates
block regeneration requests into `kayan_ai()->complete()` calls and maps
the response back into each block's data shape. Locked blocks are never
sent for regeneration.