<?php
/**
 * RUKN v3 BLOG — blog_v1
 * إعادة بناء كاملة لودجت المدونة بتصميم "مقالات ونصائح مفيدة"
 * كروت المقالات: الصورة البارزة (أو تدرج لوني بأيقونة)، شريحة التصنيف،
 * التاريخ بالعربي، العنوان، المقتطف، ورابط "اقرأ المقال"
 * البيانات تلقائية بالكامل من مقالات ووردبريس مع إمكانية تحديد التصنيفات والعدد
 */
class blog_v1 extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'blog_v1';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('Button__show','before_title','blog_display_settings','blog_head_settings','blog_more_settings','blog_posts_settings','button_Text','button_page','content','hide_date','number','read_text','taxonomy_option','title') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'المدونة';
		if( !isset( $title ) || empty( $title ) ) $title = 'مقالات ونصائح {%مفيدة%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'محتوى متخصص يساعدك على العناية بمنزلك واتخاذ القرار الصحيح.';

		# ═══════════ إعدادات ═══════════
		if( empty( $number ) ) $number = 3;
		if( !isset( $read_text ) || empty( $read_text ) ) $read_text = 'اقرأ المقال';

		# تدرجات الصور الافتراضية — بتتوزع بالتناوب على المقالات اللي مالهاش صورة بارزة
		$gradients = array(
			array( 'bg'=>'linear-gradient(135deg,var(--blue),var(--navy2))',  'icon'=>'<i class="fas fa-droplet"></i>',    'extra'=>'' ),
			array( 'bg'=>'linear-gradient(135deg,var(--turq),var(--aqua))',   'icon'=>'<i class="fas fa-layer-group"></i>','extra'=>'' ),
			array( 'bg'=>'linear-gradient(135deg,var(--gold),#ffce6b)',       'icon'=>'<i class="fas fa-snowflake"></i>',  'extra'=>'color:#3a2600' ),
			array( 'bg'=>'linear-gradient(135deg,var(--aqua),var(--turq))',   'icon'=>'<i class="fas fa-house"></i>',      'extra'=>'' ),
			array( 'bg'=>'linear-gradient(135deg,var(--navy2),var(--blue))',  'icon'=>'<i class="fas fa-wrench"></i>',     'extra'=>'' ),
			array( 'bg'=>'linear-gradient(135deg,var(--success),var(--turq))','icon'=>'<i class="fas fa-broom"></i>',      'extra'=>'' ),
		);

		# ═══════════ استعلام المقالات — نفس منطق الودجت القديمة ═══════════
		$QueryArgums = array(
			'post_type'      => 'post',
			'posts_per_page' => $number,
			'post_status'    => 'publish',
			'ignore_sticky_posts' => true,
		);

		if( isset( $taxonomy_option ) && !empty( $taxonomy_option ) && is_array( $taxonomy_option ) ){
			$QueryArgums['tax_query'] = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'term_id',
					'terms'    => $taxonomy_option,
				)
			);
		}

		$BlogQuery = new WP_Query( $QueryArgums );

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

			# شبكة المقالات
			echo '<div class="blog-grid">';

				if( isset( $use_default_content ) && !empty( $use_default_content ) ){

					# ═══ المحتوى الافتراضي — مقالات التصميم الثلاثة ═══
					$design_posts = array(
					array( 'category'=>'كشف تسربات', 'date'=>'15 يونيو 2026', 'title'=>'دليلك الشامل لكشف تسربات المياه في الإمارات', 'desc'=>'تعرّف على أحدث تقنيات الكشف بدون تكسير وكيفية اكتشاف التسرب مبكراً.' ),
					array( 'category'=>'عزل', 'date'=>'10 يونيو 2026', 'title'=>'أفضل أنواع عزل الأسطح لمناخ الإمارات الحار', 'desc'=>'مقارنة بين الفوم والأغشية البيتومينية لاختيار الأنسب لمنزلك.' ),
					array( 'category'=>'تكييف', 'date'=>'2 يونيو 2026', 'title'=>'كيف تحافظ على تكييفك طوال فصل الصيف', 'desc'=>'نصائح عملية للصيانة الدورية تطيل عمر مكيفك وتوفر فاتورة الكهرباء.' ),
				);
					$post_index = 0;
					foreach ( $design_posts as $design_post ) {
						$fallback = $gradients[ $post_index % count( $gradients ) ];
						$img_style = 'background:'.$fallback['bg'].( ( !empty( $fallback['extra'] ) ) ? ';'.$fallback['extra'] : '' );
						echo '<article class="post rv">';
							echo '<a href="#" class="post-img" style="'.$img_style.'">'.$fallback['icon'].'</a>';
							echo '<div class="post-body">';
								echo '<span class="post-cat">'.$design_post['category'].'</span>';
								echo '<span class="post-date">'.$design_post['date'].'</span>';
								echo '<h3><a href="#">'.$design_post['title'].'</a></h3>';
								echo '<p>'.$design_post['desc'].'</p>';
								echo '<a class="read" href="#">'.$read_text.' <i class="fas fa-arrow-left"></i></a>';
							echo '</div>';
						echo '</article>';
						$post_index++;
					}

				}else{

				$post_index = 0;
				while ( $BlogQuery->have_posts() ) {
					$BlogQuery->the_post();
					$post_id   = get_the_ID();
					$permalink = get_the_permalink();

					$terms = get_the_terms( $post_id, 'category' );
					$category_name = ( is_array( $terms ) && isset( $terms[0]->name ) ) ? $terms[0]->name : '';

					$thumbnail_url = get_the_post_thumbnail_url( $post_id, 'large' );
					$fallback = $gradients[ $post_index % count( $gradients ) ];

					echo '<article class="post rv">';

						# صورة المقال — صورة بارزة أو تدرج بأيقونة
						if( !empty( $thumbnail_url ) ){
							echo '<a href="'.$permalink.'" class="post-img" style="background-image:url(\''.$thumbnail_url.'\');background-size:cover;background-position:center" title="'.esc_attr( get_the_title() ).'"></a>';
						}else{
							$img_style = 'background:'.$fallback['bg'].( ( !empty( $fallback['extra'] ) ) ? ';'.$fallback['extra'] : '' );
							echo '<a href="'.$permalink.'" class="post-img" style="'.$img_style.'" title="'.esc_attr( get_the_title() ).'">'.$fallback['icon'].'</a>';
						}

						echo '<div class="post-body">';
							if( !empty( $category_name ) ) echo '<span class="post-cat">'.$category_name.'</span>';
							if( !isset( $hide_date ) || empty( $hide_date ) ) echo '<span class="post-date">'.get_the_date('j F Y').'</span>';
							echo '<h3><a href="'.$permalink.'" title="'.esc_attr( get_the_title() ).'">'.get_the_title().'</a></h3>';
							echo '<p>'.wp_trim_words( get_the_excerpt(), 15 ).'</p>';
							echo '<a class="read" href="'.$permalink.'" title="'.esc_attr( get_the_title() ).'">'.$read_text.' <i class="fas fa-arrow-left"></i></a>';
						echo '</div>';

					echo '</article>';
					$post_index++;
				}
				wp_reset_postdata();

				}

			echo '</div>';

			# زرار المزيد من المقالات
			if( isset( $Button__show ) && !empty( $Button__show ) ){
				$more_url  = ( isset( $button_page ) && !empty( $button_page ) ) ? get_the_permalink( $button_page ) : home_url('/blog/');
				$more_text = ( isset( $button_Text ) && !empty( $button_Text ) ) ? $button_Text : 'عرض جميع المقالات';
				echo '<div class="blog-more-wrap rv">';
					echo '<a href="'.$more_url.'" class="btn btn-soft">'.$more_text.' <i class="fas fa-arrow-left"></i></a>';
				echo '</div>';
			}

		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — المدونة',
			'description'=>'كروت المقالات بتصميم Rukn v3',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'blog_head_settings',
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
					'id'=>'blog_posts_settings',
					'title'=>'إعدادات المقالات',
				),
				array(
					'type'=>'Number',
					'id' => 'number',
					'title' =>'عدد المقالات (التصميم: 3)',
				),
				array(
			        'type'    => 'Taxonomy-CheckBox',
			        'id'      => 'taxonomy_option',
			        'title'   => 'اعرض من تصنيفات محددة — اتركها فارغة لكل التصنيفات',
                    'taxonomy_name' => 'category',
                    'pre'=>10
			    ),
				array(
					'type'=>'Text',
					'id'=>'read_text',
					'title'=>'نص رابط الكارت (الافتراضي: اقرأ المقال)',
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'hide_date',
					'title'=>'إخفاء تاريخ النشر',
				),

				array(
					'type'=>'Title',
					'id'=>'blog_more_settings',
					'title'=>'زرار المزيد من المقالات',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'Button__show',
					'title' =>'إظهار زرار المزيد من المقالات',
				),
				array(
	                'type'=>'Posts-Select',
	                'id' => 'button_page',
	                'post_type_name'=>'page',
	                'title' =>'صفحة المدونة',
	            ),
	            array(
	                'type'=>'Text',
	                'id' => 'button_Text',
	                'title' =>'عنوان الزرار (الافتراضي: عرض جميع المقالات)',
	            ),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'blog_display_settings',
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
(new blog_v1)->Setup();
