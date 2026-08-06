<?php # YOURCOLOR CUSTOM FIELDS MACHINE.
class YC__CFM {
	private $CustomActions_Packages;

	function __construct($arguments=array()) {

		if( is_admin() ) {
			# PLUGIN PATH.
				$this->YC__CFM_Path = trailingslashit( dirname( __FILE__ ) );

			# PLUGIN URL.
				$this->YC__CFM_URL = explode(get_template_directory(), trailingslashit( dirname( __FILE__ ) ))[1];
				$this->YC__CFM_URL = get_template_directory_uri().$this->YC__CFM_URL;

			# FIELDS EXTRACT.	
				$this->Fields__List_path = $this->YC__CFM_Path.'SetupFields/*/';
				$this->Fields__Packages = array_filter( glob( $this->Fields__List_path ), 'is_dir');				

			# FIELDS EXTRACT.	
				$this->CallBack_path = $this->YC__CFM_Path.'CallBackActions/';
				$this->CallBack__Packages = glob( $this->CallBack_path.'*.php' );

			# UI FIELDS.
				$this->UI__fields_Path = $this->YC__CFM_Path.'/FieldsContext/';
				$this->UI__Fields_Packages = array_filter( glob( $this->UI__fields_Path.'*.php') );

			# UI PARTS.
				$this->UI__fields_Path = $this->YC__CFM_Path.'/Parts/';
				$this->UI__Parts_Packages = array_filter( glob( $this->UI__fields_Path.'*.php') );

			# Custom ACTIONS.
				$this->CustomActions_Path = $this->YC__CFM_Path.'/CustomActions/';
				$this->CustomActions_Packages = array_filter( glob( $this->CustomActions_Path.'*.php') );

			# AjaxCallBack.
				$this->AjaxCallBack_Path = $this->YC__CFM_Path.'/AjaxCallBack/';
				$this->AjaxCallBack_Packages = array_filter( glob( $this->AjaxCallBack_Path.'*.php') );


			# VALUE IMPORTANT ARRAY .				
				$this->ImportantArray = array('CheckBox','File','GroupsField','Users-CheckBox','Posts-CheckBox','Taxonomy-CheckBox');

			# METHODS 
				$this->GET = $_GET;

			if( !class_exists('YC__CFM_Enqueues') ) {
				require( $this->YC__CFM_Path .'/Enqueues.php');
			}

			if( !class_exists('YC__CFM__AjaxCallBack') ) {
				require( $this->YC__CFM_Path .'/AjaxCallBack.php');
			}

			if( !class_exists('ThemeOptions') ) {
				require( $this->YC__CFM_Path .'/ThemeOptions.php');
			}
			
		}
	}

	# CLASS TOOLS
		public function Require($path, $vars=array()) {
			extract($vars);
			if( file_exists($path) ) {
				require($path);
			}else {
				echo '<p><strong>هذا المسار غير موجود :</strong>'.$path.'</p>';
			}
		}

		public function AdminPart($part, $vars=array()){
			if( isset( $vars['vars'] ) ) unset($vars['vars']);
			$files = $this->UI__Parts_Packages;
			foreach ($files as $file) {
				$file_name = basename($file);
				$file_name = explode('.php', $file_name)[0];
				if( $file_name == $part ) {
					$CurrentURL = str_replace(get_template_directory(), get_template_directory_uri(), $file);
					$CurrentURL = str_replace('#', urlencode('#'), $CurrentURL);
					$this->Require($file, array_merge(array('CurrentDir'=>$file, 'CurrentURL'=>$CurrentURL), $vars));
				}
			}
		}

		public function Fields__Part($field__type, $vars=array()) {
			if( isset( $vars['vars'] ) ) unset($vars['vars']);
			$fields = $this->UI__Fields_Packages;
			foreach ($fields as $file) {
				$file_name = basename($file);
				$file_name = explode('.php', $file_name)[0];
				if( $file_name == $field__type ) {
					$CurrentURL = str_replace(get_template_directory(), get_template_directory_uri(), $file);
					$CurrentURL = str_replace('#', urlencode('#'), $CurrentURL);
					$this->Require($file, array_merge(array('CurrentDir'=>$file, 'CurrentURL'=>$CurrentURL), $vars));
				}
			}
		}

