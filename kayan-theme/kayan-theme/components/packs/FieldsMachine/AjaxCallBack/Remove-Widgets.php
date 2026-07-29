<?php
header("Content-Type: application/json");
ob_start();

$json = array();
$Ajax__data['field_arguments'] = json_decode (base64_decode( $Ajax__data['field_arguments'] ) , true);
$Ajax__data['field_arguments']['value'] = $Ajax__data[ $Ajax__data['field_arguments']['id'] ];

if( isset( $Ajax__data['field_arguments']['value'][ $Ajax__data['widgetUniq'] ] ) ){

	unset( $Ajax__data['field_arguments']['value'][ $Ajax__data['widgetUniq'] ] );

	if( $Ajax__data['field_arguments']['update__type'] == 'option' ){

		update_option( $Ajax__data['field_arguments']['id'] , $Ajax__data['field_arguments']['value']  );
		
	}else if ( $Ajax__data['field_arguments']['update__type'] == 'term' && isset( $Ajax__data['tag_ID'] ) ){

		update_term_meta( $Ajax__data['tag_ID'], $Ajax__data['field_arguments']['id'] , $Ajax__data['field_arguments']['value']  );
	}
	#
	wp_delete_post( $Ajax__data['widget__post'] );

	$json['alert'] = 'sucsses';
}else{
	$json['alert'] = 'error';
}
#
echo json_encode($json);