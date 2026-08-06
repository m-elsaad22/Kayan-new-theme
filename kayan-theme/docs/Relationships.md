# Relationships

Meta contract: `kayan_entity_relationships`

```php
kayan_entity()->relate( 'service', $sid, 'city', $cid, 'serves', array( 'bidirectional' => true ) );
kayan_entity()->related( 'service', $sid, 'city' );
kayan_entity()->relationships->has( 'service', $sid, 'city', $cid );
```

Edge shape: `{ type, ref, rel, meta }`

Legacy bridge: city term meta `kayan_country` → `located_in` country (read path).