<?php
/**
 * RUKN v3 CONTENT HUB — rukn_hub (ودجت جديدة)
 * قسم "دليل الخدمات المنزلية" — أعمدة مركز المعرفة للسيو الداخلي
 * كل عمود: أيقونة + عنوان + الدليل الشامل المميز بنجمة ذهبية + روابط فرعية
 * الروابط الفرعية بتتكتب سطر بسطر بصيغة: النص | الرابط
 */
class rukn_hub extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'rukn_hub';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('before_title','content','guide_title','guide_url','hub_cols_settings','hub_columns','hub_display_settings','hub_head_settings','hub_title','icon','links','title') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'مركز المعرفة';
		if( !isset( $title ) || empty( $title ) ) $title = 'دليل الخدمات {%المنزلية%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'محتوى متخصص ومنظم يساعدك على فهم خدماتنا واتخاذ القرار الصحيح.';

		# ═══════════ أعمدة الدليل ═══════════
		if( !isset( $hub_columns ) || empty( $hub_columns ) || !is_array( $hub_columns ) ){
			$hub_columns = array(
				array(
					'icon'=>'<i class="fas fa-droplet"></i>', 'hub_title'=>'كشف التسربات',
					'guide_title'=>'الدليل الشامل لكشف التسربات', 'guide_url'=>'',
					'links'=>"علامات تسرب المياه المبكرة |\nالكشف بدون تكسير |\nتكلفة كشف التسربات |",
				),
				array(
					'icon'=>'<i class="fas fa-layer-group"></i>', 'hub_title'=>'العزل',
					'guide_title'=>'أنواع عزل الأسطح', 'guide_url'=>'',
					'links'=>"عزل الفوم مقابل البيتومين |\nالعزل الحراري والمائي |\nتكلفة عزل الأسطح |",
				),
				array(
					'icon'=>'<i class="fas fa-water"></i>', 'hub_title'=>'الخزانات',
					'guide_title'=>'العناية بخزانات المياه', 'guide_url'=>'',
					'links'=>"أهمية تنظيف الخزانات |\nعزل الخزانات الصحي |\nتكلفة تنظيف الخزانات |",
				),
				array(
					'icon'=>'<i class="fas fa-snowflake"></i>', 'hub_title'=>'الصيانة',
					'guide_title'=>'صيانة التكييف الموسمية', 'guide_url'=>'',
					'links'=>"صيانة المكيف في الصيف |\nالسباكة والكهرباء |\nتكلفة الصيانة المنزلية |",
				),
				array(
					'icon'=>'<i class="fas fa-bug-slash"></i>', 'hub_title'=>'مكافحة الحشرات',
					'guide_title'=>'الوقاية من الحشرات', 'guide_url'=>'',
					'links'=>"مكافحة الصراصير والنمل |\nمكافحة القوارض |\nتكلفة مكافحة الحشرات |",
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

			echo '<div class="hub-grid">';

				foreach ( $hub_columns as $column ) {
					if( !isset( $column['hub_title'] ) || empty( $column['hub_title'] ) ) continue;

					$column_icon = ( isset( $column['icon'] ) && !empty( $column['icon'] ) ) ? $column['icon'] : '<i class="fas fa-folder-open"></i>';

					echo '<div class="hub rv">';

						echo '<div class="hub-ic">'.$column_icon.'</div>';
						echo '<h3>'.$column['hub_title'].'</h3>';

						# الدليل الشامل المميز
						if( isset( $column['guide_title'] ) && !empty( $column['guide_title'] ) ){
							$guide_url = ( isset( $column['guide_url'] ) && !empty( $column['guide_url'] ) ) ? $column['guide_url'] : '#';
							echo '<a class="feat-guide" href="'.$guide_url.'"><i class="fas fa-star" style="color:var(--gold)"></i> '.$column['guide_title'].'</a>';
						}

						# الروابط الفرعية — سطر لكل رابط بصيغة: النص | الرابط
						if( isset( $column['links'] ) && !empty( $column['links'] ) ){
							echo '<ul>';
								foreach ( preg_split('/\r\n|\r|\n/', $column['links']) as $link_line ) {
									$link_line = trim( $link_line );
									if( $link_line === '' ) continue;
									$link_parts = explode('|', $link_line, 2);
									$link_text = trim( $link_parts[0] );
									$link_url  = ( isset( $link_parts[1] ) && trim( $link_parts[1] ) !== '' ) ? trim( $link_parts[1] ) : '#';
									if( $link_text === '' ) continue;
									echo '<li><a href="'.$link_url.'"><i class="fas fa-chevron-left"></i> '.$link_text.'</a></li>';
								}
							echo '</ul>';
						}

					echo '</div>';
				}

			echo '</div>';

		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — دليل الخدمات (جديدة)',
			'description'=>'أعمدة مركز المعرفة للسيو الداخلي',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'hub_head_settings',
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
					'id'=>'hub_cols_settings',
					'title'=>'أعمدة الدليل (5 في التصميم) — الروابط الفرعية سطر لكل رابط بصيغة: النص | الرابط',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'hub_columns',
					'title'=>'الأعمدة',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML)',
						),
						array(
							'type'=>'Text',
							'id'=>'hub_title',
							'title'=>'عنوان العمود (مثال: كشف التسربات)',
						),
						array(
							'type'=>'Text',
							'id'=>'guide_title',
							'title'=>'عنوان الدليل الشامل (بالنجمة الذهبية)',
						),
						array(
							'type'=>'Text',
							'id'=>'guide_url',
							'title'=>'رابط الدليل الشامل',
						),
						array(
							'type'=>'TextArea',
							'id'=>'links',
							'title'=>"الروابط الفرعية — سطر لكل رابط بصيغة: النص | الرابط",
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'hub_display_settings',
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
(new rukn_hub)->Setup();
