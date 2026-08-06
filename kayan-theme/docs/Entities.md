# Entities

Canonical entity types:

`country`, `city`, `area`, `district`, `neighborhood`, `landmark`, `service`, `category`, `faq`, `pricing`, `review`, `portfolio`, `gallery`, `video`, `article`, `brand`, `building`, `before_after`

## API

```php
kayan_entity()->get( 'service', $ref );
kayan_entity()->name( 'city', $ref );
kayan_entity()->field( 'service', $ref, 'price_from' );
kayan_entity()->api->query( 'service', array( 'number' => 10 ) );
kayan_entity()->api->get_media( 'service', $ref );
```

Prefer Entity API / Query Engine over `get_post_meta()`.