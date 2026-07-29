<?php 
$metaboxes = array(
	'title'    => 'schema ',
	'en_title'  => 'schema',
	'icon'    => '<i class="fa-solid fa-circle"></i>',
	'hide__border'=>true,
	'number'=>11,
	'disc'=>'اعدادات شكل الصفحة الرئيسية ',
	'fields'  => array(

		array(
			'title'  => 'شعار الاسكيما',
			'titleEN'=> 'disable local',
			'type'  => 'File',
			'id'    => 'logo__schema',
		),
		array(
			'title'  => 'اسم الموقع',
			'titleEN'=> 'disable local',
			'type'  => 'Text',
			'id'    => 'sitename__schema',
		),

		array(
			'title'  => 'أغلاق اسكيما القالب',
			'titleEN'=> 'disable local',
			'type'  => 'SwitchBox',
			'id'    => 'validate__schema',
		),
		array(
			'title'  => 'YourColoe Schema business',
			'titleEN'=> 'disable local',
			'type'  => 'SingleGroup',
			'id'    => 'YourColor_Schema_business',
			'is__open'=>true,
			'fields'=> array(
				array(
					'title'  => 'Hide Schema business',
					'titleEN'=> 'disable local',
					'type'  => 'SwitchBox',
					'id'    => 'hide_schema_business',
				),
				array(
					'title'  => 'Business_Name',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'Business_Name',
				),
				array(
					'title'  => 'description',
					'titleEN'=> 'disable local',
					'type'  => 'TextArea',
					'id'    => 'description',
				),
				array(
					'title'  => 'Street Address',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'Street_Address',
				),
				array(
					'title'  => 'Country',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'Country',
				),
				array(
					'title'  => 'City',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'City',
				),
				array(
					'title'  => 'State',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'State',
				),
				array(
					'title'  => 'Postal_Code',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'Postal_Code',
				),
				array(
					'title'  => 'telephone',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'telephone',
				),		
				array(
					'title'  => 'opening Hours',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'openingHours',
				),
				array(
					'title'  => 'Price Range',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'Price_Range',
				),
				array(
					'title'  => 'Facebook',
					'titleEN'=> 'Facebook',
					'type'  => 'Text',
					'id'    => 'Facebook',
				),
				array(
					'title'  => 'Twitter',
					'titleEN'=> 'Twitter',
					'type'  => 'Text',
					'id'    => 'Twitter',
				),
				array(
					'title'  => 'Instagram',
					'titleEN'=> 'Instagram',
					'type'  => 'Text',
					'id'    => 'Instagram',
				),
				array(
					'title'  => 'Pinterest',
					'titleEN'=> 'Pinterest',
					'type'  => 'Text',
					'id'    => 'Pinterest',
				),
				array(
					'title'  => 'Linkedin',
					'titleEN'=> 'Linkedin',
					'type'  => 'Text',
					'id'    => 'Linkedin',
				),
				array(
					'title'  => 'Soundcloud',
					'titleEN'=> 'Soundcloud',
					'type'  => 'Text',
					'id'    => 'Soundcloud',
				),
				array(
					'title'  => 'Tumblr',
					'titleEN'=> 'Tumblr',
					'type'  => 'Text',
					'id'    => 'Tumblr',
				),
				array(
					'title'  => 'Youtube',
					'titleEN'=> 'Youtube',
					'type'  => 'Text',
					'id'    => 'Youtube',
				),
				array(
					'title'  => 'ratingValue',
					'titleEN'=> 'ratingValue',
					'type'  => 'Text',
					'id'    => 'ratingValue',
				),
				array(
					'title'  => 'Rating_Count',
					'titleEN'=> 'Rating_Count',
					'type'  => 'Text',
					'id'    => 'Rating_Count',
				),
				array(
					'title'  => 'Service Offered Name',
					'titleEN'=> 'Service_Offered_Name',
					'type'  => 'Text',
					'id'    => 'Service_Offered_Name',
				),			
				array(
					'title'  => 'Operation_Days',
					'titleEN'=> 'Operation_Days',
					'type'  => 'Text',
					'id'    => 'Operation_Days',
				)
			),
		),

		array(
			'title'  => 'Hide Schema ImageObject',
			'titleEN'=> 'disable local',
			'type'  => 'SwitchBox',
			'id'    => 'hide_schema_ImageObject',
		),

		array(
			'title'  => 'YourColoe Schema ImageObject',
			'titleEN'=> 'disable local',
			'type'  => 'SingleGroup',
			'id'    => 'YourColor_ImageObject',
			'fields'=> array(
				array(
					'title'  => 'الوصف الافتراضي',
					'titleEN'=> 'disable local',
					'type'  => 'TextArea',
					'id'    => 'description',
				),
				array(
					'title'  => 'قيمة contentLocation الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'contentLocation',
				)
			)
		),


		array(
			'title'  => 'Hide Schema Service',
			'titleEN'=> 'disable local',
			'type'  => 'SwitchBox',
			'id'    => 'hide_schema_Service',
		),

		array(
			'title'  => 'YourColoe Schema Service',
			'titleEN'=> 'disable local',
			'type'  => 'SingleGroup',
			'id'    => 'YourColor_Service',
			'fields'=> array(

				array(
					'title'  => 'قيمة priceRange الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'priceRange',
				),
				array(
					'title'  => 'قيمة description الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'TextArea',
					'id'    => 'description',
				),
				array(
					'title'  => 'قيمة addressLocality الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'addressLocality',
				),
				array(
					'title'  => 'قيمة postalCode الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'postalCode',
				),
				array(
					'title'  => 'قيمة telephone الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'telephone',
				),
				array(
					'title'  => 'قيمة addressCountry الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'addressCountry',
				),
				array(
					'title'  => 'قيمة streetAddress الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'streetAddress',
				),
				array(
					'title'  => 'قيمة addressRegion الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'addressRegion',
				),
				array(
					'title'  => 'قيمة areaServed الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'areaServed',
				),
				array(
					'title'  => 'قيمة OfferCatalog الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'OfferCatalog',
				),
				array(
					'title'  => 'قيمة identifier الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'identifier',
				),
				array(
					'title'  => 'قيمة additionalType الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'additionalType',
				),
				array(
					'title'  => 'قيمة ratingValue الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'ratingValue',
				),
				array(
					'title'  => 'قيمة reviewCount الافتراضية',
					'titleEN'=> 'disable local',
					'type'  => 'Text',
					'id'    => 'reviewCount',
				),
			)
		),


		array(
			'title'  => 'Hide Schema Article',
			'titleEN'=> 'disable local',
			'type'  => 'SwitchBox',
			'id'    => 'hide_schema_Article',
		),

		array(
			'title'  => 'YourColoe Schema Article',
			'titleEN'=> 'disable local',
			'type'  => 'SingleGroup',
			'id'    => 'YourColor_Article',
			'fields'=> array(
				array(
					'title'  => 'قيمة headline الافتراضية ',
					'titleEN'=> 'disable local',
					'type'  => 'TextArea',
					'id'    => 'headline',
				),
				array(
					'title'  => 'قيمة description الافتراضية ',
					'titleEN'=> 'disable local',
					'type'  => 'TextArea',
					'id'    => 'description',
				),
				array(
					'title'  => 'قيمة articleBody الافتراضية ',
					'titleEN'=> 'disable local',
					'type'  => 'TextArea',
					'id'    => 'articleBody',
				)
			)
		),
		## Schema Rating
		array(
			'title'  => 'Hide Schema Rating',
			'titleEN'=> 'disable local',
			'type'  => 'SwitchBox',
			'id'    => 'hide_schema_Rating',
		),

		array(
			'title'  => 'YourColoe Schema Rating',
			'titleEN'=> 'disable local',
			'type'  => 'SingleGroup',
			'id'    => 'YourColor_Rating',
			'fields'=> array(
				array(
					'title'  => 'Rating Value(تقيم المقال الافتراضي )',
					'titleEN'=> 'Rating Value',
					'type'  => 'Number',
					'id'    => 'RatingValue_def',
				),
				array(
					'title'  => 'Best Rating (أفضل تقيم)',
					'titleEN'=> 'Best Rating',
					'type'  => 'Number',
					'id'    => 'Best_Rating_def',
				),
				array(
					'title'  => 'Rating Count(عدد التقيمات)',
					'titleEN'=> 'Rating Count',
					'type'  => 'Number',
					'id'    => 'RatingCount_def',
				),
				
			)
		),
		##
		array(
			'title'  => 'Hide Schema FAQPage',
			'titleEN'=> 'disable local',
			'type'  => 'SwitchBox',
			'id'    => 'hide_schema_faqs',
		),
		array(
			'title'  => 'YourColoe Hide Schema websites',
			'titleEN'=> 'Hide Schema websites',
			'type'  => 'SingleGroup',
			'id'    => 'YourColor_Schema_websites',
			'fields'=> array(
				array(
					'title'  => 'Hide Schema websites',
					'titleEN'=> 'Hide Schema websites',
					'type'  => 'SwitchBox',
					'id'    => 'hide_schema_websites',
				),
				
			)
		),
		


	)
);