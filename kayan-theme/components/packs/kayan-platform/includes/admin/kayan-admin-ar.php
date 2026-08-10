<?php
/**
 * Arabic UI layer for KAYAN Admin Platform (Arabic-first theme).
 *
 * Translates module nav labels + gettext strings on the kayan text domain
 * so Platform screens match the rest of the theme (YTS / CPT Arabic UI).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Module id → Arabic label.
 *
 * @return array<string,string>
 */
function kayan_admin_module_labels_ar() {
	return array(
		'dashboard'      => 'لوحة التحكم',
		'settings'       => 'الإعدادات',
		'countries'      => 'الدول',
		'languages'      => 'اللغات',
		'entities'       => 'الكيانات',
		'relationships'  => 'العلاقات',
		'templates'      => 'القوالب',
		'blueprints'     => 'المخططات',
		'blocks'         => 'البلوكات',
		'pseo'           => 'SEO البرمجي',
		'programmatic_seo' => 'SEO البرمجي',
		'ai'             => 'الذكاء الاصطناعي',
		'media'          => 'الوسائط',
		'queue'          => 'قائمة المهام',
		'logs'           => 'السجلات',
		'analytics'      => 'التحليلات',
		'performance'    => 'الأداء',
		'security'       => 'الأمان',
		'import'         => 'استيراد',
		'export'         => 'تصدير',
		'tools'          => 'أدوات',
		'system_health'  => 'صحة النظام',
		'rankmath'       => 'تكامل Rank Math',
		'permissions'    => 'الصلاحيات',
		'seo'            => 'تحسين الظهور',
	);
}

/**
 * Common English → Arabic map for __('…', 'kayan').
 *
 * @return array<string,string>
 */
