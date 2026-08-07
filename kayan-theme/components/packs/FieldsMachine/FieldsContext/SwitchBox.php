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
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3></div>';
	#
	echo '<SwitchField data-field="'.$id.'">';
		echo '<input type="checkbox" value="on" '.(( $value == 'on' ) ? 'checked' : '').' name="'.$InputName.'" id="'.$id.'YC_CheckBox" />';
		echo '<div class="Switch">';
			echo '<span>معطّل</span>';
			echo '<strong>مفعّل</strong>';
			echo '<em></em>';
		echo '</div>';
		echo '<SwitchName>'.(( isset( $disc ) ) ? $disc : $title ).'</SwitchName>';
	echo '</SwitchField>';
echo '</div>';