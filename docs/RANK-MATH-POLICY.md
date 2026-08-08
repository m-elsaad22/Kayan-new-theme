# سياسة Rank Math في KAYAN Theme (v2.4.1 — نهائية)

> **تحديث v2.4.1:** هذا الملف كان يصف حزمة `kayan-seo` التي كانت تُعطّل واجهة
> Rank Math افتراضياً وتطبع بدلاً منها. تلك الحزمة **أُزيلت** في إطار توحيد الثيم
> النهائي (v2.4.1) لأنها تتعارض مع كون Rank Math هو المرجع الوحيد للـ SEO.
> السياسة الحالية أدناه هي الصحيحة والمعتمدة.

## الوضع الحالي (نهائي)

| العنصر | الحالة |
|--------|--------|
| إضافة Rank Math | **Active** (لا تُوقف) |
| إخراج الواجهة (title / meta / canonical / OG / Twitter / JSON-LD / sitemap) | **Rank Math بالكامل** |
| سكيما القالب القديمة (`schema/setup.php`) | معطّلة تلقائياً عند تفعيل Rank Math (`Kayan_Adapter_Schema`) |
| `ThemeSeo::Title()` (fallback القالب) | لا تطبع شيئاً إن كان Rank Math مفعّلاً أو `title-tag` مدعوماً |
| `kayan-seo` (حزمة قديمة كانت تعطّل واجهة RM) | **محذوفة** — لم تعد موجودة في الثيم |

## كيف يتكامل KAYAN مع Rank Math

- `Kayan_Theme_Integration::rank_math_active()` هي نقطة الفحص الموحّدة المستخدمة في
  كل الأدابترات (schema، header، PSEO Generator) — لا يوجد أكثر من طريقة للتحقق.
- `Kayan_PSEO_Generator` يكتب فقط مفاتيح ميتا Rank Math الخاصة به
  (`rank_math_title`, `rank_math_description`, `rank_math_focus_keyword`, `rank_math_robots`)
  عندما يوفّر Blueprint قيمة — لا يطبعها بنفسه على الواجهة أبداً، Rank Math يقرأها ويطبعها.
- القالب لا يسجّل أي مسار REST أو schema أو sitemap منافس لـ Rank Math.

## تنبيه

**لا تُعِد إدخال أي منطق يعطّل واجهة Rank Math أو يطبع بديلاً عنها** (title/meta/OG/schema)
إلا بقرار معماري صريح موثّق — هذا خلاف مباشر لسياسة الثيم النهائية.
