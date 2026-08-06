<?php header("Content-Type: application/json");
ob_start();

$json = array();
#$_POST['Arguments'] = 'eyJBY3Rpb25CbGFkZSI6ImZvcm1fc2VydmljZXMiLCJDdXJyZW50VmFyIjoiU2VydmljZXNGb3JtIn0=';
$_POST['Arguments'] = json_decode( base64_decode( $_POST['Arguments']) ,true );

$json['Arguments'] = $_POST['Arguments'];

if( !isset( $_POST['Arguments']['blade'] ) ) $_POST['Arguments']['blade'] = 'Popovers';

$this->ThemeStatic->Blade($_POST['Arguments']['blade'], $_POST['Arguments'],$_POST['Arguments']['ActionBlade'] );

$output = ob_get_clean();
$json[ 'output' ] = $output;

echo json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );