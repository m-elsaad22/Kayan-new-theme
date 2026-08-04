# سياسة Rank Math في KAYAN

## الخلاصة

| العنصر | الحالة |
|--------|--------|
| إضافة Rank Math | **Active** (لا تُوقف) |
| إخراج الواجهة (head / OG / JSON-LD) | **معطّل** عبر KAYAN SEO |
| تخزين العنوان/الوصف | `rank_math_title` / `rank_math_description` |
| من يطبع `<title>` و meta و Schema | **KAYAN SEO** (+ schema القالب) |

## أين يتم التعطيل؟

`components/packs/kayan-seo/compatibility.php`

- `rank_math/frontend/disable` → `true`
- `rank_math/json_ld` → `false`
- تعطيل OpenGraph
- إزالة أكشنات `rank_math/head` و JSON-LD و OpenGraph
- إزالة `RankMath\Frontend\Head::head` من `wp_head`

## أين تُقرأ البيانات؟

`components/packs/kayan-seo/rank-math-bridge.php`

- `kayan_seo_get_rank_math_title()`
- `kayan_seo_get_rank_math_description()`

ثم:

- العنوان → فلتر `pre_get_document_title`
- الوصف → `wp_head` عبر `kayan_seo_print_meta_description`
- Schema → `components/packs/schema/setup.php` (لا يُتخطى عند وجود Rank Math)

## لماذا؟

حتى لا يتكرر `<head>` و JSON-LD بين Rank Math و KAYAN SEO، مع الإبقاء على محرر Rank Math وبياناته في الأدمن.
