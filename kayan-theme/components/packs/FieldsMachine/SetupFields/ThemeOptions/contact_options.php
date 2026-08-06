<?php 
$metaboxes = array(
	'title'    => 'إعدادات الاتصال ',
	'en_title'  => 'CONTACT OPTIONS',
	'icon'    => '<i class="fa-solid fa-share-from-square"></i>',
	'number'=>14,
	'fields'  => array(
		array(
			'id'=> 'contact__fields',
			'type'=>'Title',
			'title'=> 'اعدادات الاتصال',
		),

		array(
			'id'=> 'company__mail',
			'type'=>'Text',
			'title'=> 'االبريد الاكتروني',
		),
		array(
			'id'=> 'phonenumber',
			'type'=>'Text',
			'title'=> 'رقم التواصل ',
		),
		array(
			'id'=> 'whatsapp_number',
			'type'=>'Text',
			'title'=> 'رقم WhatsApp',
		),
		array(
			'id'=> 'rukn_cs_global_title',
			'type'=>'Title',
			'title'=> 'نظام أزرار الاتصال — التحكم العام (المستوى الأول)',
		),
		array(
			'id'=> 'rukn_hide_call_global',
			'type'=>'SwitchBox',
			'title'=> 'إخفاء زر الاتصال على مستوى الموقع بالكامل',
			'disc'=> 'يمكن تجاوز هذا الإعداد من صفحة أي تصنيف أو من داخل أي مقال. عند الإخفاء يختفي الزر من كل عناصر القالب ويتحول الواتساب لمربع محادثة مصغر.',
		),
		array(
			'id'=> 'rukn_hide_wa_global',
			'type'=>'SwitchBox',
			'title'=> 'إخفاء زر الواتساب على مستوى الموقع بالكامل',
			'disc'=> 'يمكن تجاوزه من التصنيفات أو المقالات.',
		),
		array(
			'id'=> 'company__adress',
			'type'=>'Text',
			'title'=> 'العنوان ',
		),

		array(
			'id'=> 'company__map_code',
			'type'=>'TextArea_Code',
			'title'=> 'كود الخريطة',
		),
		array(
			'id'=> 'company__map_title',
			'type'=>'Text',
			'title'=> 'عنوان الخريطة ',
			'disc'=> 'يجب اضافة عنوان للخريطة اجبارياً',
		),


		array(
			'id'=> 'Social__adress',
			'type'=>'Title',
			'title'=> 'مواقع التواصل الاجتماعى ',
		),

		array(
			'title'  => 'فيسبوك',
			'type'  => 'Text',
			'id'    => 'facebook',
		),
		array(
			'title'  => 'تويتير',
			'type'  => 'Text',
			'id'    => 'twitter',
		),
		array(
			'title'  =>'تليجرام ',
			'type'  => 'Text',
			'id'    => 'telegram',
		),
		array(
			'title'  =>'يوتيوب',
			'type'  => 'Text',
			'id'    => 'youtube',
		),
		array(
			'title'  =>'لنيكد ان ',
			'type'  => 'Text',
			'id'    => 'linkedin',
		),
		array(
			'title'  =>'انتسجرام',
			'type'  => 'Text',
			'id'    => 'instagram',
		),
		array(
			'title'  =>'threads',
			'type'  => 'Text',
			'id'    => 'threads',
		),				
	)
);