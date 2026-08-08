<?php
/**
 * RUKN v3 PRICING GUIDES — price
 * إعادة بناء كاملة لودجت الأسعار بتصميم "أدلة الأسعار والتكاليف"
 * كروت: أيقونة متدرجة، عنوان، وصف، نطاق سعري (رقم كبير + وحدة)، رابط "اقرأ الدليل"
 * وضعين للكروت:
 *   - تلقائي: من بوستات خطط الأسعار 'price' (العنوان، المقتطف، الرابط،
 *             وبيانات إضافية من post meta: price_icon / price_range / price_unit)
 *   - يدوي:  كروت كاملة التحكم مطابقة للتصميم بالملي
 */
class price extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'price';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('before_title','cards_mode','content','default_unit','desc','icon','manual_prices','number','price_auto_settings','price_cards_mode_title','price_display_settings','price_head_settings','price_manual_settings','range','read_text','title','unit','url') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
			$cards_mode = 'manual';
			$manual_prices = array(
				array( 'icon'=>'<i class="fas fa-droplet"></i>', 'title'=>'تكلفة كشف التسربات', 'desc'=>'كشف دقيق بدون تكسير مع تقرير مصور.', 'range'=>'250 – 800', 'unit'=>'درهم تقريباً', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-layer-group"></i>', 'title'=>'تكلفة عزل الأسطح', 'desc'=>'حسب المساحة ونوع العزل المستخدم.', 'range'=>'15 – 35', 'unit'=>'درهم / قدم²', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-water"></i>', 'title'=>'تكلفة عزل الخزانات', 'desc'=>'عزل صحي آمن يطيل عمر الخزان.', 'range'=>'400 – 1200', 'unit'=>'درهم تقريباً', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-spray-can-sparkles"></i>', 'title'=>'تكلفة تنظيف الخزانات', 'desc'=>'تنظيف وتعقيم كامل بمواد معتمدة.', 'range'=>'150 – 500', 'unit'=>'درهم تقريباً', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-bug-slash"></i>', 'title'=>'تكلفة مكافحة الحشرات', 'desc'=>'مواد آمنة مع ضمان عدم العودة.', 'range'=>'120 – 450', 'unit'=>'درهم تقريباً', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-house-chimney"></i>', 'title'=>'تكلفة الصيانة المنزلية', 'desc'=>'صيانة شاملة للتكييف والسباكة والكهرباء.', 'range'=>'100 – 600', 'unit'=>'درهم تقريباً', 'url'=>'' ),
			);
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'الأسعار';
		if( !isset( $title ) || empty( $title ) ) $title = 'أدلة الأسعار {%والتكاليف%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'تقديرات شفافة تساعدك على معرفة التكلفة قبل اتخاذ القرار.';

		# ═══════════ إعدادات ═══════════
		if( empty( $number ) ) $number = 6;
		if( !isset( $cards_mode ) || empty( $cards_mode ) ) $cards_mode = 'auto';
		if( !isset( $read_text ) || empty( $read_text ) ) $read_text = 'اقرأ الدليل';
		if( !isset( $default_unit ) || empty( $default_unit ) ) $default_unit = 'درهم تقريباً';

		# ═══════════ تجهيز بيانات الكروت ═══════════
		$cards = array();

		if( $cards_mode == 'manual' && isset( $manual_prices ) && !empty( $manual_prices ) && is_array( $manual_prices ) ){

			# الوضع اليدوي — كروت من لوحة التحكم
			foreach ( $manual_prices as $mp ) {
				if( !isset( $mp['title'] ) || empty( $mp['title'] ) ) continue;
				$cards[] = array(
					'icon'  => ( isset( $mp['icon'] ) && !empty( $mp['icon'] ) ) ? $mp['icon'] : '<i class="fas fa-coins"></i>',
					'title' => $mp['title'],
					'desc'  => ( isset( $mp['desc'] ) ) ? $mp['desc'] : '',
					'range' => ( isset( $mp['range'] ) ) ? $mp['range'] : '',
					'unit'  => ( isset( $mp['unit'] ) && !empty( $mp['unit'] ) ) ? $mp['unit'] : $default_unit,
					'url'   => ( isset( $mp['url'] ) && !empty( $mp['url'] ) ) ? $mp['url'] : home_url('/contact-us/'),
				);
			}

		}else{

			# الوضع التلقائي — بوستات خطط الأسعار
			$PriceQuery = new WP_Query( array(
				'post_type'      => 'price',
				'posts_per_page' => $number,
				'post_status'    => 'publish',
			) );

			while ( $PriceQuery->have_posts() ) {
				$PriceQuery->the_post();
				$post_id = get_the_ID();

				$icon = get_post_meta( $post_id, 'price_icon', true );
				$unit = get_post_meta( $post_id, 'price_unit', true );

				$cards[] = array(
					'icon'  => ( !empty( $icon ) ) ? $icon : '<i class="fas fa-coins"></i>',
					'title' => get_the_title(),
					'desc'  => wp_trim_words( get_the_excerpt(), 12 ),
					'range' => get_post_meta( $post_id, 'price_range', true ),
					'unit'  => ( !empty( $unit ) ) ? $unit : $default_unit,
					'url'   => get_the_permalink(),
				);
			}
			wp_reset_postdata();
		}

		# ════════════════════════════════════════════════════════
		# OUTPUT — كروت تفاعلية + نموذج حجز/دفع (v1.4.7)
		# ════════════════════════════════════════════════════════
		$service_title = is_singular() ? get_the_title() : get_bloginfo( 'name' );
		$uid = 'kpp_widget_' . uniqid();

		echo '<div class="wrap">';
		echo '<div class="kayan-price-booking kayan-price-booking--widget" id="kayan-price-booking" data-service="'.esc_attr( $service_title ).'">';

			# رأس القسم
			echo '<div class="shead rv">';
				if( !empty( $before_title ) ) echo '<span class="tag">'.$before_title.'</span>';
				echo '<h2>'.$title.'</h2>';
				if( !empty( $content ) ) echo '<p>'.$content.'</p>';
			echo '</div>';

			# شبكة كروت الأسعار — قابلة للاختيار
			echo '<div class="price-grid kpp-packages" role="listbox" aria-label="باقات الأسعار">';
				$ci = 0;
				foreach ( $cards as $card ) {
					$amount = '';
					if ( class_exists( 'Kayan_Price_Pay' ) ) {
						$amount = Kayan_Price_Pay::extract_amount( $card['range'] );
					} elseif ( preg_match( '/(\d+(?:[.,]\d+)?)/u', (string) $card['range'], $m ) ) {
						$amount = str_replace( ',', '', $m[1] );
					}
					$active = ( 0 === $ci ) ? ' is-active' : '';
					echo '<div class="pcard rv kpp-selectable kpp-package'.$active.'" role="option" tabindex="0" aria-pressed="'.( 0 === $ci ? 'true' : 'false' ).'" data-package="'.esc_attr( $card['title'] ).'" data-amount="'.esc_attr( $amount ).'" data-amount-raw="'.esc_attr( $card['range'] ).'" data-currency="'.esc_attr( $card['unit'] ).'">';
						echo '<div class="pic">'.$card['icon'].'</div>';
						echo '<h3 class="kpp-package-name">'.$card['title'].'</h3>';
						if( !empty( $card['desc'] ) ) echo '<p class="kpp-package-desc">'.$card['desc'].'</p>';
						if( !empty( $card['range'] ) ){
							echo '<div class="range kpp-package-price">';
								echo '<b>'.$card['range'].'</b>';
								if( !empty( $card['unit'] ) ) echo '<small>'.$card['unit'].'</small>';
							echo '</div>';
						}
						echo '<span class="read">اختر الباقة <i class="fas fa-check"></i></span>';
					echo '</div>';
					$ci++;
				}
			echo '</div>';

			# نموذج الحجز المدمج + ادفع الآن
			if ( class_exists( 'Kayan_Price_Pay' ) ) {
				Kayan_Price_Pay::render_form( array(
					'service' => $service_title,
					'uid'     => $uid,
				) );
			}

		echo '</div>'; # .kayan-price-booking--widget
		echo '</div>'; # .wrap

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — أدلة الأسعار',
			'description'=>'كروت أدلة الأسعار والتكاليف بتصميم Rukn v3',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'price_head_settings',
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
					'id'=>'price_cards_mode_title',
					'title'=>'مصدر الكروت',
				),
				array(
					'type'=>'Radio',
					'id'=>'cards_mode',
					'title'=>'طريقة عرض الكروت',
					'options'=>array(
						'auto'  =>'تلقائي — من بوستات خطط الأسعار',
						'manual'=>'يدوي — كروت كاملة التحكم',
					)
				),
				array(
					'type'=>'Text',
					'id'=>'read_text',
					'title'=>'نص رابط الكارت (الافتراضي: اقرأ الدليل)',
				),
				array(
					'type'=>'Text',
					'id'=>'default_unit',
					'title'=>'الوحدة الافتراضية (الافتراضي: درهم تقريباً)',
				),

				array(
					'type'=>'Title',
					'id'=>'price_auto_settings',
					'title'=>'إعدادات الوضع التلقائي — البيانات الإضافية من حقول price_icon / price_range / price_unit في البوست',
				),
				array(
					'type'=>'Number',
					'id' => 'number',
					'title' =>'عدد الأدلة',
				),

				array(
					'type'=>'Title',
					'id'=>'price_manual_settings',
					'title'=>'إعدادات الوضع اليدوي (الكروت)',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'manual_prices',
					'title'=>'كروت الأسعار',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML)',
						),
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'عنوان الدليل (مثال: تكلفة كشف التسربات)',
						),
						array(
							'type'=>'Text',
							'id'=>'desc',
							'title'=>'وصف قصير',
						),
						array(
							'type'=>'Text',
							'id'=>'range',
							'title'=>'النطاق السعري (مثال: 250 – 800)',
						),
						array(
							'type'=>'Text',
							'id'=>'unit',
							'title'=>'الوحدة (مثال: درهم تقريباً أو درهم / قدم²)',
						),
						array(
							'type'=>'Text',
							'id'=>'url',
							'title'=>'رابط الدليل',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'price_display_settings',
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
(new price)->Setup();
