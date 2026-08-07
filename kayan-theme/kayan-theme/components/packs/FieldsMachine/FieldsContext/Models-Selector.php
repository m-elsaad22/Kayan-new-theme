<?php $UniqID = uniqid();
# 
if( isset( $InsertElements ) ){
	unset($vars['InsertElements']);
	$InputName = 'Insert_'.$id;
}else if( isset( $parent_id ) ){
	$InputName = $parent_id.'['.$id.']';
}else{
	$InputName = $id;
}
if( !isset( $select_field['options'] ) && isset( $ModelCenter ) ){
	$ModelsPath = (new ThemeTree)->packsPath.'@'.$ModelCenter;
	$FieldsSetup = array();
	$models = array(''=>'بدون تحديد ');
	foreach( glob($ModelsPath.'/*.php') as $model ) {
		$modelname = explode('@'.$ModelCenter.'/', $model)[1];
		$modelname = explode('/', $modelname)[0];
		$modelname = explode('.php', $modelname)[0];
		#
		if( !isset( $show__items_in ) || isset( $show__items_in ) && in_array($modelname, $show__items_in) ){
			
			$model_ID = $modelname;
			$modelname = str_replace(array('-', '_'), ' ', $modelname);
			$models[ $model_ID ] = ucfirst($modelname);
			
		}
	}
	$select_field['options'] = $models;
	$choose_fields = $FieldsSetup;

	# UPDATE VARS .
	$vars['select_field']['options'] = $select_field['options'];
	$vars['choose_fields'] = $choose_fields;
}
echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '<AjaxHTML_Cut>' : '';
	if( !isset( $value ) || !is_array($value) ) $value = array();

	if( !isset( $value[ $select_field['id'] ] ) ) $value[ $select_field['id'] ] = '';

	if( isset( $create_fields ) ) $select_field['create_fields'] = $vars;

	$select_field['value'] = $value[ $select_field['id'] ];
	$select_field['UniqID'] = $UniqID;
	if( isset( $select_field['parent_id'] ) ){
		$select_field['parent_id'] = $InputName.'['.$select_field['parent_id'].']';
	}else{
		$select_field['parent_id'] = $InputName;
	}
	$this->Fields__Part($select_field['type'],$select_field);
	#
	$choose_fields = ( is_array( $choose_fields ) ) ? $choose_fields : array();
	foreach ($choose_fields as $shape => $data) {

		if( $data['id'] == $value[ $select_field['id'] ] && isset( $create_fields ) || !isset( $create_fields ) ){
			$Second_InputName = $InputName.'['.$data['id'].']';

			echo '<div class="-Hide-Boxes-Shows Group-Hide-Insert" data-uniqid="'.$UniqID.'" data-meta-key="'.$select_field['id'].'" data-show-type="'.$data['id'].'" '.( ( isset( $create_fields ) ) ? 'data-create-fields="true"' : 'data-create-fields="false"' ).' '.( ( $shape == $value[ $select_field['id'] ] ) ? '' : 'style="display:none"' ).'>';
				echo '<div class="Title-MoreForms"><i class="fa-solid fa-sliders"></i><h2>'.$data['title'].'</h2></div>';
				foreach ($data['fields'] as $k => $single_field) {
					$single_field['parent_id'] = $Second_InputName;
					#
					if( isset( $value[ $data['id'] ][ $single_field['id'] ] ) ) {
						$single_field['value'] = $value[ $data['id'] ][ $single_field['id'] ];

						if( $single_field['type'] == 'File' && isset( $value[ $data['id'] ][ $single_field['id'].'_id' ] ) ){
							$single_field['value'] = array('url'=>$value[ $data['id'] ][ $single_field['id'] ],'id'=>$value[ $data['id'] ][ $single_field['id'].'_id' ]);
						}

					}
					unset( $single_field['vars'] );
					$this->Fields__Part($single_field['type'],$single_field);
				}
			echo '</div>';		
			
		}
	}
echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '</AjaxHTML_Cut>' : '';