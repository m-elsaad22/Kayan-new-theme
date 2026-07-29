<?php
/**
 * RUKN v3 BEFORE/AFTER — rukn_results (ودجت جديدة)
 * قسم "قبل وبعد — نتائج حقيقية" بسلايدر السحب التفاعلي
 * كل بطاقة: صورتين حقيقيتين (قبل/بعد) أو تدرجات لونية بأيقونات،
 * مقبض سحب بالماوس واللمس، شارات قبل/بعد، وشريط معلومات بزرار
 */
class rukn_results extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'rukn_results';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('after_icon','after_image','after_style','after_text','ba_cards','ba_cards_settings','ba_display_settings','ba_head_settings','before_icon','before_image','before_text','before_title','button_text','button_url','card_sub','card_title','content','label_after','label_before','title') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'قبل وبعد';
		if( !isset( $title ) || empty( $title ) ) $title = 'قبل وبعد — {%نتائج حقيقية%} لأعمالنا';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'اسحب المقبض لرؤية الفرق الذي يصنعه فريقنا.';

		if( !isset( $label_before ) || empty( $label_before ) ) $label_before = 'قبل';
		if( !isset( $label_after ) || empty( $label_after ) )   $label_after  = 'بعد';

		# ═══════════ البطاقات ═══════════
		if( !isset( $ba_cards ) || empty( $ba_cards ) || !is_array( $ba_cards ) ){
			$ba_cards = array(
				array(
					'card_title'=>'كشف تسربات — دبي مارينا', 'card_sub'=>'تحديد دقيق وإصلاح بدون تكسير',
					'before_text'=>'قبل المعالجة', 'before_icon'=>'<i class="fas fa-triangle-exclamation"></i>',
					'after_text'=>'بعد المعالجة',  'after_icon'=>'<i class="fas fa-droplet"></i>',
					'after_style'=>'blue', 'before_image'=>'', 'after_image'=>'',
					'button_text'=>'شاهد التفاصيل', 'button_url'=>'',
				),
				array(
					'card_title'=>'عزل فوم — البرشاء', 'card_sub'=>'عزل حراري ومائي بضمان 10 سنوات',
					'before_text'=>'قبل العزل', 'before_icon'=>'<i class="fas fa-sun"></i>',
					'after_text'=>'بعد العزل',  'after_icon'=>'<i class="fas fa-layer-group"></i>',
					'after_style'=>'gold', 'before_image'=>'', 'after_image'=>'',
					'button_text'=>'شاهد التفاصيل', 'button_url'=>'',
				),
			);
		}

		$uniqid = uniqid('ruknba');

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

			echo '<div class="ba-wrap">';

				foreach ( $ba_cards as $card ) {
					if( !isset( $card['card_title'] ) || empty( $card['card_title'] ) ) continue;

					# صور قبل/بعد — صور حقيقية أو تدرجات بأيقونات
					$before_image_url = '';
					if( isset( $card['before_image'] ) && !empty( $card['before_image'] ) ){
						$image_src = wp_get_attachment_image_src( $card['before_image'], 'large' );
						if( isset( $image_src[0] ) ) $before_image_url = $image_src[0];
					}
					$after_image_url = '';
					if( isset( $card['after_image'] ) && !empty( $card['after_image'] ) ){
						$image_src = wp_get_attachment_image_src( $card['after_image'], 'large' );
						if( isset( $image_src[0] ) ) $after_image_url = $image_src[0];
					}

					$after_extra = '';
					if( !empty( $after_image_url ) ){
						$after_extra = ' style="background-image:url(\''.$after_image_url.'\');background-size:cover;background-position:center"';
					}elseif( isset( $card['after_style'] ) && $card['after_style'] == 'gold' ){
						$after_extra = ' style="background:linear-gradient(135deg,var(--gold),#ffce6b);color:#3a2600"';
					}

					$before_extra = ( !empty( $before_image_url ) ) ? ' style="background-image:url(\''.$before_image_url.'\');background-size:cover;background-position:center"' : '';

					echo '<div class="ba rv">';

						echo '<div class="ba-stage" data-ba>';
							echo '<div class="ba-img ba-after"'.$after_extra.'>';
								if( empty( $after_image_url ) ) echo '<span>'.( ( isset( $card['after_icon'] ) ) ? $card['after_icon'] : '' ).' '.( ( isset( $card['after_text'] ) ) ? $card['after_text'] : '' ).'</span>';
							echo '</div>';
							echo '<div class="ba-img ba-before"'.$before_extra.'>';
								if( empty( $before_image_url ) ) echo '<span>'.( ( isset( $card['before_icon'] ) ) ? $card['before_icon'] : '' ).' '.( ( isset( $card['before_text'] ) ) ? $card['before_text'] : '' ).'</span>';
							echo '</div>';
							echo '<span class="ba-label b">'.$label_before.'</span>';
							echo '<span class="ba-label a">'.$label_after.'</span>';
							echo '<div class="ba-handle"><span class="grip"><i class="fas fa-arrows-left-right"></i></span></div>';
						echo '</div>';

						echo '<div class="ba-info">';
							echo '<div><b>'.$card['card_title'].'</b><br><small>'.( ( isset( $card['card_sub'] ) ) ? $card['card_sub'] : '' ).'</small></div>';
							if( isset( $card['button_text'] ) && !empty( $card['button_text'] ) ){
								$button_url = ( isset( $card['button_url'] ) && !empty( $card['button_url'] ) ) ? $card['button_url'] : '#';
								echo '<a href="'.$button_url.'" class="btn btn-soft">'.$card['button_text'].'</a>';
							}
						echo '</div>';

					echo '</div>';
				}

			echo '</div>';

		echo '</div>';

		# ════════════════════════════════════════════════════════
		# INLINE JS — سلايدر السحب (ماوس + لمس + ضغطة مباشرة)
		# ════════════════════════════════════════════════════════
		echo '<script type="text/javascript">';
			echo '(function(){';
				echo 'var root=document.getElementById("'.$uniqid.'");if(!root)return;';
				echo 'root.querySelectorAll("[data-ba]").forEach(function(stage){';
					echo 'var before=stage.querySelector(".ba-before");';
					echo 'var handle=stage.querySelector(".ba-handle");';
					echo 'var dragging=false;';
					echo 'function setPos(clientX){';
						echo 'var rect=stage.getBoundingClientRect();';
						echo 'var pct=((clientX-rect.left)/rect.width)*100;';
						echo 'pct=Math.max(2,Math.min(98,pct));';
						echo 'before.style.clipPath="inset(0 0 0 "+pct+"%)";';
						echo 'handle.style.left=pct+"%";';
					echo '}';
					echo 'var start=function(){dragging=true};';
					echo 'var end=function(){dragging=false};';
					echo 'var move=function(e){if(!dragging)return;var x=e.touches?e.touches[0].clientX:e.clientX;setPos(x)};';
					echo 'handle.addEventListener("mousedown",start);';
					echo 'handle.addEventListener("touchstart",start,{passive:true});';
					echo 'window.addEventListener("mouseup",end);';
					echo 'window.addEventListener("touchend",end);';
					echo 'window.addEventListener("mousemove",move);';
					echo 'window.addEventListener("touchmove",move,{passive:true});';
					echo 'stage.addEventListener("click",function(e){if(e.target.closest(".ba-handle"))return;setPos(e.clientX)});';
				echo '});';
			echo '})();';
		echo '</script>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — قبل وبعد (جديدة)',
			'description'=>'سلايدر السحب التفاعلي لنتائج الأعمال',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'ba_head_settings',
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
					'type'=>'Text',
					'id'=>'label_before',
					'title'=>'شارة "قبل" (الافتراضي: قبل)',
				),
				array(
					'type'=>'Text',
					'id'=>'label_after',
					'title'=>'شارة "بعد" (الافتراضي: بعد)',
				),

				array(
					'type'=>'Title',
					'id'=>'ba_cards_settings',
					'title'=>'بطاقات قبل/بعد — ارفع صور حقيقية أو اتركها للتدرجات اللونية',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'ba_cards',
					'title'=>'البطاقات',
					'fields'=> array(
						array(
							'type'=>'File',
							'id'=>'before_image',
							'title'=>'صورة "قبل" الحقيقية',
						),
						array(
							'type'=>'File',
							'id'=>'after_image',
							'title'=>'صورة "بعد" الحقيقية',
						),
						array(
							'type'=>'Text',
							'id'=>'before_text',
							'title'=>'نص جهة "قبل" (بدون صورة — مثال: قبل المعالجة)',
						),
						array(
							'type'=>'TextArea_Code',
							'id'=>'before_icon',
							'title'=>'أيقونة جهة "قبل" (HTML)',
						),
						array(
							'type'=>'Text',
							'id'=>'after_text',
							'title'=>'نص جهة "بعد" (بدون صورة — مثال: بعد المعالجة)',
						),
						array(
							'type'=>'TextArea_Code',
							'id'=>'after_icon',
							'title'=>'أيقونة جهة "بعد" (HTML)',
						),
						array(
							'type'=>'Radio',
							'id'=>'after_style',
							'title'=>'تدرج جهة "بعد" (بدون صورة)',
							'options'=>array(
								'blue'=>'أزرق تركواز',
								'gold'=>'ذهبي',
							)
						),
						array(
							'type'=>'Text',
							'id'=>'card_title',
							'title'=>'عنوان البطاقة (مثال: كشف تسربات — دبي مارينا)',
						),
						array(
							'type'=>'Text',
							'id'=>'card_sub',
							'title'=>'الوصف الصغير',
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
					'id' => 'ba_display_settings',
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
(new rukn_results)->Setup();
