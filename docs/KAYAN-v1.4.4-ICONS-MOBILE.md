# KAYAN v1.4.4 — أيقونات + موبايل + ZIP جاهز

## الاختبار
```bash
php tests/kayan-smoke.php
# نتيجة: 32 passed, 0 failed
```

## ما تغيّر في الأيقونات
- منتقي `SVG-Icon` أصبح منتقي **Font Awesome 6 Free** (141 أيقونة مع بحث ومعاينة)
- `kayan_icon_html()` يحوّل القيم القديمة (slug SVG / HTML / Pro) إلى أيقونات تعمل
- `fa-free-fixes.css` يفرض الوزن 900 في الأدمن والواجهة
- أوزان Custom-Style كانت 800 → صارت 900
- `admin-mobile.css` مفعّل في لوحة التحكم

## الموبايل
- معالج الحجز: عمود واحد، أزرار sticky، حقول كاملة العرض تحت 640px
- أنماط responsive.css الأصلية للقالب ما زالت نشطة

## ملف الرفع
`kayan-theme-v1.4.4-ready.zip`

1. احذف القالب القديم
2. ارفع مجلد `kayan-theme`
3. امسح الكاش + احفظ الروابط الدائمة
