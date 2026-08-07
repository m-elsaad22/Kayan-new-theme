# Settings Engine

Scopes: **global**, **country**, **language**, **module**.

```php
kayan_settings()->get_global( 'feature.enabled' );
kayan_settings()->get_country( 'phone', 'ae' );
kayan_settings()->get_language( 'label', 'en' );
kayan_settings()->get_module( 'pseo', 'batch_size' );
kayan_settings()->set( 'phone', '+971…', array( 'scope' => 'country', 'country' => 'ae' ) );
```

Do not call `get_option()` from application modules.