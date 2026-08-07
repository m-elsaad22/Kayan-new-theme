<?php  /**
 * 
 */
class Schema__LocalBusiness extends YourColor__Schema {
	
	function __construct($arguments=array()){
		
	}

	public function get(){
			$YourColoe_Schema_business = get_option('YourColoe_Schema_business');
			$YourColoe_Schema_business = ( is_array( $YourColoe_Schema_business ) ) ? $YourColoe_Schema_business : array();
			if( isset( $YourColoe_Schema_business['hide_schema_business'] ) && empty( $YourColoe_Schema_business['hide_schema_business'] ) ) {

		        echo '<script type="application/ld+json">';
			        echo '{';
			          	echo '"@context": "http://schema.org",';
			          	echo '"@type": "LocalBusiness",';
			          	echo '"name": "'.( ( isset( $YourColoe_Schema_business['Business_Name'] ) && !empty( $YourColoe_Schema_business['Business_Name'] ) ) ? $YourColoe_Schema_business['Business_Name'] : '' ).'",';
			          	echo '"description": "'.( ( isset( $YourColoe_Schema_business['description'] ) && !empty( $YourColoe_Schema_business['description'] ) ) ? $YourColoe_Schema_business['description'] : '' ).'",';
			          	echo '"address": {';
				            echo '"@type": "PostalAddress",';
				            echo '"streetAddress": "'.( ( isset( $YourColoe_Schema_business['Street_Address'] ) && !empty( $YourColoe_Schema_business['Street_Address'] ) ) ? $YourColoe_Schema_business['Street_Address'] : '' ).'",';
				            echo '"addressLocality": "'.( ( isset( $YourColoe_Schema_business['City'] ) && !empty( $YourColoe_Schema_business['City'] ) ) ? $YourColoe_Schema_business['City'] : '' ).'",';
				            echo '"addressRegion": "'.( ( isset( $YourColoe_Schema_business['State'] ) && !empty( $YourColoe_Schema_business['State'] ) ) ? $YourColoe_Schema_business['State'] : '' ).'",';
				            echo '"postalCode": "'.( ( isset( $YourColoe_Schema_business['Postal_Code'] ) && !empty( $YourColoe_Schema_business['Postal_Code'] ) ) ? $YourColoe_Schema_business['Postal_Code'] : '' ).'",';
				            echo '"addressCountry": "'.( ( isset( $YourColoe_Schema_business['Country'] ) && !empty( $YourColoe_Schema_business['Country'] ) ) ? $YourColoe_Schema_business['Country'] : '' ).'"';
			          	echo '},';
			          	echo '"telephone": "'.( ( isset( $YourColoe_Schema_business['telephone'] ) && !empty( $YourColoe_Schema_business['telephone'] ) ) ? $YourColoe_Schema_business['telephone'] : '' ).'",';
			            echo '"url": "'.home_url().'",';
			            echo '"image": "'.( ( isset( $YourColoe_Schema_business['image'] ) && !empty( $YourColoe_Schema_business['image'] ) ) ? $YourColoe_Schema_business['image'] : '' ).'",';
			            echo '"openingHours": "'.( ( isset( $YourColoe_Schema_business['openingHours'] ) && !empty( $YourColoe_Schema_business['openingHours'] ) ) ? $YourColoe_Schema_business['openingHours'] : '' ).'",';
			            echo '"priceRange": "'.( ( isset( $YourColoe_Schema_business['Price_Range'] ) && !empty( $YourColoe_Schema_business['Price_Range'] ) ) ? $YourColoe_Schema_business['Price_Range'] : '' ).'",';
			          	echo '"socialMedia": {';
				            echo '"Facebook": "'.( ( isset( $YourColoe_Schema_business['Facebook'] ) && !empty( $YourColoe_Schema_business['Facebook'] ) ) ? $YourColoe_Schema_business['Facebook'] : '' ).'",';
				            echo '"Twitter": "'.( ( isset( $YourColoe_Schema_business['Twitter'] ) && !empty( $YourColoe_Schema_business['Twitter'] ) ) ? $YourColoe_Schema_business['Twitter'] : '' ).'",';
				            echo '"Instagram": "'.( ( isset( $YourColoe_Schema_business['Instagram'] ) && !empty( $YourColoe_Schema_business['Instagram'] ) ) ? $YourColoe_Schema_business['Instagram'] : '' ).'",';
				            echo '"Pinterest": "'.( ( isset( $YourColoe_Schema_business['Pinterest'] ) && !empty( $YourColoe_Schema_business['Pinterest'] ) ) ? $YourColoe_Schema_business['Pinterest'] : '' ).'",';
				            echo '"Linkedin": "'.( ( isset( $YourColoe_Schema_business['Linkedin'] ) && !empty( $YourColoe_Schema_business['Linkedin'] ) ) ? $YourColoe_Schema_business['Linkedin'] : '' ).'",';
				            echo '"Soundcloud": "'.( ( isset( $YourColoe_Schema_business['Soundcloud'] ) && !empty( $YourColoe_Schema_business['Soundcloud'] ) ) ? $YourColoe_Schema_business['Soundcloud'] : '' ).'",';
				            echo '"Tumblr": "'.( ( isset( $YourColoe_Schema_business['Tumblr'] ) && !empty( $YourColoe_Schema_business['Tumblr'] ) ) ? $YourColoe_Schema_business['Tumblr'] : '' ).'",';
				            echo '"Youtube": "'.( ( isset( $YourColoe_Schema_business['Youtube'] ) && !empty( $YourColoe_Schema_business['Youtube'] ) ) ? $YourColoe_Schema_business['Youtube'] : '' ).'";';
			          	echo '},';
			          	echo '"aggregateRating": {';
				            echo '"@type": "AggregateRating",';
				            echo '"ratingValue": "'.( ( isset( $YourColoe_Schema_business['ratingValue'] ) && !empty( $YourColoe_Schema_business['ratingValue'] ) ) ? $YourColoe_Schema_business['ratingValue'] : '' ).'",';
				            echo '"reviewCount": "'.( ( isset( $YourColoe_Schema_business['Rating_Count'] ) && !empty( $YourColoe_Schema_business['Rating_Count'] ) ) ? $YourColoe_Schema_business['Rating_Count'] : '' ).'"';
			          	echo '},';
			          	echo '"website": "'.home_url().'",';
			          	echo '"serviceOffered": {';
				            echo '"@type": "Service",';
				            echo '"url": "'.home_url().'",';
				            echo '"name": "'.( ( isset( $YourColoe_Schema_business['Service_Offered_Name'] ) && !empty( $YourColoe_Schema_business['Service_Offered_Name'] ) ) ? $YourColoe_Schema_business['Service_Offered_Name'] : '' ).'"';
			          	echo '},';
			          	echo '"operationDays": "'.( ( isset( $YourColoe_Schema_business['Operation_Days'] ) && !empty( $YourColoe_Schema_business['Operation_Days'] ) ) ? $YourColoe_Schema_business['Operation_Days'] : '' ).'"';
			        echo '}';
		        echo '</script>';

			}
	}

