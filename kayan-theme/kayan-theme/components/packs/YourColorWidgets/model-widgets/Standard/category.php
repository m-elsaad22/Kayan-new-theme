<?php
/**
 * RUKN v3 SERVICES GRID — category
 * إعادة بناء كاملة لودجت التصنيفات بتصميم كروت الخدمات الجديد
 * وضعين للعرض:
 *   - تلقائي: يسحب تصنيفات ووردبريس (الاسم، الوصف، الأيقونة من term_meta، الرابط)
 *   - يدوي:  كروت كاملة التحكم (أيقونة/عنوان/وصف/مميزات/رابط) مطابقة للتصميم بالملي
 */
class Category extends YC__WidgetsMachine {

    public function __construct() {
        parent::__construct();

		$this->widget__name = 'category';
		$this->folder__name = basename(__DIR__);
		$this->ThemeStatic = (new ThemeStatic);

    }
	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('Button__show','before_title','but_text','button_Text','button_page','cards_mode','content','desc','features','hide_category_switch','icon','manual_cards','number','svc_auto_settings','svc_cards_mode_title','svc_cta_settings','svc_display_settings','svc_head_settings','svc_manual_settings','taxonomy_option','title','url') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
			$cards_mode = 'manual';
			$manual_cards = array(
				array( 'icon'=>'<i class="fas fa-droplet"></i>', 'title'=>'كشف تسربات المياه', 'desc'=>'تحديد دقيق لمصدر التسرب بدون أي تكسير.', 'features'=>'كاميرا حرارية متطورة\\nبدون تكسير 100%\\nتقرير مصور مفصّل\\nإصلاح فوري', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-layer-group"></i>', 'title'=>'عزل الأسطح', 'desc'=>'حماية كاملة من الحرارة وتسرب المياه.', 'features'=>'عزل فوم بولي يوريثان\\nأغشية بيتومينية معدّلة\\nطلاء عازل للحرارة\\nضمان حتى 10 سنوات', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-snowflake"></i>', 'title'=>'صيانة التكييف', 'desc'=>'أداء أفضل وهواء أنقى طوال الصيف.', 'features'=>'تنظيف شامل للفلاتر والكويل\\nإصلاح جميع الأعطال\\nشحن الفريون\\nضمان على قطع الغيار', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-spray-can-sparkles"></i>', 'title'=>'التنظيف والتعقيم', 'desc'=>'نظافة عميقة وتعقيم آمن لكل المساحات.', 'features'=>'تنظيف عميق شامل\\nتعقيم بالبخار\\nإزالة البقع العنيدة\\nمواد صديقة للبيئة', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-wrench"></i>', 'title'=>'أعمال السباكة', 'desc'=>'إصلاح وتركيب احترافي يدوم طويلاً.', 'features'=>'إصلاح تسربات الأنابيب\\nتركيب وتوصيل الصنابير\\nتسليك المجاري\\nفحص شبكات المياه', 'url'=>'' ),
				array( 'icon'=>'<i class="fas fa-bug-slash"></i>', 'title'=>'مكافحة الحشرات', 'desc'=>'إبادة آمنة وفعالة مع ضمان عدم العودة.', 'features'=>'مواد آمنة ومرخصة\\nإبادة فورية وكاملة\\nضمان عدم العودة\\nلا رائحة — آمن للأطفال', 'url'=>'' ),
			);
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'خدماتنا';
		if( !isset( $title ) || empty( $title ) ) $title = 'خدماتنا المنزلية {%المتكاملة%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'حلول احترافية شاملة تغطي كل احتياجات منزلك أو منشأتك بأعلى معايير الجودة والضمان.';

		# ═══════════ إعدادات الكروت ═══════════
		if( !isset( $but_text ) || empty( $but_text ) ) $but_text = 'طلب الخدمة';
		if( empty( $number ) ) $number = 6;
		if( !isset( $cards_mode ) || empty( $cards_mode ) ) $cards_mode = 'auto';

		# ═══════════ تجهيز بيانات الكروت ═══════════
		$cards = array();

		if( $cards_mode == 'manual' && isset( $manual_cards ) && !empty( $manual_cards ) && is_array( $manual_cards ) ){

			# الوضع اليدوي — كروت من لوحة التحكم
			foreach ( $manual_cards as $mc ) {
				if( !isset( $mc['title'] ) || empty( $mc['title'] ) ) continue;
				$features = array();
				if( isset( $mc['features'] ) && !empty( $mc['features'] ) ){
					foreach ( preg_split('/\r\n|\r|\n/', $mc['features']) as $line ) {
						$line = trim( $line );
						if( $line !== '' ) $features[] = $line;
					}
				}
				$cards[] = array(
					'icon'     => ( isset( $mc['icon'] ) && !empty( $mc['icon'] ) ) ? $mc['icon'] : '<i class="fas fa-broom"></i>',
					'title'    => $mc['title'],
					'desc'     => ( isset( $mc['desc'] ) ) ? $mc['desc'] : '',
					'features' => $features,
					'url'      => ( isset( $mc['url'] ) && !empty( $mc['url'] ) ) ? $mc['url'] : home_url('/contact-us/'),
				);
			}

		}else{

			# الوضع التلقائي — تصنيفات ووردبريس (نفس منطق الودجت القديمة)
			if( isset( $taxonomy_option ) && !empty( $taxonomy_option ) && is_array( $taxonomy_option ) ){
				$get_terms = array();
				foreach ( array_slice($taxonomy_option,0,$number) as $tx__value){
					$s_tems = get_term_by('id',$tx__value,'category');
					if( isset( $s_tems->term_id ) ) $get_terms[] = $s_tems;
				}
			}else{
				$TermsArgums = array(
					'taxonomy' => 'category',
					'number'   => $number,
				);
				$get_terms = get_terms($TermsArgums);
			}

			foreach ( ( is_array( $get_terms ) ? $get_terms : array() ) as $category ) {
				$icon = get_term_meta( $category->term_id,'icon',true );
				# مميزات اختيارية من term_meta (سطر لكل ميزة) لو موجودة
				$features_meta = get_term_meta( $category->term_id,'svc_features',true );
				$features = array();
				if( !empty( $features_meta ) ){
					foreach ( preg_split('/\r\n|\r|\n/', $features_meta) as $line ) {
						$line = trim( $line );
						if( $line !== '' ) $features[] = $line;
					}
				}
				$cards[] = array(
					'icon'     => ( !empty( $icon ) ) ? $icon : '<i class="fas fa-broom"></i>',
					'title'    => $category->name,
					'desc'     => wp_trim_words( $category->description, 15 ),
					'features' => $features,
					'url'      => get_term_link( $category ),
				);
			}
		}

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

			# شبكة الكروت
			echo '<div class="services-grid">';
				foreach ( $cards as $card ) {
					echo '<div class="svc rv">';
						echo '<div class="svc-ic">'.$card['icon'].'</div>';
						echo '<h3><a href="'.$card['url'].'" title="'.esc_attr( $card['title'] ).'">'.$card['title'].'</a></h3>';
						if( !empty( $card['desc'] ) ) echo '<p class="desc">'.$card['desc'].'</p>';
						if( !empty( $card['features'] ) ){
							echo '<ul>';
								foreach ( $card['features'] as $feature ) {
									echo '<li><i class="fas fa-check"></i> '.$feature.'</li>';
								}
							echo '</ul>';
						}
						if( empty( $hide_category_switch ) ){
							echo '<a href="'.$card['url'].'" class="svc-cta" title="'.esc_attr( $card['title'] ).'">'.$but_text.' <i class="fas fa-arrow-left"></i></a>';
						}
					echo '</div>';
				}
			echo '</div>';

			# زرار المزيد من الخدمات
			if( isset( $Button__show ) && !empty( $Button__show ) ){
				$more_url  = ( isset( $button_page ) && !empty( $button_page ) ) ? get_the_permalink( $button_page ) : home_url();
				$more_text = ( isset( $button_Text ) && !empty( $button_Text ) ) ? $button_Text : 'عرض جميع الخدمات';
				echo '<div class="svc-more-wrap rv">';
					echo '<a href="'.$more_url.'" class="btn btn-soft">'.$more_text.' <i class="fas fa-arrow-left"></i></a>';
				echo '</div>';
			}

		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — شبكة الخدمات',
			'description'=>'كروت الخدمات بتصميم Rukn v3',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'svc_head_settings',
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
					'id'=>'svc_cards_mode_title',
					'title'=>'مصدر الكروت',
				),
				array(
					'type'=>'Radio',
					'id'=>'cards_mode',
					'title'=>'طريقة عرض الكروت',
					'options'=>array(
						'auto'  =>'تلقائي — من تصنيفات ووردبريس',
						'manual'=>'يدوي — كروت كاملة التحكم',
					)
				),

