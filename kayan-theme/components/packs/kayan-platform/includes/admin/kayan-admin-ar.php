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
		'KAYAN Track (existing)' => 'تتبع كيان (الحالي)',
		'Tracking remains in KAYAN Track. Admin Platform analytics module is a bridge only.' => 'التتبع يبقى في تتبع كيان. وحدة التحليلات في المنصة جسر فقط.',
		'Theme Options (existing)' => 'إعدادات القالب (الحالية)',
		'Use existing Theme Options (YTS) for theme settings.' => 'استخدم إعدادات القالب الحالية لإعدادات المظهر.',
		'Theme Options (YTS)' => 'إعدادات القالب',
		'Open Theme Options' => 'فتح إعدادات القالب',
		'Open KAYAN Track' => 'فتح تتبع كيان',
		'%d items' => '%d عناصر',
		'%d legacy term(s) remaining.' => 'يتبقى %d مصطلحاً قديماً.',
		'%d posts carry a PSEO blueprint.' => '%d مقالاً يحمل مخطط SEO برمجي.',
		'Active' => 'نشط',
		'Adapters connecting existing theme packs to the platform.' => 'محوّلات تربط حزم القالب الحالية بالمنصة.',
		'Add language' => 'إضافة لغة',
		'AI content' => 'محتوى بالذكاء الاصطناعي',
		'AI content generation arrives in Phase 5.' => 'توليد المحتوى بالذكاء الاصطناعي متاح ضمن المنصة.',
		'AI enabled' => 'تفعيل الذكاء الاصطناعي',
		'AI-authored block content arrives in Phase 5. The Generator can already refresh derived fields (contact info, related entities) via Regenerate.' => 'يمكن تحديث الحقول المشتقة (الاتصال والكيانات المرتبطة) عبر إعادة التوليد.',
		'All channels' => 'كل القنوات',
		'All levels' => 'كل المستويات',
		'Allowed relationship matrix + related-entity browser.' => 'مصفوفة العلاقات المسموحة ومتصفح الكيانات المرتبطة.',
		'Allowed to types' => 'الأنواع المسموحة',
		'Application code never talks to a provider directly — PSEO block regeneration and translation always go through this registry, so switching providers here never requires a code change.' => 'الكود لا يتصل بالمزوّد مباشرة — إعادة التوليد والترجمة تمر عبر هذا السجل، لذا تبديل المزوّد لا يتطلب تعديل كود.',
		'Approve' => 'اعتماد',
		'Arabic (default) and English are built in. Register additional languages without editing code.' => 'العربية (الافتراضية) والإنجليزية مدمجتان. سجّل لغات إضافية دون تعديل الكود.',
		'Arabic is the default and cannot be disabled. English is built in. Additional languages register through the existing kayan_platform_languages filter.' => 'العربية افتراضية ولا يمكن تعطيلها. الإنجليزية مدمجة. اللغات الإضافية عبر مرشح kayan_platform_languages.',
		'Archive' => 'أرشفة',
		'Assign a KAYAN role to a user' => 'تعيين دور كيان لمستخدم',
		'Assign role' => 'تعيين الدور',
		'Assignable page structures composed of blocks.' => 'هياكل صفحات قابلة للتعيين مكوّنة من بلوكات.',
		'Auto (first available)' => 'تلقائي (أول متاح)',
		'Back to countries' => 'العودة إلى الدول',
		'Backup or transfer platform-owned settings (not Theme Options).' => 'نسخ أو نقل إعدادات المنصة (وليس إعدادات القالب).',
		'Block' => 'بلوك',
		'Block count' => 'عدد البلوكات',
		'Block order: %s' => 'ترتيب البلوكات: %s',
		'Blueprint v' => 'مخطط ن',
		'Browse related' => 'استعراض المرتبط',
		'Built-in' => 'مدمج',
		'Bulk Generate' => 'توليد جماعي',
		'Bulk generation and regeneration jobs.' => 'مهام التوليد وإعادة التوليد الجماعي.',
		'Cancel' => 'إلغاء',
		'Capabilities' => 'الصلاحيات',
		'Check' => 'فحص',
		'Cities (slugs, empty = all)' => 'المدن (اختصارات، فارغ = الكل)',
		'Clear all logs' => 'مسح كل السجلات',
		'Clear all logs?' => 'مسح كل السجلات؟',
		'Combinations' => 'التوليفات',
		'Context' => 'السياق',
		'Count' => 'العدد',
		'Countries (comma-separated, empty = default)' => 'الدول (مفصولة بفواصل، فارغ = الافتراضي)',
		'Countries / Languages registered (built-in + custom).' => 'الدول / اللغات المسجّلة (مدمجة + مخصصة).',
		'Country' => 'الدولة',
		'Country Router + Content Resolver own rewrites. kayan-i18n rewrites are skipped.' => 'موجّه الدول ومحلّل المحتوى يملكان إعادة الكتابة. يُتخطى kayan-i18n.',
		'Create a rule' => 'إنشاء قاعدة',
		'Created/Updated/Skipped/Failed' => 'أُنشئ / حُدّث / تُخطّي / فشل',
		'Current WordPress core version.' => 'إصدار ووردبريس الحالي.',
		'Custom' => 'مخصص',
		'Data source' => 'مصدر البيانات',
		'Delete' => 'حذف',
		'Details' => 'التفاصيل',
		'Disable' => 'تعطيل',
		'Download export (.json)' => 'تنزيل التصدير (.json)',
		'Draft' => 'مسودة',
		'Edit' => 'تعديل',
		'Edit rule' => 'تعديل القاعدة',
		'Enable' => 'تفعيل',
		'Enabled' => 'مفعّل',
		'Engine contract' => 'عقد المحرك',
		'Entity inspector' => 'فاحص الكيان',
		'Export/import covers global platform settings, country profiles, and custom languages only. Theme Options (YTS), booking, payment, and tracking data are never touched.' => 'الاستيراد/التصدير يشمل إعدادات المنصة وملفات الدول واللغات المخصصة فقط. إعدادات القالب والحجز والدفع والتتبع لا تُمس.',
		'Fields' => 'الحقول',
		'Generate all matching combinations now?' => 'توليد كل التوليفات المطابقة الآن؟',
		'Generate as' => 'التوليد كـ',
		'Generated content' => 'المحتوى المُولَّد',
		'Generation rules, preview, bulk generation, and regeneration.' => 'قواعد التوليد والمعاينة والتوليد الجماعي وإعادة التوليد.',
		'GTM ID' => 'معرّف GTM',
		'hreflang' => 'hreflang',
		'hreflang alternates (legacy filter name)' => 'بدائل hreflang (اسم المرشح القديم)',
		'hreflang alternates (platform-built)' => 'بدائل hreflang (من المنصة)',
		'Import file' => 'ملف الاستيراد',
		'Inspect registered entity types and resolve a single entity.' => 'فحص أنواع الكيانات المسجّلة وحل كيان واحد.',
		'Job' => 'مهمة',
		'KAYAN roles, capabilities, and WordPress user assignment.' => 'أدوار كيان والصلاحيات وتعيين مستخدمي ووردبريس.',
		'kayan-i18n owns routing (filtered off). Confirm this is intentional.' => 'kayan-i18n يملك التوجيه (مُصفّى). تأكد أن هذا مقصود.',
		'Kind' => 'النوع',
		'Language-first canonical rewrite' => 'إعادة كتابة أساسية (اللغة أولاً)',
		'Languages (comma-separated)' => 'اللغات (مفصولة بفواصل)',
		'Legacy i18n' => 'i18n القديم',
		'Locale SEO description override' => 'تجاوز وصف SEO للمحلية',
		'Locale SEO title override' => 'تجاوز عنوان SEO للمحلية',
		'Locked blocks' => 'بلوكات مقفلة',
		'Message' => 'الرسالة',
		'Minimum supported: PHP 7.4.' => 'الحد الأدنى المدعوم: PHP 7.4.',
		'No cache driver available.' => 'لا يتوفر محرك كاش.',
		'No entity resolved for that type/ref.' => 'لم يُعثر على كيان لهذا النوع/المرجع.',
		'No generated pages yet. Use the Programmatic SEO module to create a rule and generate content.' => 'لا صفحات مولَّدة بعد. استخدم وحدة SEO البرمجي لإنشاء قاعدة وتوليد محتوى.',
		'No jobs yet. Bulk Generate from the Programmatic SEO module to enqueue one.' => 'لا مهام بعد. استخدم التوليد الجماعي من وحدة SEO البرمجي.',
		'No provider configured' => 'لا مزوّد مهيأ',
		'No related entities found.' => 'لا كيانات مرتبطة.',
		'No rules yet — create one below.' => 'لا قواعد بعد — أنشئ واحدة أدناه.',
		'None' => 'لا شيء',
		'Not active' => 'غير نشط',
		'Not detected' => 'غير مكتشف',
		'Not set' => 'غير معيّن',
		'OG locale from platform language' => 'لغة OG من لغة المنصة',
		'Open Rank Math' => 'فتح Rank Math',
		'Output status' => 'حالة الإخراج',
		'Pattern' => 'النمط',
		'Pending migrations will run automatically on the next request.' => 'الترحيلات المعلّقة ستُنفَّذ تلقائياً في الطلب التالي.',
		'Phase 5' => 'المرحلة 5',
		'Plain' => 'عادي',
		'Plain permalinks break language-first routing. Set a pretty permalink structure.' => 'الروابط العادية تعطّل توجيه اللغة أولاً. فعّل بنية روابط جميلة.',
		'Platform' => 'المنصة',
		'Post type' => 'نوع المحتوى',
		'Preferred for patterns' => 'مفضّل للأنماط',
		'Pretty' => 'جميلة',
		'Preview' => 'معاينة',
		'Process queue now' => 'معالجة الطابور الآن',
		'Progress' => 'التقدم',
		'Provider' => 'المزوّد',
		'Provider configuration' => 'إعدادات المزوّد',
		'Providers are interchangeable — configure one in the AI module.' => 'المزوّدون قابلون للتبديل — هيّئ واحداً في وحدة الذكاء الاصطناعي.',
		'Publish' => 'نشر',
		'Quality' => 'الجودة',
		'Quality report' => 'تقرير الجودة',
		'Quality report — post #%d' => 'تقرير الجودة — المقال #%d',
		'Queued / failed jobs. The Scheduler processes the queue automatically.' => 'مهام معلّقة / فاشلة. المعالِج يُشغّل الطابور تلقائياً.',
		'Queued at' => 'أُضيفت في',
		'Rank Math filter' => 'مرشح Rank Math',
		'Rank Math is active. KAYAN extends it via filters — never competing head tags.' => 'Rank Math نشط. كيان يمدّه عبر المرشحات دون تكرار وسوم الرأس.',
		'Rank Math is not detected on this site. Activate it to enable full SEO Bridge extensions.' => 'Rank Math غير مكتشف. فعّله لتمكين امتدادات جسر SEO.',
		'Rank Math is not detected. Theme schema pack runs unmodified until Rank Math is activated.' => 'Rank Math غير مكتشف. حزمة Schema للقالب تعمل كما هي حتى تفعيله.',
		'Rank Math is the active SEO engine. Theme schema is silenced by the schema adapter.' => 'Rank Math هو محرك SEO النشط. Schema القالب مُعطّل عبر المحوّل.',
		'Rank Math status' => 'حالة Rank Math',
		'Read by kayan_platform_url_mode filter consumers.' => 'يُقرأ عبر مستهلكي مرشح kayan_platform_url_mode.',
		'Recent platform log entries by channel and level.' => 'أحدث سجلات المنصة حسب القناة والمستوى.',
		'Regenerate' => 'إعادة التوليد',
		'Register a new language' => 'تسجيل لغة جديدة',
		'Registered capabilities' => 'الصلاحيات المسجّلة',
		'Registration contract' => 'عقد التسجيل',
		'Related entity browser' => 'متصفح الكيانات المرتبطة',
		'Remove' => 'إزالة',
		'Reset to Draft' => 'إعادة إلى مسودة',
		'Resolve entity' => 'حل الكيان',
		'Restore' => 'استعادة',
		'Retry' => 'إعادة المحاولة',
		'Reusable, independently regeneratable page blocks.' => 'بلوكات صفحات قابلة لإعادة الاستخدام والتوليد المستقل.',
		'Ring buffer capacity: %d entries.' => 'سعة المخزن الدائري: %d سجل.',
		'Role' => 'الدور',
		'Routing, SEO, cache, and integration status checks.' => 'فحوصات التوجيه وSEO والكاش والتكامل.',
		'Rule' => 'قاعدة',
		'Rule label' => 'اسم القاعدة',
		'Runs automatically every ~5 minutes via WP-Cron, plus a light admin fallback. No manual step is required.' => 'يعمل تلقائياً كل ~5 دقائق عبر WP-Cron مع احتياطي خفيف في لوحة التحكم. لا خطوة يدوية مطلوبة.',
		'Save AI settings' => 'حفظ إعدادات الذكاء الاصطناعي',
		'Save country profile' => 'حفظ ملف الدولة',
		'Save rule' => 'حفظ القاعدة',
		'Save settings' => 'حفظ الإعدادات',
		'Scheduled' => 'مجدول',
		'Scheduler' => 'المجدول',
		'Send to Review' => 'إرسال للمراجعة',
		'Services (slugs, empty = all)' => 'الخدمات (اختصارات، فارغ = الكل)',
		'Source' => 'المصدر',
		'Status' => 'الحالة',
		'Status of the SEO Bridge extending Rank Math (the only SEO engine).' => 'حالة جسر SEO الممتد على Rank Math (محرك SEO الوحيد).',
		'Storage meta key: %1$s. Bidirectional relations: %2$s.' => 'مفتاح الميتا: %1$s. علاقات ثنائية الاتجاه: %2$s.',
		'Template' => 'القالب',
		'Theme schema pack' => 'حزمة Schema للقالب',
		'This module is registered. Implementation arrives in a later phase.' => 'الوحدة مسجّلة. الواجهة الكاملة ستتوفر لاحقاً.',
		'This page is approved/published — regenerate its derived content anyway?' => 'هذه الصفحة معتمدة/منشورة — إعادة توليد المحتوى المشتق على أي حال؟',
		'Time (UTC)' => 'الوقت (UTC)',
		'Title' => 'العنوان',
		'Translate' => 'ترجمة',
		'Type' => 'النوع',
		'Unknown' => 'غير معروف',
		'Up to date. Migrations run automatically — no manual step required.' => 'محدّث. الترحيلات تعمل تلقائياً — لا خطوة يدوية.',
		'Using %s driver.' => 'باستخدام محرك %s.',
		'Version' => 'الإصدار',
		'Versioned, block-based page contracts, workflow state, and quality score for generated content.' => 'عقود صفحات بإصدارات وبلوكات، وحالة سير العمل، ودرجة الجودة للمحتوى المولَّد.',
		'View in Blueprints' => 'عرض في المخططات',
		'WordPress mapping' => 'ربط ووردبريس',
		'Workflow' => 'سير العمل',
		'— Select role —' => '— اختر دوراً —',
		'— Select user —' => '— اختر مستخدماً —',
		'•••••••• (leave blank to keep current key)' => '•••••••• (اتركه فارغاً للإبقاء على المفتاح الحالي)',
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
		'%d items' => '%d عناصر',
		'%d disabled' => '%d معطّلة',
		'Default: %s' => 'الافتراضي: %s',
		'Block order: %s' => 'ترتيب البلوكات: %s',
		'Quality report — post #%d' => 'تقرير الجودة — المقال #%d',
		'Ring buffer capacity: %d entries.' => 'سعة المخزن الدائري: %d سجل.',
		'Storage meta key: %1$s. Bidirectional relations: %2$s.' => 'مفتاح الميتا: %1$s. علاقات ثنائية الاتجاه: %2$s.',
		'Using %s driver.' => 'باستخدام محرك %s.',
		'%d posts carry a PSEO blueprint.' => '%d مقالاً يحمل مخطط SEO برمجي.',
		'%d legacy term(s) remaining.' => 'يتبقى %d مصطلحاً قديماً.',
	);
	return isset( $extra[ $text ] ) ? $extra[ $text ] : $translation;
}
add_filter( 'gettext', 'kayan_admin_gettext_sprintf_ar', 21, 3 );