function kayan_admin_gettext_map_ar() {
	return array(
		'KAYAN Platform' => 'منصة كيان',
		'KAYAN' => 'كيان',
		'KAYAN modules' => 'وحدات كيان',
		'Dashboard' => 'لوحة التحكم',
		'Dashboard foundation is loading.' => 'جارٍ تحميل لوحة التحكم.',
		'Settings' => 'الإعدادات',
		'Countries' => 'الدول',
		'Languages' => 'اللغات',
		'Entities' => 'الكيانات',
		'Relationships' => 'العلاقات',
		'Templates' => 'القوالب',
		'Blueprints' => 'المخططات',
		'Blocks' => 'البلوكات',
		'Programmatic SEO' => 'SEO البرمجي',
		'AI' => 'الذكاء الاصطناعي',
		'Media' => 'الوسائط',
		'Queue' => 'قائمة المهام',
		'Logs' => 'السجلات',
		'Recent Logs' => 'أحدث السجلات',
		'Analytics' => 'التحليلات',
		'Performance' => 'الأداء',
		'Security' => 'الأمان',
		'Import' => 'استيراد',
		'Export' => 'تصدير',
		'Import / Export' => 'استيراد / تصدير',
		'Tools' => 'أدوات',
		'System Health' => 'صحة النظام',
		'Rank Math Integration' => 'تكامل Rank Math',
		'Rank Math' => 'Rank Math',
		'Permissions' => 'الصلاحيات',
		'Saved successfully.' => 'تم الحفظ بنجاح.',
		'Something went wrong.' => 'حدث خطأ ما.',
		'Removed.' => 'تم الحذف.',
		'Import completed.' => 'اكتمل الاستيراد.',
		'Cleared.' => 'تم المسح.',
		'Module not found.' => 'الوحدة غير موجودة.',
		'Module registered. Screen callback not set.' => 'الوحدة مسجّلة. لم تُضبط واجهة العرض بعد.',
		'You do not have permission to access the KAYAN Admin Platform.' => 'ليس لديك صلاحية الوصول إلى منصة كيان.',
		'You do not have permission to access this module.' => 'ليس لديك صلاحية الوصول إلى هذه الوحدة.',
		'Widget slot ready. Statistics will arrive in a later phase.' => 'الخانة جاهزة. ستظهر الإحصائيات التفصيلية لاحقاً.',
		'KAYAN Admin Dashboard foundation is active. Widgets are registered as slots — no statistics in Phase 3.0.' => 'لوحة منصة كيان جاهزة. الإحصائيات الأساسية تظهر أدناه.',
		'No items found.' => 'لا توجد عناصر.',
		'Save' => 'حفظ',
		'Filter' => 'تصفية',
		'Bulk actions' => 'إجراءات جماعية',
		'Apply' => 'تطبيق',
		'Search…' => 'بحث…',
		'Default' => 'افتراضي',
		'Default country' => 'الدولة الافتراضية',
		'Phone' => 'الهاتف',
		'WhatsApp' => 'واتساب',
		'Email' => 'البريد',
		'Business name' => 'اسم النشاط',
		'Business address' => 'عنوان النشاط',
		'Currency' => 'العملة',
		'Homepage SEO title' => 'عنوان SEO للرئيسية',
		'Homepage SEO description' => 'وصف SEO للرئيسية',
		'Code' => 'الرمز',
		'Label' => 'الاسم',
		'URL path' => 'مسار الرابط',
		'Actions' => 'إجراءات',
		'Edit profile' => 'تعديل الملف',
		'Active — SEO Bridge extending it' => 'نشط — جسر SEO يعمل عليه',
		'Not detected on this site' => 'غير مكتشف في هذا الموقع',
		'registered countries' => 'دول مسجّلة',
		'registered languages' => 'لغات مسجّلة',
		'generation rules' => 'قواعد توليد',
		'running' => 'قيد التشغيل',
		'queued' => 'في الانتظار',
		'conversions (7d)' => 'تحويلات (7 أيام)',
		'None configured' => 'غير مهيأ',
		'Optimizations active' => 'التحسينات مفعّلة',
		'Disabled' => 'معطّل',
		'LCP preload' => 'تحميل مسبق لـ LCP',
		'Resource hints' => 'تلميحات الموارد',
		'KAYAN Track is disabled.' => 'نظام التتبع معطّل.',
		'Track helpers unavailable.' => 'مساعدات التتبع غير متاحة.',
		'AI Platform not available.' => 'منصة الذكاء الاصطناعي غير متاحة.',
		'Programmatic SEO not available.' => 'SEO البرمجي غير متاح.',
		'Queue not available.' => 'قائمة المهام غير متاحة.',
		'No log entries yet.' => 'لا توجد سجلات بعد.',
		'Configured' => 'مهيأ',
		'Not configured' => 'غير مهيأ',
		'Default provider' => 'المزوّد الافتراضي',
		'API key' => 'مفتاح API',
		'Model (optional)' => 'النموذج (اختياري)',
		'Platform settings' => 'إعدادات المنصة',
		'URL routing mode' => 'نمط توجيه الروابط',
		'Language-first (/en/sa/…) — recommended' => 'اللغة أولاً (/en/sa/…) — مُفضّل',
		'Legacy' => 'الوضع القديم',
		'Cache default TTL (seconds)' => 'مدة الكاش الافتراضية (ثوانٍ)',
		'Logger ring-buffer size' => 'حجم سجل الأحداث',
		'Logger' => 'السجل',
		'Enable platform logging.' => 'تفعيل تسجيل أحداث المنصة.',
		'Yes' => 'نعم',
		'Protected' => 'محمي',
		'Bridged' => 'مربوط',
		'Purpose' => 'الغرض',
		'Channel' => 'القناة',
		'Level' => 'المستوى',
		'User' => 'المستخدم',
		'Language code' => 'رمز اللغة',
		'Label (English)' => 'الاسم (إنجليزي)',
		'Label (Arabic)' => 'الاسم (عربي)',
		'Direction' => 'الاتجاه',
		'Entity type' => 'نوع الكيان',
		'Ref (ID / slug / country code)' => 'المرجع (معرف / رابط / رمز دولة)',
		'From type' => 'من نوع',
		'From ref' => 'من مرجع',
		'To type (optional)' => 'إلى نوع (اختياري)',
		'Dedicated WP role' => 'دور ووردبريس مخصص',
		'Routing ownership' => 'ملكية التوجيه',
		'Permalinks' => 'الروابط الدائمة',
		'Cache Engine' => 'محرك الكاش',
		'Locale registry' => 'سجل اللغات/الدول',
		'Theme Integration adapters' => 'محوّلات تكامل القالب',
		'Legacy city taxonomy' => 'تصنيف المدن القديم',
		'PHP version' => 'إصدار PHP',
		'WordPress version' => 'إصدار ووردبريس',
		'Schema migrations' => 'ترحيلات قاعدة البيانات',
		'PSEO queue' => 'طابور SEO البرمجي',
		'AI Platform' => 'منصة الذكاء الاصطناعي',
		'ready' => 'جاهز',
		'Ready' => 'جاهز',
		'Platform-level behavior — not a replacement for Theme Options.' => 'سلوك على مستوى المنصة — ليس بديلاً عن إعدادات القالب.',
		'These are platform-level settings. Contact details, currency, and theme appearance stay in Theme Options (YTS) — use the Countries module for per-country business profile.' => 'هذه إعدادات على مستوى المنصة. بيانات الاتصال والعملة ومظهر القالب تبقى في إعدادات القالب — استخدم وحدة الدول لملف كل دولة.',
		'Manage per-country business profile: phone, WhatsApp, currency, SEO defaults.' => 'إدارة ملف كل دولة: الهاتف، واتساب، العملة، إعدادات SEO.',
		'Countries come from the existing kayan-i18n registry. This screen edits the platform business profile only (phone, currency, SEO defaults) — it does not create or remove countries.' => 'الدول تأتي من سجل kayan-i18n. هذه الشاشة تعدّل ملف العمل فقط (هاتف، عملة، SEO) دون إضافة أو حذف دول.',
		'Falls back to Theme Options phonenumber when empty.' => 'إن تُرك فارغاً يُستخدم رقم إعدادات القالب.',
		'Used by the Booking adapter when set.' => 'يُستخدم في نظام الحجز عند التعيين.',
		'Interchangeable AI providers for PSEO block regeneration and translation.' => 'مزودو ذكاء اصطناعي قابلة للتبديل لتوليد وترجمة محتوى SEO البرمجي.',
		'%s module is registered in the Admin Platform. Feature UI arrives in a later phase.' => 'وحدة %s مسجّلة في منصة كيان. واجهة الميزات الكاملة ستصل لاحقاً.',
		'Admin dashboard foundation.' => 'أساس لوحة التحكم الإدارية.',
		'No statistics in Phase 3.0.' => 'الإحصائيات الأساسية متاحة أدناه.',
		'Future SEO overview widget.' => 'نظرة عامة على تحسين الظهور.',
		'Future countries overview widget.' => 'نظرة عامة على الدول.',
		'Future languages overview widget.' => 'نظرة عامة على اللغات.',
		'Future PSEO overview widget.' => 'نظرة عامة على SEO البرمجي.',
		'Future job queue widget.' => 'نظرة عامة على قائمة المهام.',
		'Future AI status widget.' => 'حالة الذكاء الاصطناعي.',
		'Future performance widget.' => 'نظرة عامة على الأداء.',
		'Future analytics widget.' => 'نظرة عامة على التحليلات.',
		'Future Rank Math integration widget.' => 'تكامل Rank Math.',
		'Future logs overview widget.' => 'نظرة عامة على السجلات.',
	);
}

