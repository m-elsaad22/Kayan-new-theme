# تحليل شامل: لماذا لا يعمل قالب KAYAN

**المصدر:** `kayan-theme-phase1-2-booking-payment.zip`  
**القالب بعد الفك:** `kayan-theme/kayan-theme/`  
**الإصدار المعلن:** 1.4.2 (Phase 1 Booking + Phase 2 Payment)  
**نوع المشروع:** قالب ووردبريس مخصص (Blade/Packs) — ليس قالبًا تقليديًا بملفات `header.php`/`single.php` في الجذر  
**تاريخ الفحص:** 2026-07-29

---

## الخلاصة التنفيذية

القالب **لا يعمل على الواجهة الأمامية** بسبب خطأ PHP قاتل في ملف الهيدر يوقف تحميل أي صفحة. حتى بعد إصلاحه، نظام الحجز/الدفع لن يظهر ولن يعمل بالكامل بسبب تعارض مع نظام تعطيل الأصول، وشرط حلقة ووردبريس غير متحقّق في بنية القالب.

| الأولوية | المشكلة | الأثر |
|----------|---------|--------|
| P0 | Parse error في `#header/part.php` | شاشة بيضاء على كل الصفحات |
| P1 | `disable_all_scripts()` يلغي CSS/JS الحجز | معالج الحجز بلا أصول |
| P1 | `in_the_loop()` يمنع حقن نموذج الحجز | النموذج لا يظهر أصلًا |
| P2 | صلاحيات 770/660 داخل الـ ZIP | قد يمنع قراءة الملفات على بعض السيرفرات |
| P2 | رفع ناقص / كاش قديم | سلوك النسخ السابقة (تراكب/أزرار مكررة) |
| P3 | أخطاء AjaxCenter + `index.php` + Mega-Menu | أعطال جزئية بعد الإصلاح الأساسي |

---

## 1) ما هو هذا القالب؟

- **الاسم:** KAYAN Theme  
- **الغرض:** مواقع خدمات منزلية عربية (تصميم RUKN v3)  
- **البنية:** نظام Packs داخل `components/packs/` (~75 حزمة، ~304 ملف PHP)  
- **التوجيه:** `syntax.php` → `ThemeStatic::Locate()` على `template_redirect` ثم `die()` — يتجاوز قوالب ووردبريس التقليدية  
- **القوالب:** مجلدات `@index` / `@single` / `@page` / `@404` …  
- **الأجزاء:** مجلدات `#header` / `#footer` / `#fonts` …  
- **الإضافات الجديدة في هذا الـ ZIP:**
  - `kayan-booking` (Phase 1)
  - `kayan-payment` (Phase 2 — بوابة Demo)
  - جداول DB + Ajax endpoints + إعدادات Theme Options

الملفات الناقصة في الجذر (`header.php`, `single.php`, …) **ليست سبب العطل بذاتها** — هذا تصميم القالب. السبب الحقيقي أخطاء تشغيل داخل البنية المخصصة.

---

## 2) السبب الجذري (P0): خطأ Parse في الهيدر

### الملف
`components/packs/#header/part.php`

### ماذا يحدث؟
كل صفحة أمامية تستدعي `$this->Part('header')`. الملف فيه **قوس `}` زائد** بعد بلوك meta description الخاص بـ Rank Math:

```php
// السطر 10
if( !isset($_GET['ajax']) ) {
    echo '<!DOCTYPE html>';
    // ...
    if ( empty( $hide__description_show ) && ! class_exists( 'RankMath' ) ... ) {
        // ...
    }
        }   // ← السطر 26: يغلق شرط ajax مبكرًا بالخطأ

    // بقية <head> و <body> ...
}           // ← السطر 123: يصبح Unmatched '}'
```

### نتيجة الفحص
```text
PHP Parse error: Unmatched '}' in .../#header/part.php on line 123
```

هذا هو السبب المباشر لـ **الشاشة البيضاء (WSOD)** عند تفعيل القالب: PHP يرفض تحميل الهيدر، فتتوقف الصفحة فورًا.

### الإصلاح المطلوب
حذف القوس الزائد في السطر 26، والإبقاء على إغلاق شرط `!isset($_GET['ajax'])` عند السطر 123 فقط (بعد فتح `<body>`).

---

## 3) لماذا نظام الحجز/الدفع لا يعمل حتى بعد إصلاح الهيدر

### 3.1 حقن المعالج لا يحدث أبدًا (`in_the_loop`)

في `kayan-booking/setup.php`:

```php
if ( is_admin() || ! is_singular( array( 'post', 'services' ) )
  || ! in_the_loop() || ! is_main_query() ) {
    return $content;
}
```

قالب المقال `@single/post.php` يستدعي `the_content()` **خارج حلقة ووردبريس** — لا يوجد `the_post()` / `have_posts()` في مسار `Locate → Blade('single')`.

