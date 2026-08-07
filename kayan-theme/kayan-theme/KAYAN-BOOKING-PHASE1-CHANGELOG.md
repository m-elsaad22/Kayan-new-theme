# Kayan Booking — Phase 1 (نظام الحجز الأساسي)

بناءً على Master Prompt (الأقسام 3، 5، 8) — هذه أول مرحلة من نظام SaaS الكامل.
تم البناء **داخل** بنية القالب الحالية (Packs + FieldsMachine + kayan-cpt) دون إنشاء أي بنية موازية.

## ما تم إنجازه في هذه المرحلة

### 1) قاعدة البيانات (جديدة بالكامل)
`components/packs/Database/DB/kayan-booking.php`
- `wp_kayan_bookings` — بيانات الحجز الكاملة (عميل، موقع، موعد، حالة، أسعار، دفع)
- `wp_kayan_booking_items` — الخدمات المطلوبة داخل كل حجز + بياناتها الديناميكية (JSON)
- `wp_kayan_booking_activity` — سجل تغييرات الحالة (Timeline)

تُنشأ تلقائياً عبر `dbDelta` بدون أي تدخل يدوي.

### 2) توسعة نظام الخدمات الحالي (لم يُنشأ CPT جديد)
`FieldsMachine/SetupFields/Post_Types/services.php` — تمت إضافته على `services__metabox` الموجود:
- `service_color`, `service_duration`, `service_short_desc`, `service_price_from`
- `linked_categories` (Taxonomy-CheckBox) → يربط الخدمة بتصنيفات المقالات، وهذا ما يجعل نموذج الحجز يظهر تلقائياً بحسب تصنيف كل مقال
- **`extra_fields`** (GroupsField) → أداة بناء نموذج ديناميكي **خاص بكل خدمة على حدة** (بدون كتابة كود) — بنفس نمط أداة `service_steps` الموجودة مسبقاً في `Taxonomies/category.php`

### 3) إعدادات نظام الحجز (ThemeOptions جديدة)
`FieldsMachine/SetupFields/ThemeOptions/booking_settings.php`
ساعات العمل، أيام العمل، أقل مهلة حجز، مدة كل Slot، الضريبة، العملة، بريد الإشعارات، رسالة النجاح.

### 4) الحقن التلقائي + المعالج (Pack: kayan-booking)
`components/packs/kayan-booking/`
- `setup.php` — منطق ربط الخدمات بالتصنيفات + حقن نموذج الحجز تلقائياً في نهاية أي مقال (`post`) أو صفحة خدمة (`services`) بدون إدراج يدوي، مع مفتاح إخفاء اختياري لكل مقال (`hide__kayan_booking` في إعدادات المقال)
- `admin.php` — لوحة تحكم "الحجوزات": إحصائيات سريعة، قائمة + فلترة بالحالة، تحديث الحالة، صفحة تفاصيل كاملة لكل حجز (بيانات العميل، الموقع على الخريطة، الحقول الديناميكية، سجل الحالة)
- `assets/js/kayan-booking.js` + `assets/css/kayan-booking.css` — معالج 5 خطوات (Vanilla JS بدون أي مكتبة خارجية):
  1. اختيار الخدمة (Checkbox Cards بالأيقونة واللون والسعر والمدة)
  2. الحقول الديناميكية الخاصة بكل خدمة مختارة (نص، قائمة، اختيار متعدد، مفتاح تشغيل، **رفع صورة**)
  3. بيانات العميل والموقع + زر "حدد موقعي" (Geolocation → Lat/Lng)
  4. التاريخ والوقت (Slots مبنية من ساعات العمل الفعلية + مهلة الحجز الدنيا)
  5. المراجعة (الخدمات + السعر الفرعي + الضريبة + الإجمالي) ثم الإرسال

### 5) نقاط AJAX جديدة (بنفس نمط AjaxCenter الحالي — بدون wp_ajax hooks)
- `kayan_booking_nonce` — nonce طازج لكل جلسة
- `kayan_booking_get_services` — الخدمات المرتبطة بتصنيف/خدمة معينة
- `kayan_booking_get_service_fields` — الحقول الديناميكية لكل خدمة مختارة
- `kayan_booking_submit` — التحقق الكامل + Rate Limit + Honeypot + رفع الصور + إنشاء الحجز في قاعدة البيانات + إشعار بريد + رابط تأكيد واتساب

