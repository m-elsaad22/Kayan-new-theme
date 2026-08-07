<?php
header("Content-Type: application/json");
ob_start();
#
$html = '';
$json = array();
if( isset( $Ajax__data['insert_term_taxonomy'] ) && isset( $Ajax__data['inser_term_name'] ) ){
	$Arguments = array();
	if( isset( $Ajax__data['inser_term_slug'] ) && !empty( $Ajax__data['inser_term_slug'] ) ) {
		$Arguments['slug'] = $Ajax__data['inser_term_slug'];
	}

	if( isset( $Ajax__data['insert_term_parent'] ) ) {
		$Arguments['parent'] = $Ajax__data['insert_term_parent'];
	}

	if( isset( $Ajax__data['inser_term_description'] ) ) {
		$Arguments['description'] = $Ajax__data['inser_term_description'];
	}

	if( !empty( $Arguments ) ){
		$insert = wp_insert_term($Ajax__data['inser_term_name'] , $Ajax__data['insert_term_taxonomy'] , $Arguments );
	}else{
		$insert = wp_insert_term($Ajax__data['inser_term_name'] , $Ajax__data['insert_term_taxonomy'] );
	}

	if(is_wp_error($insert)){
		$get_error_message = $insert->get_error_message();
		$json['message'] =  '<p>'.$get_error_message.'</p>';
		$json['alert'] =  'error';	
	}else{
		$json['alert'] =  'sucsses';
		$json['message'] = '<p>تم الإضافة بنجاح </p>';
		$term = get_term_by('id',$insert['term_id'],$Ajax__data['insert_term_taxonomy']);
		if( $Ajax__data['insert_term_output'] == 'filterCats' ){
			echo '<li data-id="'.$term->term_id.'">';
				echo '<em class="ChecBx"></em>';
				echo '<span>'.$term->name.'</span>';
				echo '<attr>0</attr>';
			echo '</li>';
		}else if( $Ajax__data['insert_term_output'] == 'filters' || $Ajax__data['insert_term_output'] == 'select_filters' ){
			$U = uniqid();
			echo '<div class="-CheckBox-Box-Item">';
				echo '<input type="checkbox" value="'.$term->term_id.'" name="'.$Ajax__data['insert_term_output'].'[]" id="'.$Ajax__data['insert_term_output'].$U.'" />';
				echo '<span></span>';
				echo '<em>'.$term->name.'</em>';
			echo '</div>';

		}
		$html = ob_get_clean();
	}



}else{
	$json['message'] =  '<p>حدث خطأ ما إثناء عملية الاضافة .. حاول مرة اخري </p>';
	$json['alert'] =  'error';	
}

if( !empty( $html ) ){
	$json['output'] = $html;
}
 
echo json_encode($json);