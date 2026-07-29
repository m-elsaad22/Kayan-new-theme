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
$custom_editor_id = $id."_".rand();
echo '<div class="-fix-inputs-area" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3></div>';


  echo '<div id="editor" class="LoadingEditors EditorProduct" data-input="'.$id.'">';
  	echo '<div class="LoadingEditor">';
  		echo '<div class="loadingHides">جاري تحميل المحرر .. برجاء الانتظار</div>';
  		echo '<h2>جاري تحميل المحرر .. برجاء الانتظار</h2>';
  	echo '</div>';

  	echo '<div auto-editor="'.$custom_editor_id.'">';
		$args = array('textarea_name' => $InputName);
		wp_editor( $value, $custom_editor_id, $args );
  	echo '</div>';

  echo '</div>';
  echo ( ( isset( $disc ) ) ) ? '<descor>'.$disc.'</descor>' : '';
echo '</div>';