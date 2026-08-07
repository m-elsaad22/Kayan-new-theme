# Logger

Channels: `ai`, `generator`, `queue`, `seo`, `errors`, `performance`, `security`, `general`.

```php
kayan_logger()->info( 'seo', 'bridge ready' );
kayan_logger()->error( 'errors', 'resolve failed', array( 'slug' => $slug ) );
kayan_logger()->ai( 'prompt built', array( 'block' => 'faq' ) );
kayan_logger()->generator( 'preview ok' );
kayan_logger()->queue( 'job enqueued', array( 'id' => $id ) );
kayan_logger()->performance( 'query slow', array( 'duration_ms' => 120 ) );
kayan_logger()->security( 'capability denied' );
kayan_logger()->time( 'heavy', function() { /* … */ } );
```