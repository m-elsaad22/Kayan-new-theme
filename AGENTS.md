# KAYAN Theme — Agent Instructions

## Rank Math SEO (مهم)

- **أبقِ إضافة Rank Math Active** — لا تعطّل الإضافة من شاشة الإضافات.
- **KAYAN SEO = واجهة لـ Rank Math** (ليست بديلاً): بلوك تحت المقال + إخراج head يقرآن/يكتبان `rank_math_title` و `rank_math_description`.
- **الوضع الافتراضي:** عطّل إخراج Rank Math في الفرونت عبر `kayan-seo/compatibility.php` بينما KAYAN SEO يعمل، لتفادي تكرار `<head>` / JSON-LD.
- **لاستعادة واجهة Rank Math:** اجعل option `kayan_seo_disable` غير فارغ (`1`) — عندها يتوقف KAYAN SEO ولا يُستدعى تعطيل واجهة Rank Math.
  - من إعدادات القالب → إعدادات العنوان → «تعطيل KAYAN SEO»
    - أو: `update_option( 'kayan_seo_disable', '1' );`
- **لا تشغّل إخراج KAYAN SEO وواجهة Rank Math معاً** على الفرونت.

انظر: `docs/RANK-MATH-POLICY.md`

## حجز الأسعار / الدفع

- الشورت كود `[post_prices]` + حزمة `kayan-price-pay` + زر `#kayanBookCta` في الفوتر.
- بوابة الدفع الخارجية: `https://rukn-eltatawer-pay.tanceq.com/`
