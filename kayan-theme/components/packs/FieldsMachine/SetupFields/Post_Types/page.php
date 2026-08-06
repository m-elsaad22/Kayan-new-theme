<?php 

$metaboxes['page_image'] = array(
	'title'    => 'تحديد صورة الصفحة',
	'fields' => array(
		array(
			'title'  => 'صورة الخلفية',
			'type'  => 'File',
			'id'    => 'page_back_image',
		),
	)
);

$metaboxes['pages_MetaBox'] = array(
	'title'    =>  'تحديد محتوي الصفحة ',
	'fields' => array(
		array(
			'id'=>'template',
			'type'=>'Models-Selector',
			'ModelCenter'=>'models',
			'create_fields'=>true,
			'select_field'=>array(
				'type'=>'Select',
				'id' => 'SelectedModel',
				//'parent_id'=>'template',
				'title' =>'تحديد شكل الصفحة ',
				'selected_shows'=>true,
			)
		),

	)
);

$metaboxes['first__page_edits'] = array(
	'title'    => 'إعدادات المقال ',
	'fields' => array(
		array(
			'title'  => 'المراجع ',
			'type'  => 'TextArea',
			'id'    => 'references',
		),
		array(
			'title'  => 'رقم واتس اب',
			'type'  => 'Text',
			'id'    => 'whatsapp_number',
		),
		array(
			'title'  => 'رقم الاتصال',
			'type'  => 'Text',
			'id'    => 'phone_number',
		),
	)
);

$insert_fields = array(
	array(
		'title'  => 'التقييم الافتراضي',
		'type'  => 'Number',
		'id'    => 'ratingValue',
	),
);
for ($i=1; $i < 6; $i++) { 
	$insert_fields[] = array(
		'title'  => 'اجمالى عدد الاشخاص المختارة ('.$i.')',
		'type'  => 'Number',
		'id'    => 'ratingUsers_'.$i,
	);
}

$metaboxes['rating__page_edits'] = array(
	'title'=>'التقيمات',
	'fields'=>array(
		array(
			'title'  =>'إعدادات التقييم الافتراضية',
			'type'  => 'SingleGroup',
			'id'    => 'defualt__rating',
			'is__open'=>true,
			'fields'=> $insert_fields
		)
	)
);

$metaboxes['cards__page_edits'] = array(
	'title'=>'بطاقة المقال',
	'fields'=>array(
		array(
			'title'  => 'إخفاء بطاقة طلب الخدمة',
			'type'  => 'SwitchBox',
			'id'    => 'hide__post_card',
		),

		array(
			'title'  => 'تحديد مكان الشريحة',
			'type'  => 'Select',
			'id'    => 'position__post_card',
			'options'=>array(
				'top_image'=>'اعلى الصورة',
				'top_content'=>'اعلى المحتوى',
				'bottom_content'=>'اسفل المحتوى',
				'before__references'=>'اسفل المراجع',
				'before_faqs'=>'اسفل الاسئلة الشائعة',
				'end__page'=>'اسفل الصفحة'
			)
		),

		array(
			'title'  => 'إعدادات بطاقة طلب الخدمة',
			'type'  => 'SingleGroup',
			'id'    => 'post__card__data',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'العنوان',
					'type'  => 'Text',
					'id'    => 'post_card_title',
				),
				array(
					'title'  =>'وصف مختصر',
					'type'  => 'TextArea',
					'id'    => 'post_card_content',
				),
				array(
					'title'  => 'إخفاء زرار الاتصال',
					'type'  => 'SwitchBox',
					'id'    => 'hide__card__callbutton',
				),
				array(
					'title'  => 'إخفاء زرار الواتساب',
					'type'  => 'SwitchBox',
					'id'    => 'hide__card__whatsapp',
				)
			),
		),
	
	)
);

$metaboxes['card__Popverpage_edits'] = array(
	'title'=>'إعدادات POPUP',
	'fields'=>array(
		array(
			'title'  => 'إخفاء POPUP',
			'type'  => 'SwitchBox',
			'id'    => 'hide__single__popover',
		),
		array(
			'title'  =>'إعدادات POPUP طلب الخدمة',
			'type'  => 'SingleGroup',
			'id'    => 'post__popover__data',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'عنوان POPUP',
					'type'  => 'Text',
					'id'    => 'popover_call_title',
				),
				array(
					'title'  => 'نص POPUP',
					'type'  => 'TextArea',
					'id'    => 'popover_call_content',
				),
				array(
					'title'  => 'ايقونة POPUP',
					'disc'=>'في حالة عدم تحديد ايقونة مخصصه سيتم تحديد ايقونة افتراضية',
					'type'  => 'TextArea_Code',
					'id'    => 'popover_call_icon',
				)
			)
		),
	)
);

$metaboxes['third__page_edits'] = array(
	'title'=>'إطلب الخدمة',
	'fields'=>array(
		array(
			'title'  => 'إخفاء ودجت طلب الخدمة من هذا المقال',
			'type'  => 'SwitchBox',
			'id'    => 'hide__sidebar__service_request',
		),
		array(
			'title'  =>'إعدادات ودجت طلب الخدمة',
			'type'  => 'SingleGroup',
			'id'    => 'post__service_request__data',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'عنوان ودجت اطلب الخدمة',
					'type'  => 'Text',
					'id'    => 'orderservicesy',
				),
				array(
					'title'  => 'نص ودجت اطلب الخدمة',
					'type'  => 'TextArea',
					'id'    => 'contentservicesy',
				),
				array(
					'title'  => 'إخفاء زرار الاتصال',
					'type'  => 'SwitchBox',
					'id'    => 'hide__service__callbuttony',
				),
				array(
					'title'  => 'إخفاء زرار الواتساب',
					'type'  => 'SwitchBox',
					'id'    => 'hide__service__whatsappy',
				)
			)
		)
	)
);

$metaboxes['secondary__page_edits'] = array(
	'title'=>'الاسئلة الشائعة ',
	'fields'=>array(
        array(
            'title'=>'تحديد خيارات العملة المراد عرضها ',
            'id'=> 'yourcolor__faqs',
            'type'=>'GroupsField',
            'fields'=>array(
                array(
                    'id'=> 'question',
                    'type'=>'Text',
                    'title'=>'السؤال',
                ),
                array(
                    'id'=> 'answer',
                    'type'=>'TextArea',
                    'title'=> 'الاجابه',
                )
            ),
        ),
	)
);

$metaboxes['pages__page_edits'] = array(
	'title'=>'صفحات القائمة الجانبية',
	'fields'=>array(
		array(
			'type'=>'GroupsField',
			'id' => 'Pages__List__URL',
			'title' =>'اختر الصفحات المراد عرضها ',
			'disc'=>'في حالة تحديدها من صفحة المقالة يتم اظهار الصفحات المختارة في المقالة',
			'fields'=> array(
	            array(
	                'type'=>'Posts-Select',
	                'id' => 'button_page',
	                'post_type_name'=>'page',
	                'title' =>'تحديد الصفحة',
	            ),
	            array(
	                'type'=>'Text',
	                'id' => 'button_Text',
	                'title' =>'إضافة عنوان اخر للصفحة',
	            ),
				array(
					'type'=>'TextArea_Code',
					'id'=>'button_Icon',
					'title'=>'ايقونة',
				),
				array(
					'type'=>'Text',
					'id'=>'number',
					'title'=>'رقم الترتيب ',
					'require'=>true,
				),
			)
		)
	)
);