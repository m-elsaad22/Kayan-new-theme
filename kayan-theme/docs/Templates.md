# Templates

PSEO Template Engine assigns page structure via blocks.

Templates: `tpl_service_city`, `tpl_country_service_city`, `tpl_service_area`, `tpl_faq_service`, `tpl_pricing_service`, `tpl_service_country`, `tpl_landmark_service`, `tpl_category_city`, `tpl_brand_service_city`

```php
kayan_pseo()->templates->get( 'tpl_service_city' );
kayan_pseo()->templates->build_block_instances( 'tpl_service_city' );
```

Patterns reference `template_id`. Templates own block order/defaults; the
Renderer turns them into front-end HTML at request time. No admin builder UI
for authoring new templates — extend via `kayan_pseo_register_templates`.