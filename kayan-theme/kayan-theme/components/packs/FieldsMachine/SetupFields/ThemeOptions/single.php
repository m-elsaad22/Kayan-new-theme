<?php
#
$metaboxes = array(
	'title'    => 'صفحة المقالة ',
	'en_title'  => 'single settings',
	'icon'    => '<i class="fa-solid fa-clipboard-check"></i>',
	'number'=>5,
	'fields'  => array(
		array(
			'title'  => 'إعدادات وادجت طلب الخدمة',
			'type'  => 'Title',
			'id'    => 'se,.jbfgnkls.nfklshnfcj',
		),
		array(
			'title'  => 'إخفاء وادجت طلب الخدمة',
			'type'  => 'SwitchBox',
			'id'    => 'hide__sidebar__service_request_single',
		),
		array(
			'title'  => 'إعدادات القائمة الجانبية ',
			'en_title'=> 'Side Bar Options',
			'type'  => 'Title',
			'id'    => 'shde_bar___title_opts',
		),
		array(
			'title'  => 'إخفاء الصورة البارزة',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__thumbnail__single',
		),

		array(
			'title'  => 'إخفاء المقال السابق والمقال التالى',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__next_prev_post__single',
		),

		array(
			'title'  => 'إلغاء المراجع من المقال',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__post__references',
		),	
		array(
			'title'  => 'إخفاء الاسئلة الشائعة',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__post__faqs',
		),

		array(
			'title'  => 'إخفاء مربع المشاركة في المقال',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__post__shares',
		),

		array(
			'title'  => 'إخفاء القائمة الجانبية',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__sidebar__single',
		),

		array(
			'title'  => 'إخفاء التقيمات',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__feedback__rating',
		),

		array(
			'title'  => 'إعدادات مربع بيانات المقال',
			'type'  => 'Title',
			'id'    => 'box__thum_bottom_title',
		),
		array(
			'title'  => 'إخفاء المربع',
			'type'  => 'SwitchBox',
			'id'    => 'hide__post__boxx',
		),

		array(
			'title'  => 'إخفاء التصنيف',
			'type'  => 'SwitchBox',
			'id'    => 'hide__post__category',
		),

		array(
			'title'  => 'إخفاء الكاتب',
			'type'  => 'SwitchBox',
			'id'    => 'hide__post__author',
		),

		array(
			'title'  => 'إخفاء التاريخ',
			'type'  => 'SwitchBox',
			'id'    => 'hide__post__date',
		),


		array(
			'id'=>'widgets_single__meta',
			'type'=>'Widgets',
			'title'=>'محتوي قائمة السنجل',
			'ModelCenter'=>'SingleBar',
			'update__type'=>'option',
		),
		array(
			'title'  => 'اعدادات المقالات ذات صلة ',
			'en_title'=> 'Side Bar Options',
			'type'  => 'Title',
			'id'    => 'related___title_opts',
		),

		array(
			'title'  => 'إخفاء شريحة المقالات ذات صلة ',
			'en_title'=> 'Side Bar Options',
			'type'  => 'SwitchBox',
			'id'    => 'hide__related__section',
		),
		array(
			'title'  => 'إعدادات شريحة ذات صلة ',
			'titleEN'=> 'disable local',
			'type'  => 'SingleGroup',
			'id'    => 'related__sections_data',
			'is__open'=>true,
			'fields'=>array(
				array(
					'title'  => 'عدد الاقسام ',
					'disc'=> 'عدد الاقسام المراد إظهارها في  اسفل المقال',
					'type'  => 'Number',
					'id'    => 'related__per_category',
				),
				array(
					'title'  => 'العنوان ',
					'type'  => 'Text',
					'id'    => 'related__title',
				),
				array(
					'title'  => 'قم بتحديد عدد المقالات المراد عرضها ',
					'type'  => 'Number',
					'id'    => 'related__per_page',
				),
				array(
					'title'  => 'إخفاء رابط عرض المزيد ',
					'type'  => 'SwitchBox',
					'id'    => 'hide__related__more',
				)
			)
		),
		array(
			'title'  => 'إعدادات POPUP',
			'en_title'=> 'Side Bar Options',
			'type'  => 'Title',
			'id'    => 'popoooe_bar___title_opts',
		),

		array(
			'title'  => 'إخفاء POPUP طلب الخدمة',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__post__popover',
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
		)		
	)
);