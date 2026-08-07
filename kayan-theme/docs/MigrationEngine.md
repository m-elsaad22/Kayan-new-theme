# Migration & Version Engine

Generic upgrade infrastructure — no manual upgrade step is ever required.
Runs automatically (`after_switch_theme` + a cheap cached-version check on
every `boot()`) and is idempotent, incremental, and logged.

```php
kayan_migrations()->register_migration( 'my_pack_v1', array(
  'version'          => 10,
  'type'             => 'table', // schema|table|option|meta|taxonomy|rewrite
  'description'      => 'Create my_pack table',
  'rollback_options' => array( 'my_pack_option' ), // auto-snapshotted before `up` runs
  'up'               => function( $engine ) {
    return $engine->create_or_upgrade_table( 'my_pack_table', 'id BIGINT NOT NULL AUTO_INCREMENT, PRIMARY KEY (id)' );
  },
) );

kayan_migrations()->run();               // safe to call anytime; skips already-applied migrations
kayan_migrations()->rollback( 'my_pack_v1' );
kayan_migrations()->history();           // paginated history table (also visible in System Health)
kayan_migrations()->current_version();
kayan_migrations()->target_version();
```

Hook: `kayan_migrations_register` — other packs (including existing
booking/payment/track, which keep their own working version checks) MAY
register through this engine later; nothing is forced.

Backs the PSEO Queue: the `kayan_pseo_queue` table is created by the
`pseo_queue_table_v1` core migration, not by application code.