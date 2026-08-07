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

$SearchArguments = array('field'=>$vars);
$vars['SearchArguments'] = base64_encode(json_encode($SearchArguments));

echo '<div class="-fix-inputs-area" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3></div>';

	echo '<div class="-PhoneNumber-Field-YC">';

		echo '<div class="-Select-Field-Code">';
			echo '<div class="-select-Code-number-title" data-cuntryselect-open="'.$InputName.'" data-cuntry-loded="phone_number"><span>بدون تحديد </span><i class="fas fa-angle-down"></i></div>';
			echo '<div class="-Select-DropDown-PoneNumber">';

				echo '<div class="AjaxSearchCenter">';
					echo '<input type="text" value="" placeholder="البحث السريع " data-input-search-center="'.$Uni.'" data-appender-elem="-ScrollerCenter" data-arguments="'.base64_encode( json_encode( $SearchArguments ) ).'">';
				echo '</div>';

				echo '<ul class="Lists-Select-Items-PoneNumber -ScrollerCenter" data-uniqid="'.$Uni.'"></ul>';
			echo '</div>';
		echo '</div>';


		echo '<input type="number" class="Selected-Value" value="'.$value.'" placeholder="'.$title.'" name="'.$InputName.'" id="'.$id.'">';
	echo '</div>';

	echo ( ( isset( $disc ) ) ) ? '<descor>'.$disc.'</descor>' : '';
echo '</div>';
