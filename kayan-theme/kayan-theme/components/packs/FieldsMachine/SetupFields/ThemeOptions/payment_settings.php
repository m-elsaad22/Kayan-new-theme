<?php
$metaboxes = array(
	'title'    => 'إعدادات الدفع (Demo)',
	'en_title'  => 'Payment (Demo)',
	'icon'    => '<i class="fa-solid fa-credit-card"></i>',
	'number'=>10,
	'fields'  => array(

		array(
			'id'=> 'kayan_payment_title',
			'type'=>'Title',
			'title'=> 'وسائل الدفع المتاحة',
		),
		array(
			'id'=> 'kayan_payment_methods',
			'type'=>'CheckBox',
			'title'=> 'الوسائل الظاهرة للعميل',
			'options'=>array(
				'card'=>'بطاقة ائتمان/خصم (Demo)',
				'wallet'=>'محفظة رقمية (Apple Pay / Google Pay — Demo)',
				'cash'=>'الدفع عند الاستلام',
			),
			'value'=>array('card','wallet','cash'),
		),
		array(
			'id'=> 'kayan_payment_notice',
			'type'=>'Title',
			'title'=> 'ملاحظة: هذه بوابة دفع تجريبية (Demo Gateway) لأغراض العرض فقط — لا تُجرى أي عملية سحب مالي حقيقية.',
		),

		array(
			'id'=> 'kayan_payment_title_2',
			'type'=>'Title',
			'title'=> 'الفاتورة',
		),
		array(
			'id'=> 'kayan_invoice_prefix',
			'type'=>'Text',
			'title'=> 'بادئة رقم الفاتورة',
			'value'=>'INV',
		),
		array(
			'id'=> 'kayan_invoice_company_name',
			'type'=>'Text',
			'title'=> 'اسم الشركة على الفاتورة (اتركه فارغاً لاستخدام اسم الموقع)',
		),
		array(
			'id'=> 'kayan_invoice_footer_note',
			'type'=>'TextArea',
			'title'=> 'ملاحظة أسفل الفاتورة',
			'value'=>'شكراً لثقتكم بنا.',
		),
	)
);