/**
 * Force Arabic module labels in nav / headers.
 *
 * @param array<string,array<string,mixed>> $modules Modules.
 * @return array<string,array<string,mixed>>
 */
function kayan_admin_filter_modules_ar( $modules ) {
	$map = kayan_admin_module_labels_ar();
	foreach ( $modules as $id => $module ) {
		if ( isset( $map[ $id ] ) ) {
			$modules[ $id ]['label'] = $map[ $id ];
		}
	}
	return $modules;
}
add_filter( 'kayan_admin_modules', 'kayan_admin_filter_modules_ar', 1000 );

/**
 * Translate kayan-domain gettext to Arabic (theme is Arabic-first).
 *
 * @param string $translation Translation.
 * @param string $text        Original.
 * @param string $domain      Domain.
 * @return string
 */
function kayan_admin_gettext_ar( $translation, $text, $domain ) {
	if ( 'kayan' !== $domain ) {
		return $translation;
	}
	$map = kayan_admin_gettext_map_ar();
	return isset( $map[ $text ] ) ? $map[ $text ] : $translation;
}
add_filter( 'gettext', 'kayan_admin_gettext_ar', 20, 3 );

/**
 * @param string $translation Translation.
 * @param string $single      Single.
 * @param string $plural      Plural.
 * @param int    $number      Number.
 * @param string $domain      Domain.
 * @return string
 */
function kayan_admin_ngettext_ar( $translation, $single, $plural, $number, $domain ) {
	if ( 'kayan' !== $domain ) {
		return $translation;
	}
	$map  = kayan_admin_gettext_map_ar();
	$text = ( 1 === (int) $number ) ? $single : $plural;
	return isset( $map[ $text ] ) ? $map[ $text ] : $translation;
}
add_filter( 'ngettext', 'kayan_admin_ngettext_ar', 20, 5 );

/**
 * sprintf-style strings with Arabic templates.
 *
 * @param string $translation Translation.
 * @param string $text        Original.
 * @param string $domain      Domain.
 * @return string
 */
function kayan_admin_gettext_sprintf_ar( $translation, $text, $domain ) {
	if ( 'kayan' !== $domain ) {
		return $translation;
	}
	$extra = array(
		'Default: %s'                 => 'الافتراضي: %s',
		'%d disabled'                 => '%d معطّلة',
		'%1$d calls · %2$d WhatsApp'  => '%1$d اتصال · %2$d واتساب',
	);
	return isset( $extra[ $text ] ) ? $extra[ $text ] : $translation;
}
add_filter( 'gettext', 'kayan_admin_gettext_sprintf_ar', 21, 3 );
