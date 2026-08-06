# Programmatic SEO

Architecture + APIs only — generation disabled.

```php
kayan_pseo()->patterns->all();
kayan_pseo()->generator->preview( $pattern, $entities, $country, $lang, $tokens );
kayan_pseo()->generator->materialize( $preview ); // disabled
kayan_pseo()->generator->regenerate_block( $post_id, 'faq', $args ); // stub
```

Prefer existing CPTs (`services`, `faqs`, `pricing`, …); `kayan_pseo` is fallback.