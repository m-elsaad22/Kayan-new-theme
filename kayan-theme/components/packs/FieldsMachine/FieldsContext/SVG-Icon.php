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

$ModelsPath = (new ThemeStatic)->packsPath.'/SvgCenter/icons';
$options = array(''=>'بدون تحديد ');
$FieldsSetup = array();
foreach( glob($ModelsPath.'/*.php') as $model ) {
	$modelname = explode('SvgCenter/icons/', $model)[1];
	$modelname = explode('/', $modelname)[0];
	$modelname = explode('.php', $modelname)[0];
	$model_ID = $modelname;
	$modelname = str_replace(array('-', '_'), ' ', $modelname);
	$options[ $model_ID ] = ucfirst($modelname);
}
$vars['options'] = $options;
$vars['vars'] = base64_encode(json_encode($vars));
echo '<div class="-fix-inputs-area" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3></div>';
	echo '<div class="Select-Options-Items">';
		echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '<AjaxHTML_Cut>' : '';
			echo '<input type="text" name="'.$InputName.'" id="'.$InputName.'" value="'.$value.'" style="display:none" class="Selected-Value">';
			echo '<h2 data-select-open="'.$InputName.'">';
				echo '<span>'.(( $value != '' ) ? $options[$value] : 'بدون تحديد ').'</span><i class="fas fa-angle-down"></i>';
			echo '</h2>';
			echo '<div class="-Select-DropDown">';
				echo '<ul class="Lists-Select-Items">';
					foreach ($options as $skey => $meky) {
						echo '<li data-title="'.$meky.'" '.( ( isset( $selected_shows ) ) ? 'data-shows-selected="'.$skey.'" data-meta-key="'.$id.'"' : '' ).' '.( ( isset( $create_fields ) ) ? 'data-argums-fields="'.base64_encode(json_encode($create_fields)).'"' : '' ).' data-selected="'.$skey.'" '.(($skey == $value) ? 'class="active"' : '').'>'.$meky.'</li>';
					}
				echo '</ul>';
			echo '</div>';
		echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '</AjaxHTML_Cut>' : '';	
	echo '</div>';
	echo ( ( isset( $disc ) ) ) ? '<descor>'.$disc.'</descor>' : '';
echo '</div>';
