<?php 
$metaboxes['CategoryOptions'] = array(
	'title'    => 'إعدادات التصنيف',
	'number'=>1,
	'fields'  => array(
		array(
			'title'  => 'الايقونة',
			'type'  => 'TextArea_Code',
			'id'    => 'icon',
		),
		array(
			'title'  => 'صورة التصنيف',
			'type'  => 'File',
			'id'    => 'Image-Icon',
		),		
		array(
			'title'  => 'تثبيت الصنيف',
			'type'  => 'SwitchBox',
			'id'    => 'pin_category',
		),
		array(
			'title'  => 'اضافة الى sitemap',
			'type'  => 'SwitchBox',
			'id'    => 'pin_sitemap',
		),
		array(
			'title'  => 'سعر الخدمة',
			'type'  => 'Text',
			'id'    => 'Service_price',
		),	
		array(
			'type'=>'Text',
			'id'=>'but_text',
			'title'=>'عنوان زر التصنيف',
		),
		array(
			'type'=>'SwitchBox',
			'id'=>'hide_category_switch',
			'title'=>'هل تريد اخفاء الزر موقتا',
		),	
	),
);
$metaboxes['CategoryVideo'] = array(
	'title'    => 'إعدادات التصنيف',
	'number'=>2,
	'fields'  => array(
		array(
			'title'  => 'ID الفيديو ',
			'type'  => 'Text',
			'id'    => 'videoID',
		),
	),
);
$metaboxes['CategoryCountry'] = array(
	'title'    => 'إعدادات المدن ',
	'number'=>3,
	'fields'  => array(
		array(
			'title'  => 'تحديد مدن الخدمة ',
			'type'  => 'Taxonomy-CheckBox',
			'id'    => 'country',
			'taxonomy_name'    => 'city',
		),
	),
);

$metaboxes['pages__category_edits'] = array(
	'title'=>'صفحات القائمة الجانبية',
	'fields'=>array(
		array(
			'type'=>'GroupsField',
			'id' => 'Pages__List__URL',
			'title' =>'اختر الصفحات المراد عرضها ',
			'disc'=>'في حالة تحديدها من صفحة المقالة يتم اظهار الصفحات المختارة في المقالة',
			'fields'=> array(
	            array(
	                'type'=>'Posts-Select',
	                'id' => 'button_page',
	                'post_type_name'=>'page',
	                'title' =>'تحديد الصفحة',
	            ),
	            array(
	                'type'=>'Text',
	                'id' => 'button_Text',
	                'title' =>'إضافة عنوان اخر للصفحة',
	            ),
				array(
					'type'=>'TextArea_Code',
					'id'=>'button_Icon',
					'title'=>'ايقونة',
				),
				array(
					'type'=>'Text',
					'id'=>'number',
					'title'=>'رقم الترتيب ',
					'require'=>true,
				),
			)
		)
	)
);

$insert_fields = array(
	array(
		'title'  => 'التقييم الافتراضي',
		'type'  => 'Number',
		'id'    => 'ratingValue',
	),
);
for ($i=1; $i < 6; $i++) { 
	$insert_fields[] = array(
		'title'  => 'اجمالى عدد الاشخاص المختارة ('.$i.')',
		'type'  => 'Number',
		'id'    => 'ratingUsers_'.$i,
	);
}

$metaboxes['rating__category_edits'] = array(
	'title'=>'التقيمات',
	'fields'=>array(
		array(
			'title'  =>'إعدادات التقييم الافتراضية',
			'type'  => 'SingleGroup',
			'id'    => 'defualt__rating',
			'is__open'=>true,
			'fields'=> $insert_fields
		)
	)
);

$TaxonomyesObject = get_all_taxonomies();
foreach ($TaxonomyesObject as $s => $v) {
	$TaxonomyesObject[$s] = str_replace(['_','-'], ' ', $v);
}

$metaboxes['poopover__categories'] = array(
	'title'=>'التقيمات',
	'fields'=>array(
		array(
			'type'=>'DuplicateGroup',
			'id' => 'service_steps',
			'title' =>'اعدادات  نموذج  الاتصال  لكل تصينف علي حدي',
			'fields'=> array(
				array(
					'type'=>'Text',
					'id' => 'title',
					'title'=>'عنوان النموذج ',
					'require'=>true,
				),
				array(
					'type'=>'GroupsField',
					'id'=>'fields',
					'title'=>'اداة إنشاء النماذج',
					'fields'=>array(
						array(
							'type'=>'Text',
							'id' => 'title',
							'title'=>'عنوان العنصر',
						),
						array(
							'type'=>'Text',
							'id' => 'id',
							'title'=>'معرف الفيلد ',
							'disc'=>'يجب ان يكون باللغة الانجليزية'
						),
						array(
							'type'=>'Text',
							'id' => 'disc',
							'title'=>'وصف مختصر ',
						),
						array(
							'type'=>'Radio',
							'id'=>'type',
							'title'=>'تحديد نوع الادخال ',
							'value'=>'Text',
							'options'=>array(
								'Text' => 'Text',
								'TextArea'=>'TextArea',
								'Select'=>'Select',
								'CheckBox'=>'CheckBox',
								'Radio'=>'Radio',
								'SwitchBox'=>'SwitchBox',
								'Taxonomy-Radio'=> 'Taxonomy Radio',
								'Phone-Number'=>'Phone Number',
							),
							'require'=>true,
							'Custom_says' => true,
							'shows_selected' => true,
							'show_create_fields'=>array(
								array(
									'type'=>'TextArea',
									'id' => 'options',
									'title'=>'إضافة إختيارات',
									'disc' =>'إضافة اختيارات  .. قم بكتابة الاختيار ثم اضغط ENTER واضف خيارً اخر وهكذا ',
									'shows_At'=>array('Select','CheckBox','Radio'),
								),

								array(
									'type'=>'Select',
									'id' => 'taxonomy_name',
									'title' =>'تحديد نوع الـ TAXONOMY',
									'options'=>$TaxonomyesObject,
									'shows_At'=>array('Taxonomy-Select','Taxonomy-CheckBox','Taxonomy-Radio'),
								)

							),
						),
						array(
							'type'=>'SwitchBox',
							'id'=>'Require',
							'title'=>'إجباري  ?',
							'disc'=>'هل تريد  تعيين هذا العنصر اجباريا لاستكمال الخطوة التالية ؟',
						)

					)
				),
				array(
					'type'=>'Text',
					'id' => 'button_title',
					'title'=>'عنوان زرار الSUBMIT',
					'require'=>true,
				),
				array(
					'type'=>'TextArea_Code',
					'id' => 'button_icon',
					'title'=>'ايقونة زرار الSUBMIT',
					'require'=>true,
				)				
			)
		),
	)
);



