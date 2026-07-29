<?php 
$metaboxes = array(
	'title'    => 'الاعلانات',
	'en_title'  => 'ADS settings',
	'icon'    => '<i class="fa-solid fa-rectangle-ad"></i>',
	'number'=>12,
	'fields'  => array(
		array(
			'title'  => 'إخفاء اعلان الهيدر(PC)',
			'en_title'=> 'Show Header Code',
			'type'  => 'SwitchBox',
			'id'    => 'show_ads_header',
		),
		array(
			'title'  => 'اعلان الهيدر(PC)',
			'en_title'=> 'ads Codes',
			'type'  => 'TextArea_Code',
			'id'    => 'ads_header',
		),
		array(
			'title'  => 'إخفاء اعلان الهيدر(Mobile)',
			'en_title'=> 'TopBarOpen',
			'type'  => 'SwitchBox',
			'id'    => 'show_ads_mobile_header',
		),
		array(
			'title'  => 'اعلان الهيدر (Mobile)',
			'en_title'=> 'ads Codes',
			'type'  => 'TextArea_Code',
			'id'    => 'mobile_ads_header',
		),
	)
);