<?php /**
 * 
 */
class sticky__features extends YC__WidgetsMachine{
	
	function __construct(){

		# WIDGET INFO 
			$this->widget__name = 'sticky__features';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);

		if( isset( $title ) ){
			if( empty( $title_color ) ) $title_color = 'var(--uicolor)';

			$title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$title);
			$title = str_replace('%}','</c--color>',$title);
		}

	    if( !isset( $services_form ) || isset( $services_form ) && empty( $services_form ) ) $services_form = array();
	    $services_form = Sort__this__list($services_form);
	  	$UNIQ = uniqid();
	  	$lazyload = get_option('lazyload');
		echo '<div class="container'.( ( isset( $largerContainer ) && $largerContainer == 'on' ) ? ' largerContainer' : '' ).'">';
			echo '<div class="-defult-widgets-felx-style-1">';

				if( isset( $before_title ) || isset( $title ) || isset( $content ) ){

					
					echo '<div class="-defult-widgets-title-style-1">';
						echo '<div class="-YC--main--wep-title-">';
							if( isset( $before_title ) && !empty( $before_title ) ) echo '<div class="sup-title-widget-defualt animation-hidden" data-animation-id="fadeInUpBig">'.$before_title.'</div>';
							if( isset( $title ) && !empty( $title ) ) echo '<h2 class="-widgets-h1-title animation-hidden" data-animation-id="fadeInUpBig">'.$title.'</h2>';
							if( isset( $content ) && !empty( $content ) ){
								echo '<div class="P-content animation-hidden" data-animation-id="fadeInUpBig">'.$content.'</div>';
							}
						echo '</div>';
						if( !empty( $first_button ) && isset( $first_button['button_mode'] ) && isset( $first_button[ $first_button['button_mode'] ] ) || !empty( $second_button ) && isset( $second_button['button_mode'] ) && isset( $second_button[ $second_button['button_mode'] ] ) ){
							echo '<div class="-defult-widgets-title--URLArea-v1">';
								if( !empty( $first_button ) && isset( $first_button['button_mode'] ) && isset( $first_button[ $first_button['button_mode'] ] ) ){
									$this->ThemeStatic->Part(
										'button_context',
										array(
											'attributes'=>'data-animation-id="fadeInUpBig" data-animation-delay="0.2s"',
											'class'=>' --Parent-URL-BTN animation-hidden',
											'href_class'=>'activable  btn-ket_2 -BTN--hoverable',
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
											'href_class'=>'activable  btn-ket_1 -BTN--hoverable button_url_2',
											'button_context'=>$second_button
										)
									);
								}	
							echo '</div>';
						}

					echo '</div>';
				}
			echo '</div>';
			if( isset( $services_form ) ){
				echo '<div class="-YC-gallery-featurs- animation-hidden" data-animation-id="fadeInUpBig" data-animation-delay="0.2s">';
					$VeDelay=0;
					foreach ($services_form as $k => $v) {
						$VeDelay = $VeDelay + 0.1;
						$uniqid = uniqid();
						if( isset( $v['icon'] ) ){
							$get_att_argums = array(
								'id'=>$v['icon_id'],
								'size'=>'gallery_features',
								'alt'=> $v['title'],
							);
						}
			           if(empty($button_cat)){
                        	$button_cat = ' المزيد ';
                        }
                        
						# FEATURES ITEMS
						echo '<div class="gallery--card" data-trigger-action="' .$uniqid. '">';
							if( isset( $v['_URL'] ) && !empty( $v['_URL'] ) )echo '<a href="' .$v['_URL']. '" data-trigger-url="' .$uniqid. '"></a>';
							echo '<div class="-gallery-content-">';
								echo '<div class="-gallery-img-">';
									echo (isset($get_att_argums)) ? YC_get_attachment($get_att_argums) : '';
								echo '</div>';
								echo '<div class="--gallery--content">';
									echo ( ( !empty( $v['title'] ) ) ) ? '<div class="sticky-title">'.$v['title'].'</div>' : '';
									echo ( ( !empty( $v['content'] ) ) ) ? '<div class="feature-image-text">'.$v['content'].'</div>' : '';
								echo '</div>';
							echo '</div>';
						echo '</div>';
						# END
					}
				echo '</div>';
			}
		echo '</div>';
	}
	public function widget__setup(){
		global $yc__widgets__center;
		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'title'=>'مميزات الاشتراك ',
			'description'=>' # شكل  2',
			'screen-shoot'=>'test_URL',
			'id'=>$this->widget__name,			
			'fields'=> array(
				array(
					'type'=>'Text',
					'id'=>'title',
					'title'=>'عنوان الشريحة ',
					'disc'=> "قَم بتمييز كلمات محدده في العنوان عن طريق إضافة ' {% ' قبل بداية الجملة و ' %} ' بعد نهاية الجملة .. كما يمكنك تحديد لون مخصص من خلال <p>#تحديد_الكلمات_المميزة_بالعنوان </p>" ,
				),
				array(
					'type'=>'Text',
					'id'=>'before_title',
					'title'=>'العنوان مختصر',
					'disc'=> "قم بوضع كلمة مختصرة مناسبة للزر" ,
				),
				array(
					'type'=>'Editor',
					'id' => 'content',
					'title' =>'وصف الشريحة ',
				),
				array(
					'id'=>'first_button',
					'type'=>'Models-Selector',
					'title'=>'إعدادات رابط خطط الاسعار',
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
					'title'=>'إعدادات رابط خطط الاسعار',
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
			        'type'=>'Title',
			        'id'=>'items__title2',
			        'title'=>'النص الثانوي ',
		      	),
				array(
					'type'=>'GroupsField',
					'id' => 'services_form',
					'title' =>'إنشاء عناصر المميزات ',
					'fields'=> array(
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'عنوان الميزة ',
						),
						array(
							'type'=>'Editor',
							'id'=>'content',
							'title'=>'محتوي الميزة' 
						),
						array(
							'id'    => '_URL',
							'type'  => 'Text',
							'title' => 'رابط الميزة',
						),
						array(
							'type'=>'File',
							'id'=>'icon',
							'title'=>'الصورة',
							'disc'=>'يجب ان تكون الصورة  330x250'
						),
						array(
							'type'=>'Text',
							'id'=>'number',
							'title'=>'ترتيب الميزة ',
						),
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
				array(
					'type'=>'SwitchBox',
					'id' => 'show_top_separator',
					'title' =>'تغيير لون الخلفيه',
					'disc'=>'هل تريد تغيير لون الخلفية؟',
				)

			),
		);
	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new sticky__features)->Setup();