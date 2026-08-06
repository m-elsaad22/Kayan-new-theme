# Admin UI Framework

Reusable components (no duplicated markup):

`card` · `table` · `form` · `field` · `tabs` · `panel` · `dialog` · `drawer` · `notice` · `progress` · `status` · `filters` · `bulk_actions` · `search` · `pagination` · `empty_state`

```php
echo kayan_admin()->ui->card( array(
  'title' => 'Hello',
  'content' => '<p>Body</p>',
  'status' => 'ready',
) );

echo kayan_admin()->ui->table( array(
  'columns' => array( 'name' => 'Name' ),
  'rows' => array(),
) );
```