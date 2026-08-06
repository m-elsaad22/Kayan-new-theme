<?php header("Content-Type: application/json");
ob_start();

$json = array();
$this->ThemeStatic->Blade($_POST['blade'], ['_POST'=>$_POST],$_POST['shape'] );

$output = ob_get_clean();

if( strpos( $output, '<Ex___Cut___Ajax>') !== FALSE ){

	$Next_output = explode('<Ex___Cut___Ajax>', $output)[1];
	$Next_output = explode('</Ex___Cut___Ajax>', $Next_output)[0];
	$json['Next_output'] = $Next_output;
	echo json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
}
