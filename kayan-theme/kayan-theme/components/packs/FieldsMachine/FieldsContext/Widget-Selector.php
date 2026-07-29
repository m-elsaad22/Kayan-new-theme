<?php $UniqID = uniqid();
#print_r($vars);die;
if( !isset( $select_field['options'] ) && isset( $ModelCenter ) ){
	global $yc__widgets__selector;

	$FieldsSetup = array();
	$models = array(''=>'بدون تحديد ');

	if( isset( $yc__widgets__selector[$ModelCenter] ) && isset( $yc__widgets__selector[$ModelCenter]['Packs'] ) ){

		foreach ( $yc__widgets__selector[$ModelCenter]['Packs'] as $model__name => $fields ){

			if( is_array( $fields ) && isset( $fields['fields'] ) ){
				$FieldsSetup[ $model__name ] = $fields;
				$models[ $model__name ] = $fields['title'];
			}else{
				$models[ $model__name ] = $fields['title'];
			}

		}

	}
	$select_field['options'] = $models;
	$choose_fields = $FieldsSetup;

	# UPDATE VARS .
		$vars['select_field']['options'] = $select_field['options'];
		$vars['choose_fields'] = $choose_fields;
}

if( !isset( $value ) || !is_array($value) ) $value = array();


if( !isset( $value[ $select_field['id'] ] ) ) $value[ $select_field['id'] ] = '';

if( isset( $create_fields ) ) $select_field['create_fields'] = $vars;

$select_field['value'] = $value[ $select_field['id'] ];
$select_field['UniqID'] = $UniqID;
$this->Fields__Part($select_field['type'],$select_field);
#
$choose_fields = ( is_array( $choose_fields ) ) ? $choose_fields : array();
foreach ($choose_fields as $shape => $data) {

	if( $data['id'] == $value[ $select_field['id'] ] && isset( $create_fields ) || !isset( $create_fields ) ){
		$InputName = $id.'['.$data['id'].']';

		echo '<div class="-Hide-Boxes-Shows Group-Hide-Insert" data-uniqid="'.$UniqID.'" data-meta-key="'.$select_field['id'].'" data-show-type="'.$data['id'].'" '.( ( isset( $create_fields ) ) ? 'data-create-fields="true"' : 'data-create-fields="false"' ).' '.( ( $shape == $value[ $select_field['id'] ] ) ? '' : 'style="display:none"' ).'>';
			echo '<div class="Title-MoreForms"><i class="fa-solid fa-sliders"></i><h2>'.$data['title'].'</h2></div>';
			foreach ($data['fields'] as $k => $single_field) {
				$single_field['parent_id'] = $InputName;
				#
				if( isset( $value[ $data['id'] ][ $single_field['id'] ] ) ) {
					if( isset( $value[ $data['id'] ][ $single_field['id'].'_id' ] ) && !empty( $value[ $data['id'] ][ $single_field['id'].'_id' ] ) ) {
						$single_field['value'] = array('id'=>$value[ $data['id'] ][ $single_field['id'].'_id' ],'url'=>$value[ $data['id'] ][ $single_field['id'] ]);
					}else{
						$single_field['value'] = $value[ $data['id'] ][ $single_field['id'] ];
					}

				}
				unset( $single_field['vars'] );
				$this->Fields__Part($single_field['type'],$single_field);
			}
		echo '</div>';		
		
	}
}