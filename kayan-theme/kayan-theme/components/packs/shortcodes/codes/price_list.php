<?php 
class price_list_short_code{
	function __construct($argument=array() ){
		
	}

	public function ShortCodeAppend(){
		global $post;
		
		$hide_price_list__section = get_post_meta( $post->ID,'hide_price_list__section',true );
		if( !empty( $hide_price_list__section ) ) return ;

		$post__price_list__data = get_post_meta( $post->ID,'post__price_list__data',true );
		$post__price_list__data = ( ( is_array( $post__price_list__data ) ) ) ? $post__price_list__data : array();
		if( empty( $post__price_list__data ) ) return ;

		# SHORTCODE OUTPUT .
     		ob_start();
				echo '<div class="yc-shortcode--box yc-shortcode--price_list">';

					echo ( ( isset( $post__price_list__data['price_list__title'] ) && !empty( $post__price_list__data['price_list__title'] ) ) ) ? '<h2 class="--short--code--title">'.$post__price_list__data['price_list__title'].'</h2>' : '';
					
					echo ( ( isset( $post__price_list__data['price_list__content'] ) && !empty( $post__price_list__data['price_list__content'] ) ) ) ? '<p class="--short--code--content">'.$post__price_list__data['price_list__content'].'</p>' : '';

					if( isset( $post__price_list__data['price_list__items'] ) && !empty( $post__price_list__data['price_list__items'] ) ){
						$post__price_list__data['price_list__items'] = ( ( is_array( $post__price_list__data['price_list__items'] ) ) ) ? $post__price_list__data['price_list__items'] : array();

						$price_list__table_title1 = ( ( isset( $post__price_list__data['price_list__table_title1'] ) && !empty( $post__price_list__data['price_list__table_title1'] ) ) ) ? $post__price_list__data['price_list__table_title1'] : 'الخدمة';
						$price_list__table_title2 = ( ( isset( $post__price_list__data['price_list__table_title2'] ) && !empty( $post__price_list__data['price_list__table_title2'] ) ) ) ? $post__price_list__data['price_list__table_title2'] : 'القيمة';

						echo '<div class="yc-shortcode--price_list--items">';

				           	echo '<table class="price-table">';
				        		echo '<thead>';
			            			echo '<tr>';
			                			echo '<th style="width:50%">'.$price_list__table_title1.'</th>';
			            				echo '<th style="width:50%">'.$price_list__table_title2.'</th>';
			            			echo '</tr>';
			            		echo '</thead>';

				            	echo '<tbody>';	            
						            foreach ($post__price_list__data['price_list__items'] as $tr) {
						                echo '<tr>';
							                echo '<td>'.$tr['title'].'</td>';
							                echo '<td>'.$tr['value'].'</td>';
						                echo '</tr>';
						            }
			            		echo '</tbody>';

			            	echo '</table>';

						echo '</div>';

					}

				echo '</div>';

      		$content = ob_get_clean();

	    return $content;
	}

	public function ShortCode__insert_field(){
		global $YC__CFM__global_setup_fields;

		$YC__CFM__global_setup_fields['Post_Types']['yourcolor__price_list_short_code'] = array(
			'title'=>  'شريحة جدول الاسعار',
			'ObjectType'=>'post',
			'id'=>'yourcolor__price_list_short_code',
			'ObjectType'=>array('post','page'),
            'context' => 'normal',
            'priority' => 'high',
            'MetaBox__Action'=>'fields_metabox',
			'fields'=>array(

                array(
                    'id'=> 'title_price_list_short_code',
                    'type'=>'Title',
                    'title'=>'قم باضافة المميزات في المكان المراد اضافته ف المحتوى ',
                    'disc'=>'انسخ هذا الكود   <code data-copy-action="[post_prices]"><input type="hidden" value="[post_prices]">[post_prices]</code>'
                ),

                array(
                    'id'=> 'hide_price_list__section',
                    'type'=>'SwitchBox',
                    'title'=>'إخفاء المميزات من المحتوى',
                ),

				array(
					'title'  =>'إعدادات المميزات',
					'type'  => 'SingleGroup',
					'id'    => 'post__price_list__data',
					'is__open'=>true,
					'fields'=> array(
		                array(
		                    'id'=> 'price_list__title',
		                    'type'=>'Text',
		                    'title'=>'عنوان الجدول',
		                ),
		                array(
		                    'id'=> 'price_list__content',
		                    'type'=>'TextArea',
		                    'title'=> 'وصف الجدول',
		                ),

		                array(
		                    'id'=> 'price_exptes__title',
		                    'type'=>'Title',
		                    'title'=>'إعدادات رأس الجدول',
		                ),

		                array(
		                    'id'=> 'price_list__table_title1',
		                    'type'=>'Text',
		                    'title'=>'عنوان الخدمة',
		                ),
		                array(
		                    'id'=> 'price_list__table_title2',
		                    'type'=>'Text',
		                    'title'=> 'عنوان القيمة',
		                ),

				        array(
				            'title'=>'إضافة عناصر الجدول',
				            'id'=> 'price_list__items',
				            'type'=>'GroupsField',
				            'fields'=>array(
				                array(
				                    'id'=> 'title',
				                    'type'=>'Text',
				                    'title'=>'الخدمة',
				                ),
				                array(
				                    'id'=> 'value',
				                    'type'=>'Text',
				                    'title'=> 'القيمة',
				                ),		                
				            ),
				        ),
					)
				)
			)
		);
	}

	public function Setup(){
		add_action( 'YC__CFM__global_setup_fields',array($this,'ShortCode__insert_field'),1 );
		add_action('init',function(){			
			add_shortcode( 'post_prices', array($this,'ShortCodeAppend'));
		});		
	}
}
(new price_list_short_code)->Setup();