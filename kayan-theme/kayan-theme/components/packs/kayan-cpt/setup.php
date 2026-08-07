<?php
/**
 * kayan-cpt — أنواع المنشورات والتصنيفات المخصصة (v1.3.0)
 * ════════════════════════════════════════════════════════════════
 * أقسام مستقلة مثل المقالات: خدمات، تقييمات، أسئلة، أسعار، سابقة أعمال، قبل/بعد.
 * تصنيفان مشتركان: المدن (hierarchical) + تصنيفات الخدمات.
 *
 * كل الأسماء الظاهرة تستخدم get_bloginfo('name') — لا أسماء ثابتة.
 * دعم كامل: has_archive + REST API + Dashicons + عناوين عربية.
 * SEO: كل CPT عام (public) وقابل للفهرسة، والأرشيف مفعّل، ومتوافق مع Rank Math.
 * ════════════════════════════════════════════════════════════════
 */

if ( ! function_exists( 'kayan_cpt_register' ) ) {

	function kayan_cpt_labels( $plural, $singular, $menu_icon = '' ) {
		return array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'menu_name'          => $plural,
			'name_admin_bar'     => $singular,
			'add_new'            => 'إضافة جديد',
			'add_new_item'       => 'إضافة ' . $singular,
			'new_item'           => $singular . ' جديد',
			'edit_item'          => 'تعديل ' . $singular,
			'view_item'          => 'عرض ' . $singular,
			'all_items'          => 'كل ' . $plural,
			'search_items'       => 'بحث في ' . $plural,
			'not_found'          => 'لا توجد عناصر.',
			'not_found_in_trash' => 'لا توجد عناصر في سلة المهملات.',
		);
	}

	function kayan_cpt_register() {

		# ═══════════ 1) أنواع المنشورات المخصصة ═══════════

		# الخدمات — مقال متكامل لكل خدمة (كشف تسربات، عزل...) بدون اسم مدينة
		register_post_type( 'services', array(
			'labels'       => kayan_cpt_labels( 'الخدمات', 'خدمة' ),
			'public'       => true,
			'has_archive'  => 'services',
			'menu_position'=> 5,
			'menu_icon'    => 'dashicons-admin-tools',
			'rewrite'      => array( 'slug' => 'services', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'page-attributes' ),
			'show_in_rest' => true,
			'rest_base'    => 'services',
		) );

		# تقييمات العملاء
		register_post_type( 'reviews', array(
			'labels'       => kayan_cpt_labels( 'تقييمات العملاء', 'تقييم' ),
			'public'       => true,
			'has_archive'  => 'reviews',
			'menu_position'=> 6,
			'menu_icon'    => 'dashicons-star-filled',
			'rewrite'      => array( 'slug' => 'reviews', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'show_in_rest' => true,
			'rest_base'    => 'reviews',
		) );

		# الأسئلة الشائعة
		register_post_type( 'faqs', array(
			'labels'       => kayan_cpt_labels( 'الأسئلة الشائعة', 'سؤال' ),
			'public'       => true,
			'has_archive'  => 'faqs',
			'menu_position'=> 7,
			'menu_icon'    => 'dashicons-editor-help',
			'rewrite'      => array( 'slug' => 'faqs', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'custom-fields' ),
			'show_in_rest' => true,
			'rest_base'    => 'faqs',
		) );

		# خطط الأسعار
		register_post_type( 'pricing', array(
			'labels'       => kayan_cpt_labels( 'خطط الأسعار', 'خطة' ),
			'public'       => true,
			'has_archive'  => 'pricing',
			'menu_position'=> 8,
			'menu_icon'    => 'dashicons-tag',
			'rewrite'      => array( 'slug' => 'pricing', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' ),
			'show_in_rest' => true,
			'rest_base'    => 'pricing',
		) );

		# سابقة الأعمال
		register_post_type( 'portfolio', array(
			'labels'       => kayan_cpt_labels( 'سابقة الأعمال', 'عمل' ),
			'public'       => true,
			'has_archive'  => 'portfolio',
			'menu_position'=> 9,
			'menu_icon'    => 'dashicons-portfolio',
			'rewrite'      => array( 'slug' => 'portfolio', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'show_in_rest' => true,
			'rest_base'    => 'portfolio',
		) );

		# قبل وبعد
		register_post_type( 'before_after', array(
			'labels'       => kayan_cpt_labels( 'قبل وبعد', 'مقارنة' ),
			'public'       => true,
			'has_archive'  => 'before-after',
			'menu_position'=> 10,
			'menu_icon'    => 'dashicons-image-flip-horizontal',
			'rewrite'      => array( 'slug' => 'before-after', 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'show_in_rest' => true,
			'rest_base'    => 'before-after',
		) );

		# ═══════════ 2) التصنيفات المخصصة ═══════════

		# المدن — hierarchical، مشترك مع كل الأقسام + المقالات
		register_taxonomy( 'cities', array( 'post', 'services', 'reviews', 'faqs', 'pricing', 'portfolio', 'before_after' ), array(
			'labels'            => array(
				'name'          => 'المدن',
				'singular_name' => 'مدينة',
				'menu_name'     => 'المدن',
				'all_items'     => 'كل المدن',
				'edit_item'     => 'تعديل المدينة',
				'add_new_item'  => 'إضافة مدينة',
				'search_items'  => 'بحث في المدن',
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'city', 'with_front' => false, 'hierarchical' => true ),
		) );

		# تصنيفات الخدمات — للخدمات وسابقة الأعمال فقط
		register_taxonomy( 'service_categories', array( 'services', 'portfolio' ), array(
			'labels'            => array(
				'name'          => 'تصنيفات الخدمات',
				'singular_name' => 'تصنيف خدمة',
				'menu_name'     => 'تصنيفات الخدمات',
				'all_items'     => 'كل التصنيفات',
				'edit_item'     => 'تعديل التصنيف',
				'add_new_item'  => 'إضافة تصنيف',
				'search_items'  => 'بحث في التصنيفات',
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'service-category', 'with_front' => false, 'hierarchical' => true ),
		) );
	}
	add_action( 'init', 'kayan_cpt_register', 5 );

	# ═══ إعادة توليد روابط دائمة مرة واحدة بعد التفعيل ═══
	function kayan_cpt_flush() {
		kayan_cpt_register();
		flush_rewrite_rules();
	}
	add_action( 'after_switch_theme', 'kayan_cpt_flush' );

	# علم يضمن flush تلقائي مرة واحدة عند أول تحميل بعد تحديث القالب
	function kayan_cpt_maybe_flush() {
		if ( get_option( 'kayan_cpt_flushed_v130' ) !== 'yes' ) {
			flush_rewrite_rules();
			update_option( 'kayan_cpt_flushed_v130', 'yes' );
		}
	}
	add_action( 'init', 'kayan_cpt_maybe_flush', 99 );
}
