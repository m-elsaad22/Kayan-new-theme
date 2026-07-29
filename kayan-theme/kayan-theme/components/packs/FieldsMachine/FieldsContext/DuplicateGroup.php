<?php
$NewUniq = uniqid();
if( !isset( $value ) || isset( $value ) && ( is_string($value) || $value == '' ) ) $value = array( $NewUniq=>array() );

$value = ( ( is_array( $value ) ) ) ? $value : array();
unset($vars['InsertElements']);
if( isset( $InsertElements ) ){
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
$FieldsKeyes = array();
foreach ($value as $y => $e) {
	$FieldsKeyes[] = $y;
}
$UniqItems = uniqid();

echo '<div class="-DuplicateGroup-widgets '.( ( isset( $Custom_Class ) ) ? $Custom_Class : '' ).'" data-uniq-item="'.$UniqItems.'" '.( ( isset( $parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).' '.( ( isset( $Custom_attrs ) ) ? $Custom_attrs : '' ).'>';	
	
	echo '<div class="Title-MoreForms-Dublicate"><i class="fa-solid fa-pen-to-square"></i><h2>'.$title.'</h2></div>';

	echo '<div class="Title-MoreForms-Dublicate Title-MoreForms-Dublicate-Master" '.( ( count( $value ) > 1 ) ? '' : 'style="display:none;"' ).'>';
		echo '<i class="fa-solid fa-sitemap"></i>';
		echo '<h2>لقد قُمت بتحديد <count-items>'.count( $value ).'</count-items> عنصر حتي الأن </em></h2>';
		echo '<div class="Remove-Dublicate-GroupField" data-remove-itemsgroup="group-item" data-tooltip="حذف كل العناصر "><i class="fa-solid fa-trash-can"></i></div>';
	echo '</div>';


	echo '<div class="-Fields-Insert-DuplicateGroup">';
		$c__i = 0;
		foreach ( $value as $metakey => $metavalue) {$c__i++;
			echo '<div class="-Revilotion-Fields-Dublicate" data-dublicate-group-item="'.$metakey.'">';
				echo '<div class="Title-MoreForms-Dublicate"><i class="fa-solid fa-pen-to-square"></i><h2>الشريحة  <em>['.$metakey.']</em></h2>'.( ( $c__i > 1 ) ? '<div class="Remove-Dublicate-GroupField" data-remove-dublicate-singlegroup="'.$metakey.'" data-tooltip="حذف العنصر '.$metakey.'"><i class="fa-solid fa-trash-can"></i></div>' : '' ).'</div>';
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

	echo '</div>';

	echo '<div class="-Dublicate-create-button"><div class="-Insert-Dublicate-Item" data-dublicateitem-id="'.$UniqItems.'" data-keys-argums="'.base64_encode(json_encode($FieldsKeyes)).'"><i class="fa-solid fa-plus"></i><span>إنشاء عنصر جديد </span></div></div>';

echo '</div>';