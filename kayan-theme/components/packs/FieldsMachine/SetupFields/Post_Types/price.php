<?php 
$currency__items = array();
$currency__shows = get_option('currency__shows');
$currency__shows = ( ( is_array( $currency__shows ) ) ) ? $currency__shows : array();
foreach ( $currency__shows as $curr_item ) {
	$currency__items[ $curr_item['item__id'] ] = $curr_item['title'];
}
if( empty( $currency__items ) ) $currency__items = array('SAR'=>'ريال ','AED'=>'درهم','USD'=>'دولار');

$metaboxes['price__plans__metabox'] = array(
	'title'    => 'مميزات الخطة ',
	'fields' => array(

		array(
			'title'  => 'السعر',
			'en_title'=> 'Price',
			'type'=>'Text',
			'id'    => 'price_text',
		),
		array(
			'title'  => 'الاقسام',
			'en_title'=> 'Categories',
			'type'  => 'Taxonomy-Checkbox',
			'id'    => 'categories',
			'taxonomy_name'=>'category'
		),
		array(
			'title'  => 'عنوان الزر',
			'en_title'=> 'button Title',
			'type'=>'Text',
			'id'    => 'btn_title',
		),
		array(
			'title'  => 'نسبة الخصم',
			'en_title'=> 'offer',
			'type'=>'Number',
			'id'    => 'offer',
		),
        array(
            'title'  => 'العملة',
            'nameEN'=> 'Price',
            'type'  => 'Select',
            'id'    => 'price_icon',
            'options'=>$currency__items
        ), 
		array(
			'title'  => 'الخدمات المقدمه',
			'en_title'=> 'services',
			'type'  => 'GroupsField',
			'id'    => 'services_text',
			'fields' => array(
				array(
					'title'  => 'خدمه',
					'en_title'=> 'service',
					'type'=>'Text',
					'id'    => 'service_info',
				),
				array(
					'title'  => 'خدمة غير متاحة',
					'en_title'=> 'service',
					'type'=>'SwitchBox',
					'id'    => 'available',
				),
			)
		),


	)
);

##

$PlanesFilters = ( is_array( get_option('PlanesFilters') ) ) ? get_option('PlanesFilters') : array();
if( !empty( $PlanesFilters ) ){
	$metaboxes['PriceFilterFileds'] = array(
		'title'    => 'فلاتر خطط الاسعار ',
	);

	foreach ( $PlanesFilters as $e => $v_name) {
		$metaboxes['PriceFilterFileds']['fields'][] = array(
			'title'  => $v_name['title'],
			'type'  => 'SwitchBox',
			'id'    => 'metafilter_'.$v_name['id'],
		);
	}
}