<?php  class FormsUI{
	function __construct($arguments=array()) {
		$this->ThemeStatic = new ThemeStatic;
	}

	public function FormData($id){
		$Return = array();

		if( is_object( $id ) ){
			$post = $id;
		}else{
			$post = get_post($id);
		}
		$Return['post'] = $post;
		#
		$actionType = get_post_meta($post->ID,'actionType',true);
		if( !empty( $actionType ) && $actionType == 'mail' ){
			$post_formate_mail = get_post_meta($post->ID,'post_formate_mail',true);
			$Return['actionType'] = $actionType;
			$Return['post_formate_mail'] = $post_formate_mail;
		}else if( !empty( $actionType ) && $actionType == 'support_pannel' ){
			$Return['actionType'] = $actionType;
		}else if( !empty( $actionType ) && $actionType == 'contact_page' ){
			$Return['actionType'] = $actionType;

		}else if( !empty( $actionType ) ){
			$ActionParams = explode('_', $actionType);
			#
			$Return['ActionParams']['ObjectType'] = $ActionParams[0];
			$Return['ActionParams']['ObjectName'] = $ActionParams[1];
			$Return['ActionParams']['Object_id'] = $ActionParams[2];
			#

			#
			$Return['actionType'] = 'ObjectAction';
		}else{
			$Return['actionType'] = false;
		}
		#

		$get__fields = $this->Extract__fields(
			array(
				'object__type'=>'posts',
				'current__object'=>$post,
				'meta__key'=>'TempForms',
			)
		);

		if( $Return != false && is_array( $Return ) ) $Return = array_merge( $Return,$get__fields );
		
/*		$TempForms = ( is_array( get_post_meta($post->ID,'TempForms',true) ) ) ? get_post_meta($post->ID,'TempForms',true) : array();
		#
		if( !empty( $TempForms ) ){

			# HOW TO STEPS .
			$StepsCount = count($TempForms);
			$ShowSteps = false;
			if( $StepsCount > 1 ){
				$ShowSteps = true;
			}
			$Return['StepsShow'] = $ShowSteps;
			$Return['StepsCount'] = $StepsCount;
			#
			foreach ( $TempForms as $k => $step ) {
				$Return['TempForms'][$k]['title'] = $step['title'];
				$Return['TempForms'][$k]['button_title'] = ( ( isset( $step['button_title'] ) ) ) ? $step['button_title'] : 'ارسال ';
				$Return['TempForms'][$k]['button_icon'] = ( ( isset( $step['button_icon'] ) ) ) ? $step['button_icon'] : '';
				$Return['TempForms'][$k]['key'] = $k;

				foreach ($step['fields'] as $f => $fields) {

					$fields['type'] = $fields['FieldType'];

					if( !empty( $fields['options'] ) && $fields['options'] == false ){
						$fields['options'] = '';
					}


					if( $fields['FieldType'] == 'Select' || $fields['FieldType'] == 'CheckBox' || $fields['FieldType'] == 'Radio' ){
						if( !empty( $fields['options'] ) ){
							$OPt = explode(PHP_EOL, $fields['options']);
							$fields['options'] = array();
							foreach ($OPt as $y => $te) {
								if( !empty( $te ) && $te != '' ){
									$fields['options'][] = $te;
								}
							}
						}	
					}

					if( $fields['FieldType'] == 'Taxonomy-Select' || $fields['FieldType'] == 'Taxonomy-CheckBox' || $fields['FieldType'] == 'Taxonomy-Radio' ){
						$fields['options'] = array();
						if( !empty( $fields['taxonomy_name'] ) && !empty( $fields['taxonomy_field'] ) ){
							$TaxonomyArguments = array('taxonomy'=>$fields['taxonomy_name'],'hide_empty'=>0);
							foreach ( get_terms($TaxonomyArguments) as $s => $term) {
								$fields['options'][$term->term_id] = $term->name;
							}
						}	
					}

					if( isset( $fields['showsin'] ) && !empty( $fields['showsin'] ) ){
						$Return[$fields['showsin']] = $f;
					}

					$Return['TempForms'][$k]['fields'][$f] = $fields;

					if( $actionType == 'contact_page' && isset( $fields['First_Field'] ) && $fields['First_Field'] == 'on' ){
						$Return['ConatctField'][$f] = $fields;
					}
				}

			}

		}*/

		return $Return;
	}

	public function Extract__fields( $data ){
		/*
			$object__type => POSTS || TAXONOMIES 
			$meta__key => FOUND FIELDS METAKEY
			$forms__fields => CURRENT FORMS FIELDS EXTRACT,
			$before_steps => APPEND BEFORE FIELDS .
			$after_after => APPEND AFTER FIELDS .
			$current__action=> SEND YOUR CURRENT ACTION .
			$current_step=> SEND YOUR CURRENT STEP .
		*/
		extract($data);
		# FOUND REQUIRE FIELDS .	
			if( isset( $current__object ) && isset( $current__object->term_id ) && $object__type == 'taxonomies' ){
				$forms__fields = get_term_meta($current__object->term_id,$meta__key,true);
			}else if( isset( $current__object ) && isset( $current__object->ID ) && $object__type == 'posts' ){
				$forms__fields = get_post_meta($current__object->ID,$meta__key,true);
			}

			$forms__fields = ( isset( $forms__fields ) && is_array( $forms__fields ) ) ? $forms__fields : array();

			if( isset( $before_steps ) ) $forms__fields = array_merge($before_steps, $forms__fields);
			if( isset( $after_after ) ) $forms__fields = array_merge($forms__fields, $after_after);

		# EXTRACT FIELDS .	
			if( !empty( $forms__fields ) ){

				# HOW TO STEPS .
					$ShowSteps = false;

					$StepsCount = count($forms__fields);
					if( $StepsCount > 1 ) $ShowSteps = true;

					$Return['StepsShow'] = $ShowSteps;
					$Return['StepsCount'] = $StepsCount;
					$Return['first_step'] = false;
					$Return['end_step'] = false;


				# EXTRACT FIELDS .
					foreach ( $forms__fields as $k => $step ) {
					
						if( $Return['first_step'] == false ) $Return['first_step'] = $k;
						$Return['end_step'] = $k;
						# START UP FIELDS .
							if( isset( $step['title'] ) ) $Return['forms__fields'][$k]['title'] = $step['title'];
							$Return['forms__fields'][$k]['button_title'] = ( ( isset( $step['button_title'] ) ) ) ? $step['button_title'] : 'ارسال ';
							$Return['forms__fields'][$k]['button_icon'] = ( ( isset( $step['button_icon'] ) ) ) ? $step['button_icon'] : '';
							$Return['forms__fields'][$k]['id'] = $k;

						# FIELDS STEPS .	
							foreach ( $step['fields'] as $f => $fields ) {

								if( isset( $fields['FieldType'] ) && !isset( $fields['type'] ) ) $fields['type'] = $fields['FieldType'];

								if( !empty( $fields['options'] ) && $fields['options'] == false ){
									$fields['options'] = '';
								}


								if( $fields['type'] == 'Select' || $fields['type'] == 'CheckBox' || $fields['type'] == 'Radio' ){
									if( !empty( $fields['options'] ) && !is_array( $fields['options'] ) ){

										$OPt = explode(PHP_EOL, $fields['options']);
										$fields['options'] = array();
										foreach ($OPt as $y => $te) {
											if( !empty( $te ) && $te != '' ){
												$fields['options'][] = $te;
											}
										}
									}	
								}

								if( isset( $fields['showsin'] ) && !empty( $fields['showsin'] ) ){
									$Return[$fields['showsin']] = $f;
								}

								$Return['forms__fields'][$k]['fields'][$f] = $fields;
							}

					


					}

			}


		return $Return;

	}

}