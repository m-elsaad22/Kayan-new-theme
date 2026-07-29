<?php 
$ModeVariable = array();
if( isset( $AjaxPart ) && $AjaxPart != false ) {

	$ModeVariable['icon'] = ( ( isset( $__Ajax_empty_icon ) ) ) ? $__Ajax_empty_icon : '';
	$ModeVariable['title'] = ( ( isset( $__Ajax_empty_title ) ) ) ? $__Ajax_empty_title : '';
	$ModeVariable['description'] = ( ( isset( $__Ajax_empty_description ) ) ) ? $__Ajax_empty_description : '';
}else{
	$ModeVariable['icon'] = ( ( isset( $__empty_icon ) ) ) ? $__empty_icon : '';
	$ModeVariable['title'] = ( ( isset( $__empty_title ) ) ) ? $__empty_title : '';
	$ModeVariable['description'] = ( ( isset( $__empty_description ) ) ) ? $__empty_description : '';
}

echo '<div class="--empty-attchment-area">';
	echo '<div class="--empty-attchment-svg-v1">';
		echo ( ( $ModeVariable['icon'] != '' ) ) ? $__empty_icon : '<i class="fa-solid fa-ban"></i>';
	echo '</div>';

	echo '<h1>'.$ModeVariable['title'].'</h1>';
	echo '<p>'.$ModeVariable['description'].'</p>';

echo '</div>';