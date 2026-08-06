<?php
/**
 * RUKN v3 STATS — rukn_stats (ودجت جديدة)
 * قسم "أرقام تتحدث عن جودتنا" — الشريط الكحلي المتدرج بالعدادات المتحركة
 * كل عداد: أيقونة + رقم متحرك (مع لاحقة وكسور عشرية اختيارية) + تسمية
 * لو سيبت الرقم فاضي والنص الثابت مكتوب (مثال: 24/7) بيظهر بدون حركة
 */
class rukn_stats extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'rukn_stats';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('before_title','content','decimals','icon','label','number','st_display_settings','st_head_settings','st_items_settings','static_text','stats_items','suffix','title') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'أرقامنا';
		if( !isset( $title ) || empty( $title ) ) $title = 'أرقام تتحدث عن جودتنا';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'ثقة الآلاف من العملاء في جميع أنحاء الإمارات.';

		# ═══════════ العدادات ═══════════
		if( !isset( $stats_items ) || empty( $stats_items ) || !is_array( $stats_items ) ){
			$stats_items = array(
				array( 'icon'=>'<i class="fas fa-users"></i>',      'number'=>'15000', 'suffix'=>'+', 'decimals'=>'', 'static_text'=>'', 'label'=>'عميل راضٍ' ),
				array( 'icon'=>'<i class="fas fa-briefcase"></i>',  'number'=>'30000', 'suffix'=>'+', 'decimals'=>'', 'static_text'=>'', 'label'=>'خدمة منجزة' ),
				array( 'icon'=>'<i class="fas fa-award"></i>',      'number'=>'12',    'suffix'=>'+', 'decimals'=>'', 'static_text'=>'', 'label'=>'سنة خبرة' ),
				array( 'icon'=>'<i class="fas fa-user-gear"></i>',  'number'=>'50',    'suffix'=>'+', 'decimals'=>'', 'static_text'=>'', 'label'=>'فني معتمد' ),
				array( 'icon'=>'<i class="fas fa-face-smile"></i>', 'number'=>'98',    'suffix'=>'%', 'decimals'=>'', 'static_text'=>'', 'label'=>'رضا العملاء' ),
				array( 'icon'=>'<i class="fas fa-headset"></i>',    'number'=>'',      'suffix'=>'',  'decimals'=>'', 'static_text'=>'24/7', 'label'=>'دعم فوري' ),
			);
		}

		# ════════════════════════════════════════════════════════
		# OUTPUT — نفس بنية التصميم الجديد
		# ════════════════════════════════════════════════════════
		echo '<div class="rukn-stats-inner">';
			echo '<div class="wrap">';

				# رأس القسم
				echo '<div class="shead rv">';
					if( !empty( $before_title ) ) echo '<span class="tag">'.$before_title.'</span>';
					echo '<h2>'.$title.'</h2>';
					if( !empty( $content ) ) echo '<p>'.$content.'</p>';
				echo '</div>';

				# شبكة العدادات
				echo '<div class="stats-grid">';
					foreach ( $stats_items as $stat ) {
						if( ( !isset( $stat['number'] ) || $stat['number'] === '' ) && ( !isset( $stat['static_text'] ) || $stat['static_text'] === '' ) ) continue;
						$stat_icon = ( isset( $stat['icon'] ) && !empty( $stat['icon'] ) ) ? $stat['icon'] : '<i class="fas fa-circle-check"></i>';

						echo '<div class="stat rv">';
							echo $stat_icon;
							if( isset( $stat['number'] ) && $stat['number'] !== '' ){
								$stat_suffix   = ( isset( $stat['suffix'] ) ) ? $stat['suffix'] : '';
								$stat_decimals = ( isset( $stat['decimals'] ) && is_numeric( $stat['decimals'] ) ) ? $stat['decimals'] : '';
								echo '<div class="num" data-count="'.$stat['number'].'"'.( ( $stat_suffix !== '' ) ? ' data-suffix="'.$stat_suffix.'"' : '' ).( ( $stat_decimals !== '' ) ? ' data-dec="'.$stat_decimals.'"' : '' ).'>0</div>';
							}else{
								echo '<div class="num">'.$stat['static_text'].'</div>';
							}
							echo '<div class="lbl">'.( ( isset( $stat['label'] ) ) ? $stat['label'] : '' ).'</div>';
						echo '</div>';
					}
				echo '</div>';

			echo '</div>';
		echo '</div>';

		# ════════════════════════════════════════════════════════
		# INLINE JS — العدادات المتحركة (نفس محرك الهيرو — بحماية من التكرار)
		# ════════════════════════════════════════════════════════
		echo '<script type="text/javascript">';
			echo 'if(typeof window.ruknCounted==="undefined"){';
				echo 'window.ruknCounted=new Set();';
				echo 'window.ruknAnimateCount=function(el){';
					echo 'if(window.ruknCounted.has(el))return;window.ruknCounted.add(el);';
					echo 'var target=parseFloat(el.dataset.count);';
					echo 'var dec=parseInt(el.dataset.dec||"0");';
					echo 'var suffix=el.dataset.suffix||"";';
					echo 'var dur=1600,start=performance.now();';
					echo 'function step(now){';
						echo 'var t=Math.min((now-start)/dur,1);';
						echo 'var eased=1-Math.pow(1-t,3);';
						echo 'var val=target*eased;';
						echo 'el.textContent=(dec?val.toFixed(dec):Math.floor(val).toLocaleString("en-US"))+suffix;';
						echo 'if(t<1)requestAnimationFrame(step);else el.textContent=(dec?target.toFixed(dec):Math.floor(target).toLocaleString("en-US"))+suffix;';
					echo '}';
					echo 'requestAnimationFrame(step);';
				echo '};';
				echo 'window.ruknCio=new IntersectionObserver(function(entries){';
					echo 'entries.forEach(function(e){if(e.isIntersecting){window.ruknAnimateCount(e.target);window.ruknCio.unobserve(e.target)}});';
				echo '},{threshold:.4});';
			echo '}';
			echo 'document.querySelectorAll(".rukn-stats-inner [data-count]").forEach(function(el){window.ruknCio.observe(el)});';
		echo '</script>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — الأرقام والعدادات (جديدة)',
			'description'=>'الشريط الكحلي بالعدادات المتحركة',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'st_head_settings',
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
					'id'=>'st_items_settings',
					'title'=>'العدادات (6 في التصميم)',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'stats_items',
					'title'=>'العدادات',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML)',
						),
						array(
							'type'=>'Text',
							'id'=>'number',
							'title'=>'الرقم المتحرك (مثال: 15000 أو 4.9)',
						),
						array(
							'type'=>'Text',
							'id'=>'suffix',
							'title'=>'اللاحقة (مثال: + أو %)',
						),
						array(
							'type'=>'Text',
							'id'=>'decimals',
							'title'=>'عدد الكسور العشرية (مثال: 1)',
						),
						array(
							'type'=>'Text',
							'id'=>'static_text',
							'title'=>'نص ثابت بدل الرقم (مثال: 24/7) — اترك الرقم فارغاً',
						),
						array(
							'type'=>'Text',
							'id'=>'label',
							'title'=>'التسمية (عميل راضٍ / خدمة منجزة...)',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'st_display_settings',
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
				)

			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new rukn_stats)->Setup();