## الحالة الحالية
- كل حجز يُنشأ بالحالة `pending` و `payment_status = unpaid` (بانتظار نظام الدفع Demo في المرحلة التالية)
- زر الخطوة الأخيرة حالياً بعنوان "ادفع الآن" وهو من سيُربط مباشرة بشاشات الدفع في **المرحلة 2**

---

# Phase 2 — نظام الدفع (kayan-payment) ✅ تم

بعد "المراجعة" في المعالج، الحجز بيتسجل أولاً (pending/unpaid) ثم يدخل العميل مباشرة على **خطوة دفع سادسة** داخل نفس المعالج بدون أي إعادة تحميل.

## ما تم إنجازه

### 1) قاعدة بيانات جديدة
`Database/DB/kayan-payment.php` → `wp_kayan_payments`: سجل مستقل لكل محاولة دفع (طريقة، مبلغ، حالة، آخر 4 أرقام من البطاقة، عدد محاولات OTP، رقم الفاتورة).

### 2) إعدادات (ThemeOptions)
`FieldsMachine/SetupFields/ThemeOptions/payment_settings.php` — تفعيل/تعطيل كل وسيلة دفع (بطاقة/محفظة/كاش)، بادئة رقم الفاتورة، اسم الشركة على الفاتورة، ملاحظة أسفل الفاتورة.

### 3) Pack: `kayan-payment`
`setup.php` — كلاس `Kayan_Payment` (توليد أرقام معاملات وفواتير فريدة، تصنيف نوع البطاقة، تسجيل الأنشطة).

### 4) 3 طرق دفع Demo كاملة (AjaxCenter):
- **بطاقة** (`kayan_payment_charge_card` → `kayan_payment_verify_otp`): تحقق من صيغة الرقم/الانتهاء/CVV، ثم شاشة OTP (تجريبي — أي رمز من 6 أرقام يُقبل، ويظهر الرمز التجريبي على الشاشة لأنها بوابة Demo)
- **محفظة رقمية** (`kayan_payment_charge_wallet`): محاكاة Apple Pay / Google Pay — دفعة فورية بضغطة واحدة
- **الدفع عند الاستلام** (`kayan_payment_confirm_cash`): تأكيد فوري بدون أي بيانات دفع

كل الطرق: تُحدّث `wp_kayan_bookings` (`payment_status`, `payment_method`, `status=confirmed`)، وتُسجّل نشاط، وتُصدر رقم فاتورة.

### 5) فاتورة عامة قابلة للطباعة
`AjaxCenter/kayan_invoice_view.php` — صفحة HTML مستقلة (`/AjaxCenter/kayan_invoice_view/?ref=BOOKING_REF`) فيها بيانات الحجز، جدول الخدمات، الإجمالي، QR Code (عبر خدمة QR خارجية مجانية)، وزر طباعة/حفظ PDF من المتصفح مباشرة.

### 6) واجهة المعالج
تم توسيع نفس `kayan-booking.js`/`.css` (بدل ملفات منفصلة) بشاشات: اختيار وسيلة الدفع → نموذج البطاقة → Processing → OTP → شاشة نجاح موحّدة فيها رقم الفاتورة + زر عرض/طباعة الفاتورة + رابط تأكيد واتساب.

### 7) لوحة التحكم
تفاصيل كل حجز في `kayan-bookings` بقت بتعرض وسيلة الدفع، آخر 4 أرقام (لو بطاقة)، حالة الدفع، رقم المعاملة، ورابط الفاتورة.

**ملاحظة مهمة:** دي بوابة دفع **تجريبية بالكامل** لأغراض العرض — مفيش أي تكامل حقيقي مع بنك أو معالج دفع (Stripe/PayTabs/Telr...). لما تكون جاهز تربطها ببوابة حقيقية، الأماكن اللي هتتغير محصورة في 3 ملفات فقط (`kayan_payment_charge_card.php`, `kayan_payment_charge_wallet.php`, `kayan_payment_verify_otp.php`) لأن باقي النظام (DB، الفواتير، الواجهة) مبني بشكل مستقل عن تفاصيل البوابة.

## المرحلة القادمة المقترحة
- **AI Engine** (القسم لاحق في البرومبت) أو **Programmatic SEO** — قوللي أنهي واحدة تحب تكمل بيها.
