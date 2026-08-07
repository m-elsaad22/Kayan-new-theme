<?php 
if( !isset( $value ) ) $value = '';

$Uni = uniqid();
if( isset( $InsertElements ) ){
	unset($vars['InsertElements']);
	$InputName = 'Insert_'.$id;
}else if( isset( $parent_id ) ){
	$InputName = $parent_id.'['.$id.']';
}else{
	$InputName = $id;
}
$vars['vars'] = base64_encode(json_encode($vars));

echo '<div class="-fix-inputs-area -Yc-Selected-Field" data-field-id="'.$id.'">';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3>'.( ( isset( $disc ) ) ? '<descor data-tooltip="'.$disc.'"><i class="fa-solid fa-circle-info"></i></descor>' : '').'</div>';
	echo '<div class="Select-Options-Items">';
		echo '<input type="text" name="'.$InputName.'" id="'.$InputName.'" value="'.$value.'" style="display:none" class="Selected-Value">';
		echo '<h2 data-select-open="'.$InputName.'">';
			echo '<span>'.(( $value != '' && isset( $options[$value] ) ) ? $options[$value] : 'بدون تحديد ' ).'</span><i class="fas fa-angle-down"></i>';
		echo '</h2>';
		echo '<div class="-Select-DropDown">';
			echo '<ul class="Lists-Select-Items">';
				foreach ($options as $skey => $meky) {
					echo '<li data-title="'.$meky.'" '.( ( isset( $selected_shows ) ) ? 'data-shows-selected="'.$skey.'" data-meta-key="'.$id.'"' : '' ).' '.( ( isset( $create_fields ) ) ? 'data-argums-fields="'.base64_encode(json_encode($create_fields)).'"' : '' ).' data-selected="'.$skey.'" '.(($skey == $value) ? 'class="active"' : '').'>'.$meky.'</li>';
				}
			echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';
