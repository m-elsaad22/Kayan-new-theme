<?php 
$metaboxes = array(
	'title'    => 'صفحة اتصل بنا ',
	'en_title'  => 'CONTACT OPTIONS',
	'icon'    => '<i class="fa-solid fa-pager"></i>',
	'number'=>8,
	'fields'  => array(
		array(
			'id'=> 'contact__us__page',
			'type'=>'Posts-Select',
			'post_type_name'=>'page',
			'title'=> 'تحديد صفحة اتصل بنا',
		),
		array(
			'id'=>'widgets_contactus__meta',
			'type'=>'Widgets',
			'title'=>'محتوي الصفحة الرئيسية ',
			'ModelCenter'=>'Standard',
			'update__type'=>'option',
		),
	)
);