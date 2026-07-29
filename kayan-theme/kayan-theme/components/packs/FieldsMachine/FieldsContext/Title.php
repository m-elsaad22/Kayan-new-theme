<?php
if( !isset( $value ) ) $value = '';

if( isset( $InsertElements ) ){
	unset($vars['InsertElements']);
	$InputName = 'Insert_'.$id;
}else if( isset( $parent_id ) ){
	$InputName = $parent_id.'['.$id.']';
}else{
	$InputName = $id;
}

$vars['vars'] = base64_encode(json_encode($vars));

echo '<div class="-fix-inputs-area" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';
	echo '<div class="-fix-forms-title-fields">'.( ( isset( $icon ) ) ? $icon : '<i class="fa-solid fa-circle-dot"></i>').'<div class="-fix-form-InnerTitle"><h3>'.$title.'</h3>'.( ( isset( $disc ) ) ? '<descor>'.$disc.'</descor>' : '').'</div></div>';
echo '</div>';