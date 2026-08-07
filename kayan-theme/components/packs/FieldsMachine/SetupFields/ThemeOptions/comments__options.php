<?php 
$metaboxes = array(
	'title'    => 'التعليقات',
	'en_title'  => 'Comments settings',
	'icon'    => '<i class="fa-solid fa-comments"></i>',
	'number'=>10,
	'fields'  => array(

		array(
			'title'  => 'تعطيل التعليقات في الـ DESKTOP',
			'en_title'=> 'disable_comments',
			'type'  => 'SwitchBox',
			'id'    => 'disable_comments',
		),


		array(
			'title'  => 'تعطيل التعليقات في الـ MOBILE',
			'en_title'=> 'disable_comments_mobile',
			'type'  => 'SwitchBox',
			'id'    => 'disable_mobile_comments',
		),

		array(
			'title'  => 'عنوان التعليقات',
			'en_title'=> 'show comment date',
			'type'  => 'Text',
			'id'    => 'comments__section__title',
		),
		array(
			'title'  => 'وصف التعليقات',
			'en_title'=> 'show comment date',
			'type'  => 'TextArea',
			'id'    => 'comments__section__content',
		),
		
	)
);