# Countries

```php
kayan_platform()->countries->all();
kayan_platform()->countries->get_default();
kayan_platform()->countries->normalize( $code );
kayan_settings()->get_country( 'phone', 'ae' );
kayan_platform_setting( 'whatsapp', 'ae' ); // BC helper
```

Country profiles live in Country Settings repository; Settings Engine is the preferred API.

## Admin UI (Phase 3)

The `countries` Admin Platform module edits the business profile
(phone/WhatsApp/currency/SEO/GTM) per existing country — it does not add
or remove countries (those come from kayan-i18n).