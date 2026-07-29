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
echo '<div class="-fix-inputs-area -for-date-field" data-field-id="'.$id.'">';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3>'.( ( isset( $disc ) ) ? '<descor data-tooltip="'.$disc.'"><i class="fa-solid fa-circle-info"></i></descor>' : '').'</div>';
	echo '<input type="date" data-language="en" class="DatePreview" data-time="'.( ( isset( $time ) && $time == true ) ? 'true' : 'false').'" data-format="'.( ( isset( $format ) ) ? $format : 'd-m-Y H:i:s').'" name="'.$InputName.'" id="'.$id.'" value="'.$value.'" spellcheck="false" attrtype="text" attrname="'.$id.'">';
echo '</div>';
