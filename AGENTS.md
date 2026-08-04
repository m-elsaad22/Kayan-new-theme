# KAYAN Theme — Agent Instructions

## Rank Math SEO (مهم)

- **أبقِ إضافة Rank Math Active** — لا تعطّل الإضافة من شاشة الإضافات.
- **الوضع الافتراضي:** عطّل واجهة Rank Math فقط عبر `kayan-seo/compatibility.php` بينما KAYAN SEO يعمل، لتفادي تكرار `<head>` / JSON-LD.
- التخزين عبر ميتا Rank Math؛ الجسر يقرأ `rank_math_title` و `rank_math_description`.
- **لاستعادة واجهة Rank Math:** اجعل option `kayan_seo_disable` غير فارغ (`1`) — عندها يتوقف KAYAN SEO ولا يُستدعى تعطيل واجهة Rank Math.
  - من إعدادات القالب → إعدادات العنوان → «تعطيل KAYAN SEO»
  - أو: `update_option( 'kayan_seo_disable', '1' );`
- **لا تشغّل KAYAN SEO وواجهة Rank Math معاً** على الفرونت.

انظر: `docs/RANK-MATH-POLICY.md`

## حجز الأسعار / الدفع

- الشورت كود `[post_prices]` + حزمة `kayan-price-pay` + زر `#kayanBookCta` في الفوتر.
- بوابة الدفع الخارجية: `https://rukn-eltatawer-pay.tanceq.com/`