				array(
					'type'=>'Title',
					'id'=>'svc_auto_settings',
					'title'=>'إعدادات الوضع التلقائي (التصنيفات)',
				),
				array(
					'type'=>'Number',
					'id' => 'number',
					'title' =>'عدد التصنيفات',
				),
				array(
			        'type'    => 'Taxonomy-CheckBox',
			        'id'      => 'taxonomy_option',
			        'title'   => 'اختار التصنيفات',
                    'taxonomy_name' => 'category',
                    'pre'=>10
			    ),

				array(
					'type'=>'Title',
					'id'=>'svc_manual_settings',
					'title'=>'إعدادات الوضع اليدوي (الكروت)',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'manual_cards',
					'title'=>'كروت الخدمات',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML)',
						),
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'اسم الخدمة',
						),
						array(
							'type'=>'Text',
							'id'=>'desc',
							'title'=>'وصف قصير',
						),
						array(
							'type'=>'TextArea',
							'id'=>'features',
							'title'=>'المميزات — سطر لكل ميزة',
						),
						array(
							'type'=>'Text',
							'id'=>'url',
							'title'=>'رابط الخدمة',
						),
					)
				),

				array(
					'type'=>'Title',
					'id'=>'svc_cta_settings',
					'title'=>'زرار الكارت وزرار المزيد',
				),
				array(
					'type'=>'Text',
					'id'=>'but_text',
					'title'=>'عنوان زرار الكارت (الافتراضي: طلب الخدمة)',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_category_switch',
					'title'=>'إخفاء زرار الكارت',
				),
				array(
	                'type'=>'Posts-Select',
	                'id' => 'button_page',
	                'post_type_name'=>'page',
	                'title' =>'صفحة "جميع الخدمات"',
	            ),
	            array(
	                'type'=>'Text',
	                'id' => 'button_Text',
	                'title' =>'عنوان زرار "جميع الخدمات"',
	            ),
				array(
					'type'=>'SwitchBox',
					'id' => 'Button__show',
					'title' =>'إظهار زرار المزيد من الخدمات',
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'svc_display_settings',
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
(new category)->Setup();
