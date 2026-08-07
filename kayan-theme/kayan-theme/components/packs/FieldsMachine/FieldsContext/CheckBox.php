<?php
if( !isset( $value ) || !isset( $value ) && ( is_string($value) || $value == '' ) ) $value = array();

unset($vars['InsertElements']);
if( isset( $InsertElements ) ){
	$InputName = 'Insert_'.$id;
}else if( isset( $parent_id ) ){
	$InputName = $parent_id.'['.$id.']';
}else{
	$InputName = $id;
}
$value = ( ( is_array( $value ) ) ) ? $value : array();
$vars['vars'] = base64_encode(json_encode($vars));
echo '<div class="-fix-inputs-area" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : '' ).(( isset($InsertElements) ) ? ' data-insert="true"' : '').'>';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3></div>';
	#

	echo '<div class="-CheckBox-Box-InnerArea">';
		foreach ($options as $fky => $fvlue) {
			echo '<div class="-CheckBox-Box-Item">';
				echo '<input'.(( in_array( $fky, $value) ) ? ' checked' : '').' type="checkbox" value="'.$fky.'" name="'.$InputName.'[]" id="'.$id.$fky.'" />';
				echo '<span></span>';
				echo '<em>'.$fvlue.'</em>';
			echo '</div>';
		}
		#
	echo '</div>';
	echo ( ( isset( $disc ) ) ) ? '<descor>'.$disc.'</descor>' : '';
echo '</div>';