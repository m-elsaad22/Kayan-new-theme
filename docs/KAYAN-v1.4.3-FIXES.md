# إصلاحات v1.4.3 — تشغيل القالب + الحجز/الدفع

## ما تم إصلاحه

1. **شاشة بيضاء:** حذف القوس الزائد في `#header/part.php`
2. **معالج الحجز لا يظهر:** إزالة شرط `in_the_loop()` غير المتوافق مع Blade
3. **CSS/JS الحجز يُحذفان:** حماية handles `kayan-*` و`yourcolor-*` من `disable_all_scripts`
4. **AjaxCenter 404 لأسماء CamelCase:** مطابقة عبر `sanitize_key`
5. **مسار Ajax:** توحيد `/AjaxCenter/`
6. **`TempURL`:** أصبح URI وليس مسار ملفات
7. **`get_the_terms`:** حماية من `foreach` على `false`
8. **Mega-Menu:** إصلاح Parse error
9. **`index.php`:** استدعاء Blade بدل دالة غير موجودة
10. **صلاحيات الملفات:** `755` للمجلدات و`644` للملفات

## طريقة الرفع

1. فعّل قالبًا افتراضيًا مؤقتًا واحذف `kayan-theme` القديم بالكامل
2. ارفع محتويات `kayan-theme-v1.4.3-fixed.zip` إلى `wp-content/themes/`
3. فعّل القالب
4. امسح كاش LiteSpeed / أي كاش آخر
5. الإعدادات ← الروابط الدائمة ← حفظ التغييرات
6. أنشئ خدمة (`services`) واربط `linked_categories` بتصنيف مقال لاختبار المعالج
