<?php 
$menus = array();
foreach( wp_get_nav_menus() as $t ) {
    $menus[$t->term_id] = $t->name;
}
$metaboxes = array(
    'title'    => 'إعدادات الهيدر',
    'en_title' => 'Header Options',
    'icon'     => '<i class="fa-solid fa-arrow-up"></i>',
    'number'   => 2,
    'fields'   => array(
    	#
    	array(
			'title'  => 'إعدادات شعار الهيدر',
			'disc' => 'إعدادات الشعار في منطقة الهيدر ',
			'en_title'=> 'logoo_setting',
			'type'  => 'Title',
			'id'    => 'logoo_setting',
		),
		array(
			'id'=>'logo__data',
			'type'=>'Models-Selector',
			'title'=>'تحديد نوع الشعار',
			'select_field'=>array(
				'id'=>'logo__mode',
				'type'=>'Select',
				'selected_shows'=>true,
				'title'=>'تحديد نوع الشعار',
				'options'=>array(
					'Image' => 'صورة',
					'Text'=>'نص',
				),
			),
			'create_fields'=>true,
			'choose_fields'=>array(
				'Image' => array(
					'id'=>'Image',
					'title' => 'صورة',
					'fields'=> array(
						array(
							'id'    => 'image_logo',
							'type'  => 'File',
							'title' => 'الشعار',
						),
						array(
							'id'    => 'header__alt',
							'type'  => 'Text',
							'title' => 'النص البديل',
						),

						array(
							'id'    => 'image_logo_width',
							'type'  => 'Text',
							'title' => 'عرض الشعار',
						),
						array(
							'id'    => 'image_logo_height',
							'type'  => 'Text',
							'title' => 'طول الشعار',
						),
					),
				),
				'Text'=>array(
					'id'=>'Text',
					'title' => 'نص',
					'fields'=> array(
						array(
							'id'    => 'logo_Text',
							'type'  => 'Text',
							'title' => 'نص الشعار ',
							'disc'=> "قَم بتمييز كلمة محددة في الشعار عن طريق إضافة ' {% ' قبل بداية الكلمة و ' %} ' بعد نهاية الكلمة .. كما يمكنك تحديد لون مخصص من خلال <p>#تحديد_الكلمة_المميزة_بالشعار </p>" ,
						),
						array(
							'id'    => 'header__alt',
							'type'  => 'Text',
							'title' => 'النص البديل',
						),
						array(
							'type'=>'Color',
							'id' => 'secondary_color',
							'title' =>'تحديد الكلمة المميزة بالشعار',
						),
					),
				)
			)
		),


    	array(
			'title'  => 'اعدادات  الرمز ( Favicon )',
			'disc' => 'قُم بإضافة رمز Favicon الى موقعك',
			'en_title'=> 'Favicon_setting',
			'type'  => 'Title',
			'id'    => 'Favicon_setting',
		),
		array(
			'title'  => 'رمز الموقع',
			'en_title'=> 'Favicon',
			'type'  => 'File',
			'id'    => 'favicon',
		),

		array(
			'id'    => 'header__text',
			'type'  => 'Text',
			'title' => 'الاسم',
		),

	 	array(
			'title'  => 'اعدادات مربع البحث',
			'disc' => 'التحكم في البحث الخاص بمنطقة الهيدر',
			'en_title'=> 'search_setting',
			'type'  => 'Title',
			'id'    => 'search_setting',
			'icon' => '<i class="fa-solid fa-magnifying-glass"></i>',
		),
		array(
			'title'  => 'إخفاء البحث',
			'en_title'=> 'search',
			'type'  => 'SwitchBox',
			'id'    => 'hide_search',
		),
		array(
			'title'  => 'نص البحث',
			'disc'  => 'نص مربع البحث مثل : ابحث في موقع "اسم موقعك"',
			'en_title'=> 'search',
			'type'  => 'Text',
			'id'    => 'search_placeholder',
		),

		array(
			'title'  => 'عنوان البحث',
			'en_title'=> 'search',
			'type'  => 'Text',
			'id'    => 'search_title',
		),
		array(
			'id'=>'search__Button',
			'type'=>'Models-Selector',
			'title'=>'عنوان زرار البحث',
			'select_field'=>array(
				'id'=>'button_mode',
				'type'=>'Select',
				'selected_shows'=>true,
				'title'=>'عنوان زرار البحث',
				'options'=>array(
					'Icon' => 'ايقونة البحث ',
					'Text'=>'نص',
				),
			),
			'create_fields'=>true,
			'choose_fields'=>array(
				'Text'=>array(
					'id'=>'Text',
					'title' => 'نص',
					'fields'=> array(
						array(
							'id'    => 'logo_Text',
							'type'  => 'Text',
							'title' => 'نص زرار البحث',
						),
					),
				)
			)
		),
		# social setteings
		array(
			'title'  => 'روابط التواصل الاجتماعي',
			'disc' => 'التحكم في روابط التواصل الاجتماعى الخاصة بالهيدر',
			'en_title'=> 'social setting header',
			'type'  => 'Title',
			'id'    => 'social_setting_header',
		),
		array(
			'title'  => 'إخفاء روابط التواصل الاجتماعي',
			'en_title'=> 'social_header',
			'type'  => 'SwitchBox',
			'id'    => 'hide_social_header',
		),
		array(
			'title'  => 'تحديد عناصر الاتصال المراد عرضها',
			'en_title'=> 'social_header_list',
			'type'  => 'CheckBox',
			'id'    => 'social_header_list',
			'options'=>array(
				'facebook'=>'facebook',
				'twitter'=>'twitter',
				'telegram'=>'telegram',
				'youtube'=>'youtube',
				'linkedin'=>'linkedin',
			)
		),

		array(
			'id'    => 'germkl',
			'type'  => 'Title',
			'title' => 'الجزء الخاص بأعدادات المدن',
		),
		array(
			'id'    => 'hide_city_bar',
			'type'  => 'SwitchBox',
			'title' => 'هل تريد اخفاء المدن مؤقتا',
		),
		array(
            'type'=> 'GroupsField',
            'en_title'=> 'city setting',
            'id'=> 'city_groub',
            'title'=> 'إعدادات المدن',
            'fields'=>array(
            	array(
					'title'  => 'إسم المدينة',
					'type'  => 'Text',
					'id'    => 'city_name',
				),
    			array(
					'title'  => 'رقم هاتف المدينه',
					'type'  => 'Number',
					'id'    => 'number_city',
				),
            )
        ),
    ),

);