<?php
header("Content-Type: application/json");
ob_start();
#
$json = array();
#
$Ajax__data['field_arguments'] = json_decode (base64_decode( $Ajax__data['field_arguments'] ) , true);
$Vars__Blade = array_merge($Ajax__data['field_arguments'],array('widget__post'=>$Ajax__data['widget__post'],'single_widget__uniq'=>$Ajax__data['single_widget__uniq']));

$this->AdminPart('open_widgets_UI',$Vars__Blade);
#
$html__output = ob_get_clean();
$json['output'] = $html__output;
#
echo json_encode($json);