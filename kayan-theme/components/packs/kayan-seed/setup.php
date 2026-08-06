<?php
/**
 * kayan-seed — تلقيم المحتوى الافتراضي الفعلي في إعدادات الصفحة الرئيسية (v1.4.1)
 * ════════════════════════════════════════════════════════════════════════
 * يكتب محتوى تصميم RUKN v3 الكامل داخل حقول الإعدادات فعلياً (وليس كـ fallback
 * في الكود)، فتفتح لوحة التحكم وتجد كل النصوص مكتوبة أمامك جاهزة للتعديل.
 *
 * البنية: كل ودجت = بوست من نوع widgets__posts، قيمه في widget_post_meta،
 * والترتيب في الخيار widgets_home__meta.
 *
 * يعمل مرة واحدة فقط (علم kayan_seed_home_done_v141). لا يمس أي إعداد موجود:
 * إن كانت الرئيسية مُعدّة مسبقاً لا يفعل شيئاً إطلاقاً.
 * ════════════════════════════════════════════════════════════════════════
 */

if ( ! function_exists( 'kayan_seed_home_defaults' ) ) {

	function kayan_seed_home_content_map() {
		return array(

			# ═══ rukn_hub ═══
			'rukn_hub' => array(
				'before_title' => 'مركز المعرفة',
				'title' => 'دليل الخدمات {%المنزلية%}',
				'content' => 'محتوى متخصص ومنظم يساعدك على فهم خدماتنا واتخاذ القرار الصحيح.',
				'hub_columns' => array(
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
				),
			),

			# ═══ benefits ═══
			'benefits' => array(
				'before_title' => 'لماذا نحن',
				'title' => 'لماذا يختار الآلاف {%'.esc_html( get_bloginfo('name') ).'؟%}',
				'content' => 'نجمع بين التقنية المتطورة والخبرة العميقة والضمان الحقيقي لنمنحك راحة بال كاملة.',
				'timeline_title' => 'رحلتك معنا بسيطة وواضحة',
				'timeline_sub' => 'من أول اتصال إلى تسليم العمل بضمان مكتوب.',
				'timeline_steps' => array(
				array( 'step_title'=>'تواصل ومعاينة مجانية', 'step_desc'=>'نصل إليك ونعاين الموقع بدون أي رسوم.' ),
				array( 'step_title'=>'عرض سعر شفاف',          'step_desc'=>'تكلفة واضحة بدون رسوم خفية.' ),
				array( 'step_title'=>'تنفيذ احترافي',          'step_desc'=>'فريق معتمد بأحدث الأجهزة.' ),
				array( 'step_title'=>'ضمان مكتوب ومتابعة',    'step_desc'=>'ضمان موثق ودعم مستمر بعد الخدمة.' ),
				),
				'feature_cards' => array(
				array( 'icon'=>'<i class="fas fa-microchip"></i>',        'title'=>'تقنية متطورة',   'desc'=>'أجهزة الكشف الحراري والصوتي الأحدث في السوق.' ),
				array( 'icon'=>'<i class="fas fa-user-shield"></i>',      'title'=>'فريق معتمد',     'desc'=>'جميع فنيينا حاصلون على شهادات اعتماد دولية.' ),
				array( 'icon'=>'<i class="fas fa-bolt"></i>',             'title'=>'استجابة سريعة',  'desc'=>'نصل إليك خلال ساعة في حالات الطوارئ.' ),
				array( 'icon'=>'<i class="fas fa-file-contract"></i>',    'title'=>'ضمان حقيقي',     'desc'=>'ضمان مكتوب وموثّق لجميع الأعمال.' ),
				array( 'icon'=>'<i class="fas fa-tags"></i>',             'title'=>'أسعار تنافسية',  'desc'=>'أفضل جودة بأفضل سعر وبدون رسوم خفية.' ),
				array( 'icon'=>'<i class="fas fa-map-location-dot"></i>', 'title'=>'تغطية شاملة',    'desc'=>'جميع إمارات الدولة السبع بلا استثناء.' ),
				array( 'icon'=>'<i class="fas fa-award"></i>',            'title'=>'خبرة 12 عاماً',  'desc'=>'سجل حافل في السوق الإماراتي.' ),
				array( 'icon'=>'<i class="fas fa-headset"></i>',          'title'=>'دعم مستمر',      'desc'=>'خدمة عملاء على مدار الساعة 24/7.' ),
				),
			),

			# ═══ rukn_stats ═══
			'rukn_stats' => array(
				'before_title' => 'أرقامنا',
				'title' => 'أرقام تتحدث عن جودتنا',
				'content' => 'ثقة الآلاف من العملاء في جميع أنحاء الإمارات.',
				'stats_items' => array(
				array( 'icon'=>'<i class="fas fa-users"></i>',      'number'=>'15000', 'suffix'=>'+', 'decimals'=>'', 'static_text'=>'', 'label'=>'عميل راضٍ' ),
				array( 'icon'=>'<i class="fas fa-briefcase"></i>',  'number'=>'30000', 'suffix'=>'+', 'decimals'=>'', 'static_text'=>'', 'label'=>'خدمة منجزة' ),
				array( 'icon'=>'<i class="fas fa-award"></i>',      'number'=>'12',    'suffix'=>'+', 'decimals'=>'', 'static_text'=>'', 'label'=>'سنة خبرة' ),
				array( 'icon'=>'<i class="fas fa-user-gear"></i>',  'number'=>'50',    'suffix'=>'+', 'decimals'=>'', 'static_text'=>'', 'label'=>'فني معتمد' ),
				array( 'icon'=>'<i class="fas fa-face-smile"></i>', 'number'=>'98',    'suffix'=>'%', 'decimals'=>'', 'static_text'=>'', 'label'=>'رضا العملاء' ),
				array( 'icon'=>'<i class="fas fa-headset"></i>',    'number'=>'',      'suffix'=>'',  'decimals'=>'', 'static_text'=>'24/7', 'label'=>'دعم فوري' ),
				),
			),

			# ═══ rukn_compare ═══
			'rukn_compare' => array(
				'before_title' => 'مقارنة',
				'title' => 'لماذا يختار العملاء {%'.esc_html( get_bloginfo('name') ).'؟%}',
				'content' => 'مقارنة واضحة بين خدماتنا والخدمات التقليدية.',
				'col_criteria' => 'المعيار',
				'col_others' => 'شركات أخرى',
			),

			# ═══ rukn_cases ═══
			'rukn_cases' => array(
				'before_title' => 'قصص النجاح',
				'title' => 'قصص نجاح حقيقية من {%مشاريعنا%}',
				'content' => 'تعرف على كيفية حل المشكلات المعقدة وتحقيق نتائج مميزة لعملائنا في مختلف إمارات الإمارات.',
				'label_problem' => 'المشكلة',
				'label_diagnosis' => 'التشخيص',
				'label_solution' => 'الحل',
				'cases_items' => array(
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
				),
			),

			# ═══ rukn_results ═══
			'rukn_results' => array(
				'before_title' => 'قبل وبعد',
				'title' => 'قبل وبعد — {%نتائج حقيقية%} لأعمالنا',
				'content' => 'اسحب المقبض لرؤية الفرق الذي يصنعه فريقنا.',
				'label_before' => 'قبل',
				'label_after' => 'بعد',
				'ba_cards' => array(
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
				),
			),

			# ═══ rukn_reviews ═══
			'rukn_reviews' => array(
				'before_title' => 'آراء العملاء',
				'title' => 'آراء عملائنا — {%تقييم 4.9 من 5%}',
				'content' => 'تقييمات حقيقية موثقة من عملاء Google.',
				'verified_text' => 'موثّق عبر Google',
				'reviews_items' => array(
				array( 'name'=>'محمد الشمري',    'location'=>'دبي مارينا', 'stars'=>'5', 'text'=>'خدمة ممتازة جداً! الفريق جاء في الموعد تماماً وحدّد مكان التسرب بدقة بدون أي تكسير. احترافية عالية وأسعار عادلة.' ),
				array( 'name'=>'سارة البلوشي',   'location'=>'البرشاء',    'stars'=>'5', 'text'=>'عزلنا السطح معهم منذ 3 سنوات ولم نواجه أي مشكلة حتى الآن رغم حرارة الصيف. ضمان حقيقي وعمل متقن.' ),
				array( 'name'=>'عبدالله الكعبي', 'location'=>'جميرا',      'stars'=>'5', 'text'=>'اتصلت بهم مساءً بسبب تسرب طارئ ووصلوا خلال أقل من ساعة. سرعة استجابة مذهلة وخدمة 24 ساعة فعلاً.' ),
				array( 'name'=>'فاطمة المنصوري', 'location'=>'أبوظبي',     'stars'=>'5', 'text'=>'صيانة التكييف كانت دقيقة جداً وأصبح المنزل أبرد بكثير. فريق مؤدب ونظيف في عمله. أنصح بهم بشدة.' ),
				array( 'name'=>'خالد النعيمي',   'location'=>'الشارقة',    'stars'=>'5', 'text'=>'أفضل شركة تعاملت معها للتنظيف والتعقيم. النتيجة فاقت التوقعات والمواد آمنة على الأطفال.' ),
				array( 'name'=>'ريم الحمادي',    'location'=>'عجمان',      'stars'=>'5', 'text'=>'تعامل راقٍ من أول مكالمة. عرض السعر كان شفافاً بدون أي مفاجآت، والعمل سُلّم في الوقت المحدد.' ),
				),
			),

			# ═══ price ═══
			'price' => array(
				'before_title' => 'الأسعار',
				'title' => 'أدلة الأسعار {%والتكاليف%}',
				'content' => 'تقديرات شفافة تساعدك على معرفة التكلفة قبل اتخاذ القرار.',
				'cards_mode' => 'auto',
				'read_text' => 'اقرأ الدليل',
				'default_unit' => 'درهم تقريباً',
			),

			# ═══ Faqs__simple2 ═══
			'Faqs__simple2' => array(
				'before_title' => 'الأسئلة الشائعة',
				'title' => 'الأسئلة {%الشائعة%}',
				'content' => 'إجابات واضحة لأكثر ما يسأل عنه عملاؤنا.',
				'items_mode' => 'auto',
				'all_filter_text' => 'الكل',
			),

			# ═══ city__widget ═══
			'city__widget' => array(
				'before_title' => 'مناطق الخدمة',
				'title' => 'خدماتنا في جميع {%إمارات الدولة%}',
				'content' => 'أينما كنت في الإمارات، فريق '.esc_html( get_bloginfo('name') ).' قريب منك وجاهز للخدمة.',
				'map_title' => 'تغطية كاملة لـ 7 إمارات',
				'map_sub' => 'استجابة سريعة وفريق محلي في كل إمارة.',
				'cards_mode' => 'auto',
				'card_icon' => '<i class="fas fa-city"></i>',
				'cta_card_title' => 'لم تجد مدينتك؟',
				'cta_card_sub' => 'تواصل معنا الآن',
				'map_badges' => array(
				array( 'icon'=>'<i class="fas fa-location-dot" style="color:var(--aqua)"></i>', 'title'=>'7 إمارات' ),
				array( 'icon'=>'<i class="fas fa-bolt" style="color:var(--aqua)"></i>',         'title'=>'استجابة خلال ساعة' ),
				),
			),

			# ═══ rukn_certs ═══
			'rukn_certs' => array(
				'before_title' => 'الموثوقية',
				'title' => 'التراخيص والشهادات {%والاعتمادات%}',
				'content' => 'نعمل بشفافية كاملة وفق التراخيص والمعايير المعتمدة في دولة الإمارات.',
				'certs_items' => array(
				array( 'icon'=>'<i class="fas fa-file-signature"></i>',   'cert_title'=>'رخصة تجارية',   'desc'=>'رخصة سارية لمزاولة نشاط الخدمات المنزلية.', 'badge'=>'موثّق',  'doc_label'=>'مستند الرخصة',  'doc_file'=>'', 'doc_url'=>'' ),
				array( 'icon'=>'<i class="fas fa-building-columns"></i>', 'cert_title'=>'سجل تجاري',     'desc'=>'سجل تجاري معتمد لدى الجهات الرسمية.',        'badge'=>'موثّق',  'doc_label'=>'مستند السجل',   'doc_file'=>'', 'doc_url'=>'' ),
				array( 'icon'=>'<i class="fas fa-receipt"></i>',          'cert_title'=>'تسجيل ضريبي',   'desc'=>'رقم تسجيل ضريبي (VAT) رسمي وفواتير نظامية.', 'badge'=>'موثّق',  'doc_label'=>'شهادة الضريبة', 'doc_file'=>'', 'doc_url'=>'' ),
				array( 'icon'=>'<i class="fas fa-medal"></i>',            'cert_title'=>'شهادة جودة',    'desc'=>'التزام بمعايير الجودة في جميع مراحل العمل.',  'badge'=>'معتمد',  'doc_label'=>'شهادة الجودة',  'doc_file'=>'', 'doc_url'=>'' ),
				array( 'icon'=>'<i class="fas fa-helmet-safety"></i>',    'cert_title'=>'شهادة السلامة', 'desc'=>'اعتماد إجراءات السلامة المهنية للفرق.',        'badge'=>'معتمد',  'doc_label'=>'شهادة السلامة', 'doc_file'=>'', 'doc_url'=>'' ),
				array( 'icon'=>'<i class="fas fa-shield-halved"></i>',    'cert_title'=>'برنامج الضمان', 'desc'=>'ضمان مكتوب وموثق يصل إلى 10 سنوات.',          'badge'=>'مضمون',  'doc_label'=>'وثيقة الضمان',  'doc_file'=>'', 'doc_url'=>'' ),
				),
			),

			# ═══ rukn_brands ═══
			'rukn_brands' => array(
				'before_title' => 'شركاؤنا',
				'title' => 'شركاؤنا في {%التميز%}',
				'brands_items' => array(
				array( 'name'=>'Sika',            'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				array( 'name'=>'Fosroc',          'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				array( 'name'=>'Jotun',           'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				array( 'name'=>'Mapei',           'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				array( 'name'=>'National Paints', 'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				array( 'name'=>'Weber',           'logo'=>'', 'icon'=>'<i class="fas fa-cube"></i>' ),
				),
			),

			# ═══ contact__form ═══
			'contact__form' => array(
				'widget_mode' => 'cta',
				'title' => 'جاهزون لخدمتك في أي وقت — 24/7',
				'content' => 'تواصل معنا الآن واحصل على معاينة مجانية وعرض سعر شفاف.',
				'quote_button_text' => 'طلب عرض سعر',
				'before_title' => 'تواصل معنا',
				'title' => 'تواصل {%معنا%}',
				'content' => 'فريقنا جاهز للرد على استفساراتك على مدار الساعة.',
				'form_title' => 'أرسل لنا رسالة',
				'submit_text' => 'إرسال الرسالة',
				'form_note' => 'بياناتك محفوظة ولن تُستخدم إلا للتواصل معك.',
				'success_text' => 'تم إرسال رسالتك بنجاح وسيتم التواصل معك قريباً.',
				'trust_badges' => array(
				array( 'icon'=>'<i class="fas fa-shield-halved"></i>',    'title'=>'ضمان 10 سنوات' ),
				array( 'icon'=>'<i class="fas fa-magnifying-glass"></i>', 'title'=>'معاينة مجانية' ),
				array( 'icon'=>'<i class="fas fa-headset"></i>',          'title'=>'خدمة طوارئ' ),
				),
			),
		);
	}

	/**
	 * ترتيب ودجات الرئيسية (تصميم RUKN v3)
	 */
	function kayan_seed_home_order() {
		return array_keys( kayan_seed_home_content_map() );
	}

	/**
	 * ينشئ بوست ودجت واحداً ويكتب قيمه الفعلية في widget_post_meta
	 */
	function kayan_seed_create_widget( $widget_id, $values, $index ) {
		$post_id = wp_insert_post( array(
			'post_title'  => $widget_id . '_home_' . $index,
			'post_type'   => 'widgets__posts',
			'post_status' => 'publish',
		) );

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return false;
		}

		update_post_meta( $post_id, 'widget_type', $widget_id );
		# ═══ هنا يُكتب المحتوى فعلياً في الحقول (لا fallback) ═══
		update_post_meta( $post_id, 'widget_post_meta', $values );

		return $post_id;
	}

	/**
	 * التلقيم الرئيسي — يعمل مرة واحدة ولا يمس إعداداً موجوداً
	 */
	function kayan_seed_home_defaults( $force = false ) {

		# لا تفعل شيئاً إن كانت الرئيسية مُعدّة مسبقاً (حماية بيانات المستخدم)
		$existing = get_option( 'widgets_home__meta' );
		if ( ! $force && is_array( $existing ) && ! empty( $existing ) ) {
			return false;
		}

		$map   = kayan_seed_home_content_map();
		$order = kayan_seed_home_order();
		$list  = array();
		$i     = 0;

		foreach ( $order as $widget_id ) {
			if ( ! isset( $map[ $widget_id ] ) ) {
				continue;
			}
			# تخطّي الودجات غير المسجّلة (إن حُذف ملفها)
			if ( ! class_exists( $widget_id ) ) {
				continue;
			}

			$i++;
			$post_id = kayan_seed_create_widget( $widget_id, $map[ $widget_id ], $i );
			if ( ! $post_id ) {
				continue;
			}

			$list[] = array(
				'widget_id'       => $widget_id,
				'widget_post__id' => $post_id,
			);
		}

		if ( empty( $list ) ) {
			return false;
		}

		update_option( 'widgets_home__meta', $list );
		return count( $list );
	}

	/**
	 * التشغيل التلقائي مرة واحدة عند تفعيل القالب أو أول تحميل
	 */
	function kayan_seed_maybe_run() {
		if ( 'yes' === get_option( 'kayan_seed_home_done_v141' ) ) {
			return;
		}
		# يحتاج نوع البوست widgets__posts مسجّلاً — ننتظر init
		update_option( 'kayan_seed_home_done_v141', 'yes' );
		kayan_seed_home_defaults();
	}
	add_action( 'init', 'kayan_seed_maybe_run', 20 );
	add_action( 'after_switch_theme', 'kayan_seed_maybe_run' );

	/**
	 * زر إعادة التلقيم اليدوي في لوحة التحكم
	 * (المظهر ← إعادة تلقيم محتوى الرئيسية)
	 */
	function kayan_seed_admin_page() {
		add_theme_page(
			'محتوى الرئيسية الافتراضي',
			'محتوى الرئيسية الافتراضي',
			'manage_options',
			'kayan-seed-home',
			'kayan_seed_admin_render'
		);
	}
	add_action( 'admin_menu', 'kayan_seed_admin_page' );

	function kayan_seed_admin_render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$done = false;
		if ( isset( $_POST['kayan_seed_run'] ) && check_admin_referer( 'kayan_seed_action' ) ) {
			$count = kayan_seed_home_defaults( true );
			$done  = $count;
		}

		$current = get_option( 'widgets_home__meta' );
		$has     = is_array( $current ) && ! empty( $current );

		echo '<div class="wrap" dir="rtl" style="max-width:820px">';
			echo '<h1>محتوى الصفحة الرئيسية الافتراضي</h1>';

			# لوحة تشخيص الإصدار — تكشف فوراً إن كان الرفع ناقصاً أو النسخة قديمة
			if ( function_exists( 'kayan_version_render_panel' ) ) {
				kayan_version_render_panel();
			}

			if ( false !== $done && $done ) {
				echo '<div class="notice notice-success"><p>تم كتابة محتوى ' . intval( $done ) . ' قسماً في إعدادات الرئيسية. افتح <strong>إعدادات القالب ← الرئيسية</strong> لتعديل النصوص.</p></div>';
			}

			echo '<div class="card" style="padding:16px;max-width:100%">';
				echo '<p>هذه الأداة تكتب محتوى تصميم RUKN v3 <strong>فعلياً داخل حقول الإعدادات</strong> — فتفتح إعدادات الرئيسية وتجد كل النصوص مكتوبة أمامك جاهزة للتعديل، بدلاً من حقول فارغة.</p>';

				if ( $has ) {
					echo '<p style="color:#b32d2e"><strong>تنبيه:</strong> الصفحة الرئيسية معدّة حالياً بـ ' . count( $current ) . ' قسماً. الضغط على الزر سيستبدلها بالمحتوى الافتراضي، وستفقد تعديلاتك الحالية.</p>';
				} else {
					echo '<p>الصفحة الرئيسية فارغة حالياً — اضغط الزر لتعبئتها بمحتوى التصميم الكامل.</p>';
				}

				echo '<form method="post">';
					wp_nonce_field( 'kayan_seed_action' );
					echo '<p><button type="submit" name="kayan_seed_run" value="1" class="button button-primary"' . ( $has ? ' onclick="return confirm(\'سيتم استبدال محتوى الرئيسية الحالي بالكامل. هل أنت متأكد؟\')"' : '' ) . '>كتابة المحتوى الافتراضي في الإعدادات</button></p>';
				echo '</form>';

				echo '<h3>الأقسام التي ستُكتب (' . count( kayan_seed_home_order() ) . ')</h3>';
				echo '<ol style="columns:2">';
					foreach ( kayan_seed_home_content_map() as $wid => $vals ) {
						echo '<li><code>' . esc_html( $wid ) . '</code> — ' . count( $vals ) . ' حقل</li>';
					}
				echo '</ol>';
			echo '</div>';
		echo '</div>';
	}
}