		public function Require_Fields( $path, $vars=array() ) {
			extract($vars);
			if( file_exists($path) ) {
				require($path);
				if( isset( $metaboxes ) ){
					return $metaboxes;
				}else{
					$output = ob_get_clean();
					$output = json_decode($output,true);
					return $output;
				}

			}
			return false;
		}

		public function SorterFields($fields,$folder=''){
			$Return = array();
			#
			$NumberLoop = array();
			$LoopNumbers = 1;
			foreach ( $fields as $k => $v) {
				if( isset( $v[ $folder.'_number' ] ) ){

					$NumberLoop[ $v[ $folder.'_number' ] ] = $v;
					if( $v[ $folder.'_number' ] > $LoopNumbers ) $LoopNumbers = $v[ $folder.'_number' ];		

				}else if( isset( $v[ 'number' ] ) ){

					$NumberLoop[ $v['number'] ] = $v;
					if( $v['number'] > $LoopNumbers ) $LoopNumbers = $v['number'];

				}
			}
			# 
			for ($i=0; $i <= $LoopNumbers; $i++) { 
				if( isset( $NumberLoop[ $i ] ) ){
					$Return[ $NumberLoop[ $i ]['id'] ] = $NumberLoop[ $i ];
				}
			}
			#
			foreach ( $fields as $kt => $vtow ) {
				if( !isset( $Return[ $kt ] ) ){
					$Return[ $kt ] = $vtow;
				}
			}

			return $Return;
		}

		public function Setup__CallBack_Fields($selector=''){
			$Return = array();

			foreach ( is_array( $this->CallBack__Packages ) ? $this->CallBack__Packages : array() as $k => $file__object ) {
				$object__name = basename($file__object);
				$object__name = explode('.php', $object__name)[0];
				#
				if( $selector == '' || $selector != '' && $object__name == $selector ){
					
					$final__metabox = $this->Require_Fields($file__object, array('CurrentDir'=>$file__object,'json'=>true));
					
					if( $final__metabox != false ){

						$final__metabox['id'] = $object__name;
						$final__metabox['metaBox__path'] = $file__object;
						if( isset( $final__metabox['ObjectType'] ) && isset( $final__metabox['ObjectType']['Post_Types'] ) ){
							$final__metabox['ObjectType']['Post_Types'] = ( is_array( $final__metabox['ObjectType']['Post_Types'] ) ) ? $final__metabox['ObjectType']['Post_Types'] : array($final__metabox['ObjectType']['Post_Types']);

							$post_type_metaBox = $final__metabox;
							$post_type_metaBox['ObjectType'] = $final__metabox['ObjectType']['Post_Types'];
							$Return[ 'Post_Types' ][] = $post_type_metaBox;

						}

						if( isset( $final__metabox['ObjectType'] ) && isset( $final__metabox['ObjectType']['Taxonomies'] ) ){
							$final__metabox['ObjectType']['Taxonomies'] = ( is_array( $final__metabox['ObjectType']['Taxonomies'] ) ) ? $final__metabox['ObjectType']['Taxonomies'] : array($final__metabox['ObjectType']['Taxonomies']);

							$post_type_metaBox = $final__metabox;
							$post_type_metaBox['ObjectType'] = $final__metabox['ObjectType']['Taxonomies'];
							$Return[ 'Taxonomies' ][] = $post_type_metaBox;
							
						}

						if( isset( $final__metabox['ObjectType'] ) && isset( $final__metabox['ObjectType']['ThemeOptions'] ) ){
							$final__metabox['ObjectType']['ThemeOptions'] = ( is_array( $final__metabox['ObjectType']['ThemeOptions'] ) ) ? $final__metabox['ObjectType']['ThemeOptions'] : array($final__metabox['ObjectType']['ThemeOptions']);

							$post_type_metaBox = $final__metabox;
							$post_type_metaBox['ObjectType'] = $final__metabox['ObjectType']['ThemeOptions'];
							$Return[ 'ThemeOptions' ][] = $post_type_metaBox;
							
						}					
					}
				}
			}

			return $Return;
		}

