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
echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '<AjaxHTML_Cut>' : '';
echo '<div class="-fix-inputs-area --radio--fields" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';
	echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3></div>';
	#
	echo '<div class="-Radio-Box-InnerArea">';
		foreach ($options as $fky => $fvlue) {
			echo '<div class="-Radio-Box-Item" '.(( isset( $Custom_says ) ) ? ' data-argums-say="'.$fky.'"' : '' ).' data-curr-value-field="'.$value.'">';
				echo '<input'.(($fky == $value) ? ' checked' : '').' type="radio" value="'.$fky.'" name="'.$InputName.'" id="'.$id.$fky.'" />';
				echo '<span></span>';
				echo '<em>'.$fvlue.'</em>';
			echo '</div>';
		}
		#
		if( isset( $Custom_says ) ){
			echo '<div class="Says-Field-Argums">';
				echo '<div class="Sayes-fields-ShowsIn"><i class="fas fa-eye"></i><span>مثال على اختياري </span></div>';
				
				echo '<div class="-Sayes-Fields-Context" style="display:none">';
					foreach ($options as $skey => $meky) {
						$NewFieldsArgums = array(
							'type' => $skey,
							'id' => 'test_'.$skey,
							'title' => 'تجربة نوع الادخال '.$meky,
							'value' => '',
						);

						if( $skey == 'Radio' || $skey == 'CheckBox' || $skey == 'Select' ){
							for ($i=1; $i < 11; $i++) { 
								$NewFieldsArgums['options'][$i] = 'الاختيار رقم '.$i;
								if( $i == 1 ){
									if( $skey == 'CheckBox' ){
										$NewFieldsArgums['value'] = array();
										$NewFieldsArgums['value'][] = $i;
									}else{
										$NewFieldsArgums['value'] = $i;
									}
								}
							}
						}else if( $skey == 'Taxonomy-Select' || $skey == 'Taxonomy-CheckBox' || $skey == 'Taxonomy-Radio' ){
							$NewFieldsArgums['taxonomy_name'] = 'category';
							$NewFieldsArgums['taxonomy_field'] = 'term_id';
							if( $skey == 'Taxonomy-CheckBox' ){
								$NewFieldsArgums['value'] = array();
							}							
						}
						echo '<div class="Says-Single-Field" data-say-id="'.$skey.'" '.(($skey != $value) ? 'style="display:none;"' : '').'>';
							$this->Fields__Part($NewFieldsArgums['type'],$NewFieldsArgums);
						echo '</div>';
					}
				echo '</div>';
			echo '</div>';
		}
		echo ( ( isset( $disc ) ) ) ? '<descor>'.$disc.'</descor>' : '';
	echo '</div>';

echo '</div>';
if( isset( $shows_selected ) ){
	foreach ($show_create_fields as $e => $v) {
		if( isset( $InsertElements ) ){
			$v['InsertElements'] = true;
		}
		if( isset( $parent_id ) ){
			$v['parent_id'] = $parent_id;
		}
		echo '<field-inserts class="Insert-SelectOptions" data-field-id="'.$v['id'].'" data-id="'.base64_encode( json_encode( $v['shows_At'] ) ).'" '.( ( in_array($value, $v['shows_At']) ) ? '' : 'style="display:none"' ).'>';
			$this->Fields__Part($v['type'],$v);
		echo '</field-inserts>';
	}
}
echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '</AjaxHTML_Cut>' : '';