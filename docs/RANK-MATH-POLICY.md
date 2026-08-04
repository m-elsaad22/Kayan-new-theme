# سياسة Rank Math في KAYAN

## الوضع الافتراضي

| العنصر | الحالة |
|--------|--------|
| إضافة Rank Math | **Active** (لا تُوقف) |
| إخراج الواجهة (head / OG / JSON-LD) | **معطّل** عبر KAYAN SEO |
| تخزين العنوان/الوصف | `rank_math_title` / `rank_math_description` |
| من يطبع `<title>` و meta و Schema | **KAYAN SEO** (+ schema القالب) |

## استعادة واجهة Rank Math (الخيار الأفضل)

KAYAN SEO يعمل طالما `kayan_seo_disable` **فارغ**.  
إذا صار غير فارغ → KAYAN SEO يتوقف → Rank Math يرجع يطبع العنوان والميتا والسكيما.

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

### الخيار البرمجي البديل
علّق/احذف استدعاءات التعطيل في  
`components/packs/kayan-seo/compatibility.php`  
(الأسطر التي تسجّل `kayan_seo_disable_rank_math_frontend` على `plugins_loaded` / `init` / `wp`).

## تنبيه

**لا تشغّل KAYAN SEO وواجهة Rank Math معاً** — قد يتكرر title/meta/schema في `<head>`.  
اختر واحداً فقط يطبع في الواجهة.

## الملفات

- `kayan-seo/helpers.php` — `kayan_seo_is_disabled()` / `kayan_seo_is_enabled()`
- `kayan-seo/compatibility.php` — تعطيل واجهة Rank Math (فقط إذا KAYAN SEO مفعّل)
- `kayan-seo/rank-math-bridge.php` — قراءة `rank_math_title` / `rank_math_description`