	public function insert__schema__fields(){
		global $YC__CFM__global_setup_fields;

		$YC__CFM__global_setup_fields['Post_Types']['yourcolor__features_short_code'] = array(
			'title'=> 'المميزات داخل المحتوي',
			'id'=>'yourcolor__features_short_code',
			'ObjectType'=>array('post','page'),
            'context' => 'normal',
            'priority' => 'high',
            'MetaBox__Action'=>'fields_metabox',
			'fields'=>array(
                array(
                    'id'=> 'title_features_short_code',
                    'type'=>'Title',
                    'title'=>'قم باضافة المميزات في المكان المراد اضافته ف المحتوى ',
                    'disc'=>'انسخ هذا الكود   <code data-copy-action="[post_features]"><input type="hidden" value="[post_features]">[post_features]</code>'
                ),
                array(
                    'id'=> 'hide_features__section',
                    'type'=>'SwitchBox',
                    'title'=>'إخفاء المميزات من المحتوى',
                ),
				array(
					'title'  =>'إعدادات المميزات',
					'type'  => 'SingleGroup',
					'id'    => 'post__features__data',
					'is__open'=>true,
					'fields'=> array(
		                array(
		                    'id'=> 'features__title',
		                    'type'=>'Text',
		                    'title'=>'عنوان الشريحة',
		                ),
		                array(
		                    'id'=> 'features__content',
		                    'type'=>'TextArea',
		                    'title'=> 'وصف الشريحة',
		                ),
				        array(
				            'title'=>'المميزات',
				            'id'=> 'yourcolor__post_features',
				            'type'=>'GroupsField',
				            'fields'=>array(
				                array(
				                    'id'=> 'title',
				                    'type'=>'Text',
				                    'title'=>'العنوان',
				                ),
				                array(
				                    'id'=>  'content',
				                    'type'=>'TextArea',
				                    'title'=> 'المحتوي',
				                ),
				                array(
				                    'id'=> 'icon',
				                    'type'=>'TextArea_Code',
				                    'title'=>'الايقونة',
				                )		                
				            ),
				        )
					)
				)

			)
		);
	}



	public function Setup(){
		add_action( 'YC__CFM__global_setup_fields',array($this,'insert__schema__fields'),1 );
	}


}