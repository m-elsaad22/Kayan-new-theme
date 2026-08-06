<?php
/**
 * RUKN v3 WHY US — benefits
 * إعادة بناء كاملة لودجت المميزات بتصميم "لماذا يختار الآلاف"
 * العمود الأيمن: بطاقة متدرجة كحلية لاصقة فيها تايم لاين رحلة العميل (4 خطوات)
 * العمود الأيسر: شبكة 8 كروت مميزات
 * كل المحتوى قابل للتعديل من لوحة التحكم مع قيم افتراضية مطابقة للتصميم
 */
class benefits extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'benefits';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('before_title','content','desc','feature_cards','hide_timeline','icon','step_desc','step_title','timeline_steps','timeline_sub','timeline_title','title','why_cards_settings','why_display_settings','why_head_settings','why_timeline_settings') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'لماذا نحن';
		if( !isset( $title ) || empty( $title ) ) $title = 'لماذا يختار الآلاف {%'.esc_html( get_bloginfo('name') ).'؟%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'نجمع بين التقنية المتطورة والخبرة العميقة والضمان الحقيقي لنمنحك راحة بال كاملة.';

		# ═══════════ بطاقة التايم لاين ═══════════
		if( !isset( $timeline_title ) || empty( $timeline_title ) ) $timeline_title = 'رحلتك معنا بسيطة وواضحة';
		if( !isset( $timeline_sub ) || empty( $timeline_sub ) )     $timeline_sub   = 'من أول اتصال إلى تسليم العمل بضمان مكتوب.';

		if( !isset( $timeline_steps ) || empty( $timeline_steps ) || !is_array( $timeline_steps ) ){
			$timeline_steps = array(
				array( 'step_title'=>'تواصل ومعاينة مجانية', 'step_desc'=>'نصل إليك ونعاين الموقع بدون أي رسوم.' ),
				array( 'step_title'=>'عرض سعر شفاف',          'step_desc'=>'تكلفة واضحة بدون رسوم خفية.' ),
				array( 'step_title'=>'تنفيذ احترافي',          'step_desc'=>'فريق معتمد بأحدث الأجهزة.' ),
				array( 'step_title'=>'ضمان مكتوب ومتابعة',    'step_desc'=>'ضمان موثق ودعم مستمر بعد الخدمة.' ),
			);
		}

		# ═══════════ كروت المميزات ═══════════
		if( !isset( $feature_cards ) || empty( $feature_cards ) || !is_array( $feature_cards ) ){
			$feature_cards = array(
				array( 'icon'=>'<i class="fas fa-microchip"></i>',        'title'=>'تقنية متطورة',   'desc'=>'أجهزة الكشف الحراري والصوتي الأحدث في السوق.' ),
				array( 'icon'=>'<i class="fas fa-user-shield"></i>',      'title'=>'فريق معتمد',     'desc'=>'جميع فنيينا حاصلون على شهادات اعتماد دولية.' ),
				array( 'icon'=>'<i class="fas fa-bolt"></i>',             'title'=>'استجابة سريعة',  'desc'=>'نصل إليك خلال ساعة في حالات الطوارئ.' ),
				array( 'icon'=>'<i class="fas fa-file-contract"></i>',    'title'=>'ضمان حقيقي',     'desc'=>'ضمان مكتوب وموثّق لجميع الأعمال.' ),
				array( 'icon'=>'<i class="fas fa-tags"></i>',             'title'=>'أسعار تنافسية',  'desc'=>'أفضل جودة بأفضل سعر وبدون رسوم خفية.' ),
				array( 'icon'=>'<i class="fas fa-map-location-dot"></i>', 'title'=>'تغطية شاملة',    'desc'=>'جميع إمارات الدولة السبع بلا استثناء.' ),
				array( 'icon'=>'<i class="fas fa-award"></i>',            'title'=>'خبرة 12 عاماً',  'desc'=>'سجل حافل في السوق الإماراتي.' ),
				array( 'icon'=>'<i class="fas fa-headset"></i>',          'title'=>'دعم مستمر',      'desc'=>'خدمة عملاء على مدار الساعة 24/7.' ),
			);
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

			echo '<div class="why">';

				# ═══════════ بطاقة التايم لاين اللاصقة ═══════════
				if( !isset( $hide_timeline ) || empty( $hide_timeline ) ){
					echo '<div class="why-time rv">';
						echo '<div class="inner">';
							echo '<h2>'.$timeline_title.'</h2>';
							echo '<p>'.$timeline_sub.'</p>';
							echo '<div class="tline">';
								$step_number = 0;
								foreach ( $timeline_steps as $step ) {
									if( !isset( $step['step_title'] ) || empty( $step['step_title'] ) ) continue;
									$step_number++;
									echo '<div class="tl">';
										echo '<span class="dot">'.$step_number.'</span>';
										echo '<div>';
											echo '<b>'.$step['step_title'].'</b>';
											if( isset( $step['step_desc'] ) && !empty( $step['step_desc'] ) ) echo '<small>'.$step['step_desc'].'</small>';
										echo '</div>';
									echo '</div>';
								}
							echo '</div>';
						echo '</div>';
					echo '</div>';
				}

				# ═══════════ شبكة كروت المميزات ═══════════
				echo '<div class="why-cards">';
					foreach ( $feature_cards as $feat ) {
						if( !isset( $feat['title'] ) || empty( $feat['title'] ) ) continue;
						$feat_icon = ( isset( $feat['icon'] ) && !empty( $feat['icon'] ) ) ? $feat['icon'] : '<i class="fas fa-circle-check"></i>';
						echo '<div class="feat rv">';
							echo '<div class="fic">'.$feat_icon.'</div>';
							echo '<h3>'.$feat['title'].'</h3>';
							if( isset( $feat['desc'] ) && !empty( $feat['desc'] ) ) echo '<p>'.$feat['desc'].'</p>';
						echo '</div>';
					}
				echo '</div>';

			echo '</div>';

		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — لماذا يختارنا',
			'description'=>'التايم لاين + كروت المميزات بتصميم Rukn v3',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'why_head_settings',
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
					'id'=>'why_timeline_settings',
					'title'=>'بطاقة رحلة العميل (التايم لاين الكحلي)',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_timeline',
					'title'=>'إخفاء بطاقة التايم لاين',
				),
				array(
					'type'=>'Text',
					'id'=>'timeline_title',
					'title'=>'عنوان البطاقة',
				),
				array(
					'type'=>'Text',
					'id'=>'timeline_sub',
					'title'=>'النص الفرعي للبطاقة',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'timeline_steps',
					'title'=>'خطوات الرحلة — الترقيم تلقائي',
					'fields'=> array(
						array(
							'type'=>'Text',
							'id'=>'step_title',
							'title'=>'عنوان الخطوة',
						),
						array(
							'type'=>'Text',
							'id'=>'step_desc',
							'title'=>'وصف الخطوة',
						),
					)
				),

				array(
					'type'=>'Title',
					'id'=>'why_cards_settings',
					'title'=>'كروت المميزات (8 كروت في التصميم)',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'feature_cards',
					'title'=>'المميزات',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML)',
						),
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'عنوان الميزة',
						),
						array(
							'type'=>'Text',
							'id'=>'desc',
							'title'=>'وصف الميزة',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'why_display_settings',
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
					'disc'=>'القسم في التصميم الأصلي بخلفية بيضاء — فعّل السويتش ده للمطابقة',
				)

			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new benefits)->Setup();
