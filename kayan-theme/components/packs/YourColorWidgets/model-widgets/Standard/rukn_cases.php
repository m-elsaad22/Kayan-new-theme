<?php
/**
 * RUKN v3 CASE STUDIES — rukn_cases (ودجت جديدة)
 * قسم "قصص نجاح حقيقية من مشاريعنا" — كروت القصص بمنهجية:
 * المشكلة ← التشخيص ← الحل ← النتيجة الخضراء + شبكة الميتا (موقع/خدمة/مدة/تاريخ)
 */
class rukn_cases extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'rukn_cases';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('before_title','button_text','button_url','case_place','case_title','cases_items','content','cs_display_settings','cs_head_settings','cs_items_settings','cs_labels_settings','diagnosis','icon','label_diagnosis','label_problem','label_solution','meta_date','meta_duration','meta_location','meta_service','problem','result','solution','title','top_style') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'قصص النجاح';
		if( !isset( $title ) || empty( $title ) ) $title = 'قصص نجاح حقيقية من {%مشاريعنا%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'تعرف على كيفية حل المشكلات المعقدة وتحقيق نتائج مميزة لعملائنا في مختلف إمارات الإمارات.';

		# ═══════════ عناوين البلوكات ═══════════
		if( !isset( $label_problem ) || empty( $label_problem ) )     $label_problem   = 'المشكلة';
		if( !isset( $label_diagnosis ) || empty( $label_diagnosis ) ) $label_diagnosis = 'التشخيص';
		if( !isset( $label_solution ) || empty( $label_solution ) )   $label_solution  = 'الحل';

		# تدرجات رأس الكارت
		$top_gradients = array(
			'navy' => 'var(--grad)',
			'gold' => 'linear-gradient(135deg,var(--gold),#d98b15)',
			'turq' => 'linear-gradient(135deg,var(--turq),var(--aqua))',
		);

		# ═══════════ القصص ═══════════
		if( !isset( $cases_items ) || empty( $cases_items ) || !is_array( $cases_items ) ){
			$cases_items = array(
				array(
					'icon'=>'<i class="fas fa-droplet"></i>', 'top_style'=>'navy',
					'case_title'=>'تسرب مياه خفي', 'case_place'=>'فيلا — دبي مارينا',
					'problem'=>'تسرب مياه مستمر تسبب في رطوبة وتلف الجدران دون مصدر ظاهر.',
					'diagnosis'=>'فحص بالكاميرا الحرارية وأجهزة الكشف الصوتي لتحديد موقع التسرب بدقة.',
					'solution'=>'إصلاح الموقع المحدد فقط بدون تكسير وإعادة العزل الموضعي.',
					'result'=>'حل التسرب بنسبة 100%',
					'meta_location'=>'دبي مارينا', 'meta_service'=>'كشف تسربات', 'meta_duration'=>'يوم واحد', 'meta_date'=>'مايو 2026',
					'button_text'=>'عرض القصة كاملة', 'button_url'=>'',
				),
				array(
					'icon'=>'<i class="fas fa-layer-group"></i>', 'top_style'=>'gold',
					'case_title'=>'فشل عزل السطح', 'case_place'=>'فيلا — البرشاء',
					'problem'=>'ارتفاع حرارة المنزل وفواتير كهرباء مرتفعة بسبب عزل قديم متضرر.',
					'diagnosis'=>'قياس انتقال الحرارة على السطح وكشف نقاط ضعف العزل القديم.',
					'solution'=>'تركيب عزل فوم بولي يوريثان وطلاء عاكس للحرارة بضمان 10 سنوات.',
					'result'=>'خفض انتقال الحرارة 40%',
					'meta_location'=>'البرشاء', 'meta_service'=>'عزل أسطح', 'meta_duration'=>'3 أيام', 'meta_date'=>'أبريل 2026',
					'button_text'=>'عرض القصة كاملة', 'button_url'=>'',
				),
				array(
					'icon'=>'<i class="fas fa-water"></i>', 'top_style'=>'turq',
					'case_title'=>'تلوث خزان المياه', 'case_place'=>'مبنى — الشارقة',
					'problem'=>'تغير طعم ولون المياه وشكاوى متكررة من السكان.',
					'diagnosis'=>'فحص الخزان وتحليل عينات المياه وكشف تشققات في العزل الداخلي.',
					'solution'=>'تفريغ وتنظيف وتعقيم الخزان وإعادة العزل بمواد آمنة صحياً.',
					'result'=>'مياه نظيفة مطابقة للمعايير',
					'meta_location'=>'الشارقة', 'meta_service'=>'تنظيف خزانات', 'meta_duration'=>'يومان', 'meta_date'=>'مارس 2026',
					'button_text'=>'عرض القصة كاملة', 'button_url'=>'',
				),
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

			echo '<div class="cs-grid">';

				foreach ( $cases_items as $case ) {
					if( !isset( $case['case_title'] ) || empty( $case['case_title'] ) ) continue;

					$case_icon = ( isset( $case['icon'] ) && !empty( $case['icon'] ) ) ? $case['icon'] : '<i class="fas fa-briefcase"></i>';
					$top_style = ( isset( $case['top_style'] ) && isset( $top_gradients[ $case['top_style'] ] ) ) ? $top_gradients[ $case['top_style'] ] : $top_gradients['navy'];

					echo '<div class="cs rv">';

						echo '<div class="cs-top"'.( ( $case['top_style'] != 'navy' ) ? ' style="background:'.$top_style.'"' : '' ).'>';
							echo $case_icon;
							echo '<div><b>'.$case['case_title'].'</b><small>'.( ( isset( $case['case_place'] ) ) ? $case['case_place'] : '' ).'</small></div>';
						echo '</div>';

						echo '<div class="cs-body">';

							if( isset( $case['problem'] ) && !empty( $case['problem'] ) ){
								echo '<div class="cs-block"><div class="k"><i class="fas fa-triangle-exclamation"></i> '.$label_problem.'</div><p>'.$case['problem'].'</p></div>';
							}
							if( isset( $case['diagnosis'] ) && !empty( $case['diagnosis'] ) ){
								echo '<div class="cs-block"><div class="k"><i class="fas fa-magnifying-glass"></i> '.$label_diagnosis.'</div><p>'.$case['diagnosis'].'</p></div>';
							}
							if( isset( $case['solution'] ) && !empty( $case['solution'] ) ){
								echo '<div class="cs-block"><div class="k"><i class="fas fa-screwdriver-wrench"></i> '.$label_solution.'</div><p>'.$case['solution'].'</p></div>';
							}

							if( isset( $case['result'] ) && !empty( $case['result'] ) ){
								echo '<div class="cs-result"><i class="fas fa-circle-check"></i> '.$case['result'].'</div>';
							}

							$has_meta = false;
							foreach ( array('meta_location','meta_service','meta_duration','meta_date') as $meta_key ) {
								if( isset( $case[ $meta_key ] ) && !empty( $case[ $meta_key ] ) ) $has_meta = true;
							}
							if( $has_meta ){
								echo '<div class="cs-meta">';
									if( !empty( $case['meta_location'] ) ) echo '<span><i class="fas fa-location-dot"></i> '.$case['meta_location'].'</span>';
									if( !empty( $case['meta_service'] ) )  echo '<span><i class="fas fa-screwdriver-wrench"></i> '.$case['meta_service'].'</span>';
									if( !empty( $case['meta_duration'] ) ) echo '<span><i class="fas fa-clock"></i> '.$case['meta_duration'].'</span>';
									if( !empty( $case['meta_date'] ) )     echo '<span><i class="fas fa-calendar-check"></i> '.$case['meta_date'].'</span>';
								echo '</div>';
							}

							if( isset( $case['button_text'] ) && !empty( $case['button_text'] ) ){
								$button_url = ( isset( $case['button_url'] ) && !empty( $case['button_url'] ) ) ? $case['button_url'] : home_url('/contact-us/');
								echo '<a href="'.$button_url.'" class="btn btn-soft">'.$case['button_text'].'</a>';
							}

						echo '</div>';

					echo '</div>';
				}

			echo '</div>';

		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — قصص النجاح (جديدة)',
			'description'=>'كروت القصص: المشكلة ← التشخيص ← الحل ← النتيجة',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'cs_head_settings',
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
					'id'=>'cs_labels_settings',
					'title'=>'عناوين البلوكات الثلاثة',
				),
				array(
					'type'=>'Text',
					'id'=>'label_problem',
					'title'=>'عنوان بلوك المشكلة (الافتراضي: المشكلة)',
				),
				array(
					'type'=>'Text',
					'id'=>'label_diagnosis',
					'title'=>'عنوان بلوك التشخيص (الافتراضي: التشخيص)',
				),
				array(
					'type'=>'Text',
					'id'=>'label_solution',
					'title'=>'عنوان بلوك الحل (الافتراضي: الحل)',
				),

				array(
					'type'=>'Title',
					'id'=>'cs_items_settings',
					'title'=>'القصص (3 في التصميم)',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'cases_items',
					'title'=>'القصص',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML)',
						),
						array(
							'type'=>'Radio',
							'id'=>'top_style',
							'title'=>'تدرج رأس الكارت',
							'options'=>array(
								'navy'=>'كحلي',
								'gold'=>'ذهبي',
								'turq'=>'تركواز',
							)
						),
						array(
							'type'=>'Text',
							'id'=>'case_title',
							'title'=>'عنوان القصة (مثال: تسرب مياه خفي)',
						),
						array(
							'type'=>'Text',
							'id'=>'case_place',
							'title'=>'المكان (مثال: فيلا — دبي مارينا)',
						),
						array(
							'type'=>'TextArea',
							'id'=>'problem',
							'title'=>'المشكلة',
						),
						array(
							'type'=>'TextArea',
							'id'=>'diagnosis',
							'title'=>'التشخيص',
						),
						array(
							'type'=>'TextArea',
							'id'=>'solution',
							'title'=>'الحل',
						),
						array(
							'type'=>'Text',
							'id'=>'result',
							'title'=>'النتيجة (السطر الأخضر — مثال: حل التسرب بنسبة 100%)',
						),
						array(
							'type'=>'Text',
							'id'=>'meta_location',
							'title'=>'ميتا: الموقع',
						),
						array(
							'type'=>'Text',
							'id'=>'meta_service',
							'title'=>'ميتا: الخدمة',
						),
						array(
							'type'=>'Text',
							'id'=>'meta_duration',
							'title'=>'ميتا: المدة',
						),
						array(
							'type'=>'Text',
							'id'=>'meta_date',
							'title'=>'ميتا: التاريخ',
						),
						array(
							'type'=>'Text',
							'id'=>'button_text',
							'title'=>'نص الزرار (اتركه فارغاً لإخفائه)',
						),
						array(
							'type'=>'Text',
							'id'=>'button_url',
							'title'=>'رابط الزرار',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'cs_display_settings',
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
(new rukn_cases)->Setup();
