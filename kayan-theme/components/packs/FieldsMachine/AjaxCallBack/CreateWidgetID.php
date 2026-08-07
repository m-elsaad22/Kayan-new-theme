<?php
header("Content-Type: application/json");
ob_start();
	
	$post_id = wp_insert_post( 
		array(
			'post_title'=>$Ajax__data['widget_type'].'_'.$Ajax__data['uniqKey'].'_'.$Ajax__data['input'],
			'post_type'=>'widgets__posts',
		)
	);

	update_post_meta($post_id,'widget_type',$Ajax__data['widget_type']);

$json['post_id'] = $post_id;
echo json_encode($json);