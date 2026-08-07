<?php
header("Content-Type: application/json");
ob_start();

$json = array();
if( isset( $_POST['Arguments'] ) ){

	$_POST['Arguments'] = json_decode( base64_decode( $_POST['Arguments'] ),true );
	$TableName = new $_POST['Arguments']['TableName'];
	$New_CourseID = $TableName->RemoveID($_POST['remove__id']);
	$json['success'] = true;
}

echo json_encode($json);