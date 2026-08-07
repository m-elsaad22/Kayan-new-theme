<?php 
function FieldsJson($data){
	$newFields = $data;
	foreach ($newFields as $k => $v) {
		$data[$k]['vars'] = base64_encode( json_encode( $v ) );
		if( isset( $v['fields'] ) ){
			$data[$k]['fields'] = FieldsJson($v['fields']);
		}
		if( isset( $v['choose_fields']['fields'] ) ){
			$data[$k]['choose_fields']['fields'] = FieldsJson($v['choose_fields']['fields']);
		}
	}
	return $data;
}