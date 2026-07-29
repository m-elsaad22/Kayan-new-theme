<?php
#
$metaboxes = array(
	'title'    => 'صفحة الاعمال السابقة',
	'en_title'  => 'single settings',
	'icon'    => '<i class="fa-solid fa-clipboard-check"></i>',
	'number'=>6,
	'fields'  => array(
		array(
			'title'  => 'إعدادات صفحة الاعمال السابقة',
			'en_title'=> 'select filters',
			'type'  => 'Title',
			'id'    => 'sections--title--bb',
		),

		array(
			'id'=>'widgets_works_page__meta',
			'type'=>'Widgets',
			'title'=>'إعدادات صفحة الاعمال السابقة',
			'ModelCenter'=>'Standard',
			'update__type'=>'option',
		),
	)
);