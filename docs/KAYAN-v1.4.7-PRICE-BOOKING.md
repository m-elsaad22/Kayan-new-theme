# KAYAN v1.4.7 — حجز تفاعلي + دفع خارجي + زر احجز الآن

## ما الذي تغيّر؟

1. **زر عائم 3D «احجز الآن»** في `#footer` — يسار الشاشة، يظهر بعد تمرير 1%، ينقل بسلاسة إلى `#kayan-price-booking`.
2. **`[post_prices]` / price_list.php** — كل صف باقة قابلة للاختيار + نموذج حجز + زر «ادفع الآن».
3. **ودجت الأسعار (price.php)** — كروت تفاعلية + نفس نموذج الحجز/الدفع.
4. **#PriceBoxes** — كروت الخطط قابلة للتحديد مع بيانات الباقة/السعر.
5. **Rank Math** — حزمة `kayan-rankmath` لإعادة التفعيل الكامل (انظر `docs/RANK-MATH-REENABLE.md`).

## رابط الدفع

```
https://rukn-eltatawer-pay.tanceq.com/?service=...&package=...&amount=...&name=...&phone=...&address=...&date=...&time=...&notes=...
```

## الرفع

1. ارفع `kayan-theme-v1.4.7-ready.zip`
2. احذف مجلد الثيم القديم ثم فك الضغط
3. تأكد من `Version: 1.4.7` في `style.css`
4. Purge All من LiteSpeed
5. ضع الشورت كود `[post_prices]` داخل المقال وأضف الباقات من الميتا بوكس
