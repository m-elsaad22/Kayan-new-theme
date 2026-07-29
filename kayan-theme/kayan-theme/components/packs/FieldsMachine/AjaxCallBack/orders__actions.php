<?php
header("Content-Type: application/json");
ob_start();
$json = array();
$arguments = json_decode(base64_decode($Ajax__data['args']), true);
if( isset( $arguments['action'] ) && isset( $arguments['postID'] ) ){
	update_post_meta( $arguments['postID'],'activation', $arguments['action'] );
	$json['alert'] = 'done';
}
echo json_encode( $json );