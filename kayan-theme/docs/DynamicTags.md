# Dynamic Data Tags

Syntax: `{{tag_name}}`

## Registered tags

- `{{service_name}}`
- `{{city_name}}`
- `{{country_name}}`
- `{{category_name}}`
- `{{language}}`
- `{{faq}}`
- `{{gallery}}`
- `{{featured_image}}`
- `{{price_from}}`
- `{{phone}}`
- `{{whatsapp}}`
- `{{average_rating}}`
- `{{review_count}}`
- `{{related_services}}`
- `{{related_articles}}`
- `{{cta_title}}`
- `{{hero_title}}`
- `{{meta_title}}`

## Resolve

```php
kayan_tags()->resolve( 'Book {{service_name}} in {{city_name}} — {{phone}}', array(
  'country'  => 'ae',
  'language' => 'ar',
  'entities' => array( 'service' => '123', 'city' => '45' ),
) );
```

Templates and future AI must consume tags instead of hardcoded values.