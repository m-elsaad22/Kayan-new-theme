<?php
/**
 * RUKN v3 FAQ — Faqs__simple2
 * إعادة بناء كاملة لودجت الأسئلة الشائعة بتصميم الأكورديون الجديد
 * فلاتر تصنيفات تلقائية + أكورديون بفتح سؤال واحد في المرة
 * وضعين للأسئلة:
 *   - تلقائي: من بوستات الأسئلة 'faq' (السؤال=العنوان، الإجابة=المحتوى،
 *             والتصنيف الاختياري من post meta: faq_category)
 *   - يدوي:  أسئلة كاملة التحكم من لوحة التحكم
 */
class Faqs__simple2 extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'Faqs__simple2';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('all_filter_text','answer','before_title','category','content','faq_auto_settings','faq_display_settings','faq_filters_settings','faq_head_settings','faq_items_mode_title','faq_manual_settings','hide_filters','items_mode','manual_faqs','number','question','title') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
			$items_mode = 'manual';
			$manual_faqs = array(
				array( 'question'=>'ما تكلفة كشف تسربات المياه في الإمارات؟', 'answer'=>'نقدم معاينة مجانية وعرض سعر شفاف قبل البدء. تختلف التكلفة حسب نوع التسرب والمساحة والإمارة، مع أسعار تنافسية وبدون رسوم خفية.', 'category'=>'leak' ),
				array( 'question'=>'هل كشف التسربات يحتاج تكسير الجدران؟', 'answer'=>'لا. نعتمد على الكاميرات الحرارية وأجهزة الكشف الصوتي لتحديد مكان التسرب بدقة بدون تكسير، ثم نصلح الموقع المحدد فقط.', 'category'=>'leak' ),
				array( 'question'=>'هل تقدمون خدمة طوارئ على مدار الساعة؟', 'answer'=>'نعم، فريقنا متاح 24/7 في جميع أيام الأسبوع، ونصل إليك خلال ساعة في حالات الطوارئ داخل المدن الرئيسية.', 'category'=>'maint' ),
				array( 'question'=>'ما هو الضمان على أعمال العزل؟', 'answer'=>'نقدم ضماناً مكتوباً وموثقاً يصل إلى 10 سنوات على أعمال العزل المائي والحراري.', 'category'=>'insul' ),
				array( 'question'=>'هل تعملون في جميع إمارات الدولة؟', 'answer'=>'نعم، نغطي جميع إمارات الدولة السبع: دبي، أبوظبي، الشارقة، عجمان، رأس الخيمة، الفجيرة، وأم القيوين.', 'category'=>'maint' ),
				array( 'question'=>'كم يستغرق تنفيذ عزل السطح؟', 'answer'=>'يعتمد على مساحة السطح ونوع العزل، لكن غالبية المشاريع السكنية تُنجز خلال يوم إلى ثلاثة أيام عمل.', 'category'=>'insul' ),
			);
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'الأسئلة الشائعة';
		if( !isset( $title ) || empty( $title ) ) $title = 'الأسئلة {%الشائعة%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'إجابات واضحة لأكثر ما يسأل عنه عملاؤنا.';

		# ═══════════ إعدادات ═══════════
		if( empty( $number ) ) $number = 8;
		if( !isset( $items_mode ) || empty( $items_mode ) ) $items_mode = 'auto';
		if( !isset( $all_filter_text ) || empty( $all_filter_text ) ) $all_filter_text = 'الكل';

		# ═══════════ تجهيز بيانات الأسئلة ═══════════
		$items = array();

		if( $items_mode == 'manual' && isset( $manual_faqs ) && !empty( $manual_faqs ) && is_array( $manual_faqs ) ){

			# الوضع اليدوي — أسئلة من لوحة التحكم
			foreach ( $manual_faqs as $mf ) {
				if( !isset( $mf['question'] ) || empty( $mf['question'] ) ) continue;
				$items[] = array(
					'question' => $mf['question'],
					'answer'   => ( isset( $mf['answer'] ) ) ? $mf['answer'] : '',
					'category' => ( isset( $mf['category'] ) ) ? $mf['category'] : '',
				);
			}

		}else{

			# الوضع التلقائي — بوستات الأسئلة
			$FaqQuery = new WP_Query( array(
				'post_type'      => 'faq',
				'posts_per_page' => $number,
				'post_status'    => 'publish',
				'order'          => 'ASC',
				'orderby'        => 'menu_order date',
			) );

			while ( $FaqQuery->have_posts() ) {
				$FaqQuery->the_post();
				$items[] = array(
					'question' => get_the_title(),
					'answer'   => wpautop( get_the_content() ),
					'category' => get_post_meta( get_the_ID(), 'faq_category', true ),
				);
			}
			wp_reset_postdata();
		}

		# ═══════════ بناء الفلاتر من تصنيفات الأسئلة ═══════════
		$filters = array();
		foreach ( $items as $item ) {
			if( !empty( $item['category'] ) && !in_array( $item['category'], $filters ) ) $filters[] = $item['category'];
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

			echo '<div class="faq-wrap" data-faq-wrap>';

				# فلاتر التصنيفات
				if( ( !isset( $hide_filters ) || empty( $hide_filters ) ) && !empty( $filters ) ){
					echo '<div class="faq-cats rv" data-faq-filters>';
						echo '<button class="active" data-fc="all">'.$all_filter_text.'</button>';
						foreach ( $filters as $filter_name ) {
							echo '<button data-fc="'.esc_attr( $filter_name ).'">'.$filter_name.'</button>';
						}
					echo '</div>';
				}

				# الأكورديون
				foreach ( $items as $item ) {
					echo '<div class="faq-item rv" data-cat="'.esc_attr( $item['category'] ).'">';
						echo '<div class="faq-q" data-faq-toggle>';
							echo '<span>'.$item['question'].'</span>';
							echo '<i class="fas fa-chevron-down"></i>';
						echo '</div>';
						echo '<div class="faq-a">';
							echo ( strpos( $item['answer'], '<p' ) !== FALSE ) ? $item['answer'] : '<p>'.$item['answer'].'</p>';
						echo '</div>';
					echo '</div>';
				}

			echo '</div>';

		echo '</div>';

		# ════════════════════════════════════════════════════════
		# INLINE JS — الأكورديون (سؤال واحد مفتوح) + الفلترة
		# ════════════════════════════════════════════════════════
		echo '<script type="text/javascript">';
			echo '(function(){';
				echo 'var faqWrap=document.querySelector("[data-faq-wrap]");if(!faqWrap)return;';
				# الأكورديون
				echo 'faqWrap.querySelectorAll("[data-faq-toggle]").forEach(function(q){';
					echo 'q.addEventListener("click",function(){';
						echo 'var item=q.parentElement;';
						echo 'var ans=item.querySelector(".faq-a");';
						echo 'var isOpen=item.classList.contains("faq-open");';
						echo 'faqWrap.querySelectorAll(".faq-item.faq-open").forEach(function(o){o.classList.remove("faq-open");o.querySelector(".faq-a").style.maxHeight=null});';
						echo 'if(!isOpen){item.classList.add("faq-open");ans.style.maxHeight=ans.scrollHeight+"px"}';
					echo '});';
				echo '});';
				# الفلترة
				echo 'var filterWrap=faqWrap.querySelector("[data-faq-filters]");if(!filterWrap)return;';
				echo 'var faqItems=faqWrap.querySelectorAll(".faq-item");';
				echo 'filterWrap.querySelectorAll("button").forEach(function(btn){';
					echo 'btn.addEventListener("click",function(){';
						echo 'filterWrap.querySelectorAll("button").forEach(function(b){b.classList.remove("active")});';
						echo 'btn.classList.add("active");';
						echo 'var f=btn.getAttribute("data-fc");';
						echo 'faqItems.forEach(function(item){';
							echo 'item.classList.toggle("hide",f!=="all"&&item.getAttribute("data-cat")!==f);';
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
			'title'=>'RUKN v3 — الأسئلة الشائعة',
			'description'=>'أكورديون الأسئلة بالفلاتر بتصميم Rukn v3',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'faq_head_settings',
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
					'id'=>'faq_filters_settings',
					'title'=>'الفلاتر — بتتبني تلقائياً من تصنيفات الأسئلة وبتختفي لو مافيش تصنيفات',
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
					'id'=>'faq_items_mode_title',
					'title'=>'مصدر الأسئلة',
				),
				array(
					'type'=>'Radio',
					'id'=>'items_mode',
					'title'=>'طريقة عرض الأسئلة',
					'options'=>array(
						'auto'  =>'تلقائي — من بوستات الأسئلة',
						'manual'=>'يدوي — أسئلة كاملة التحكم',
					)
				),

				array(
					'type'=>'Title',
					'id'=>'faq_auto_settings',
					'title'=>'إعدادات الوضع التلقائي — التصنيف الاختياري من حقل faq_category في البوست',
				),
				array(
					'type'=>'Number',
					'id' => 'number',
					'title' =>'عدد الأسئلة',
				),

				array(
					'type'=>'Title',
					'id'=>'faq_manual_settings',
					'title'=>'إعدادات الوضع اليدوي (الأسئلة)',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'manual_faqs',
					'title'=>'الأسئلة',
					'fields'=> array(
						array(
							'type'=>'Text',
							'id'=>'question',
							'title'=>'السؤال',
						),
						array(
							'type'=>'TextArea',
							'id'=>'answer',
							'title'=>'الإجابة',
						),
						array(
							'type'=>'Text',
							'id'=>'category',
							'title'=>'التصنيف (اختياري — لفلاتر الأعلى)',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'faq_display_settings',
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
(new Faqs__simple2)->Setup();
