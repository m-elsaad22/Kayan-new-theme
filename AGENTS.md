# KAYAN Theme — Agent Instructions

## Rank Math SEO (مهم)

- **أبقِ إضافة Rank Math Active** — لا تعطّل الإضافة من شاشة الإضافات.
- **عطّل واجهة Rank Math فقط** عبر `components/packs/kayan-seo/compatibility.php`:
  - `rank_math/frontend/disable` → `true`
  - `rank_math/json_ld` → `false`
  - تعطيل OpenGraph
  - إزالة أكشنات `rank_math/head` و `rank_math/json_ld` و `rank_math/opengraph/*`
- السبب: منع تكرار `<head>` و JSON-LD مع مخرجات **KAYAN SEO**.
- التخزين يبقى عبر ميتا Rank Math، والجسر يقرأ:
  - `rank_math_title`
  - `rank_math_description`
  من `components/packs/kayan-seo/rank-math-bridge.php`
- **لا** تُعد تفعيل إخراج Rank Math في الواجهة (`__return_false` على `rank_math/frontend/disable`) إلا بطلب صريح يغيّر هذه السياسة.

## حجز الأسعار / الدفع

- الشورت كود `[post_prices]` + حزمة `kayan-price-pay` + زر `#kayanBookCta` في الفوتر.
- بوابة الدفع الخارجية: `https://rukn-eltatawer-pay.tanceq.com/`
