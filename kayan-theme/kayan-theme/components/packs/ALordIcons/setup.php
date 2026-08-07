<?php
/**
 * ALordIcons — بديل مجاني كامل
 * كانت الدالة تحقن أيقونات LordIcon المدفوعة من cdn.lordicon.com
 * الآن تُرجع أيقونة Font Awesome 6 Free متحركة بنفس التوقيع
 * (لا حاجة لأي سكربت خارجي — صفر اعتماديات مدفوعة)
 * KAYAN v1.4.2+ — خريطة مكتملة لكل الأكواد المستخدمة في الثيم
 */
function LoardIcons($json="",$w="50px",$h="50px",$options=array("primary"=>'#ffffff',"secondary"=>'#ffffff',"stroke"=>'70',"trigger"=>'loop',"delay"=>'3000')){
if($json == '') return false;
if(!isset($options['primary']) || empty($options['primary'])) $options['primary'] = '#151c28';
if(!isset($options['secondary']) || empty($options['secondary'])) $options['secondary'] = '#1269eb';

# ═══ خريطة أكواد LordIcon → أيقونات FA 6 Free مكافئة ═══
# الأكواد الثابتة (مضمّنة مباشرة في كود الثيم)
$map = array(
    // ── FieldsMachine / Products ──
    'slkvcfos' => 'fa-solid fa-file-circle-plus',   // BeforeInser_products — إضافة ملف/منتج
    'jvucoldz' => 'fa-solid fa-layer-group',         // Widgets — مجموعة عناصر
    'tdrtiskw' => 'fa-solid fa-trash-can',           // حذف
    'zczmziog' => 'fa-solid fa-circle-check',        // نجاح / تأكيد

    // ── export-import/setup.php ──
    'uukerzzv' => 'fa-solid fa-file-import',         // استيراد (import)
    'puvaffet' => 'fa-solid fa-file-export',         // تصدير (export/download)

    // ── FieldsMachine — أيقونة الصفحة الافتراضية ──
    'xvlmqqih' => 'fa-solid fa-file-pen',            // تحرير / صفحة جديدة
);

$icon = isset($map[$json]) ? $map[$json] : 'fa-solid fa-wand-magic-sparkles';

$out  = '<span class="yc-free-icon" style="--yc-fi-p:'.esc_attr($options['primary']).';--yc-fi-s:'.esc_attr($options['secondary']).';width:'.esc_attr($w).';height:'.esc_attr($h).'">';
$out .= '<i class="'.esc_attr($icon).'"></i>';
$out .= '</span>';
return $out;
}

# ستايل الأيقونة البديلة — مرة واحدة في لوحة التحكم والواجهة
function yc_free_icon_style(){
echo '<style id="yc-free-icon-css">
.yc-free-icon{display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:color-mix(in srgb,var(--yc-fi-s,#1269eb) 10%,transparent)}
.yc-free-icon>i{font-size:45%;line-height:1;background:linear-gradient(135deg,var(--yc-fi-p,#151c28),var(--yc-fi-s,#1269eb));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;animation:ycFiPulse 2.4s ease-in-out infinite}
@keyframes ycFiPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.12)}}
</style>';
}
add_action('wp_head','yc_free_icon_style',5);
add_action('admin_head','yc_free_icon_style',5);
