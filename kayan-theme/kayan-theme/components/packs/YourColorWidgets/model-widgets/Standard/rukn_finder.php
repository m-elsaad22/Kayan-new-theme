<?php
/**
 * RUKN v3 SERVICE FINDER — rukn_finder (ودجت جديدة)
 * باحث الخدمات الذكي: "ما الخدمة التي تحتاجها؟"
 * خطوتين (الخدمة + الإمارة) → نتيجة فورية بوقت الاستجابة وحالة التوفر
 * + زرار إكمال الطلب عبر واتساب برسالة جاهزة فيها اختيارات العميل
 * مصدران للبيانات:
 *   - تلقائي: الخدمات من تصنيفات ووردبريس والمدن من تصنيف 'city'
 *             (وقت الاستجابة من term meta: city_response_time)
 *   - يدوي:  قوائم كاملة التحكم من لوحة التحكم
 */
class rukn_finder extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'rukn_finder';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('availability_text','before_title','button_text','content','data_mode','default_time','fn_data_settings','fn_display_settings','fn_head_settings','fn_texts_settings','hide_whatsapp_button','manual_cities','manual_services','name','result_sub_template','support_text','time','title','whatsapp_text') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'ابدأ الآن';
		if( !isset( $title ) || empty( $title ) ) $title = 'ما الخدمة التي {%تحتاجها؟%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'اختر الخدمة والإمارة واحصل على عرض سعر فوري مع تقدير لوقت الاستجابة وحالة التوفر.';

		# ═══════════ إعدادات ═══════════
		if( !isset( $data_mode ) || empty( $data_mode ) ) $data_mode = 'manual';
		if( !isset( $default_time ) || empty( $default_time ) ) $default_time = 'خلال ساعتين';
		if( !isset( $result_sub_template ) || empty( $result_sub_template ) ) $result_sub_template = 'فريق متخصص جاهز لخدمتك في {city} مع معاينة مجانية وعرض سعر شفاف.';
		if( !isset( $availability_text ) || empty( $availability_text ) ) $availability_text = 'متوفرة الآن';
		if( !isset( $support_text ) || empty( $support_text ) )           $support_text     = 'مفعّل 24/7';
		if( !isset( $button_text ) || empty( $button_text ) )             $button_text      = 'احصل على عرض سعر فوري';
		if( !isset( $whatsapp_text ) || empty( $whatsapp_text ) )         $whatsapp_text    = 'أكمل الطلب عبر واتساب';
		$whatsapp_number = get_option('whatsapp_number');

		# ═══════════ تجهيز الخدمات ═══════════
		$services = array();
		if( $data_mode == 'auto' ){
			$service_terms = get_terms( array( 'taxonomy'=>'category', 'hide_empty'=>false ) );
			foreach ( ( is_array( $service_terms ) ? $service_terms : array() ) as $service_term ) {
				$services[] = $service_term->name;
			}
		}elseif( isset( $manual_services ) && !empty( $manual_services ) ){
			foreach ( preg_split('/\r\n|\r|\n/', $manual_services) as $line ) {
				$line = trim( $line );
				if( $line !== '' ) $services[] = $line;
			}
		}
		if( empty( $services ) ){
			$services = array('كشف تسربات المياه','عزل الأسطح','عزل الخزانات','تنظيف الخزانات','مكافحة الحشرات','صيانة عامة','سباكة','كهرباء');
		}

		# ═══════════ تجهيز المدن + أوقات الاستجابة ═══════════
		$cities = array();
		if( $data_mode == 'auto' ){
			$city_terms = get_terms( array( 'taxonomy'=>'city', 'hide_empty'=>false ) );
			foreach ( ( is_array( $city_terms ) ? $city_terms : array() ) as $city_term ) {
				$city_time = get_term_meta( $city_term->term_id, 'city_response_time', true );
				$cities[] = array( 'name'=>$city_term->name, 'time'=>( ( !empty( $city_time ) ) ? $city_time : $default_time ) );
			}
		}elseif( isset( $manual_cities ) && !empty( $manual_cities ) && is_array( $manual_cities ) ){
			foreach ( $manual_cities as $mc ) {
				if( !isset( $mc['name'] ) || empty( $mc['name'] ) ) continue;
				$cities[] = array( 'name'=>$mc['name'], 'time'=>( ( isset( $mc['time'] ) && !empty( $mc['time'] ) ) ? $mc['time'] : $default_time ) );
			}
		}
		if( empty( $cities ) ){
			$cities = array(
				array( 'name'=>'دبي',         'time'=>'خلال 60 دقيقة' ),
				array( 'name'=>'أبوظبي',      'time'=>'خلال 90 دقيقة' ),
				array( 'name'=>'الشارقة',     'time'=>'خلال 75 دقيقة' ),
				array( 'name'=>'عجمان',       'time'=>'خلال 90 دقيقة' ),
				array( 'name'=>'رأس الخيمة',  'time'=>'خلال ساعتين' ),
				array( 'name'=>'الفجيرة',     'time'=>'خلال ساعتين' ),
				array( 'name'=>'أم القيوين',  'time'=>'خلال ساعتين' ),
			);
		}

		# خريطة الأوقات للجافاسكريبت
		$times_map = array();
		foreach ( $cities as $city_item ) $times_map[ $city_item['name'] ] = $city_item['time'];

		$uniqid = uniqid('ruknfinder');

		# ════════════════════════════════════════════════════════
		# OUTPUT — نفس بنية التصميم الجديد
		# ════════════════════════════════════════════════════════
		echo '<div class="wrap" id="'.$uniqid.'">';

			# رأس القسم
			echo '<div class="shead rv">';
				if( !empty( $before_title ) ) echo '<span class="tag">'.$before_title.'</span>';
				echo '<h2>'.$title.'</h2>';
				if( !empty( $content ) ) echo '<p>'.$content.'</p>';
			echo '</div>';

			# صندوق الباحث
			echo '<div class="finder rv">';

				echo '<div class="finder-step">';
					echo '<label><span class="snum">1</span> اختر الخدمة</label>';
					echo '<div class="sel"><i class="fas fa-screwdriver-wrench"></i>';
						echo '<select data-fn-svc aria-label="اختر الخدمة">';
							foreach ( $services as $service_name ) {
								echo '<option value="'.esc_attr( $service_name ).'">'.$service_name.'</option>';
							}
						echo '</select>';
					echo '</div>';
				echo '</div>';

				echo '<div class="finder-step">';
					echo '<label><span class="snum">2</span> اختر الإمارة</label>';
					echo '<div class="sel"><i class="fas fa-location-dot"></i>';
						echo '<select data-fn-city aria-label="اختر الإمارة">';
							foreach ( $cities as $city_item ) {
								echo '<option value="'.esc_attr( $city_item['name'] ).'">'.$city_item['name'].'</option>';
							}
						echo '</select>';
					echo '</div>';
				echo '</div>';

				echo '<div class="finder-step finder-go">';
					echo '<button class="btn btn-quote" data-fn-btn><i class="fas fa-bolt"></i> '.$button_text.'</button>';
				echo '</div>';

			echo '</div>';

			# صندوق النتيجة
			echo '<div class="finder-result" data-fn-result hidden>';
				echo '<div class="fr-lead">';
					echo '<b data-fr-title></b>';
					echo '<small data-fr-sub></small>';
				echo '</div>';
				echo '<div class="fr-stat"><i class="fas fa-clock"></i><b data-fr-time></b><small>وقت الاستجابة</small></div>';
				echo '<div class="fr-stat"><i class="fas fa-circle-check"></i><b class="fr-ok">'.$availability_text.'</b><small>حالة الخدمة</small></div>';
				echo '<div class="fr-stat"><i class="fas fa-headset"></i><b class="fr-ok">'.$support_text.'</b><small>دعم الطوارئ</small></div>';
			echo '</div>';

			# زرار الواتساب — برسالة جاهزة فيها اختيارات العميل
			if( !empty( $whatsapp_number ) && ( !isset( $hide_whatsapp_button ) || empty( $hide_whatsapp_button ) ) ){
				echo '<div class="finder-wa-wrap rv">';
					echo '<a href="https://wa.me/'.$whatsapp_number.'" target="_blank" rel="noopener" class="btn btn-wa" data-fn-wa data-wa-number="'.$whatsapp_number.'"><i class="fab fa-whatsapp"></i> '.$whatsapp_text.'</a>';
				echo '</div>';
			}

		echo '</div>';

		# ════════════════════════════════════════════════════════
		# INLINE JS — منطق الباحث
		# ════════════════════════════════════════════════════════
		echo '<script type="text/javascript">';
			echo '(function(){';
				echo 'var root=document.getElementById("'.$uniqid.'");if(!root)return;';
				echo 'var btn=root.querySelector("[data-fn-btn]");';
				echo 'var svc=root.querySelector("[data-fn-svc]");';
				echo 'var city=root.querySelector("[data-fn-city]");';
				echo 'var res=root.querySelector("[data-fn-result]");';
				echo 'var wa=root.querySelector("[data-fn-wa]");';
				echo 'var times='.json_encode( $times_map, JSON_UNESCAPED_UNICODE ).';';
				echo 'var subTemplate='.json_encode( $result_sub_template, JSON_UNESCAPED_UNICODE ).';';
				echo 'function updateWA(){';
					echo 'if(!wa)return;';
					echo 'var msg="مرحباً، أرغب في طلب خدمة: "+svc.value+" — في "+city.value;';
					echo 'wa.href="https://wa.me/"+wa.getAttribute("data-wa-number")+"?text="+encodeURIComponent(msg);';
				echo '}';
				echo 'btn.addEventListener("click",function(){';
					echo 'var s=svc.value,c=city.value;';
					echo 'root.querySelector("[data-fr-title]").textContent=s+" — "+c;';
					echo 'root.querySelector("[data-fr-sub]").textContent=subTemplate.split("{city}").join(c).split("{service}").join(s);';
					echo 'root.querySelector("[data-fr-time]").textContent=times[c]||'.json_encode( $default_time, JSON_UNESCAPED_UNICODE ).';';
					echo 'res.hidden=false;';
					echo 'updateWA();';
					echo 'res.scrollIntoView({behavior:"smooth",block:"center"});';
				echo '});';
				echo 'svc.addEventListener("change",updateWA);';
				echo 'city.addEventListener("change",updateWA);';
				echo 'updateWA();';
			echo '})();';
		echo '</script>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — باحث الخدمات الذكي (جديدة)',
			'description'=>'خطوتين: الخدمة + الإمارة → نتيجة فورية وطلب واتساب',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'fn_head_settings',
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
					'id'=>'fn_data_settings',
					'title'=>'مصدر البيانات',
				),
				array(
					'type'=>'Radio',
					'id'=>'data_mode',
					'title'=>'الخدمات والمدن',
					'options'=>array(
						'manual'=>'يدوي — قوائم كاملة التحكم',
						'auto'  =>'تلقائي — الخدمات من التصنيفات والمدن من تصنيف المدن',
					)
				),
				array(
					'type'=>'TextArea',
					'id'=>'manual_services',
					'title'=>'الخدمات (وضع يدوي) — سطر لكل خدمة',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'manual_cities',
					'title'=>'المدن وأوقات الاستجابة (وضع يدوي)',
					'fields'=> array(
						array(
							'type'=>'Text',
							'id'=>'name',
							'title'=>'اسم الإمارة/المدينة',
						),
						array(
							'type'=>'Text',
							'id'=>'time',
							'title'=>'وقت الاستجابة (مثال: خلال 60 دقيقة)',
						),
					)
				),
				array(
					'type'=>'Text',
					'id'=>'default_time',
					'title'=>'وقت الاستجابة الافتراضي (الافتراضي: خلال ساعتين)',
				),

				array(
					'type'=>'Title',
					'id'=>'fn_texts_settings',
					'title'=>'نصوص النتيجة والأزرار',
				),
				array(
					'type'=>'Text',
					'id'=>'button_text',
					'title'=>'نص زرار البحث',
				),
				array(
					'type'=>'Text',
					'id'=>'result_sub_template',
					'title'=>'قالب وصف النتيجة — استخدم {service} و {city}',
				),
				array(
					'type'=>'Text',
					'id'=>'availability_text',
					'title'=>'نص حالة الخدمة (الافتراضي: متوفرة الآن)',
				),
				array(
					'type'=>'Text',
					'id'=>'support_text',
					'title'=>'نص دعم الطوارئ (الافتراضي: مفعّل 24/7)',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_whatsapp_button',
					'title'=>'إخفاء زرار الواتساب',
				),
				array(
					'type'=>'Text',
					'id'=>'whatsapp_text',
					'title'=>'نص زرار الواتساب — الرقم من إعدادات القالب والرسالة بتتجهز تلقائياً باختيارات العميل',
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'fn_display_settings',
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
(new rukn_finder)->Setup();
