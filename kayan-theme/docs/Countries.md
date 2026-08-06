# Countries

```php
kayan_platform()->countries->all();
kayan_platform()->countries->get_default();
kayan_platform()->countries->normalize( $code );
kayan_settings()->get_country( 'phone', 'ae' );
kayan_platform_setting( 'whatsapp', 'ae' ); // BC helper
```

Country profiles live in Country Settings repository; Settings Engine is the preferred API.