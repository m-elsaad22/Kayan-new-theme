# Rank Math — سبب التعطيل وطريقة إعادة التفعيل

## من الذي عطّله؟

نظام **KAYAN Stabilization / Lockdown** (`components/packs/kayan-stabilization/lockdown.php`) في مرحلة قديمة (Phase 1.9) كان يعطّل مخرجات Rank Math من الواجهة عبر دالة باسم:

`disable_rank_math_frontend()`

الهدف القديم: جعل القالب مصدر SEO الوحيد، والإبقاء على Rank Math لتخزين البيانات فقط.

كذلك دالة `disable_all_scripts()` في `Enqueues/setup.php` كانت تزيل من الطابور كل السكربتات/الأنماط تقريباً — بما فيها أصول Rank Math — إلا إن وُجدت حماية صريحة.

## ماذا فعلنا لإعادة التفعيل؟ (v1.4.7)

1. حذف/تعطيل منطق `disable_rank_math_frontend` — لم يعد يُستدعى.
2. حماية handles التي تبدأ بـ `rank-math` / `rank_math` من `dequeue`.
3. حزمة جديدة: `components/packs/kayan-rankmath/setup.php`  
   - تزيل أي hooks قديمة للتعطيل  
   - تفرض `rank_math/frontend/disable = false`  
   - تفعّل `title-tag`
4. الهيدر لا يطبع `<title>` أو meta description يدوياً عند وجود Rank Math.

## خطواتك على السيرفر

1. ارفع قالب **v1.4.7** (أو أحدث) واستبدل المجلد بالكامل.
2. تأكد أن إضافة **Rank Math SEO** مفعّلة:  
   الإضافات → Rank Math → مفعّل.
3. LiteSpeed / أي كاش → **Purge All**.
4. افتح مصدر الصفحة الرئيسية وابحث عن:
   - `rank-math-schema`
   - وسوم `og:title` / `og:description`
5. من Rank Math → الحالة العامة: يجب أن تظهر الفحوصات باللون الأخضر قدر الإمكان.

## إن بقي معطّلاً

- تعطيل كاش الصفحات مؤقتاً واختبار Incognito.
- تأكد أنه لا يوجد Code Snippet يعيد استدعاء `disable_rank_math_frontend` أو يزيل `wp_head`.
- تأكد أن `style.css` في الثيم يعرض `Version: 1.4.7` أو أحدث.
