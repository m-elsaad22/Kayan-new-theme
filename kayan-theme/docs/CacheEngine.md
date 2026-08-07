# Cache Engine

Unified API — object cache, transients, future Redis/Memcached drivers.

```php
kayan_cache()->get( $key, $group );
kayan_cache()->set( $key, $value, $ttl, $group );
kayan_cache()->remember( $key, function() { return $expensive; }, 300, 'query' );
kayan_cache()->delete( $key, $group );
kayan_cache()->flush_group( 'query' );
```

Register custom drivers on `kayan_cache_register_drivers` without changing app code.