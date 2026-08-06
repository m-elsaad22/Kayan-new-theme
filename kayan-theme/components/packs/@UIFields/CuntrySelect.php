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
echo '<div class="-fix-inputs-area -Yc-Selected-Field -Select-Cuntryes-Item" data-field-id="'.$id.'">';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3>'.( ( isset( $disc ) ) ? '<descor data-tooltip="'.$disc.'"><i class="fa-solid fa-circle-info"></i></descor>' : '').'</div>';
	echo '<div class="Select-Options-Items">';
		echo '<input type="text" name="'.$InputName.'" id="'.$InputName.'" value="'.$value.'" style="display:none" class="Selected-Value">';
		echo '<h2 data-select-open="'.$InputName.'" data-cuntry-loded="'.$type.'" data-value="'.$value.'">';
			echo '<span>'.(( $value != '' && isset( $options[$value] ) ) ? $options[$value] : 'بدون تحديد ' ).'</span><i class="fas fa-angle-down"></i>';
		echo '</h2>';
		echo '<div class="-Select-DropDown">';

			echo '<div class="AjaxSearchCenter">';
				echo '<input type="text" value="" placeholder="البحث السريع " data-input-search-center="'.$Uni.'" data-appender-elem="-ScrollerCenter" data-field-type="'.$type.'">';
			echo '</div>';

			echo '<ul class="Lists-Select-Items -ScrollerCenter" data-uniqid="'.$Uni.'"></ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';
