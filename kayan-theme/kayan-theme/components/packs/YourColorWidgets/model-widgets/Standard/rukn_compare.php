<?php
/**
 * RUKN v3 COMPARE — rukn_compare (ودجت جديدة)
 * جدول "لماذا يختار العملاء '.esc_html( get_bloginfo('name') ).'؟" — مقارنة بين خدماتنا والشركات الأخرى
 * رأس الجدول لاصق أثناء التمرير، عمودنا بالتدرج التركواز وخلفية زرقا خفيفة
 * كل صف: المعيار + قيمتنا (صح/غلط/نص) + قيمتهم (صح/غلط/نص)
 */
class rukn_compare extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'rukn_compare';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('before_title','cmp_cols_settings','cmp_display_settings','cmp_head_settings','cmp_rows_settings','col_criteria','col_others','col_us','compare_rows','content','label','others_text','others_value','title','us_text','us_value') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'مقارنة';
		if( !isset( $title ) || empty( $title ) ) $title = 'لماذا يختار العملاء {%'.esc_html( get_bloginfo('name') ).'؟%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'مقارنة واضحة بين خدماتنا والخدمات التقليدية.';

		# ═══════════ رؤوس الأعمدة ═══════════
		if( !isset( $col_criteria ) || empty( $col_criteria ) ) $col_criteria = 'المعيار';
		if( !isset( $col_us ) || empty( $col_us ) )             $col_us       = esc_html( get_bloginfo('name') );
		if( !isset( $col_others ) || empty( $col_others ) )     $col_others   = 'شركات أخرى';

		# ═══════════ صفوف المقارنة ═══════════
		if( !isset( $compare_rows ) || empty( $compare_rows ) || !is_array( $compare_rows ) ){
			$default_labels = array('ضمان مكتوب','كشف بدون تكسير','دعم 24/7','فنيون معتمدون','أجهزة متطورة','استجابة سريعة','تغطية جميع الإمارات','أسعار شفافة','دعم ما بعد الخدمة','خدمة الطوارئ');
			$compare_rows = array();
			foreach ( $default_labels as $default_label ) {
				$compare_rows[] = array( 'label'=>$default_label, 'us_value'=>'yes', 'us_text'=>'', 'others_value'=>'no', 'others_text'=>'' );
			}
		}

		# دالة رسم قيمة الخلية: صح / غلط / نص
		if( !function_exists('rukn_compare_cell') ){
			function rukn_compare_cell( $value, $text ){
				if( !empty( $text ) ) return $text;
				return ( $value == 'no' ) ? '<i class="fas fa-circle-xmark"></i>' : '<i class="fas fa-circle-check"></i>';
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

			# الجدول
			echo '<div class="cmp rv">';

				echo '<div class="cmp-row cmp-head">';
					echo '<div class="ch">'.$col_criteria.'</div>';
					echo '<div class="ch rk">'.$col_us.'</div>';
					echo '<div class="ch">'.$col_others.'</div>';
				echo '</div>';

				foreach ( $compare_rows as $row ) {
					if( !isset( $row['label'] ) || empty( $row['label'] ) ) continue;
					$us_value     = ( isset( $row['us_value'] ) ) ? $row['us_value'] : 'yes';
					$us_text      = ( isset( $row['us_text'] ) ) ? $row['us_text'] : '';
					$others_value = ( isset( $row['others_value'] ) ) ? $row['others_value'] : 'no';
					$others_text  = ( isset( $row['others_text'] ) ) ? $row['others_text'] : '';

					echo '<div class="cmp-row">';
						echo '<div class="cc lbl">'.$row['label'].'</div>';
						echo '<div class="cc val rk">'.rukn_compare_cell( $us_value, $us_text ).'</div>';
						echo '<div class="cc val ot'.( ( $others_value == 'yes' && empty( $others_text ) ) ? ' ot-yes' : '' ).'">'.rukn_compare_cell( $others_value, $others_text ).'</div>';
					echo '</div>';
				}

			echo '</div>';

		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — جدول المقارنة (جديدة)',
			'description'=>'مقارنة خدماتنا بالشركات الأخرى برأس لاصق',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'cmp_head_settings',
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
					'id'=>'cmp_cols_settings',
					'title'=>'رؤوس الأعمدة',
				),
				array(
					'type'=>'Text',
					'id'=>'col_criteria',
					'title'=>'عمود المعيار (الافتراضي: المعيار)',
				),
				array(
					'type'=>'Text',
					'id'=>'col_us',
					'title'=>'عمودنا (الافتراضي: '.esc_html( get_bloginfo('name') ).')',
				),
				array(
					'type'=>'Text',
					'id'=>'col_others',
					'title'=>'عمود المنافسين (الافتراضي: شركات أخرى)',
				),

				array(
					'type'=>'Title',
					'id'=>'cmp_rows_settings',
					'title'=>'صفوف المقارنة — النص الاختياري بيظهر بدل أيقونة الصح/الغلط',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'compare_rows',
					'title'=>'الصفوف',
					'fields'=> array(
						array(
							'type'=>'Text',
							'id'=>'label',
							'title'=>'المعيار (مثال: ضمان مكتوب)',
						),
						array(
							'type'=>'Radio',
							'id'=>'us_value',
							'title'=>'قيمتنا',
							'options'=>array(
								'yes'=>'✓ متوفر',
								'no' =>'✗ غير متوفر',
							)
						),
						array(
							'type'=>'Text',
							'id'=>'us_text',
							'title'=>'نص بديل لعمودنا (اختياري — مثال: 10 سنوات)',
						),
						array(
							'type'=>'Radio',
							'id'=>'others_value',
							'title'=>'قيمة المنافسين',
							'options'=>array(
								'yes'=>'✓ متوفر',
								'no' =>'✗ غير متوفر',
							)
						),
						array(
							'type'=>'Text',
							'id'=>'others_text',
							'title'=>'نص بديل لعمود المنافسين (اختياري)',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'cmp_display_settings',
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
(new rukn_compare)->Setup();
