<?php
/**
 * RUKN v3 PROJECTS — works_v1
 * إعادة بناء كاملة لودجت سابقة الأعمال بتصميم "مشاريعنا المنجزة"
 * فلاتر تفاعلية + كروت مشاريع (صورة/تدرج بأيقونة، شريحة تصنيف، موقع، مدة، نتيجة)
 * وضعين للكروت:
 *   - تلقائي: من بوستات سابقة الأعمال 'works' (العنوان، الصورة البارزة، التصنيف،
 *             وبيانات إضافية من post meta: proj_location / proj_duration / proj_result)
 *   - يدوي:  كروت كاملة التحكم مطابقة للتصميم بالملي
 */
class works_v1 extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'works_v1';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('all_filter_text','before_title','cards_mode','category','content','duration','hide_filters','icon','image','location','manual_projects','number','proj_auto_settings','proj_cards_mode_title','proj_display_settings','proj_filters_settings','proj_head_settings','proj_manual_settings','result','title','url') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
			$cards_mode = 'manual';
			$manual_projects = array(
				array( 'image'=>'', 'icon'=>'<i class="fas fa-layer-group"></i>', 'title'=>'عزل سطح فيلا', 'category'=>'عزل', 'location'=>'دبي مارينا', 'duration'=>'3 أيام', 'result'=>'خفض حرارة السطح بنسبة 40%', 'url'=>'' ),
				array( 'image'=>'', 'icon'=>'<i class="fas fa-droplet"></i>', 'title'=>'كشف تسربات', 'category'=>'كشف تسربات', 'location'=>'البرشاء', 'duration'=>'يوم واحد', 'result'=>'إصلاح بدون تكسير الجدران', 'url'=>'' ),
				array( 'image'=>'', 'icon'=>'<i class="fas fa-snowflake"></i>', 'title'=>'صيانة تكييف شامل', 'category'=>'صيانة', 'location'=>'الشارقة', 'duration'=>'يومان', 'result'=>'تحسين كفاءة التبريد', 'url'=>'' ),
				array( 'image'=>'', 'icon'=>'<i class="fas fa-spray-can-sparkles"></i>', 'title'=>'تنظيف وتعقيم فيلا', 'category'=>'تنظيف', 'location'=>'أبوظبي', 'duration'=>'يوم واحد', 'result'=>'تعقيم كامل بالبخار', 'url'=>'' ),
				array( 'image'=>'', 'icon'=>'<i class="fas fa-water"></i>', 'title'=>'عزل خزانات', 'category'=>'عزل', 'location'=>'عجمان', 'duration'=>'يومان', 'result'=>'مياه نظيفة وآمنة', 'url'=>'' ),
				array( 'image'=>'', 'icon'=>'<i class="fas fa-bug-slash"></i>', 'title'=>'مكافحة حشرات', 'category'=>'تنظيف', 'location'=>'رأس الخيمة', 'duration'=>'يوم واحد', 'result'=>'ضمان عدم العودة', 'url'=>'' ),
			);
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'أعمالنا';
		if( !isset( $title ) || empty( $title ) ) $title = 'مشاريعنا {%المنجزة%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'نماذج من مئات المشاريع التي نفذناها بنجاح في جميع الإمارات.';

		# ═══════════ إعدادات ═══════════
		if( empty( $number ) ) $number = 6;
		if( !isset( $cards_mode ) || empty( $cards_mode ) ) $cards_mode = 'auto';
		if( !isset( $all_filter_text ) || empty( $all_filter_text ) ) $all_filter_text = 'الكل';

		# تدرجات الصور الافتراضية — بتتوزع بالتناوب على الكروت اللي مالهاش صورة
		$gradients = array(
			'linear-gradient(135deg,var(--turq),var(--blue))',
			'linear-gradient(135deg,var(--blue),var(--navy2))',
			'linear-gradient(135deg,var(--aqua),var(--turq))',
			'linear-gradient(135deg,var(--success),var(--turq))',
			'linear-gradient(135deg,var(--gold),#ffce6b)',
			'linear-gradient(135deg,var(--navy2),var(--blue))',
		);

		# ═══════════ تجهيز بيانات الكروت ═══════════
		$cards = array();

		if( $cards_mode == 'manual' && isset( $manual_projects ) && !empty( $manual_projects ) && is_array( $manual_projects ) ){

			# الوضع اليدوي — كروت من لوحة التحكم
			foreach ( $manual_projects as $mp ) {
				if( !isset( $mp['title'] ) || empty( $mp['title'] ) ) continue;
				$image_url = '';
				if( isset( $mp['image'] ) && !empty( $mp['image'] ) ){
					$image_src = wp_get_attachment_image_src( $mp['image'], 'large' );
					if( isset( $image_src[0] ) ) $image_url = $image_src[0];
				}
				$cards[] = array(
					'title'    => $mp['title'],
					'icon'     => ( isset( $mp['icon'] ) && !empty( $mp['icon'] ) ) ? $mp['icon'] : '<i class="fas fa-briefcase"></i>',
					'category' => ( isset( $mp['category'] ) ) ? $mp['category'] : '',
					'location' => ( isset( $mp['location'] ) ) ? $mp['location'] : '',
					'duration' => ( isset( $mp['duration'] ) ) ? $mp['duration'] : '',
					'result'   => ( isset( $mp['result'] ) ) ? $mp['result'] : '',
					'image'    => $image_url,
					'url'      => ( isset( $mp['url'] ) ) ? $mp['url'] : '',
				);
			}

		}else{

			# الوضع التلقائي — بوستات سابقة الأعمال
			$WorksQuery = new WP_Query( array(
				'post_type'      => 'works',
				'posts_per_page' => $number,
				'post_status'    => 'publish',
			) );

			while ( $WorksQuery->have_posts() ) {
				$WorksQuery->the_post();
				$post_id = get_the_ID();

				$terms = get_the_terms( $post_id, 'category' );
				$category_name = ( is_array( $terms ) && isset( $terms[0]->name ) ) ? $terms[0]->name : '';

				$result = get_post_meta( $post_id, 'proj_result', true );
				if( empty( $result ) ) $result = wp_trim_words( get_the_excerpt(), 8 );

				$cards[] = array(
					'title'    => get_the_title(),
					'icon'     => '<i class="fas fa-briefcase"></i>',
					'category' => $category_name,
					'location' => get_post_meta( $post_id, 'proj_location', true ),
					'duration' => get_post_meta( $post_id, 'proj_duration', true ),
					'result'   => $result,
					'image'    => get_the_post_thumbnail_url( $post_id, 'large' ),
					'url'      => get_the_permalink(),
				);
			}
			wp_reset_postdata();
		}

		# ═══════════ بناء قائمة الفلاتر من تصنيفات الكروت ═══════════
		$filters = array();
		foreach ( $cards as $card ) {
			if( !empty( $card['category'] ) && !in_array( $card['category'], $filters ) ) $filters[] = $card['category'];
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

			# الفلاتر
			if( ( !isset( $hide_filters ) || empty( $hide_filters ) ) && !empty( $filters ) ){
				echo '<div class="filters rv" data-proj-filters>';
					echo '<button class="active" data-f="all">'.$all_filter_text.'</button>';
					foreach ( $filters as $filter_name ) {
						echo '<button data-f="'.esc_attr( $filter_name ).'">'.$filter_name.'</button>';
					}
				echo '</div>';
			}

			# شبكة المشاريع
			echo '<div class="proj-grid" data-proj-grid>';
				$card_index = 0;
				foreach ( $cards as $card ) {
					echo '<div class="proj rv" data-cat="'.esc_attr( $card['category'] ).'">';

						# صورة المشروع — صورة حقيقية أو تدرج بأيقونة
						if( !empty( $card['image'] ) ){
							echo '<div class="proj-img" style="background-image:url(\''.$card['image'].'\');background-size:cover;background-position:center">';
						}else{
							echo '<div class="proj-img" style="background:'.$gradients[ $card_index % count( $gradients ) ].'">';
							echo $card['icon'];
						}
						if( !empty( $card['category'] ) ) echo '<span class="cat">'.$card['category'].'</span>';
						echo '</div>';

						echo '<div class="proj-body">';
							if( !empty( $card['url'] ) ){
								echo '<h3><a href="'.$card['url'].'" title="'.esc_attr( $card['title'] ).'">'.$card['title'].'</a></h3>';
							}else{
								echo '<h3>'.$card['title'].'</h3>';
							}
							if( !empty( $card['location'] ) || !empty( $card['duration'] ) ){
								echo '<div class="proj-meta">';
									if( !empty( $card['location'] ) ) echo '<span><i class="fas fa-location-dot"></i> '.$card['location'].'</span>';
									if( !empty( $card['duration'] ) ) echo '<span><i class="fas fa-clock"></i> '.$card['duration'].'</span>';
								echo '</div>';
							}
							if( !empty( $card['result'] ) ){
								echo '<div class="proj-res"><i class="fas fa-circle-check"></i> '.$card['result'].'</div>';
							}
						echo '</div>';

					echo '</div>';
					$card_index++;
				}
			echo '</div>';

		echo '</div>';

		# ════════════════════════════════════════════════════════
		# INLINE JS — فلترة المشاريع
		# ════════════════════════════════════════════════════════
		echo '<script type="text/javascript">';
			echo '(function(){';
				echo 'var wrap=document.querySelector("[data-proj-filters]");if(!wrap)return;';
				echo 'var cards=document.querySelectorAll("[data-proj-grid] .proj");';
				echo 'wrap.querySelectorAll("button").forEach(function(btn){';
					echo 'btn.addEventListener("click",function(){';
						echo 'wrap.querySelectorAll("button").forEach(function(b){b.classList.remove("active")});';
						echo 'btn.classList.add("active");';
						echo 'var f=btn.getAttribute("data-f");';
						echo 'cards.forEach(function(card){';
							echo 'card.classList.toggle("hide",f!=="all"&&card.getAttribute("data-cat")!==f);';
						echo '});';
					echo '});';
				echo '});';
			echo '})();';
		echo '</script>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — مشاريعنا المنجزة',
			'description'=>'معرض المشاريع بالفلاتر التفاعلية بتصميم Rukn v3',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'proj_head_settings',
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
					'id'=>'proj_filters_settings',
					'title'=>'الفلاتر — بتتبني تلقائياً من تصنيفات الكروت',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_filters',
					'title'=>'إخفاء شريط الفلاتر',
				),
				array(
					'type'=>'Text',
					'id'=>'all_filter_text',
					'title'=>'نص فلتر "الكل"',
				),

				array(
					'type'=>'Title',
					'id'=>'proj_cards_mode_title',
					'title'=>'مصدر الكروت',
				),
				array(
					'type'=>'Radio',
					'id'=>'cards_mode',
					'title'=>'طريقة عرض الكروت',
					'options'=>array(
						'auto'  =>'تلقائي — من بوستات سابقة الأعمال',
						'manual'=>'يدوي — كروت كاملة التحكم',
					)
				),

				array(
					'type'=>'Title',
					'id'=>'proj_auto_settings',
					'title'=>'إعدادات الوضع التلقائي — البيانات الإضافية من حقول proj_location / proj_duration / proj_result في البوست',
				),
				array(
					'type'=>'Number',
					'id' => 'number',
					'title' =>'عدد المشاريع',
				),

				array(
					'type'=>'Title',
					'id'=>'proj_manual_settings',
					'title'=>'إعدادات الوضع اليدوي (الكروت)',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'manual_projects',
					'title'=>'كروت المشاريع',
					'fields'=> array(
						array(
							'type'=>'File',
							'id'=>'image',
							'title'=>'صورة المشروع — اتركها فارغة للتدرج اللوني بالأيقونة',
						),
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML) — تظهر فقط بدون صورة',
						),
						array(
							'type'=>'Text',
							'id'=>'title',
							'title'=>'اسم المشروع',
						),
						array(
							'type'=>'Text',
							'id'=>'category',
							'title'=>'التصنيف (شريحة الكارت + الفلتر)',
						),
						array(
							'type'=>'Text',
							'id'=>'location',
							'title'=>'الموقع (مثال: دبي مارينا)',
						),
						array(
							'type'=>'Text',
							'id'=>'duration',
							'title'=>'المدة (مثال: 3 أيام)',
						),
						array(
							'type'=>'Text',
							'id'=>'result',
							'title'=>'النتيجة (السطر الأخضر أسفل الكارت)',
						),
						array(
							'type'=>'Text',
							'id'=>'url',
							'title'=>'رابط المشروع (اختياري)',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'proj_display_settings',
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
(new works_v1)->Setup();
