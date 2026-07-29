<?php 
$metaboxes = array(
	'title'    => ' الرئيسية ',
	'en_title'  => 'HOME OPTIONS',
	'icon'    => '<i class="fa-solid fa-house-chimney"></i>',
	'number'=>4,
	'disc'=>'اعدادات شكل الصفحة الرئيسية ',
	'fields'  => array(
		array(
			'id'=>'HomeIntro',
			'type'=>'Widget-Selector',
			'ModelCenter'=>'intro-models',
			'create_fields'=>true,
			'select_field'=>array(
				'type'=>'Select',
				'id' => 'SelectedModel',
				'parent_id'=>'HomeIntro',
				'title' =>'إعدادات Intro',
				'selected_shows'=>true,
			)
		),

		array(
			'id'=>'widgets_home__meta',
			'type'=>'Widgets',
			'title'=>'محتوي الصفحة الرئيسية ',
			'ModelCenter'=>'Standard',
			'update__type'=>'option',
		)
	)
);