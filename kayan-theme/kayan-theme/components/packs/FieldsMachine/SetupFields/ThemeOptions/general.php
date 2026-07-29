<?php 
$metaboxes = array(
	'title'    => 'الإعدادات العامة',
	'en_title'  => 'General settings',
	'icon'    => '<i class="fas fa-sliders"></i>',
	'number'=>1,
	'fields'  => array(
		array(
			'title'  => 'اختار لون مخصص',
			'en_title'=> 'Choose your own color',
			'type'  => 'Color',
			'id'    => 'site_color',
			'desc'=>'في حالة الرغبة في الغاء اللون المخصص  اترك الحقل فارغ'
		),	
		array(
			'title'  => 'تحديد لون الكتابة ',
			'en_title'=> 'Choose your text color',
			'type'  => 'Color',
			'id'    => 'text_Color',
			'desc'=>'إمكانية تحديد لون الكتابة '
		),
		array(
			'title'  => 'إسم الموقع',
			'en_title'=> 'Sitename',
			'type'  => 'Text',
			'id'    => 'sitename',
		),
		array(
			'title'  => 'صورة lazy load',
			'en_title'=> 'lazy load photo',
			'type'  => 'File',
			'id'    => 'lazyload',
			'desc'=>'امكانية وضع صورة قبل التحميل '
		),
		
	)
);