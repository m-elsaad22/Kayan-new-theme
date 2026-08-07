# Entities

Canonical entity types:

`country`, `city`, `area`, `district`, `neighborhood`, `brand`, `building`, `service`, `category`, `faq`, `landmark`, `pricing`, `review`, `portfolio`, `before_after`, `gallery`, `video`, `article`

## API

```php
kayan_entity()->get( 'service', $ref );
kayan_entity()->name( 'city', $ref );
kayan_entity()->field( 'service', $ref, 'price_from' );
kayan_entity()->api->query( 'service', array( 'number' => 10 ) );
kayan_entity()->api->get_media( 'service', $ref );
```

Prefer Entity API / Query Engine over `get_post_meta()`.