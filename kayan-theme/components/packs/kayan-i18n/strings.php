<?php 
if ( ! function_exists( 'kayan_i18n_ui_strings' ) ) {
	function kayan_i18n_ui_strings() {
		return array(
			'btn_whatsapp'       => array( 'ar' => 'واتساب', 'en' => 'WhatsApp' ),
			'btn_whatsapp_full'  => array( 'ar' => 'تواصل عبر واتساب', 'en' => 'Chat on WhatsApp' ),
			'btn_call'           => array( 'ar' => 'اتصل الآن', 'en' => 'Call now' ),
			'btn_quote'          => array( 'ar' => 'طلب عرض سعر', 'en' => 'Get a quote' ),
			'btn_service'        => array( 'ar' => 'طلب خدمة', 'en' => 'Request service' ),
			'btn_call_short'     => array( 'ar' => 'اتصال', 'en' => 'Call' ),
			'nav_services'       => array( 'ar' => 'الخدمات', 'en' => 'Services' ),
			'nav_cities'         => array( 'ar' => 'المدن', 'en' => 'Areas' ),
			'nav_projects'       => array( 'ar' => 'المشاريع', 'en' => 'Projects' ),
			'nav_blog'           => array( 'ar' => 'المدونة', 'en' => 'Blog' ),
			'nav_about'          => array( 'ar' => 'من نحن', 'en' => 'About' ),
			'nav_faq'            => array( 'ar' => 'الأسئلة الشائعة', 'en' => 'FAQ' ),
			'menu_open'          => array( 'ar' => 'القائمة', 'en' => 'Menu' ),
			'menu_close'         => array( 'ar' => 'إغلاق', 'en' => 'Close' ),
			'read_article'       => array( 'ar' => 'اقرأ المقال', 'en' => 'Read article' ),
			'switcher_label'     => array( 'ar' => 'تبديل الدولة واللغة', 'en' => 'Switch country and language' ),
			'section_country'    => array( 'ar' => 'الدولة', 'en' => 'Country' ),
			'section_language'   => array( 'ar' => 'اللغة', 'en' => 'Language' ),
			'lang_ar'            => array( 'ar' => 'العربية', 'en' => 'Arabic' ),
			'lang_en'            => array( 'ar' => 'English', 'en' => 'English' ),
		);
	}
}

if ( ! function_exists( 'kayan_i18n_t' ) ) {
	function kayan_i18n_t( $key, $fallback = '' ) {
		$strings = kayan_i18n_ui_strings();
		if ( ! isset( $strings[ $key ] ) ) {
			return $fallback;
		}
		$lang = function_exists( 'kayan_i18n_get_lang' ) ? kayan_i18n_get_lang() : 'ar';
		if ( isset( $strings[ $key ][ $lang ] ) ) {
			return $strings[ $key ][ $lang ];
		}
		return $fallback;
	}
}
