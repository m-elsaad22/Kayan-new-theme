<?php
/**
 * RUKN v3 BRANDS — rukn_brands (ودجت جديدة)
 * قسم "شركاؤنا في التميز" — شريط اللوجوهات المتحرك اللانهائي
 * اللوجوهات رمادية وبترجع لألوانها عند الهوفر، والحركة بتتوقف عند مرور الماوس
 * كل شريك: لوجو صورة (يفضَّل) أو أيقونة + اسم — والقائمة بتتكرر تلقائياً للحركة اللانهائية
 */
class rukn_brands extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'rukn_brands';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('before_title','br_display_settings','br_head_settings','br_items_settings','brands_items','icon','logo','name','scroll_seconds','title') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'شركاؤنا';
		if( !isset( $title ) || empty( $title ) ) $title = 'شركاؤنا في {%التميز%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);

		# ═══════════ سرعة الحركة ═══════════
		if( !isset( $scroll_seconds ) || empty( $scroll_seconds ) || !is_numeric( $scroll_seconds ) ) $scroll_seconds = 26;

		# ═══════════ الشركاء ═══════════
		if( !isset( $brands_items ) || empty( $brands_items ) || !is_array( $brands_items ) ){
			$brands_items = array(
				array( 'name'=>'Sika',            'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				array( 'name'=>'Fosroc',          'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				array( 'name'=>'Jotun',           'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				array( 'name'=>'Mapei',           'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				array( 'name'=>'National Paints', 'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				array( 'name'=>'Weber',           'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
			);
		}

		# دالة رسم شريك واحد
		if( !function_exists('rukn_brands_render_item') ){
			function rukn_brands_render_item( $brand ){
				$logo_url = '';
				if( isset( $brand['logo'] ) && !empty( $brand['logo'] ) ){
					$image_src = wp_get_attachment_image_src( $brand['logo'], 'medium' );
					if( isset( $image_src[0] ) ) $logo_url = $image_src[0];
				}
				echo '<span class="brand">';
					if( !empty( $logo_url ) ){
						echo '<img src="'.$logo_url.'" alt="'.esc_attr( ( isset( $brand['name'] ) ) ? $brand['name'] : '' ).'" loading="lazy">';
					}else{
						echo ( ( isset( $brand['icon'] ) && !empty( $brand['icon'] ) ) ? $brand['icon'] : '<i class="fas fa-cube"></i>' ).' '.( ( isset( $brand['name'] ) ) ? $brand['name'] : '' );
					}
				echo '</span>';
			}
		}

		# ════════════════════════════════════════════════════════
		# OUTPUT — نفس بنية التصميم الجديد
		# ════════════════════════════════════════════════════════
		echo '<div class="wrap">';
			echo '<div class="shead rv" style="margin-bottom:40px">';
				if( !empty( $before_title ) ) echo '<span class="tag">'.$before_title.'</span>';
				echo '<h2>'.$title.'</h2>';
			echo '</div>';
		echo '</div>';

		# شريط اللوجوهات — القائمة مكررة مرتين للحركة اللانهائية زي التصميم
		echo '<div class="logo-cloud">';
			echo '<div class="logo-track" style="animation-duration:'.intval( $scroll_seconds ).'s">';
				foreach ( $brands_items as $brand ) {
					if( ( !isset( $brand['name'] ) || empty( $brand['name'] ) ) && ( !isset( $brand['logo'] ) || empty( $brand['logo'] ) ) ) continue;
					rukn_brands_render_item( $brand );
				}
				foreach ( $brands_items as $brand ) {
					if( ( !isset( $brand['name'] ) || empty( $brand['name'] ) ) && ( !isset( $brand['logo'] ) || empty( $brand['logo'] ) ) ) continue;
					rukn_brands_render_item( $brand );
				}
			echo '</div>';
		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — شركاؤنا (جديدة)',
			'description'=>'شريط اللوجوهات المتحرك اللانهائي',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'br_head_settings',
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
					'type'=>'Number',
					'id'=>'scroll_seconds',
					'title'=>'مدة دورة الحركة بالثواني (الافتراضي: 26 — أقل = أسرع)',
				),

				array(
					'type'=>'Title',
					'id'=>'br_items_settings',
					'title'=>'الشركاء — ارفع لوجو صورة أو اترك الأيقونة والاسم',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'brands_items',
					'title'=>'الشركاء',
					'fields'=> array(
						array(
							'type'=>'File',
							'id'=>'logo',
							'title'=>'لوجو الشريك (صورة)',
						),
						array(
							'type'=>'Text',
							'id'=>'name',
							'title'=>'اسم الشريك',
						),
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML) — تظهر فقط بدون لوجو',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'br_display_settings',
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
(new rukn_brands)->Setup();
