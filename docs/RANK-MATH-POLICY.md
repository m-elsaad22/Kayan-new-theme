# سياسة Rank Math في KAYAN

## الوضع الافتراضي

| العنصر | الحالة |
|--------|--------|
| إضافة Rank Math | **Active** (لا تُوقف) |
| تخزين العنوان/الوصف | `rank_math_title` / `rank_math_description` (جداول/ميتا Rank Math) |
| بلوك التحرير تحت المقال | **KAYAN SEO** (واجهة تكتب نفس ميتا Rank Math) |
| إخراج الواجهة (head / OG / JSON-LD من Rank Math) | **معطّل** عبر `kayan-seo/compatibility.php` |
| من يطبع `<title>` و meta description | **KAYAN SEO** (+ schema القالب) |

KAYAN SEO **ليس** محرك SEO بديلاً عن Rank Math — هو واجهة/طبقة إخراج تستخدم بيانات Rank Math.

## استعادة واجهة Rank Math (الخيار الأفضل)

KAYAN SEO يعمل طالما `kayan_seo_disable` **فارغ**.  
إذا صار غير فارغ → يتوقف بلوك KAYAN + إخراج القالب → Rank Math يرجع يطبع العنوان والميتا والسكيما في الفرونت، وتظهر ميتا بوكس Rank Math الأصلية.

### من لوحة التحكم
المظهر / إعدادات القالب → **إعدادات العنوان (SEO)** →  
فعّل: **«تعطيل KAYAN SEO (استعادة واجهة Rank Math)»**

### برمجياً / من قاعدة البيانات
```php
update_option( 'kayan_seo_disable', '1' );
```
أو عبر WP-CLI:
```bash
wp option update kayan_seo_disable 1
```

لإعادة تفعيل KAYAN SEO:
```bash
wp option delete kayan_seo_disable
# أو
wp option update kayan_seo_disable ''
```

## تنبيه

**لا تشغّل إخراج KAYAN SEO وواجهة Rank Math معاً على الفرونت** — قد يتكرر title/meta/schema في `<head>`.  
اختر واحداً فقط يطبع في الواجهة. التخزين دائماً عبر ميتا Rank Math.

## الملفات

- `kayan-seo/helpers.php` — `kayan_seo_is_disabled()` / `kayan_seo_is_enabled()`
- `kayan-seo/compatibility.php` — تعطيل إخراج Rank Math في الفرونت (فقط إذا KAYAN SEO مفعّل)
- `kayan-seo/rank-math-bridge.php` — قراءة `rank_math_title` / `rank_math_description`
- `kayan-seo/admin-metabox.php` — بلوك تحت المقال للكتابة في نفس الميتا
