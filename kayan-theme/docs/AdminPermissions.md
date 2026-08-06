# Admin Permissions

Access capability: `kayan_access_admin`

## Roles

- Administrator
- SEO Manager (`kayan_seo_manager`)
- Content Manager (`kayan_content_manager`)
- Editor (caps granted to WP `editor`)
- Translator (`kayan_translator`)
- Marketing (`kayan_marketing`)
- Developer (`kayan_developer`)
- Custom roles via `register_role()`

```php
kayan_admin()->permissions->can( 'kayan_manage_pseo' );
kayan_admin()->permissions->register_capability( 'kayan_custom', array( 'label' => 'Custom' ) );
kayan_admin()->permissions->register_role( 'kayan_custom_role', array(
  'label' => 'Custom Role',
  'create_wp_role' => true,
  'capabilities' => array( 'kayan_access_admin', 'kayan_custom' ),
) );
```