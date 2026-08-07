<?php
$metaboxes = array(
	'title'    => 'إعدادات نظام الحجز',
	'en_title'  => 'Booking System',
	'icon'    => '<i class="fa-solid fa-calendar-check"></i>',
	'number'=>9,
	'fields'  => array(

		array(
			'id'=> 'kayan_booking_title',
			'type'=>'Title',
			'title'=> 'ساعات العمل',
		),
		array(
			'id'=> 'kayan_booking_work_from',
			'type'=>'Text',
			'title'=> 'بداية الدوام (HH:MM)',
			'value'=>'09:00',
		),
		array(
			'id'=> 'kayan_booking_work_to',
			'type'=>'Text',
			'title'=> 'نهاية الدوام (HH:MM)',
			'value'=>'21:00',
		),
		array(
			'id'=> 'kayan_booking_work_days',
			'type'=>'CheckBox',
			'title'=> 'أيام العمل',
			'options'=>array(
				'0'=>'الأحد', '1'=>'الاثنين', '2'=>'الثلاثاء', '3'=>'الأربعاء',
				'4'=>'الخميس', '5'=>'الجمعة', '6'=>'السبت',
			),
			'value'=>array('0','1','2','3','4','6'),
		),
		array(
			'id'=> 'kayan_booking_lead_hours',
			'type'=>'Text',
			'title'=> 'أقل مهلة قبل الموعد (بالساعات)',
			'value'=>'2',
		),
		array(
			'id'=> 'kayan_booking_slot_minutes',
			'type'=>'Text',
			'title'=> 'مدة كل فترة زمنية (بالدقائق)',
			'value'=>'60',
		),

		array(
			'id'=> 'kayan_booking_title_2',
			'type'=>'Title',
			'title'=> 'الأسعار والعملة',
		),
		array(
			'id'=> 'kayan_booking_currency',
			'type'=>'Text',
			'title'=> 'رمز العملة',
			'value'=>'AED',
		),
		array(
			'id'=> 'kayan_booking_tax_rate',
			'type'=>'Text',
			'title'=> 'نسبة الضريبة (%)',
			'value'=>'5',
		),

		array(
			'id'=> 'kayan_booking_title_3',
			'type'=>'Title',
			'title'=> 'الإشعارات',
		),
		array(
			'id'=> 'kayan_booking_notify_email',
			'type'=>'Text',
			'title'=> 'بريد استقبال إشعارات الحجوزات (اتركه فارغاً لاستخدام بريد الموقع)',
		),
		array(
			'id'=> 'kayan_booking_success_message',
			'type'=>'TextArea',
			'title'=> 'رسالة نجاح الحجز',
			'value'=>'تم استلام طلب حجزك بنجاح، سيتواصل معك فريقنا للتأكيد قريباً.',
		),
	)
);
