<?php
#
$metaboxes = array(
	'title'    => 'إعدادات خطط الاسعار',
	'en_title'  => 'single settings',
	'icon'    => '<i class="fa-solid fa-clipboard-check"></i>',
	'number'=>6,
	'fields'  => array(
		array(
			'title'  => 'إعدادات زرار الخطط ',
			'en_title'=> 'Side Bar Options',
			'type'  => 'Title',
			'id'    => 'shde_bar___title_pricee',
		),

		array(
			'id'    => 'hide__price__button',
			'type'  => 'SwitchBox',
			'title' => 'إخفاء زرار الخطط ',
		),

		array(
			'id'    => 'price__button__text',
			'type'  => 'Text',
			'title' => 'محتوى رابط الخطط ',
		),

		array(
			'id'=>'price__option_data',
			'type'=>'Models-Selector',
			'title'=>'إعدادات رابط خطط الاسعار',
			'select_field'=>array(
				'id'=>'price__mode',
				'type'=>'Select',
				'selected_shows'=>true,
				#'parent_id'=>'price__option_data',
				'title'=>'تحديد نوع رابط الزرار',
				'options'=>array(
					'manual' => 'يدويا',
					'watshapp'=>'WhatsApp',
					'phonenumber'=>'Phone',
					'page'=>'Page',
				),
			),
			'create_fields'=>true,
			'choose_fields'=>array(
				'manual' => array(
					'id'=>'manual',
					'title' => 'يدوي',
					'fields'=> array(
						array(
							'id'    => 'button__URL',
							'type'  => 'Text',
							'title' => 'الرابط',
						),
					),
				),
				'watshapp'=>array(
					'id'=>'watshapp',
					'title' => 'رقم watshapp',
					'fields'=> array(
						array(
							'id'    => 'watshapp',
							'type'  => 'Text',
							'title' => 'رقم watshapp',
						),
					),
				),
				'phonenumber'=>array(
					'id'=>'phonenumber',
					'title' => 'رقم phonenumber',
					'fields'=> array(
						array(
							'id'    => 'phonenumber',
							'type'  => 'Text',
							'title' => 'phonenumber',
						),
					),
				),
				'page'=>array(
					'id'=>'page',
					'title' => 'تحديد صفحة من الصفحات',
					'fields'=> array(
			            array(
			                'type'=>'Posts-Select',
			                'id' => 'button_page',
			                'post_type_name'=>'page',
			                'title' =>'تحديد الصفحة',
			            )
					),
				)
			)
		),

		array(
			'title'  => 'إعدادات صفحة الخطط ',
			'en_title'=> 'select filters',
			'type'  => 'Title',
			'id'    => 'sections--title--aa',
		),

		array(
			'id'=>'widgets_price_page__meta',
			'type'=>'Widgets',
			'title'=>'محتوي الصفحة الرئيسية ',
			'ModelCenter'=>'Standard',
			'update__type'=>'option',
		),
	)
);