		public function get_fields_selector($folder,$file){
			global $YC__CFM__global_setup_fields;

			$Return = array();
			if( !isset( $YC__CFM__global_setup_fields[ $folder ] ) ) return $Return;

			foreach ( $YC__CFM__global_setup_fields[ $folder ] as $metabox__key => $metabox_value ) {
				$metabox_value['ObjectType'] = ( ( is_array( $metabox_value['ObjectType'] ) ) ) ? $metabox_value['ObjectType'] : array($metabox_value['ObjectType']);
				if( in_array($file,$metabox_value['ObjectType'])  ){
					$Return[ $metabox__key ] = $metabox_value;
				}
			}

			return $Return;
		}


		public function Extract_Fields($folder,$file=''){
			$return = array();
			# EXTRACT FIELDS TYPE FIELDS .
				$Packages__List = $this->Fields__Packages;
				foreach ( $Packages__List as $k => $f ) {

					$item__name = basename($f);
					if( $item__name == $folder ){

						$files__list = array_filter( glob( $f.'*.php') );

						foreach ( ( is_array( $files__list ) ) ? $files__list : array() as $w => $file__object ) {
							$object__name = basename($file__object);
							$object__name = explode('.php', $object__name)[0];


							# THEME OPTION.
								if( $item__name == 'ThemeOptions' ){

									if( $file == '' || $file != '' && $object__name == $file ){

										$Fields__Data = $this->Require_Fields($file__object, array('CurrentDir'=>$file__object));
										if( $Fields__Data != false ){
											$Fields__Data[ 'id' ] = $object__name;
											if( !isset( $Fields__Data['id'] ) ) $Fields__Data['id'] = $object__name;	
											$return[ $object__name ] = $Fields__Data;
										}
									}
								}

							# TAXONOMY OR POST TYPES .	
								if( $item__name == 'Taxonomies' || $item__name == 'Post_Types' ){

									if( $file == '' || $file != '' && $object__name == $file ){

										$Fields__Data = $this->Require_Fields($file__object, array('CurrentDir'=>$file__object));
										if( $Fields__Data != false ){
											foreach ( $Fields__Data as $er => $final__fields) {
												$final__fields['MetaBox__Action'] = 'fields_metabox';

												$final__fields['ObjectType'] = $object__name;
												if( !isset( $final__fields['context'] ) ) $final__fields['context'] = 'normal';
												if( !isset( $final__fields['priority'] ) ) $final__fields['priority'] = 'high';
												if( !isset( $final__fields['id'] ) ) $final__fields['id'] = $er;
												$return[ $er ] = $final__fields;
											}
										}
									}
								}
						
						}
					
					}

				}

			#  CALL BACK FIELDS .
				$CallBackPackages = $this->Setup__CallBack_Fields();

				if( isset( $CallBackPackages[ $folder ] ) ){

					# THEME OPTION.
						if( $folder == 'ThemeOptions' ){
							foreach ( $CallBackPackages[ $folder ] as $cb_k => $cb_v) {

								if( !isset( $cb_v['context'] ) ) $cb_v['context'] = 'normal';
								if( !isset( $cb_v['priority'] ) ) $cb_v['priority'] = 'high';
								$cb_v['MetaBox__Action'] = 'callback_metabox';
								#
								$cb_v['ObjectType'] = ( is_array( $cb_v['ObjectType'] ) ) ? $cb_v['ObjectType'] : array($cb_v['ObjectType']);
								foreach ( $cb_v['ObjectType'] as $r => $s_act) {

									if( isset( $return[ $s_act ] ) ){
										$return[ $s_act ]['fields'][$cb_v[ 'id' ]] = $cb_v;
									}

								}
							}
						}

					# TAXONOMY OR POST TYPES.
						if( $folder == 'Taxonomies' || $folder == 'Post_Types' ){

							foreach ( $CallBackPackages[ $folder ] as $cb_k => $cb_v) {

								if( !isset( $cb_v['context'] ) ) $cb_v['context'] = 'normal';
								if( !isset( $cb_v['priority'] ) ) $cb_v['priority'] = 'high';
								$cb_v['MetaBox__Action'] = 'callback_metabox';
								$return[ $cb_v[ 'id' ] ] = $cb_v;

							}

						}
				}
			return $return;
		}

