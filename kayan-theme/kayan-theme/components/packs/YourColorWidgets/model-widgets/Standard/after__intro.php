<?php /**
 * 
 */
class after__intro extends YC__WidgetsMachine{
	
	function __construct(){

		# WIDGET INFO 
			$this->widget__name = 'after__intro';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);

		if( isset( $title ) ){
			if( empty( $title_color ) ) $title_color = 'var(--uicolor2)';

			$title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$title);
			$title = str_replace('%}','</c--color>',$title);
		}

		if( isset( $secondary_title ) ){
			if( empty( $title_color ) ) $title_color = 'var(--uicolor)';

			$secondary_title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$secondary_title);
			$secondary_title = str_replace('%}','</c--color>',$secondary_title);
		}

		$phonenumber = get_option('phonenumber');
		$whatsapp_number = get_option('whatsapp_number');

		if( isset( $first_image ) ){
			$get_att_argums = array(
				'id'=>$first_image_id,
				'size'=>'about__us',
			);
			if( isset( $imagae__alt ) ) $get_att_argums['alt'] = $imagae__alt;
		}
		$bg_shap1 = get_template_directory_uri().'/components/styles/img/about-v1-shape1.webp';
		if(IsSpeed() == false){
		echo '<div class="--back-ground-after-into-">';
			echo '<img height="100%" width="100%" src="'.$bg_shap1.'"/>';
		echo '</div>';
		}
		echo '<div class="--intro--background">';
			echo '<div class="container">';
				echo '<div class="container--intro--items">';
					echo '<div class="-after__intro-image animation-hidden" data-animation-id="fadeInUpBig">';
						echo '<div class="--yc-after--intro-image--">';
							echo '<div class="first--image-left--after-intro">';
								echo ( ( isset( $get_att_argums ) ) ) ? YC_get_attachment( $get_att_argums ) : '';
							echo '</div>';
							echo '<div class="--image-after-intro---">';
								echo ( ( isset( $secondary__att_argums ) ) ) ? YC_get_attachment( $secondary__att_argums ) : '';
							echo '</div>';
						echo '</div>';
					echo '</div>';
					echo '<div class="-after_intro--img-point-">';
						echo '<div class="-after__intro-container">';

							if( isset( $before_title ) && !empty( $before_title ) ) echo '<div class="-widget-after-intro-h4 animation-hidden" data-animation-id="fadeInUpBig">'.$before_title.'</div>';
							if( isset( $title ) && !empty( $title ) ) echo '<h1 class="-widget-after-intro-h1 animation-hidden" data-animation-id="fadeInUpBig">'.$title.'</h1>';
							if( isset( $text_content ) && !empty( $text_content ) ) echo '<div class="-pp-content animation-hidden" data-animation-id="fadeInUpBig">'.$text_content.'</div>';
							echo '<div class="after--intro-point--image-">';
								if( isset( $services_text ) && !empty( $services_text ) ){
									echo '<div class="-after__intro-contain--lists">';
					                	$VeDelay = 0;
							        	foreach ( $services_text as $item__features ) {
							        		$VeDelay = $VeDelay + 0.1;
							        		echo '<div class="about_lists-style1 animation-hidden" data-animation-id="fadeInUpBig" data-animation-delay="'.$VeDelay.'s">';
						                        echo '<div class="about_lists-icon">';
						                        	echo '<div class="about_icon">';
						                            echo '</div>';
						                        echo '</div>';
						                        echo'<div class="features_icon">';
						                        	if( isset( $item__features['service_info'] ) || isset( $item__features['after_icon'] ) ){
						                        	 	echo '<div class="about_lists-title">'.$item__features['after_icon'].''.$item__features['service_info'].'</div>';
						                        	}
						                      	echo'</div>';
						                    echo '</div>';    
							            } 
				                    echo '</div>';    
								}
							echo '</div>';
							echo '<div class="-defult-intro-title-URL animation-hidden" data-animation-id="fadeInUpBig">';

								if( !empty( $first_button ) && isset( $first_button['button_mode'] ) && isset( $first_button[ $first_button['button_mode'] ] ) || !empty( $second_button ) && isset( $second_button['button_mode'] ) && isset( $second_button[ $second_button['button_mode'] ] ) ){
									echo '<div class="-defult-widgets-title--URLArea-v1">';
										if( !empty( $first_button ) && isset( $first_button['button_mode'] ) && isset( $first_button[ $first_button['button_mode'] ] ) ){
											$this->ThemeStatic->Part(
												'button_context',
												array(
													'attributes'=>'data-animation-id="fadeInUpBig" data-animation-delay="0.2s"',
													'class'=>'--Parent-URL-BTN animation-hidden',
													'href_class'=>'btn-ket_1 -BTN--hoverable',
													'button_context'=>$first_button
												)
											);
										}
										if( !empty( $second_button ) && isset( $second_button['button_mode'] ) && isset( $second_button[ $second_button['button_mode'] ] ) ){
											$this->ThemeStatic->Part(
												'button_context',
												array(
													'attributes'=>'data-animation-id="fadeInUpBig" data-animation-delay="0.2s"',
													'class'=>' animation-hidden',
													'href_class'=>'activable -BTN--hoverable btn-ket_2',
													'button_context'=>$second_button
												)
											);
										}
									echo '</div>';
								}
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'title'=>'محتوى نصي بعد الـ intro',
			'description'=>' # شكل ',
			'screen-shoot'=>'test_URL',
			'id'=>$this->widget__name,
			'fields'=> array(
				array(
					'type'=>'Text',
					'id'=>'before_title',
					'title'=>'قبل العنوان ',
				),				
				array(
					'type'=>'Text',
					'id'=>'title',
					'title'=>'عنوان الشريحة ',
					'disc'=> "قَم بتمييز كلمات محدده في العنوان عن طريق إضافة ' {% ' قبل بداية الجملة و ' %} ' بعد نهاية الجملة .. كما يمكنك تحديد لون مخصص من خلال <p>#تحديد_الكلمات_المميزة_بالعنوان </p>" ,
				),
				array(
					'type'=>'Editor',
					'id'=>'text_content',
					'title'=>'وصف الشريحة',
				),
				array(
					'type'=>'File',
					'id' => 'first_image',
					'title' =>'صورة',
					'disc'=>'يجب ان تكون الصورة  616x552'
				),
				array(
					'title'  => 'المميزات',
					'en_title'=> 'services',
					'type'  => 'GroupsField',
					'id'    => 'services_text',
					'fields' => array(
						array(
							'title'  => 'الميزة',
							'en_title'=> 'service',
							'type'=>'Text',
							'id'    => 'service_info',
						),
						array(
							'type'=>'TextArea_Code',
							'id'=>'after_icon',
							'title'=>'الايقونة',
						),						
					)
				),
				array(
					'id'=>'first_button',
					'type'=>'Models-Selector',
					'title'=>'أعدادت الزر الاول after intro',
					'select_field'=>array(
						'id'=>'button_mode',
						'type'=>'Select',
						'selected_shows'=>true,
						'title'=>'تحديد نوع رابط الزرار',
						'options'=>array(
							'default' => 'بدون تحديد ',
							'manual' => 'يدويا',
							'watshapp'=>'WhatsApp',
							'phonenumber'=>'Phone',
							'page'=>'Page',
						),
					),
					'create_fields'=>true,
					'choose_fields'=>array(
						'manual' => array(
							'id'=>'manual',
							'title' => 'يدوي',
							'fields'=> array(
								array(
									'id'    => 'button__URL',
									'type'  => 'Text',
									'title' => 'الرابط',
								),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),						
							),
						),
						'watshapp'=>array(
							'id'=>'watshapp',
							'title' => 'رقم watshapp',
							'fields'=> array(
								array(
									'id'    => 'watshapp',
									'type'  => 'Text',
									'title' => 'رقم watshapp',
								),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),						
							),
						),
						'phonenumber'=>array(
							'id'=>'phonenumber',
							'title' => 'رقم phonenumber',
							'fields'=> array(
								array(
									'id'    => 'phonenumber',
									'type'  => 'Text',
									'title' => 'phonenumber',
								),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),						
							),
						),
						'page'=>array(
							'id'=>'page',
							'title' => 'تحديد صفحة من الصفحات',
							'fields'=> array(
					            array(
					                'type'=>'Posts-Select',
					                'id' => 'button_page',
					                'post_type_name'=>'page',
					                'title' =>'تحديد الصفحة',
					            ),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),			            
					            		            
							),
						)
					)
				),
				array(
					'id'=>'second_button',
					'type'=>'Models-Selector',
					'title'=>'أعدادت الزر الثاني after intro',
					'select_field'=>array(
						'id'=>'button_mode',
						'type'=>'Select',
						'selected_shows'=>true,
						'title'=>'تحديد نوع رابط الزرار',
						'options'=>array(
							'default' => 'بدون تحديد ',
							'manual' => 'يدويا',
							'watshapp'=>'WhatsApp',
							'phonenumber'=>'Phone',
							'page'=>'Page',
						),
					),
					'create_fields'=>true,
					'choose_fields'=>array(
						'manual' => array(
							'id'=>'manual',
							'title' => 'يدوي',
							'fields'=> array(
								array(
									'id'    => 'button__URL',
									'type'  => 'Text',
									'title' => 'الرابط',
								),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),						
							),
						),
						'watshapp'=>array(
							'id'=>'watshapp',
							'title' => 'رقم watshapp',
							'fields'=> array(
								array(
									'id'    => 'watshapp',
									'type'  => 'Text',
									'title' => 'رقم watshapp',
								),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),						
							),
						),
						'phonenumber'=>array(
							'id'=>'phonenumber',
							'title' => 'رقم phonenumber',
							'fields'=> array(
								array(
									'id'    => 'phonenumber',
									'type'  => 'Text',
									'title' => 'phonenumber',
								),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),						
							),
						),
						'page'=>array(
							'id'=>'page',
							'title' => 'تحديد صفحة من الصفحات',
							'fields'=> array(
					            array(
					                'type'=>'Posts-Select',
					                'id' => 'button_page',
					                'post_type_name'=>'page',
					                'title' =>'تحديد الصفحة',
					            ),
					            array(
					                'type'=>'Text',
					                'id' => 'button_Text',
					                'title' =>'إضافة عنوان للزرار الاول',
					            ),
								array(
									'type'=>'TextArea_Code',
									'id'=>'button_Icon',
									'title'=>'ايقونة الزرار الاول',
								),			            
					            		            
							),
						)
					)
				),
				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'wsedewdfd',
					'title' =>'إعدادات الظهور ',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'hide_section__switch',
					'title' =>'هل تريد إخفاء هذه الشريحة مؤقتاً',
				),		
				array(
					'type'=>'SwitchBox',
					'id' => 'mobile_hide_section__switch',
					'title' =>'هل تريد إخفاء هذه الشريحة مؤقتاً في الموبيل',
				),	
			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new after__intro)->Setup();