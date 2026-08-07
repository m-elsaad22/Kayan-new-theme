<?php 
$metaboxes = array(
	'title'    => 'إعدادات العنوان',
	'en_title'  => 'SEO OPTIONS',
	'icon'    => '<i class="fa-solid fa-heading"></i>',
	'number'=>15,
	'fields'  => array(
		array(
			'id'=> 'kayan_seo_disable',
			'type'=>'SwitchBox',
			'title'=> 'تعطيل KAYAN SEO (استعادة واجهة Rank Math)',
			'disc'=>'عند التفعيل: يتوقف KAYAN SEO عن طباعة العنوان/الميتا/السكيما، وتعود واجهة Rank Math للطباعة في head. لا تشغّل الاثنين معاً على الواجهة لتفادي التكرار. الإضافة Rank Math تبقى Active في كل الأحوال.'
		),
		array(
			'id'=> 'hide__theme_seo',
			'type'=>'SwitchBox',
			'title'=> 'إخفاء عنوان القالب',
			'disc'=>'إذا كنت تستخدم اضافات سيو تقوم باضافة العنوان اوتماتيكيا يمكنك اغلاق عنوان القالب لتفادي تكرار العنوان'
		),
		array(
			'id'=> 'hide__description_show',
			'type'=>'SwitchBox',
			'title'=> 'إخفاء  وصف الافتراضي',
			'disc'=>'إذا كنت تستخدم اضافات سيو تقوم باضافة العنوان اوتماتيكيا يمكنك اغلاق عنوان القالب لتفادي تكرار العنوان'
		),
		array(
			'id'=> 'seo__title_showsin',
			'type'=>'Radio',
			'title'=> 'تحديد نوع العنوان المراد إستخدامه ',
			'options'=>array(
				'wordpress'=>'افتراضي',
				'theme_seo'=>'القالب',
			)
		),
		array(
			'id'=> 'home__title',
			'type'=>'Text',
			'title'=> 'عنوان الصفحة الرئيسية',
		),
		array(
			'id'=> 'default__title',
			'type'=>'Text',
			'title'=> 'عنوان افتراضي',
		),
		array(
			'id'=> 'seo__site_name',
			'type'=>'Text',
			'title'=> 'كلمة بعد كل عنوان',
			'disc'=>'قُم بتحديد كلمة لإضافتها بعد عنوان كل صفحة مثل : اتصل بنا | اسم موقعك'
		),
	)
);