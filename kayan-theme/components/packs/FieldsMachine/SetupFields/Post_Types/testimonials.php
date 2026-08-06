<?php 
$metaboxes['testimonials__metabox'] = array(
	'title'    => 'بيانات التقييم',
	'fields' => array(
        array(
            'id'=> 'client_name',
            'type'=>'Text',
            'title'=>'اسم العميل',
        ),
        array(
            'id'=> 'client_city',
            'type'=>'Text',
            'title'=>'مدينة العميل',
        ),
        array(
            'id'=> 'rating',
            'type'=>'Number',
            'title'=>'التقييم (من 1 إلى 5)',
            'max'=>'5',
        ),
	)
);
