<?php class ThemeOptions{
	function __construct( $arguments=array() ) {
		$this->YC__CFM = new YC__CFM;

		$this->PagesSetup = array(
			'title'=>'إعدادات القالب',
			'en_title' => 'Theme settings',
			'roles'=>'administrator',
			'switch' => true,
			'mobile' => true,
			'menuicon'=>'dashicons-smiley',
			'pageTitle'=>'إعدادات القالب ',
			'disc'=>'إدارة اعدادات القالب ',
			'sluged'=>'YTS',
			'url'=> 'admin.php?page=YTS',
		);
	}	

	# CLASS TOOLS
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



	# UPDATE OPTION DATA STAFF

		public function UpdateOption($key, $val) {
			if( $this->CanSave() ) {
				update_option($key, $val);
			}
		}
		public function RemoveOption($key) {
			if( $this->CanSave() ) {
				delete_option($key);
			}
		}

		public function SaveOptions($metabox__data) {

			if( $this->CanSave() ) {
				do_action('YC__CFM__Before_Save_Options_metabox_'.$metabox__data['id']);

				foreach ( is_array( $metabox__data['fields'] ) ? $metabox__data['fields'] : array() as $field__key => $field__data) {

					# CALLBACK FIELD.
						if( isset( $field__data['metaBox__path'] ) ){
							foreach ( $field__data['fields'] as $k => $id_field) {

								if( isset( $this->Methods()[ $id_field ] ) ) {

									$this->UpdateOption($id_field , $this->Methods()[ $id_field ]);

									# FIELD TYPE FILE __ ID .
										if( isset( $this->Methods()[ $id_field.'_id' ] ) ){
											$this->UpdateOption($id_field.'_id' , $this->Methods()[ $id_field.'_id' ] );
										}else{
											$this->RemoveOption($id_field.'_id' );
										}

								}else{
									$this->RemoveOption($id_field );
								}
							}
						}

					# SINGLE FIELD.
						if( !isset( $field['metaBox__path'] ) ){

							if( isset( $this->Methods()[ $field__data['id'] ] ) ) {

								$this->UpdateOption($field__data['id'] , $this->Methods()[ $field__data['id'] ]);

								# FIELD TYPE FILE __ ID .
									if( isset( $this->Methods()[ $field__data['id'].'_id' ] ) ){
										$this->UpdateOption($field__data['id'].'_id' , $this->Methods()[ $field__data['id'].'_id' ] );
									}else{
										$this->RemoveOption($field__data['id'].'_id' );
									}

							}else{
								$this->RemoveOption($field__data['id'] );
							}

						}
				}

				do_action('YC__CFM__After_Save_Options_metabox_'.$metabox__data['id']);
			}
		}

	# PAGE CONTEXT DATA	
		public function PageContext(){
			global $YC__CFM__global_setup_fields;

			if( isset( $YC__CFM__global_setup_fields['ThemeOptions'] ) && !empty( $YC__CFM__global_setup_fields['ThemeOptions'] ) ){
				$fields__Setup = $YC__CFM__global_setup_fields['ThemeOptions'];
				#
				$Activable__Page = $this->YC__CFM->GET['page'];
				$Activable__Page = ( ( strpos($Activable__Page, 'yts-' ) !== FALSE ) ) ? explode('yts-',$Activable__Page)[1] : $Activable__Page;
				if( !isset( $fields__Setup[ $Activable__Page ] ) ){
					$i = 0;
					foreach ( $fields__Setup as $e => $ve) {$i++;
						if( $i == 1 ) $Activable__Page = $e;
					}
				}
				
				$current__language__page = ( ( isset( $this->YC__CFM->GET['language__tabs__page'] ) ) ) ? $this->YC__CFM->GET['language__tabs__page'] : 'ar';

				echo '<div class="-SetupEditors" style="display:none">';
					wp_editor( '', 'tester_editor_ar', array('textarea_name' =>'tester_editor_ar','textarea_rows' => 3) );
				echo '</div>';				
				#
				echo '<yourcolorapi--conatiner>';
				
					echo '<Inseder--Appender>';	
						echo '<header class="YS_Header">';
							echo '<div class="PluginName">';
								echo '<div class="Head--Text">';
									echo '<h2>'.$fields__Setup[ $Activable__Page ]['title'].'</h2><p>'.( ( isset( $fields__Setup[ $Activable__Page ]['disc'] ) ) ? $fields__Setup[ $Activable__Page ]['disc'] : '' ).'</p>';
								echo '</div>';
								echo '<div class="Header Icon">'.( ( isset( $fields__Setup[ $Activable__Page ]['icon'] ) )  ? $fields__Setup[ $Activable__Page ]['icon'] : '' ).'</div>';
							echo '</div>';
						echo '</header>';

						echo '<div class="PagesModelConainer -PageIs--ThemeOptions">';
							echo '<div class="-Head-options-supages">';

								echo '<div class="-Active-Item-supages">';
									foreach ( $fields__Setup as $f => $f__value) {
										if( $f == $Activable__Page ){
											echo '<div class="UI-Menu-YTS-item active">';
												echo '<a href="admin.php?page=yts-'.strtolower($f).'" class="hoverable activable">';
													echo $f__value['icon'];
													echo '<span>'.$f__value['title'].'</span>';
												echo '</a>';
											echo '</div>';
										}
									}
								echo '</div>';
								echo '<div class="-Pages-Taps">';
									foreach ( $fields__Setup as $f => $f__value) {
										if( $f != $Activable__Page ){
											echo '<div class="UI-Menu-YTS-item">';
												echo '<a href="admin.php?page=yts-'.strtolower($f).'" class="hoverable activable">';
													echo $f__value['icon'];
													echo '<span>'.$f__value['title'].'</span>';
												echo '</a>';
											echo '</div>';
										}
									}

								echo '</div>';
							echo '</div>';

							if( isset( $fields__Setup[ $Activable__Page ]['languages__avilable'] ) && $fields__Setup[ $Activable__Page ]['languages__avilable'] == true ){

								if( isset( $fields__Setup[ $Activable__Page ]['language__tabs'][ $current__language__page ] ) ){
									$fields__Setup[ $Activable__Page ]['fields'] = $fields__Setup[ $Activable__Page ]['language__tabs'][ $current__language__page ];
								}


								echo '<div class="-languages--top-bars">';
									echo '<div class="--language--taps-title">'.$fields__Setup[ $Activable__Page ]['icon'].'<span>'.$fields__Setup[ $Activable__Page ]['title'].'</span> <div class="-ch--lang-is"><span><img src="'.$fields__Setup[ $Activable__Page ]["{$current__language__page}_list"]['image'].'"></span><strong>'.$fields__Setup[ $Activable__Page ]["{$current__language__page}_list"]['name'].'</strong></div> </div>';

									$each_lang__list = array_keys($fields__Setup[ $Activable__Page ]['language__tabs']);
									echo '<div class="-get--page--languages--lists">';
										echo '<ul>';
											foreach ( $each_lang__list as $lang__name ) {
												echo '<li><a class="'.( ( $lang__name == $current__language__page ) ? 'active' :  'hoverable').' activable" href="'.admin_url("admin.php?page=yts-{$Activable__Page}&language__tabs__page={$fields__Setup[ $Activable__Page ]["{$lang__name}_list"]['id']}").'">'.$fields__Setup[ $Activable__Page ]["{$lang__name}_list"]['name'].'</a></li>';
											}
										echo '</ul>';

									echo '</div>';
								echo '</div>';

							}

							/*echo '<pre>';
								print_r($fields__Setup[ $Activable__Page ]);
							echo '</pre>';
							die;*/

							if( isset( $this->Methods()['YTSSubmit'] ) ) {

								$this->SaveOptions($fields__Setup[ $Activable__Page ]);

								echo '<div id="message" class="updated notice notice-success is-dismissible">';
									echo '<p>'.((is_rtl()) ? 'تم حفظ الإعدادات بنجاح !!' : 'Settings saved !!').'</p>';
									echo '<button type="button" class="notice-dismiss" onClick="$(this).parent().remove();"><span class="screen-reader-text"></span></button>';
								echo '</div>';
							}


							echo '<div class="-New-YTC-Pannel-Boxes'.( ( isset( $fields__Setup[ $Activable__Page ]['hide__border'] ) && $fields__Setup[ $Activable__Page ]['hide__border'] == true ) ? ' --hide--page--border' : '' ).'">';

								echo '<form action="" method="POST" enctype="multipart/form-data">';
									$fields__Setup[ $Activable__Page ]['fields'] = $this->YC__CFM->SorterFields( $fields__Setup[ $Activable__Page ]['fields'] ,'ThemeOptions' );
									foreach ( $fields__Setup[ $Activable__Page ]['fields'] as $k => $field) {

										# CALLBACK FIELD.
											if( isset( $field['metaBox__path'] ) ){
												foreach ( $field['fields'] as $k => $id_field) {

													$CurrentValue = get_option($id_field);

													$field['Values'][ $id_field ] = $CurrentValue;
												}

												$this->YC__CFM->Require($field['metaBox__path'],$field);
											}

										# SINGLE FIELD.
											if( !isset( $field['metaBox__path'] ) ){

												if( !isset( $field['value'] ) || ( isset( $field['value'] ) && $field['important_value'] ) ){

													$CurrentValue = get_option($field['id']);
													
													if( !empty( $CurrentValue ) ){

														if( $field['type'] == 'File' && is_string( $CurrentValue ) ){
															$NewCurrentValue = get_option($field['id'].'_id');
															$CurrentValue = array('url'=>$CurrentValue,'id'=>$NewCurrentValue);
														}
														if( empty( $CurrentValue ) ) $CurrentValue = ( ( in_array( $field['id'] , $this->YC__CFM->ImportantArray ) ) ) ? array() : '';

														$field['value'] = $CurrentValue;
													}
												}
												#
												$this->YC__CFM->Fields__Part($field['type'],$field);
											}
									}

									echo '<input type="hidden" name="YTSSubmit" value="1" />';
									echo '<div class="YTSBottomBar">';
										$text = (is_rtl()) ? 'حفظ الإعدادات' : 'Save settings';
										echo '<button type="submit">'.$text.'<i class="fa-solid fa-arrow-left"></i></button>';
									echo '</div>';
								echo '</form>';

							echo '</div>';
						echo '</div>';

					echo '</Inseder--Appender>';

				echo '</yourcolorapi--conatiner>';
			}
		}

	# SETUP PAGE ACTION .
		public function Page__setup() {
			global $YC__CFM__global_setup_fields;

			if( isset( $YC__CFM__global_setup_fields['ThemeOptions'] ) && !empty( $YC__CFM__global_setup_fields['ThemeOptions'] ) ){
				$name = (is_rtl()) ? $this->PagesSetup['title'] : $this->PagesSetup['en_title'];

				add_menu_page( $name, $name, $this->PagesSetup['roles'], 'YTS', array($this, "PageContext"), 'dashicons-smiley' );

				foreach ( $YC__CFM__global_setup_fields['ThemeOptions'] as $pagename => $pagedata ) {

					$innername = (is_rtl()) ? $pagedata['title'] : $pagedata['en_title'];

					$id = 'yts-'.strtolower($pagename);

					add_submenu_page( 'YTS', $innername, $innername, $this->PagesSetup['roles'], $id, array($this,'PageContext') );
				}
			}
			#
			#add_submenu_page( 'YTS', 'إستيراد / تصدير', 'إستيراد / تصدير', $this->ConfigTheme['roles'], 'yts-dataexport', array($this, 'ImportExportPanel'));
		}

		public function Setup() {
			add_action('admin_menu', array($this, 'Page__setup'));
			#add_action( 'init', array( $this, 'QueryEndpoint'));
		}
}
(new ThemeOptions)->Setup();