		protected function stripslashes_deep($value) {
	    $value = is_array($value) ? array_map(array($this, 'stripslashes_deep'), $value) : stripslashes($value);
    	return $value;
		}

		private function Methods() {
			return $this->stripslashes_deep($_POST);
		}

		public function CanSave() {
			$return = false;
			if( current_user_can('edit_posts') and current_user_can('edit_published_posts') ) {
				$return = true;
			}
			return $return;
		}

	# POSTS META BOX EDITS.	

		# SETUP POSTS META BOXES .
			public function Setup__PostsMetaBox(){
				global $YC__CFM__global_setup_fields;

				if( isset( $YC__CFM__global_setup_fields['Post_Types'] ) && !empty( $YC__CFM__global_setup_fields['Post_Types'] ) ){
					$fields__Setup = $YC__CFM__global_setup_fields['Post_Types'];
					# DO ACTION BEFORE FIELDS APPEND .
						do_action('YC__CFM__Before_PostsFields_Append');
					foreach ($fields__Setup as $metabox__key => $metabox__data) {

						if( isset( $metabox__data[ 'ObjectType' ] ) ){

							add_meta_box(
						    'YC__CFM-'.$metabox__key,
						    __( $metabox__data['title'], 'YC__CFM' ),
						    array($this, 'PostsMetaBox'),
						    $metabox__data['ObjectType'],
						    $metabox__data['context'],
						    $metabox__data['priority'],
						    $metabox__data
							);
						}
					}

					# DO ACTION AFTER FIELDS APPEND .
						do_action('YC__CFM__After_PostsFields_Append');
				}
			}

			public function PostsMetaBox( $post , $metabox__data ) {

				echo ( ( !isset( $metabox__data['args']['hide_metabox_row'] ) ) ) ? '<div class="-YC-Fields-MetaBox-item MetaBoxID-'.$metabox__data['args']['id'].'">' : '';

					# CALLBACK FIELDS .
						if( $metabox__data['args']['MetaBox__Action'] == 'callback_metabox' ){

							$metabox__data['args']['Activable__Object'] = $post;

							if( isset( $metabox__data['args']['Set__Object_terms'] ) ){

								$CurrentValue = ( ( is_array( get_the_terms( $post->ID, $metabox__data['args']['Set__Object_terms']['taxonomy'],true ) ) ) ) ? get_the_terms( $post->ID,$metabox__data['args']['Set__Object_terms']['taxonomy'],true ) : array();
								$metabox__data['args']['Values'][ $metabox__data['args']['Set__Object_terms']['taxonomy'] ] = $CurrentValue;

							}
							if( isset( $metabox__data['args']['fields'] ) ){
								foreach ( (is_array( $metabox__data['args']['fields'] )) ? $metabox__data['args']['fields'] : array() as $k => $field) {

									$CurrentValue = get_post_meta($post->ID,$field,true);

									$metabox__data['args']['Values'][ $field ] = $CurrentValue;
								}

							}

							$this->Require($metabox__data['args']['metaBox__path'],$metabox__data['args']);

						}

					# FIELDS METABOX .
						if( $metabox__data['args']['MetaBox__Action'] == 'fields_metabox' ){

							foreach ( $metabox__data['args']['fields'] as $k => $field) {

								$CurrentValue = get_post_meta($post->ID,$field['id'],true);

								if( $field['type'] == 'File' && is_string( $CurrentValue ) ){
									$NewCurrentValue = get_post_meta($post->ID,$field['id'].'_id',true);
									$CurrentValue = array('url'=>$CurrentValue,'id'=>$NewCurrentValue);
								}
								if( empty( $CurrentValue ) ) $CurrentValue = ( ( in_array( $field['id'] , $this->ImportantArray ) ) ) ? array() : '';

								$field['value'] = $CurrentValue;
								#
								$this->Fields__Part($field['type'],$field);
							}
						}

				echo ( ( !isset( $metabox__data['args']['hide_metabox_row'] ) ) ) ? '</div>' : '';
			}

