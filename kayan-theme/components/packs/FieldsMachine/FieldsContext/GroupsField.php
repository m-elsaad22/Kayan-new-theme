<?php global $ThemeStatic;

if( !isset( $value ) || !isset( $value ) && ( is_string($value) || $value == '' ) ) $value = array();

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
echo '<div class="-fix-inputs-area fix-Inputs-GroupField '.( ( isset( $Custom_Class ) ) ? $Custom_Class : '' ).'" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).' '.( ( isset( $Custom_attrs ) ) ? $Custom_attrs : '' ).'>';	
	echo '<div class="InputsAppender--Fields-BoxArea -FieldTo-'.$id.'">';
		echo '<div class="Title-MoreForms"><i class="fa-solid fa-store"></i><h2>'.$title.'</h2></div>';
		$FieldsKeyes = array();
		foreach ($value as $y => $e) {
			$FieldsKeyes[] = $y;
		}
		#
		echo '<div class="-Create-More-Fields">';
			if( !isset( $hide__more ) || isset( $hide__more ) && $hide__more == false ){
				echo '<div class="Title-MoreForms"><i class="fa-solid fa-plus"></i><h2>إضافة عنصر جديد </h2></div>';
			}
			
			echo '<div class="-Insert-Fields-Tool">';
				$UniqItems = uniqid();
				if( !isset( $hide__more ) || isset( $hide__more ) && $hide__more == false ){
					echo '<div class="Insert-Setp-InForm" data-uniq-item="'.$UniqItems.'" data-title="لقد قُمت بتحديد  _Counter_  عنصر حتي الأن ">';

						foreach ($fields as $k => $v) {
							$v['parent_id'] = $InputName;
							$v['InsertElements'] = true;
							$this->Fields__Part($v['type'],$v);
						}

					echo '</div>';
				}
				echo '<div class="-row-create-button"><div class="-Insert-Form-Item" data-item-id="'.$UniqItems.'" data-keys-argums="'.base64_encode(json_encode($FieldsKeyes)).'"><i class="fa-solid fa-plus"></i><span>إنشاء عنصر جديد </span></div></div>';

				echo '<div class="-Fields-Insert-Area">';
					if( !empty( $value ) ){
						echo '<div class="Title-MoreForms">';
							echo '<i class="fa-solid fa-sitemap"></i>';
							echo '<h2>لقد قُمت بتحديد <count-items>'.count( $value ).'</count-items> عنصر حتي الأن </em></h2>';
							echo '<div class="Remove-GroupField" data-remove-itemsgroup="group-item" data-tooltip="حذف كل العناصر "><i class="fa-solid fa-trash-can"></i></div>';
						echo '</div>';
						foreach ( $value as $metakey => $metavalue) {
							echo '<div class="-Revilotion-Inputs-Fields" data-group-item="'.$metakey.'" data-field-type="">';
								echo '<div class="Title-MoreForms"><i class="fa-solid fa-pen-to-square"></i><h2>العنصر <em>['.$metakey.']</em></h2><div class="Remove-GroupField" data-remove-singlegroup="'.$metakey.'" data-tooltip="حذف العنصر '.$metakey.'"><i class="fa-solid fa-trash-can"></i></div></div>';
								#
								foreach ($fields as $k => $v) {
									$v['parent_id'] = $InputName.'['.$metakey.']';

									if( isset( $metavalue[ $v['id'] ] ) ){
										if( isset( $metavalue[ $v['id'].'_id' ] ) ){
											$v['value']['url'] = $metavalue[ $v['id'] ];
											$v['value']['id'] = $metavalue[ $v['id'].'_id' ];
										}else {
											$v['value'] = $metavalue[ $v['id'] ];
										}
									}else{
										$v['value'] = ( ( in_array( $v['type'] , $ImportantValuesArray) ) ) ? array() : '';
									}

									if( isset( $v['show_create_fields'] ) ){
										$nFF = $v['show_create_fields'];
										foreach ($nFF as $d => $w) {
											if( isset( $metavalue[ $w['id'] ] ) ){
												$v['show_create_fields'][$d]['value'] = $metavalue[ $w['id'] ];
											}
										}
									}
									$this->Fields__Part($v['type'],$v);
								}
								#
							echo '</div>';
						}
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';