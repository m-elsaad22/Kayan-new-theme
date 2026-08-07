<?php  header("Content-Type: application/json");
ob_start();
$arguments = json_decode(base64_decode($_POST['args']), true);

$Parts = json_decode(base64_decode($_POST['part']), true);

$json = array();
if( !isset($arguments["paged"]) ) $arguments["paged"] = 1;
$arguments["paged"] = $arguments["paged"] + 1;
$Parts['PostsArguments'] = $arguments;
$Parts['AjaxPart'] = true;
$UniqId = $_POST['uniq'];

$json['Parts'] = $Parts;

(new ThemeStatic)->Part('Posts',$Parts);

$Clean = ob_get_clean();

$CutAjax = explode('<CutAjax>', $Clean)[1];
$CutAjax = explode('</CutAjax>', $CutAjax)[0];
$CutAjax = json_decode( $CutAjax,true );
$json['arguments'] = base64_encode( json_encode( $CutAjax['arguments'] ) );
$json['ScrollLoader'] = $CutAjax['ScrollLoader'];

$output = explode('<CutAjax>', $Clean)[0];
$json['output'] = $output;

echo json_encode( $json );