نتيجة البحث في القالب: `in_the_loop` يُستخدم فقط داخل حزمة الحجز، بينما `the_post()` يظهر في بعض الودجات فقط وليس في قالب المقال المفرد.

**النتيجة:** فلتر `the_content` يخرج مبكرًا → لا يُحقن HTML المعالج → يظهر المقال بدون نظام حجز.

### 3.2 حتى لو حُقن المعالج: CSS/JS يُحذفان

في `Enqueues/setup.php` الدالة `disable_all_scripts()` مربوطة بـ:

```php
add_action('wp_enqueue_scripts', 'disable_all_scripts', 999999);
```

وتقوم بـ `wp_dequeue_*` + `wp_deregister_*` لكل شيء ما عدا handles تبدأ بـ `rank-math`.

بينما الحجز يسجّل أصوله بالطريقة القياسية:

```php
wp_enqueue_style( 'kayan-booking', ... );
wp_enqueue_script( 'kayan-booking', ... );
wp_localize_script( 'kayan-booking', 'KayanBookingConfig', ... );
```

**النتيجة:** ملفات `kayan-booking.css` / `kayan-booking.js` و`KayanBookingConfig` لا تصل للمتصفح. نفس المصير لـ `kayan-ui` و`kayan-i18n` و`kayan-track`.

القالب الأصلي يتجاوز ذلك بتحميل CSS عبر `require` داخل `<style>` في الهيدر، وjQuery عبر `wp_footer` — لكن حزمة الحجز الجديدة لم تُدمَج في هذا المسار.

### 3.3 شروط ظهور الحجز (منطق المنتج)

حتى بعد إصلاح الكود، المعالج لن يظهر إلا إذا:

1. الصفحة من نوع `post` أو `services`
2. المقال ليس عليه `hide__kayan_booking = on`
3. للمقالات: توجد خدمات (`services`) منشورة و`linked_categories` يطابق تصنيف المقال
4. إعادة حفظ الروابط الدائمة لتفعيل endpoint: `/AjaxCenter/...`
5. جداول DB موجودة (`wp_kayan_bookings` وغيرها) — تُنشأ عند التفعيل/`init`

الدفع Demo يعتمد على نجاح الحجز أولًا ثم خطوات Ajax منفصلة.

---

## 4) مشاكل إضافية مؤكدة

### 4.1 صلاحيات الملفات داخل الـ ZIP
معظم المجلدات `770` والملفات `660` (بدون قراءة لـ others). على استضافات يكون فيها مستخدم PHP مختلفًا عن مستخدم الرفع، قد يفشل قراءة القالب أو الأصول → ثيم لا يظهر أو أصول 403.

**التوصية عند الرفع:** تأكد أن المجلدات `755` والملفات `644`.

### 4.2 `index.php` يستدعي دالة غير موجودة
```php
$ThemeTree->TemplatePart('home'); // لا توجد Method اسمها TemplatePart
```
عادة لا يُستدعى لأن `Locate()` يعمل `die()` قبل الوصول إليه، لكنه قنبلة موقوتة إن فشل الاعتراض.

### 4.3 خطأ Parse ثانٍ (أقل خطورة)
`components/packs/@Mega-Menu/Taxonomy-posts.php` — `Unclosed '{'` (بنية if/elseif مكسورة). يؤثر على قائمة الميجا وليس على كل الصفحات.

### 4.4 كسر جزئي لـ AjaxCenter بعد حماية LFI
`sanitize_key()` يحوّل أسماء مثل `TabsActions` → `tabsactions` بينما اسم الملف `TabsActions.php`. endpoints ذات أحرف كبيرة/شرطات قد تعيد 404. أسماء الحجز (`kayan_booking_*`) سليمة نسبيًا لأنها lowercase + underscore.

### 4.5 مسار Ajax في الفوتر بحروف صغيرة
```js
var AdminAjax = '.../ajaxcenter/';
```
بينما الـ rewrite endpoint معرّف كـ `AjaxCenter`. على سيرفرات حسّاسة لحالة الأحرف قد تفشل الطلبات. كود الحجز يستخدم `home_url('/AjaxCenter')` — أفضل، لكن يحتاج flush للـ permalinks.

### 4.6 الرئيسية الفارغة ليست «عطل كود» بالضرورة
`@index/shape.php` يعتمد على خيارات:
- `HomeIntro`
- `widgets_home__meta`

إن لم تُشغَّل أداة `kayan-seed` بعد التفعيل، الرئيسية قد تبدو فارغة (هيدر/فوتر فقط).

