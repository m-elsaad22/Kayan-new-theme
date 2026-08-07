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
$vars['vars'] = base64_encode(json_encode($vars));
echo '<div class="-fix-inputs-area" data-field-id="'.$id.'">';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3>'.( ( isset( $disc ) ) ? '<descor data-tooltip="'.$disc.'"><i class="fa-solid fa-circle-info"></i></descor>' : '').'</div>';
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
echo '</div>';