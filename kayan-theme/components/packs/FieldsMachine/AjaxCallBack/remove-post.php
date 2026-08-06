<?php header("Content-Type: application/json");
ob_start();
$json = array();
if( isset( $Ajax__data['removedID'] ) ){
	$RemoveList = array();
	if( strpos($Ajax__data['removedID'],',') !== FALSE ){
		$RemoveList = explode(',', $Ajax__data['removedID']);
	}else{
		$RemoveList[] = $Ajax__data['removedID'];
	}
	foreach ($RemoveList as $post_id) {
		$post = get_post($post_id);
		if( isset( $post->ID ) ){
			delete_post_meta($post->ID, 'video_id');
			delete_post_meta($post->ID, 'character_id');
			wp_delete_post($post->ID);
			$json['type'] = 'sucsses';
		}		
	}

	if( isset( $Ajax__data['location'] ) && $Ajax__data['location'] != 'stay' ){
		$json['reload__page'] = $Ajax__data['location'];
	}


}else{
	$json['type'] = 'error';
}
echo json_encode($json);