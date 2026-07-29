<?php
if( !isset( $value ) ) $value = '';

if( !isset($UniqID) ) $UniqID = uniqid();
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
	echo '<div class="Select-Options-Items">';
		echo '<input type="text" name="'.$InputName.'" id="'.$InputName.'" value="'.$value.'" style="display:none" class="Selected-Value">';
		echo '<h2 data-select-open="'.$InputName.'">';

			$current__name = 'بدون تحديد ';
			if( $value != '' && isset( $options[$value] ) ){
				if( is_array( $options[$value] ) ){
					$active__image = ( ( isset( $options[$value]['image'] ) ) ) ? '<img src="'.$options[$value]['image'].'" alt="'.$options[$value]['image'].'">' : '';
					$active__icon = ( ( isset( $options[$value]['icon'] ) ) ) ? $options[$value]['icon'] : '';
					$current__name = $active__image.$active__icon.$options[$value]['title'];
				}else{
					$current__name = $options[$value];
				}
			}

			echo '<span>'.$current__name.'</span><i class="fas fa-angle-down"></i>';
		echo '</h2>';
		echo '<div class="-Select-DropDown">';
			echo '<ul class="Lists-Select-Items">';
				foreach ($options as $skey => $meky) {
					if( is_array( $meky ) ){
						echo '<li data-uniqid="'.$UniqID.'" data-title="'.$meky['title'].'" '.( ( isset( $selected_shows ) ) ? 'data-shows-selected="'.$skey.'" data-meta-key="'.$id.'"' : '' ).' '.( ( isset( $create_fields ) ) ? 'data-argums-fields="'.base64_encode(json_encode($create_fields)).'"' : '' ).' data-selected="'.$skey.'" class="remove--selected--icon'.(($skey == $value) ? ' active' : '').'">'.( ( isset( $meky['image'] ) ) ? '<img src="'.$meky['image'].'" alt="'.$meky['title'].'">' : '').( ( isset( $meky['icon'] ) ) ? $meky['icon'] : '' ).'<span>'.$meky['title'].'</span></li>';
					}else{
						echo '<li data-uniqid="'.$UniqID.'" data-title="'.$meky.'" '.( ( isset( $selected_shows ) ) ? 'data-shows-selected="'.$skey.'" data-meta-key="'.$id.'"' : '' ).' '.( ( isset( $create_fields ) ) ? 'data-argums-fields="'.base64_encode(json_encode($create_fields)).'"' : '' ).' data-selected="'.$skey.'" '.(($skey == $value) ? 'class="active"' : '').'>'.$meky.'</li>';
					}
				}
			echo '</ul>';
		echo '</div>';
	echo '</div>';
	echo ( ( isset( $disc ) ) ) ? '<descor>'.$disc.'</descor>' : '';
echo '</div>';

if( isset( $create_hide_fields ) ){
	foreach ( $create_hide_fields as $ek => $evalue) {
		if( isset( $parent_id ) ){
			echo '<div class="-Hide-Boxes-Shows Select-Hide-Insert Group-Hide-Insert" data-meta-key="'.$id.'" data-show-type="'.$ek.'" '.( ( $value ==  $ek ) ? '' : 'style="display:none"' ).' data-uniqid="'.$UniqID.'">';
				echo '<div class="Title-MoreForms"><i class="fa-solid fa-sliders"></i><h2>'.(( isset( $options[$ek] ) ) ? $options[$ek] : '').'</h2></div>';
				if( isset( $evalue['fields'] ) && !empty( $evalue['fields'] ) ){
					foreach ( $evalue['fields'] as $kyess => $newfeid) {
						$newfeid['parent_id'] = $parent_id;
						$this->Fields__Part($newfeid['type'],$newfeid);
					}
				}
			echo '</div>';
		}
	}
}