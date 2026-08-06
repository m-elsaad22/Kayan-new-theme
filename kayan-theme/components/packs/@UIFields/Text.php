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

echo '<div class="-fix-inputs-area" data-field-id="'.$id.'">';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3>'.( ( isset( $disc ) ) ? '<descor data-tooltip="'.$disc.'"><i class="fa-solid fa-circle-info"></i></descor>' : '').'</div>';
	echo '<input type="text" value="'.$value.'" '.( ( isset( $disabled ) && $disabled == true ) ? 'disabled' : '' ).' placeholder="'.$title.'" name="'.$InputName.'" id="'.$id.'">';

echo '</div>';