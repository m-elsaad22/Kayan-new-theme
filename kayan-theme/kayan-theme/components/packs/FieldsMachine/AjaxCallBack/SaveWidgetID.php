<?php
header("Content-Type: application/json");
ob_start();

$json = array();

$Ajax__data = YC_stripslashes_deep($Ajax__data);
$Ajax__data['field_arguments'] = json_decode (base64_decode( $Ajax__data['field_arguments'] ) , true);
$Ajax__data['field_arguments']['value'] = $Ajax__data[ $Ajax__data['field_arguments']['id'] ];

if( $Ajax__data['field_arguments']['update__type'] == 'option' ){

	update_option( $Ajax__data['field_arguments']['id'] , $Ajax__data['field_arguments']['value']  );

}else if ( $Ajax__data['field_arguments']['update__type'] == 'term' && isset( $Ajax__data['tag_ID'] ) ){
	update_term_meta( $Ajax__data['tag_ID'], $Ajax__data['field_arguments']['id'] , $Ajax__data['field_arguments']['value']  );

}else if ( $Ajax__data['field_arguments']['update__type'] == 'post' && isset( $Ajax__data['post_ID'] ) ){
	update_post_meta( $Ajax__data['post_ID'], $Ajax__data['field_arguments']['id'] , $Ajax__data['field_arguments']['value']  );
}
#
update_post_meta( $Ajax__data['widget__post'], 'widget_post_meta', $Ajax__data[ $Ajax__data['field__id'] ] );
// # 
$Vars__Blade = array_merge($Ajax__data['field_arguments'],array('widget__post'=>$Ajax__data['widget__post'],'single_widget__uniq'=>$Ajax__data['single_widget__uniq']));

$this->AdminPart('open_widgets_UI',$Vars__Blade);
#
$html__output = ob_get_clean();
$json['output'] = $html__output;
#
echo json_encode($json);