<?php 

$metaboxes = array(
    'title'    => 'إعدادات SITEMAP',
    'en_title' => 'SITEMAP Options',
    'icon'     => '<i class="fa-solid fa-sitemap"></i>',
    'number'   => 20,
    'fields'   => array(
    	# CATEGORY #
	        array(
	            'type'=>'Title',
	            'id' => 'category__mapItems_Justtitle',
	            'title' =>'إعدادات شريحة الاقسام',
	        ),

	        array(
	            'type'=>'Text',
	            'id' => 'category__mapItems_title',
	            'title' =>'عنوان الاقسام',
	        ),

			array(
				'type'=>'Compo-Select-Field',
				'id'=>'category__mapItems_list',
				'title'=>'تحديد قائمة الاقسام',
				'object__type'=>'taxonomy',
				'object__name'=>'category',
				'show__perview__items'=>true,
				'per'=>5,
				'multiple'=>true,
			),

	        array(
	            'type'=>'Text',
	            'id' => 'category__mapItems_sort',
	            'title' =>'الترتيب',
	        ),

        # COUNTRY #
	        array(
	            'type'=>'Title',
	            'id' => 'country__mapItems_Justtitle',
	            'title' =>'إعدادات شريحة المدن',
	        ),

	        array(
	            'type'=>'Text',
	            'id' => 'country__mapItems_title',
	            'title' =>'عنوان المدن',
	        ),
			array(
				'type'=>'Compo-Select-Field',
				'id'=>'country__mapItems_list',
				'title'=>'تحديد قائمة المدن',
				'object__type'=>'taxonomy',
				'object__name'=>'city',
				'show__perview__items'=>true,
				'per'=>5,
				'multiple'=>true,
			),
	        array(
	            'type'=>'Text',
	            'id' => 'country__mapItems_sort',
	            'title' =>'الترتيب',
	        ),

        # POSTS #
	        array(
	            'type'=>'Title',
	            'id' => 'post__mapItems_Justtitle',
	            'title' =>'إعدادات شريحة المقالات',
	        ),

	        array(
	            'type'=>'Text',
	            'id' => 'post__mapItems_title',
	            'title' =>'عنوان المقالات',
	        ),
			array(
				'type'=>'Compo-Select-Field',
				'id'=>'post__mapItems_list',
				'title'=>'تحديد قائمة المقالات',
				'object__type'=>'posts',
				'object__name'=>'post',
				'show__perview__items'=>true,
				'per'=>5,
				'multiple'=>true,
			),
	        array(
	            'type'=>'Text',
	            'id' => 'post__mapItems_sort',
	            'title' =>'الترتيب',
	        ),
        # PAGES #
	        array(
	            'type'=>'Title',
	            'id' => 'page__mapItems_Justtitle',
	            'title' =>'إعدادات شريحة الصفحات',
	        ),

	        array(
	            'type'=>'Text',
	            'id' => 'page__mapItems_title',
	            'title' =>'عنوان الصفحات',
	        ),
			array(
				'type'=>'Compo-Select-Field',
				'id'=>'page__mapItems_list',
				'title'=>'تحديد قائمة الصفحات',
				'object__type'=>'posts',
				'object__name'=>'page',
				'show__perview__items'=>true,
				'per'=>5,
				'multiple'=>true,
			),			
	        array(
	            'type'=>'Text',
	            'id' => 'page__mapItems_sort',
	            'title' =>'الترتيب',
	        ),
    ),
);
