# KAYAN Theme — Agent Instructions

## Rank Math SEO (مهم)

- **أبقِ إضافة Rank Math Active** — لا تعطّل الإضافة من شاشة الإضافات.
- **السياسة النهائية (منذ v2.4.1):** Rank Math هو المرجع الوحيد للـ SEO على الواجهة —
  العنوان، الوصف، Canonical، Open Graph، Twitter، السكيما، خرائط الموقع.
  القالب **لا يعطّل** واجهة Rank Math ولا يكرر مخرجاتها.
- حزمة `kayan-seo` (التي كانت تعطّل واجهة Rank Math افتراضياً وتطبع بدلاً منها) **أُزيلت عمداً**
  في v2.4.1 لأنها تتعارض مع هذه السياسة — انظر `PHASE-INTEGRATION-2.4.1-REPORT.md` للتفاصيل.
- `schema/setup.php` (سكيما القالب القديمة) تبقى مُعطّلة تلقائياً عند تفعيل Rank Math عبر
  `Kayan_Adapter_Schema` (الفلتر `pre_option_validate__schema`) — لا يوجد تكرار.
- `theme-seo/setup.php`'s `ThemeSeo::Title()` لا تطبع شيئاً عند وجود Rank Math أو عند دعم
  `title-tag` (وكلاهما مفعّل دائماً) — fallback نظري فقط لمواقع بلا Rank Math وبلا title-tag.
- **لا تُعِد إدخال منطق "تعطيل واجهة Rank Math" مهما كان السبب** إلا بموافقة صريحة ومكتوبة —
  هذا قرار معماري نهائي وليس تفصيلاً تقنياً.

انظر: `kayan-theme/components/packs/kayan-platform/PHASE-INTEGRATION-2.4.1-REPORT.md`

## حجز الأسعار / الدفع

- الشورت كود `[post_prices]` + حزمة `kayan-price-pay` + زر `#kayanBookCta` في الفوتر.
- بوابة الدفع الخارجية: `https://rukn-eltatawer-pay.tanceq.com/` (رابط خاص بعميل هذا القالب —
  إن استُخدم القالب لعميل آخر، حدّث `Kayan_Price_Pay::PAY_BASE`).

## بنية الثيم

- **مجلد الإنتاج الوحيد هو `kayan-theme/`** — لا يوجد أي نسخة بديلة أو متداخلة.
- منصة `kayan-platform` (Phases 1–6: Admin Platform, PSEO, AI, Workflow, Quality,
  Migration Engine) تعيش في `kayan-theme/components/packs/kayan-platform/` ولا تُكرَّر.
