# Dynamic Data Tags

Syntax: `{{tag_name}}`

## Registered tags

- `{{service_name}}`
- `{{city_name}}`
- `{{phone}}`

## Resolve

```php
kayan_tags()->resolve( 'Book {{service_name}} in {{city_name}} — {{phone}}', array(
  'country'  => 'ae',
  'language' => 'ar',
  'entities' => array( 'service' => '123', 'city' => '45' ),
) );
```

Templates and future AI must consume tags instead of hardcoded values.