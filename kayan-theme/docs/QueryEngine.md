# Query Engine

Centralized data access. Resources: `service`, `article`, `city`, `country`, `area`, `district`, `faq`, `review`, `portfolio`, `pricing`, `programmatic_page`, `category`, `user`, `page`, `before_after`

```php
kayan_query()->services( array( 'number' => 10 ) );
kayan_query()->get( 'service', $slug_or_id );
kayan_query()->meta( $post_id, 'kayan_public_slug' );
kayan_query()->cities();
kayan_query()->programmatic_pages();
kayan_query()->flush();
```

Cache-ready via Cache Engine group `query`.