		# SAVE POST META POXES .
			public function UpdatePost($id, $key, $val) {
				if( $this->CanSave() ) {
					update_post_meta($id, $key, $val);
				}
			}
			public function RemovePost($id, $key) {
				if( $this->CanSave() ) {
					delete_post_meta($id, $key);
				}
			}
			public function Save__Post($postID){
				if ( wp_is_post_revision( $postID ) ) return;
        if ( ! current_user_can( 'manage_options' ) ) return $postID;

				global $YC__CFM__global_setup_fields;
        $post = get_post($postID);

        if( $this->CanSave() && isset( $this->Methods()['apbupdate'] ) ) {

        	do_action('YC__CFM__Before_Save_post_metabox',$post);

					$fields__Setup = $this->get_fields_selector('Post_Types',$post->post_type);
					if( !empty( $fields__Setup ) ){

						foreach ( $fields__Setup as $metabox__key => $metabox__data ) {

							# CALLBACK UPDATED.
								if( $metabox__data['MetaBox__Action'] == 'callback_metabox' && !isset( $metabox__data['Set__Object_terms'] ) ){
									foreach ( is_array( $metabox__data['fields'] ) ? $metabox__data['fields'] : array() as $c__k => $c_fields__id) {

										if( isset( $this->Methods()[ $c_fields__id ] ) ) {

											$this->UpdatePost($post->ID,  $c_fields__id , $this->Methods()[ $c_fields__id ]);
											
										}else{
											$this->RemovePost($post->ID,  $c_fields__id );
										}
									}
								}
							# SAVE METABOX FIELDS.
								if( $metabox__data['MetaBox__Action'] == 'fields_metabox' ){
									foreach ( is_array( $metabox__data['fields'] ) ? $metabox__data['fields'] : array() as $field__key => $field__data) {

										if( isset( $this->Methods()[ $field__data['id'] ] ) ) {

											$this->UpdatePost($post->ID,  $field__data['id'] , $this->Methods()[ $field__data['id'] ]);

											# FIELD TYPE FILE __ ID .
												if( isset( $this->Methods()[ $field__data['id'].'_id' ] ) ){
													$this->UpdatePost($post->ID, $field__data['id'].'_id' , $this->Methods()[ $field__data['id'].'_id' ] );
												}else{
													$this->RemovePost($post->ID, $field__data['id'].'_id' );
												}

										}else{
											$this->RemovePost($post->ID,  $field__data['id'] );
										}
									}
								}							
						}
					}

					do_action('YC__CFM__After_Save_post_metabox',$post);

        }
			}

