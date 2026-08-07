<?php
/**
 * RUKN v3 SERVICE AREAS — city__widget
 * إعادة بناء كاملة لودجت المدن بتصميم "خدماتنا في جميع إمارات الدولة"
 * العمود الأيمن: بطاقة خريطة الإمارات الكحلية بالدبابيس النابضة
 * العمود الأيسر: كروت المدن القابلة للفتح (Accordion) بشرائح الخدمات
 * وضعين للكروت:
 *   - تلقائي: يسحب مصطلحات تصنيف المدن 'city' (الاسم، الرابط، الوصف، وشرائح خدمات من term meta)
 *   - يدوي:  كروت كاملة التحكم مطابقة للتصميم بالملي
 */
class city__widget extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'city__widget';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('areas_auto_settings','areas_cards_mode_title','areas_cta_settings','areas_display_settings','areas_head_settings','areas_manual_settings','areas_map_settings','before_title','card_icon','cards_mode','content','cta_card_sub','cta_card_title','cta_card_url','hide_cta_card','hide_map_card','icon','manual_cities','map_badges','map_sub','map_title','number','services','small','taxonomy_option','title','url') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
			$cards_mode = 'manual';
			$manual_cities = array(
				array( 'icon'=>'<i class="fas fa-city"></i>', 'title'=>'دبي', 'small'=>'6 خدمات متوفرة', 'services'=>'كشف تسربات\\nعزل\\nتكييف\\nتنظيف\\nسباكة\\nمكافحة حشرات', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-city"></i>', 'title'=>'أبوظبي', 'small'=>'6 خدمات متوفرة', 'services'=>'كشف تسربات\\nعزل\\nتكييف\\nتنظيف\\nسباكة\\nمكافحة حشرات', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-city"></i>', 'title'=>'الشارقة', 'small'=>'6 خدمات متوفرة', 'services'=>'كشف تسربات\\nعزل\\nتكييف\\nتنظيف\\nسباكة\\nمكافحة حشرات', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-city"></i>', 'title'=>'عجمان', 'small'=>'5 خدمات متوفرة', 'services'=>'كشف تسربات\\nعزل\\nتكييف\\nتنظيف\\nسباكة', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-city"></i>', 'title'=>'رأس الخيمة', 'small'=>'5 خدمات متوفرة', 'services'=>'كشف تسربات\\nعزل\\nتكييف\\nتنظيف\\nمكافحة حشرات', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-city"></i>', 'title'=>'الفجيرة', 'small'=>'4 خدمات متوفرة', 'services'=>'كشف تسربات\\nعزل\\nتكييف\\nتنظيف', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-city"></i>', 'title'=>'أم القيوين', 'small'=>'4 خدمات متوفرة', 'services'=>'كشف تسربات\\nعزل\\nتكييف\\nتنظيف', 'url'=>'' ),
			);
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'مناطق الخدمة';
		if( !isset( $title ) || empty( $title ) ) $title = 'خدماتنا في جميع {%إمارات الدولة%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'أينما كنت في الإمارات، فريق '.esc_html( get_bloginfo('name') ).' قريب منك وجاهز للخدمة.';

		# ═══════════ بطاقة الخريطة ═══════════
		if( !isset( $map_title ) || empty( $map_title ) ) $map_title = 'تغطية كاملة لـ 7 إمارات';
		if( !isset( $map_sub ) || empty( $map_sub ) )     $map_sub   = 'استجابة سريعة وفريق محلي في كل إمارة.';

		if( !isset( $map_badges ) || empty( $map_badges ) || !is_array( $map_badges ) ){
			$map_badges = array(
				array( 'icon'=>'<i class="fas fa-location-dot" style="color:var(--aqua)"></i>', 'title'=>'7 إمارات' ),
				array( 'icon'=>'<i class="fas fa-bolt" style="color:var(--aqua)"></i>',         'title'=>'استجابة خلال ساعة' ),
			);
		}

		# ═══════════ إعدادات الكروت ═══════════
		if( empty( $number ) ) $number = 7;
		if( !isset( $cards_mode ) || empty( $cards_mode ) ) $cards_mode = 'auto';
		if( !isset( $card_icon ) || empty( $card_icon ) ) $card_icon = '<i class="fas fa-city"></i>';

		# ═══════════ تجهيز بيانات كروت المدن ═══════════
		$cards = array();

		if( $cards_mode == 'manual' && isset( $manual_cities ) && !empty( $manual_cities ) && is_array( $manual_cities ) ){

			# الوضع اليدوي — كروت من لوحة التحكم
			foreach ( $manual_cities as $mc ) {
				if( !isset( $mc['title'] ) || empty( $mc['title'] ) ) continue;
				$services = array();
				if( isset( $mc['services'] ) && !empty( $mc['services'] ) ){
					foreach ( preg_split('/\r\n|\r|\n/', $mc['services']) as $line ) {
						$line = trim( $line );
						if( $line !== '' ) $services[] = $line;
					}
				}
				$small = ( isset( $mc['small'] ) && !empty( $mc['small'] ) ) ? $mc['small'] : ( count( $services ).' خدمات متوفرة' );
				$cards[] = array(
					'icon'     => ( isset( $mc['icon'] ) && !empty( $mc['icon'] ) ) ? $mc['icon'] : $card_icon,
					'title'    => $mc['title'],
					'small'    => $small,
					'services' => $services,
					'url'      => ( isset( $mc['url'] ) ) ? $mc['url'] : '',
				);
			}

		}else{

			# الوضع التلقائي — تصنيف المدن (نفس منطق الودجت القديمة)
			if( isset( $taxonomy_option ) && !empty( $taxonomy_option ) && is_array( $taxonomy_option ) ){
				$get_terms = array();
				foreach ( array_slice($taxonomy_option,0,$number) as $tx__value) {
					$s_tems = get_term_by('id',$tx__value,'city');
					if( isset( $s_tems->term_id ) ) $get_terms[] = $s_tems;
				}
			}else{
				$TermsArgums = array(
					'taxonomy'   => 'city',
					'number'     => $number,
					'hide_empty' => false,
				);
				$get_terms = get_terms($TermsArgums);
			}

			foreach ( ( is_array( $get_terms ) ? $get_terms : array() ) as $city_term ) {
				# شرائح الخدمات من term meta (سطر لكل خدمة) لو موجودة
				$services_meta = get_term_meta( $city_term->term_id,'city_services',true );
				$services = array();
				if( !empty( $services_meta ) ){
					foreach ( preg_split('/\r\n|\r|\n/', $services_meta) as $line ) {
						$line = trim( $line );
						if( $line !== '' ) $services[] = $line;
					}
				}
				$term_icon = get_term_meta( $city_term->term_id,'icon',true );
				$small = ( !empty( $services ) ) ? count( $services ).' خدمات متوفرة' : wp_trim_words( $city_term->description, 6 );
				if( empty( $small ) ) $small = 'خدماتنا متوفرة';
				$cards[] = array(
					'icon'     => ( !empty( $term_icon ) ) ? $term_icon : $card_icon,
					'title'    => $city_term->name,
					'small'    => $small,
					'services' => $services,
					'url'      => get_term_link( $city_term ),
				);
			}
		}

		# ═══════════ كارت "لم تجد مدينتك؟" ═══════════
		if( !isset( $cta_card_title ) || empty( $cta_card_title ) ) $cta_card_title = 'لم تجد مدينتك؟';
		if( !isset( $cta_card_sub ) || empty( $cta_card_sub ) )     $cta_card_sub   = 'تواصل معنا الآن';
		if( !isset( $cta_card_url ) || empty( $cta_card_url ) )     $cta_card_url   = home_url('/contact-us/');

		# ════════════════════════════════════════════════════════
		# OUTPUT — نفس بنية التصميم الجديد
		# ════════════════════════════════════════════════════════
		echo '<div class="wrap">';

			# رأس القسم
			echo '<div class="shead rv">';
				if( !empty( $before_title ) ) echo '<span class="tag">'.$before_title.'</span>';
				echo '<h2>'.$title.'</h2>';
				if( !empty( $content ) ) echo '<p>'.$content.'</p>';
			echo '</div>';

			echo '<div class="areas">';

				# ═══════════ بطاقة الخريطة الكحلية ═══════════
				if( !isset( $hide_map_card ) || empty( $hide_map_card ) ){
					echo '<div class="area-map rv-l">';
						echo '<div><h3>'.$map_title.'</h3><p>'.$map_sub.'</p></div>';
						echo '<div class="mp">';
							echo '<svg class="uae-svg" viewBox="0 0 300 220" aria-hidden="true">';
								echo '<path d="M40,70 L90,40 L150,30 L210,45 L260,55 L270,90 L250,130 L235,175 L180,195 L120,185 L70,160 L45,120 Z"/>';
							echo '</svg>';
							echo '<span class="pin" style="top:55%;left:42%"></span>';
							echo '<span class="pin" style="top:70%;left:28%"></span>';
							echo '<span class="pin" style="top:40%;left:60%"></span>';
							echo '<span class="pin" style="top:35%;left:48%"></span>';
						echo '</div>';
						echo '<div class="dash-trust" style="margin-top:8px">';
							foreach ( $map_badges as $badge ) {
								if( !isset( $badge['title'] ) || empty( $badge['title'] ) ) continue;
								$badge_icon = ( isset( $badge['icon'] ) && !empty( $badge['icon'] ) ) ? $badge['icon'] : '<i class="fas fa-circle-check" style="color:var(--aqua)"></i>';
								echo '<span class="dt">'.$badge_icon.' '.$badge['title'].'</span>';
							}
						echo '</div>';
					echo '</div>';
				}

				# ═══════════ كروت المدن ═══════════
				echo '<div class="area-cards">';

					foreach ( $cards as $card ) {
						echo '<div class="acard rv" data-acard-toggle>';
							echo '<div class="ah">';
								echo $card['icon'];
								echo '<div>';
									if( !empty( $card['url'] ) ){
										echo '<a href="'.$card['url'].'" class="acard-link" title="'.esc_attr( $card['title'] ).'"><b>'.$card['title'].'</b></a>';
									}else{
										echo '<b>'.$card['title'].'</b>';
									}
									echo '<small>'.$card['small'].'</small>';
								echo '</div>';
							echo '</div>';
							if( !empty( $card['services'] ) ){
								echo '<div class="svcs">';
									foreach ( $card['services'] as $srv ) {
										echo '<span>'.$srv.'</span>';
									}
								echo '</div>';
							}
						echo '</div>';
					}

					# كارت "لم تجد مدينتك؟"
					if( !isset( $hide_cta_card ) || empty( $hide_cta_card ) ){
						echo '<div class="acard acard-cta rv" onclick="location.href=\''.$cta_card_url.'\'">';
							echo '<div>';
								echo '<i class="fas fa-headset" style="font-size:26px;margin-bottom:8px"></i>';
								echo '<b style="color:#fff">'.$cta_card_title.'</b>';
								echo '<small style="color:rgba(255,255,255,.8)">'.$cta_card_sub.'</small>';
							echo '</div>';
						echo '</div>';
					}

				echo '</div>';

			echo '</div>';

		echo '</div>';

		# ════════════════════════════════════════════════════════
		# INLINE JS — فتح/غلق شرائح الخدمات (روابط المدن شغالة عادي)
		# ════════════════════════════════════════════════════════
		echo '<script type="text/javascript">';
			echo 'document.querySelectorAll("[data-acard-toggle]").forEach(function(card){';
				echo 'card.addEventListener("click",function(e){';
					echo 'if(e.target.closest("a"))return;';
					echo 'card.classList.toggle("open");';
				echo '});';
			echo '});';
		echo '</script>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — مناطق الخدمة (المدن)',
			'description'=>'خريطة الإمارات + كروت المدن بتصميم Rukn v3',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'areas_head_settings',
					'title'=>'رأس القسم',
				),
				array(
					'type'=>'Text',
					'id'=>'before_title',
					'title'=>'الشارة فوق العنوان (Tag)',
				),
				array(
					'type'=>'Text',
					'id'=>'title',
					'title'=>'عنوان الشريحة',
					'disc'=> "قَم بتمييز كلمات محددة في العنوان بتدرج لوني عن طريق إضافة ' {% ' قبل الكلمة و ' %} ' بعدها",
				),
				array(
					'type'=>'Editor',
					'id' => 'content',
					'title' =>'وصف الشريحة',
				),

				array(
					'type'=>'Title',
					'id'=>'areas_map_settings',
					'title'=>'بطاقة خريطة الإمارات (الكحلية)',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_map_card',
					'title'=>'إخفاء بطاقة الخريطة',
				),
				array(
					'type'=>'Text',
					'id'=>'map_title',
					'title'=>'عنوان البطاقة',
				),
				array(
					'type'=>'Text',
					'id'=>'map_sub',
					'title'=>'النص الفرعي للبطاقة',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'map_badges',
					'title'=>'شارات أسفل الخريطة',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML)',
						),
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'النص',
						),
					)
				),

				array(
					'type'=>'Title',
					'id'=>'areas_cards_mode_title',
					'title'=>'مصدر كروت المدن',
				),
				array(
					'type'=>'Radio',
					'id'=>'cards_mode',
					'title'=>'طريقة عرض الكروت',
					'options'=>array(
						'auto'  =>'تلقائي — من تصنيف المدن',
						'manual'=>'يدوي — كروت كاملة التحكم',
					)
				),
				array(
					'type'=>'TextArea_Code',
					'id'=>'card_icon',
					'title'=>'الأيقونة الافتراضية للكروت (HTML)',
				),

				array(
					'type'=>'Title',
					'id'=>'areas_auto_settings',
					'title'=>'إعدادات الوضع التلقائي (تصنيف المدن)',
				),
				array(
					'type'=>'Number',
					'id' => 'number',
					'title' =>'عدد المدن',
				),
				array(
			        'type'    => 'Taxonomy-CheckBox',
			        'id'      => 'taxonomy_option',
			        'title'   => 'اختار المدن',
                    'taxonomy_name' => 'city',
                    'pre'=>10
			    ),

				array(
					'type'=>'Title',
					'id'=>'areas_manual_settings',
					'title'=>'إعدادات الوضع اليدوي (الكروت)',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'manual_cities',
					'title'=>'كروت المدن',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML) — اتركها فارغة للأيقونة الافتراضية',
						),
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'اسم المدينة',
						),
						array(
							'type'=>'Text',
							'id'=>'small',
							'title'=>'النص الصغير — اتركه فارغاً لعدّ الخدمات تلقائياً',
						),
						array(
							'type'=>'TextArea',
							'id'=>'services',
							'title'=>'الخدمات — سطر لكل خدمة (تظهر عند فتح الكارت)',
						),
						array(
							'type'=>'Text',
							'id'=>'url',
							'title'=>'رابط صفحة المدينة (اختياري)',
						),
					)
				),

				array(
					'type'=>'Title',
					'id'=>'areas_cta_settings',
					'title'=>'كارت "لم تجد مدينتك؟"',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_cta_card',
					'title'=>'إخفاء الكارت',
				),
				array(
					'type'=>'Text',
					'id'=>'cta_card_title',
					'title'=>'عنوان الكارت',
				),
				array(
					'type'=>'Text',
					'id'=>'cta_card_sub',
					'title'=>'النص الفرعي',
				),
				array(
					'type'=>'Text',
					'id'=>'cta_card_url',
					'title'=>'الرابط',
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'areas_display_settings',
					'title' =>'إعدادات الظهور',
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
					'title' =>'خلفية بيضاء للشريحة',
					'disc'=>'التبديل بين الخلفية الفاتحة والبيضاء لعمل تناوب بين الأقسام',
				)

			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new city__widget)->Setup();
