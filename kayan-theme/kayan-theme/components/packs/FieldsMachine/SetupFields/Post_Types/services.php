<?php 
$metaboxes['services__metabox'] = array(
	'title'    => 'بيانات الخدمة',
	'fields' => array(
        array(
            'id'=> 'service_icon',
            'type'=>'SVG-Icon',
            'title'=>'أيقونة الخدمة',
        ),
        array(
            'id'=> 'service_price',
            'type'=>'Text',
            'title'=>'سعر الخدمة (اختياري)',
        ),
        array(
            'id'=> 'service_price_from',
            'type'=>'SwitchBox',
            'title'=>'عرض السعر بصيغة "يبدأ من"',
        ),
        array(
            'id'=> 'service_color',
            'type'=>'Text',
            'title'=>'اللون المميز للخدمة (Hex)',
            'disc'=>'مثال : #2563EB',
        ),
        array(
            'id'=> 'service_duration',
            'type'=>'Text',
            'title'=>'المدة التقديرية للتنفيذ',
            'disc'=>'مثال : من 60 إلى 90 دقيقة',
        ),
        array(
            'id'=> 'service_short_desc',
            'type'=>'TextArea',
            'title'=>'وصف مختصر (يظهر في بطاقة اختيار الخدمة بنموذج الحجز)',
        ),
        array(
            'id'=> 'linked_categories',
            'type'=>'Taxonomy-CheckBox',
            'taxonomy_name'=>'category',
            'title'=>'تصنيفات المقالات المرتبطة بهذه الخدمة',
            'disc'=>'نموذج الحجز داخل أي مقال يظهر تلقائياً بحسب تصنيف المقال المطابق هنا',
        ),
	)
);

$metaboxes['services__booking_fields'] = array(
	'title'    => 'حقول نموذج الحجز الخاصة بهذه الخدمة',
	'disc'     => 'اترك القائمة فارغة لاستخدام الحقول الافتراضية (بيانات العميل والموقع والتاريخ فقط) دون خطوة تفاصيل إضافية',
	'fields' => array(
		array(
			'type'=>'GroupsField',
			'id'=>'extra_fields',
			'title'=>'حقول الخطوة الثانية (تفاصيل الخدمة)',
			'disc'=>'كل خدمة لها نموذجها الخاص بالكامل — أضف الحقول التي تحتاجها هذه الخدمة تحديداً',
			'fields'=> array(
				array(
					'type'=>'Text',
					'id' => 'title',
					'title'=>'عنوان الحقل',
					'require'=>true,
				),
				array(
					'type'=>'Text',
					'id' => 'id',
					'title'=>'معرف الحقل',
					'disc'=>'يجب أن يكون باللغة الإنجليزية وبدون مسافات (مثال: rooms_count)',
					'require'=>true,
				),
				array(
					'type'=>'Text',
					'id' => 'disc',
					'title'=>'وصف / تلميح مختصر (اختياري)',
				),
				array(
					'type'=>'Radio',
					'id'=>'type',
					'title'=>'نوع الحقل',
					'value'=>'Text',
					'options'=>array(
						'Text' => 'نص قصير',
						'Number'=>'رقم',
						'TextArea'=>'نص طويل',
						'Select'=>'قائمة اختيار',
						'CheckBox'=>'اختيار متعدد',
						'Radio'=>'اختيار واحد',
						'SwitchBox'=>'مفتاح تشغيل/إيقاف',
						'File'=>'رفع صورة',
					),
					'require'=>true,
					'Custom_says' => true,
					'shows_selected' => true,
					'show_create_fields'=>array(
						array(
							'type'=>'TextArea',
							'id' => 'options',
							'title'=>'إضافة اختيارات',
							'disc' =>'اكتب الاختيار ثم اضغط ENTER لإضافة اختيار آخر',
							'shows_At'=>array('Select','CheckBox','Radio'),
						),
					),
				),
				array(
					'type'=>'SwitchBox',
					'id'=>'Require',
					'title'=>'حقل إجباري ؟',
				),
			),
		),
	)
);