		# UPDATE OBJECT TERMS IN MY METABOX.
			public function Set__Object_terms( $postID, $post_after, $post_before ){

				if ( wp_is_post_revision( $postID ) ) return;

        if ( ! current_user_can( 'manage_options' ) ) return $postID;

        global $YC__CFM__global_setup_fields;
        $post = get_post($postID);

        if( $this->CanSave() && isset( $this->Methods()['apbupdate'] ) ) {

        	if( isset( $YC__CFM__global_setup_fields['Post_Types'] ) && !empty( $YC__CFM__global_setup_fields['Post_Types'] ) ){

						foreach ( $YC__CFM__global_setup_fields['Post_Types'] as $metabox__key => $metabox__data ) {
							if( $metabox__data['MetaBox__Action'] == 'callback_metabox' && isset( $metabox__data['Set__Object_terms'] )  ){

								if( isset( $this->Methods()[ $metabox__data['Set__Object_terms']['metakey'] ] ) ){

									$object_terms_data = (is_array(get_the_terms($postID,$metabox__data['Set__Object_terms']['taxonomy'],true))) ? get_the_terms($postID,$metabox__data['Set__Object_terms']['taxonomy'],true) : array();
									foreach ($object_terms_data as $rid) {
										wp_remove_object_terms($postID,$rid->term_id,$metabox__data['Set__Object_terms']['taxonomy']);
									}

									$each__new_term = array();
									foreach ( ( is_array( $this->Methods()[ $metabox__data['Set__Object_terms']['metakey'] ] ) ) ? $this->Methods()[ $metabox__data['Set__Object_terms']['metakey'] ] : array() as $evid) {
										$termo = get_term_by('id',$evid,$metabox__data['Set__Object_terms']['taxonomy']);
										if( isset( $termo->slug ) ){
											$each__new_term[] = $termo->slug;
										}
									}
									if( !empty( $each__new_term ) ){
										wp_set_object_terms($postID,array_values($each__new_term),$metabox__data['Set__Object_terms']['taxonomy']);
									}
								}else{
									$object_terms_data = (is_array(get_the_terms($postID,$metabox__data['Set__Object_terms']['taxonomy'],true))) ? get_the_terms($postID,$metabox__data['Set__Object_terms']['taxonomy'],true) : array();
									foreach ($object_terms_data as $rid) {
										wp_remove_object_terms($postID,$rid->term_id,$metabox__data['Set__Object_terms']['taxonomy']);
									}
								}

							}
						}
					}
        }
			}


	# TAXONOMY META BOX EDITS.	

			# SETUP TAXONOMY META BOXES .
				public function Setup__TaxonomyMetaBox(){
					if( is_admin() ) {


						# DO ACTION BEFORE FIELDS APPEND .
							do_action('YC__CFM__Before_TaxonomyFields_Append');

						global $YC__CFM__global_setup_fields;

						if( isset( $YC__CFM__global_setup_fields['Taxonomies'] ) && !empty( $YC__CFM__global_setup_fields['Taxonomies'] ) ){

							foreach ( $YC__CFM__global_setup_fields['Taxonomies'] as $metabox__key => $metabox__data) {
								if( !isset( $metabox__data['id'] ) ) $metabox__data['id'] = $metabox__key;

								if( isset( $metabox__data[ 'ObjectType' ] ) ){
									$metabox__data[ 'ObjectType' ] = ( is_array( $metabox__data[ 'ObjectType' ] ) ) ? $metabox__data[ 'ObjectType' ] : array( $metabox__data[ 'ObjectType' ] );
									foreach ( $metabox__data[ 'ObjectType' ] as $c => $ob_type) {
										add_action ( 'edited_'.$ob_type, array( $this, 'Save__Terms_MetaBox' ));
										add_action ( $ob_type.'_edit_form_fields', function($a) use ($metabox__data) { (new YC__CFM)->TaxonomiesMetaBox($metabox__data); } );									
									}
								}
							}
						}

						# DO ACTION AFTER FIELDS APPEND .
							do_action('YC__CFM__After_TaxonomyFields_Append' );
					}
				}

