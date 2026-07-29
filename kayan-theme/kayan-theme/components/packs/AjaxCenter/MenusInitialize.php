<?php 
header("Content-Type: application/json");
ob_start();

$json = array();
$_POST['args'] = json_decode( base64_decode( $_POST['args'] ),true );
$json['kk'] = $_POST['args'];
$this->ThemeStatic->Blade('Mega-Menu',$_POST['args'],$_POST['args']['Blade_ID']);
$html = ob_get_clean();
$json['output'] = $html;

echo json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);