<?php
header("Content-Type: application/json");
ob_start();
$argums = json_decode(base64_decode($Ajax__data['argums']), true);
extract($argums);
$json['test'] = $argums;
if( !isset( $argums['value'] ) ) $argums['value'] = '';
$custom_editor_id = $id."_".$Ajax__data['UniqID'];
$json['EditorID'] = $custom_editor_id;
echo '<div auto-editor="'.$custom_editor_id.'">';
	$args = array(
    'textarea_name' => $Ajax__data['InputName'],
    'textarea_rows' => 3
  );
	wp_editor( $argums['value'], $custom_editor_id, $args );
echo '</div>';
$output = ob_get_clean();

$json['output'] = $output;

ob_start();
_WP_Editors::enqueue_scripts();
print_footer_scripts();
_WP_Editors::editor_js();

$js = ob_get_clean();

//$json['js'] = $js;

echo json_encode($json);