				public function TaxonomiesMetaBox( $metabox__data ) {

					if ( ! did_action( 'wp_enqueue_media' ) )  wp_enqueue_media();
					echo '</table>';
					
					#
					$Activable__Object = get_term_by('id',$this->GET['tag_ID'],$this->GET['taxonomy']);
					#

					echo '<div id="YC__CFM-'.$metabox__data['id'].'" class="postbox -TaxonomiesMetaBox-YC">';

						echo '<div class="postbox-header">';
				    	echo '<h2>'.$metabox__data['title'].'</h2>';
						echo '</div>';

				    echo '<div class="inside" style="padding:15px;">';

							echo '<div class="-YC-Fields-MetaBox-item MetaBoxID-'.$metabox__data['id'].'">';
								# CALLBACK FIELDS .
									if( $metabox__data['MetaBox__Action'] == 'callback_metabox' ){
										$metabox__data['Activable__Object'] = $Activable__Object;
										foreach ( $metabox__data['fields'] as $k => $field) {

											$CurrentValue = get_term_meta( $this->GET['tag_ID'],$field,true);

											$metabox__data['Values'][ $field ] = $CurrentValue;
										}
										$this->Require($metabox__data['metaBox__path'],$metabox__data);
									}

								# FIELDS METABOX .
									if( $metabox__data['MetaBox__Action'] == 'fields_metabox' ){
										foreach ( $metabox__data['fields'] as $k => $field) {

											$CurrentValue = get_term_meta( $this->GET['tag_ID'],$field['id'],true);

											if( $field['type'] == 'File' && is_string( $CurrentValue ) ){
												$NewCurrentValue = get_term_meta( $this->GET['tag_ID'],$field['id'].'_id',true);
												$CurrentValue = array('url'=>$CurrentValue,'id'=>$NewCurrentValue);
											}
											if( empty( $CurrentValue ) ) $CurrentValue = ( ( in_array( $field['id'] , $this->ImportantArray ) ) ) ? array() : '';

											$field['value'] = $CurrentValue;
											#
											$this->Fields__Part($field['type'],$field);
										}
									}

							echo '</div>';				

						echo '</div>';
					echo '</div>';
				}
			
			# SAVE TERM META BOXES .
				public function UpdateTerm($id, $key, $val) {
					if( $this->CanSave() ) {
						update_term_meta($id, $key, $val);
					}
				}

				public function RemoveTerm($id, $key) {
					if( $this->CanSave() ) {
						delete_term_meta($id, $key);
					}
				}

				public function Save__Terms_MetaBox($tagID){

	        if( $this->CanSave() && isset( $this->Methods()['apbupdate'] ) && $this->Methods()['taxonomy'] ) {
	        	$Object_term = get_term_by('id',$tagID,$this->Methods()['taxonomy']);

	        	do_action('YC__CFM__Before_Save_term_metabox',$Object_term);

	        	global $YC__CFM__global_setup_fields;

	        	if( isset( $YC__CFM__global_setup_fields['Taxonomies'] ) && !empty( $YC__CFM__global_setup_fields['Taxonomies'] ) ){

							$fields__Setup = $this->get_fields_selector( 'Taxonomies',$Object_term->taxonomy );

							foreach ( $fields__Setup as $metabox__key => $metabox__data ) {
									
								# CALLBACK UPDATED.
									if( $metabox__data['MetaBox__Action'] == 'callback_metabox' ){
										foreach ( is_array( $metabox__data['fields'] ) ? $metabox__data['fields'] : array() as $c__k => $c_fields__id) {

											if( isset( $this->Methods()[ $c_fields__id ] ) ) {

												$this->UpdateTerm($Object_term->term_id,  $c_fields__id , $this->Methods()[ $c_fields__id ]);

												# FIELD TYPE FILE __ ID .
													if( isset( $this->Methods()[ $c_fields__id.'_id' ] ) ){
														$this->UpdateTerm($Object_term->term_id, $c_fields__id.'_id' , $this->Methods()[ $c_fields__id.'_id' ] );
													}else{
														$this->RemoveTerm($Object_term->term_id, $c_fields__id.'_id' );
													}

											}else{
												$this->RemoveTerm($Object_term->term_id,  $c_fields__id );
											}

										}
									}

								# SAVE METABOX FIELDS.
									if( $metabox__data['MetaBox__Action'] == 'fields_metabox' ){

										foreach ( is_array( $metabox__data['fields'] ) ? $metabox__data['fields'] : array() as $field__key => $field__data) {

											if( isset( $this->Methods()[ $field__data['id'] ] ) ) {

												$this->UpdateTerm($Object_term->term_id,  $field__data['id'] , $this->Methods()[ $field__data['id'] ]);

												# FIELD TYPE FILE __ ID .
													if( isset( $this->Methods()[ $field__data['id'].'_id' ] ) ){
														$this->UpdateTerm($Object_term->term_id, $field__data['id'].'_id' , $this->Methods()[ $field__data['id'].'_id' ] );
													}else{
														$this->RemoveTerm($Object_term->term_id, $field__data['id'].'_id' );
													}

											}else{
												$this->RemoveTerm($Object_term->term_id,  $field__data['id'] );
											}
										}
									}
							}
	        	}

						do_action('YC__CFM__After_Save_term_metabox',$Object_term);

	        }
				}

