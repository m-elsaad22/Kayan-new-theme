<?php
#
$metaboxes = array(
	'title'    => 'إعدادات الصفحات',
	'en_title'  => 'single settings',
	'icon'    => '<i class="fa-solid fa-clipboard-check"></i>',
	'number'=>6,
	'fields'  => array(
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
			'id'    => 'hide__thumbnail__pages',
		),

		array(
			'title'  => 'إلغاء المراجع من الصفحة',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__pages__references',
		),	
		array(
			'title'  => 'إخفاء الاسئلة الشائعة',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__pages__faqs',
		),

		array(
			'title'  => 'إخفاء مربع المشاركة في الصفحة',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__pages__shares',
		),

		array(
			'title'  => 'إخفاء القائمة الجانبية',
			'en_title'=> 'select filters',
			'type'  => 'SwitchBox',
			'id'    => 'hide__sidebar__pages',
		),

		array(
			'id'=>'widgets_pages__meta',
			'type'=>'Widgets',
			'title'=>'محتوي قائمة الصفحة',
			'ModelCenter'=>'SingleBar',
			'update__type'=>'option',
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
			'id'    => 'hide__page__popover',
		),
		array(
			'title'  =>'إعدادات POPUP طلب الخدمة',
			'type'  => 'SingleGroup',
			'id'    => 'page__popover__data',
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