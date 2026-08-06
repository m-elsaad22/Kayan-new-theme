<?php 
$metaboxes = array(
	'title'    => 'العروض والحجز',
	'en_title'  => 'OFFERS & BOOKING',
	'icon'    => '<i class="fa-solid fa-gift"></i>',
	'number'=>8,
	'fields'  => array(
		array(
			'title'  => 'شارة العروض',
			'en_title'=> 'Offers badge',
			'type'  => 'Text',
			'id'    => 'offers_badge',
			'desc'=>'نص الشارة الذي يظهر على بطاقة كل عرض (مثال: عرض خاص)'
		),
		array(
			'type'=>'DuplicateGroup',
			'id' => 'offers_data',
			'title' =>'قائمة العروض',
			'fields'=> array(
				array(
					'type'=>'Text',
					'id' => 'offer_title',
					'title'=>'عنوان العرض',
					'require'=>true,
				),
				array(
					'type'=>'TextArea',
					'id' => 'offer_content',
					'title'=>'تفاصيل العرض',
				),
				array(
					'type'=>'Text',
					'id' => 'offer_price',
					'title'=>'السعر بعد الخصم',
				),
				array(
					'type'=>'Text',
					'id' => 'offer_old_price',
					'title'=>'السعر قبل الخصم',
				),
				array(
					'type'=>'SVG-Icon',
					'id' => 'offer_icon',
					'title'=>'أيقونة العرض',
				),
			),
		),
		array(
			'title'  => 'ملاحظة صفحة الحجز',
			'en_title'=> 'Booking page note',
			'type'  => 'TextArea',
			'id'    => 'booking__note',
			'desc'=>'نص تعريفي يظهر بجانب نموذج الحجز'
		),
	)
);