	public function FieldsHook(){
		global $YC__CFM__global_setup_fields;

		# EXTRACT DEFULT THEME FIELDS 'Post_Types' AND APPEND IN '$YC__CFM__global_setup_fields'.

			# EXTRACT FIELDS 
				$Post_Types = $this->Extract_Fields('Post_Types');
				$Post_Types = ( ( is_array($Post_Types) ) ) ? $Post_Types : array();
				$Post_Types = $this->SorterFields( $Post_Types ,'Post_Types');

			# APPEND FIELDS .	
				if( !empty( $Post_Types ) ) $YC__CFM__global_setup_fields['Post_Types'] = $Post_Types;
		
		# EXTRACT DEFULT THEME FIELDS 'Taxonomies' AND APPEND IN '$YC__CFM__global_setup_fields'.

			# EXTRACT FIELDS 
				$Taxonomies = $this->Extract_Fields('Taxonomies');
				$Taxonomies = ( ( is_array($Taxonomies) ) ) ? $Taxonomies : array();
				$Taxonomies = $this->SorterFields( $Taxonomies ,'Taxonomies');

			# APPEND FIELDS .	
				if( !empty( $Taxonomies ) ) $YC__CFM__global_setup_fields['Taxonomies'] = $Taxonomies;

		# EXTRACT DEFULT THEME FIELDS 'ThemeOptions' AND APPEND IN '$YC__CFM__global_setup_fields'.

			# EXTRACT FIELDS 
				$ThemeOptions = $this->Extract_Fields('ThemeOptions');
				$ThemeOptions = ( is_array( $ThemeOptions ) ) ? $ThemeOptions : array();
				$ThemeOptions = $this->SorterFields( $ThemeOptions,'ThemeOptions' );

			# APPEND FIELDS .	
				if( !empty( $ThemeOptions ) ) $YC__CFM__global_setup_fields['ThemeOptions'] = $ThemeOptions;

		do_action('YC__CFM__global_setup_fields');

		# NAV MENUS .
			global $YC__nav__menus;
			$YC__nav__menus = array();

			do_action('YC__nav__menus');

	}			

	public function Setup(){
		
		if( !is_admin() ) return ;

		# INIT FIELDS HOOK 
			add_action('after_setup_theme',array($this,'FieldsHook'),1);

		# POST TYPE	META BOX EDITS

			# SETUP POSTS META BOX
				add_action( 'add_meta_boxes', array($this,'Setup__PostsMetaBox') );

			# SAVE POSTS ACTIONS	
				add_action( 'save_post', array( $this, 'Save__Post' ), 10, 2 );
				add_action( 'edit_post', array( $this, 'Save__Post' ), 10, 2 );
			
			# SAVE OBJECT TERMS META BOXES		
				add_action( 'post_updated', array( $this, 'Set__Object_terms' ), 10, 3 );

		# TERMS	META BOX SETUP
			add_action( 'after_setup_theme', array( $this, 'Setup__TaxonomyMetaBox' ) );

		# CUSTOM OPTIONS			
			foreach ( is_array( $this->CustomActions_Packages ) ? $this->CustomActions_Packages : array() as $files ) {
				$this->Require( $files, array( 'CurrentDir'=> $files ) );
			}		

	}
	
}
(new YC__CFM)->Setup();