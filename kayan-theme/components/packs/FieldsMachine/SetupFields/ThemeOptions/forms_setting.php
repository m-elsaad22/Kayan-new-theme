<?php 
$metaboxes = array(
	'title'    => 'إعدادات  نموذج الخدمات',
	'en_title'  => 'forms setting',
	'icon'    => '<i class="fa-solid fa-heading"></i>',
	'number'=>15,
	'fields'  => array(
		
		array(
			'id'=> 'form__title',
			'type'=>'Text',
			'title'=> 'عنوان النموذج',
		),
		array(
			'id'=> 'dis__title',
			'type'=>'Text',
			'title'=> 'وصف النموذج',
		),
			
		array(
			'id'=> 'alert_def',
			'type'=>'Text',
			'title'=> 'رساله الانتهاء',
		),

		array(
			'id'=> 'tttttitle',
			'type'=>'Title',
			'title'=> 'اعدادات الحقول داخل النموذج',
		),

		array(
			'id'=> 'catargey__title',
			'type'=>'Text',
			'title'=> 'عنوان حقل التصنيف',
		),

		array(
			'id'=> 'button__title',
			'type'=>'Text',
			'title'=> 'عنوان زر الفورم',
		),

		array(
			'id'=> 'button__icon',
			'type'=>'TextArea_Code',
			'title'=> 'الايكون',
		),
	)
);