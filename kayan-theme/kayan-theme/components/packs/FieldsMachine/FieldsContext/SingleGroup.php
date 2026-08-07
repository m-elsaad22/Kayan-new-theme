<?php global $ThemeStatic;

if( !isset( $value ) || isset( $value ) && ( is_string($value) || $value == '' ) ) $value = array();
if( !isset( $is__open ) ) $is__open = false;

$value = ( ( is_array( $value ) ) ) ? $value : array();

if( isset( $InsertElements ) ){
	unset($vars['InsertElements']);
	$InputName = 'Insert_'.$id;
}else if( isset( $parent_id ) ){
	$InputName = $parent_id.'['.$id.']';
}else{
	$InputName = $id;
}
$UniqId = uniqid();

$ImportantValuesArray = array('CheckBox','File','GroupsField','Users-CheckBox','Posts-CheckBox','Taxonomy-CheckBox');
#
$vars['vars'] = base64_encode(json_encode($vars));
#
echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '<AjaxHTML_Cut>' : '';
	echo '<div class="-fix-inputs-area fix-Inputs-SingleGroupField '.( ( isset( $Custom_Class ) ) ? $Custom_Class : '' ).'" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).' '.( ( isset( $Custom_attrs ) ) ? $Custom_attrs : '' ).'>';

		echo '<div class="-YC-SingleGroup-Item-v1'.( ( $is__open == true ) ? ' active' : '').'">';

			echo '<div class="-YC-SingleGroup-Title" data-toggle-singlegroup="'.$UniqId.'">';
				echo '<h2>'.$title.( ( isset( $disc ) ) ? '<p>'.$disc.'</p>' : '' ).'</h2>';
				echo '<i class="fa-solid fa-plus"></i>';
			echo '</div>';

			echo '<div class="-SingleGroup-Content-Row-v1 -Toggle-Content">';
				echo '<div class="-p-ContentValue-v1 -ToggleContentValue">';
					foreach ($fields as $k => $v ) {
						$v['parent_id'] = $InputName;
						if( !empty( $value ) && isset( $value[ $v['id'] ] ) ){
							$v['value'] = $value[ $v['id'] ];
						}

						if( isset( $value[ $v['id'] ] ) ){
							if( isset( $value[ $v['id'].'_id' ] ) ){
								$v['value']['url'] = $value[ $v['id'] ];
								$v['value']['id'] = $value[ $v['id'].'_id' ];
							}else {
								$v['value'] = $value[ $v['id'] ];
							}
						}else{
							$v['value'] = ( ( in_array( $v['type'] , $ImportantValuesArray) ) ) ? array() : '';
						}

						if( isset( $v['show_create_fields'] ) ){
							$nFF = $v['show_create_fields'];
							foreach ($nFF as $d => $w) {
								if( isset( $value[ $w['id'] ] ) ){
									$v['show_create_fields'][$d]['value'] = $value[ $w['id'] ];
								}
							}
						}

						$this->Fields__Part($v['type'],$v);
					}
				echo '</div>';
			echo '</div>';

		echo '</div>';
	echo '</div>';
echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '</AjaxHTML_Cut>' : '';