### 4.7 تعليمات الرفع في الملف المشفّر بالاسم
الملف:
`#U0643#U064a#U0641#U064a#U0629-#U0627#U0644#U0631#U0641#U0639-#U0627#U0644#U0635#U062d#U064a#U062d-#U0627#U0642#U0631#U0623#U0646#U064a.txt`
= **كيفية-الرفع-الصحيح-اقرأني.txt**

يؤكد أن مشاكل سابقة كانت من: رفع فوق نسخة قديمة + كاش LiteSpeed بدون حذف كامل للمجلد.

### 4.8 ملاحظة حول `syntax.php` (مقال بلا تصنيف)
```php
$category = get_the_terms($post->ID, 'category', '');
foreach( $category as $term ) { ... }
```
إن رجعت `false` يحدث Fatal. نادر لكنه حقيقي.

---

## 5) هيكل الـ ZIP وهل الرفع صحيح؟

```text
kayan-theme-phase1-2-booking-payment.zip
└── kayan-theme/          ← هذا المجلد يجب أن يصل إلى wp-content/themes/kayan-theme/
    ├── style.css         ← Theme Name: KAYAN Theme
    ├── functions.php
    ├── index.php
    ├── syntax.php
    ├── screenshot.png
    └── components/
```

- بنية الـ ZIP مناسبة لرفع ووردبريس (مجلد واحد فيه `style.css`).
- الخطأ الشائع: رفع المجلد الأب أو دمج ملفات فوق نسخة قديمة دون حذف.
- بعد أي رفع: احذف القالب القديم → ارفع كاملًا → امسح الكاش → أعد حفظ الروابط الدائمة.

---

## 6) خريطة الحزم الحرجة

| الحزمة | الدور | حالة الفحص |
|--------|--------|------------|
| `#header` | هيكل HTML لكل الصفحات | **معطوب (Parse error)** |
| `Enqueues` | تعطيل أصول WP | **يقتل أصول الحجز** |
| `kayan-booking` | معالج الحجز | منطق حقن + enqueue معطوبان |
| `kayan-payment` | دفع Demo | سليم نسبيًا لكنه يعتمد على الحجز |
| `Database/DB` | جداول الحجوزات/الدفع | موجودة + `dbDelta` |
| `AjaxCenter` | نقاط API | ملفات الحجز موجودة؛ حماية LFI تكسر endpoints قديمة |
| `FieldsMachine` | إعدادات الحجز/الدفع/الخدمات | الحقول موجودة |
| `kayan-seed` | تعبئة محتوى الرئيسية | يعمل عند `after_switch_theme` |
| `kayan-version` | تشخيص ذاتي في الأدمن | مفيد بعد إصلاح الهيدر |

---

## 7) ترتيب الإصلاح المقترح

1. **أصلح `#header/part.php`** (احذف `}` الزائد في السطر 26) — بدون هذا لا صفحة تعمل.
2. **استثنِ handles الحجز/الواجهة من `disable_all_scripts`** أو حمّل أصول الحجز يدويًا مثل باقي القالب (داخل الهيدر/الفوتر).
3. **أزل شرط `in_the_loop()`** من `InjectWizard` (أو استدعِ `the_post()`/`setup_postdata` قبل `the_content` في قوالب `@single`).
4. أصلح صلاحيات الملفات عند الرفع (`755`/`644`).
5. احذف القالب القديم بالكامل، ارفع النسخة المصلحة، امسح الكاش، احفظ الروابط الدائمة.
6. أنشئ خدمات واربط `linked_categories` بتصنيفات المقالات لاختبار المعالج.
7. اختياري: أصلح Mega-Menu parse error + توحيد حالة AjaxCenter + `TemplatePart`.

---

## 8) كيف تتحقق بعد الإصلاح

1. تفعيل القالب دون شاشة بيضاء.
2. شارة الأدمن `KAYAN v1.4.2` خضراء (حزمة `kayan-version`).
3. الرئيسية تعرض أقسام RUKN (بعد seed).
4. مقال له تصنيف مربوط بخدمة → يظهر `#kayan-booking-wizard-*`.
5. في Network: تحميل `kayan-booking.js` + `kayan-booking.css` + وجود `KayanBookingConfig`.
6. إرسال حجز تجريبي → سجل في قائمة **الحجوزات** + خطوة دفع Demo + فاتورة.

---

## 9) الحكم النهائي

**القالب لا يعمل لأن ملف الهيدر فيه خطأ PHP يمنع أي صفحة من التحميل.**  
هذا ليس بسبب غياب ملفات ووردبريس التقليدية، ولا لأن نظام الحجز «ناقص الملفات» — ملفات Phase 1/2 موجودة.  

بعد إصلاح الهيدر سيظهر الموقع، لكن **نظام الحجز/الدفع سيبقى معطلًا وظيفيًا** إلى أن يُعالَج تعارض `disable_all_scripts` وشرط `in_the_loop` غير المتوافق مع بنية Blade الحالية.
