<?php 
class features_short_code{
	function __construct($argument=array() ){
		
	}

	public function ShortCodeAppend(){
  		global $post;

		$hide_features__section = get_post_meta( $post->ID,'hide_features__section',true );
		if( !empty( $hide_features__section ) ) return ;

		$post__features__data = get_post_meta( $post->ID,'post__features__data',true );
		$post__features__data = ( ( is_array( $post__features__data ) ) ) ? $post__features__data : array();
		if( empty( $post__features__data ) ) return ;

		# SHORTCODE OUTPUT .
     		ob_start();
				echo '<div class="yc-shortcode--box yc-shortcode--features">';

					echo ( ( isset( $post__features__data['features__title'] ) && !empty( $post__features__data['features__title'] ) ) ) ? '<h2 class="--short--code--title">'.$post__features__data['features__title'].'</h2>' : '';
					
					echo ( ( isset( $post__features__data['features__content'] ) && !empty( $post__features__data['features__content'] ) ) ) ? '<p class="--short--code--content">'.$post__features__data['features__content'].'</p>' : '';

					if( isset( $post__features__data['yourcolor__post_features'] ) && !empty( $post__features__data['yourcolor__post_features'] ) ){
						$post__features__data['yourcolor__post_features'] = ( ( is_array( $post__features__data['yourcolor__post_features'] ) ) ) ? $post__features__data['yourcolor__post_features'] : array();

						echo '<div class="yc-shortcode--features--items">';
							foreach ( $post__features__data['yourcolor__post_features'] as $post_features ) {

								echo '<div class="yc-shortcode--single-features-item">';

									if( isset( $post_features['icon'] ) && !empty( $post_features['icon'] ) ){
										echo '<div class="yc-shortcode-features--icon">'.$post_features['icon'].'</div>';
									}

									echo '<div class="yc-shortcode--step--info">';
										echo ( ( isset( $post_features['title'] ) && !empty( $post_features['title'] ) ) ) ? '<h3>'.$post_features['title'].'</h3>' : '';
										echo ( ( isset( $post_features['content'] ) && !empty( $post_features['content'] ) ) ) ? '<p>'.$post_features['content'].'</p>' : '';
									echo '</div>';

						        echo '</div>';
							}

						echo '</div>';

					}

				echo '</div>';

      		$content = ob_get_clean();

	    return $content;	  	
	}

	public function ShortCode__insert_field(){
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
		add_action( 'YC__CFM__global_setup_fields',array($this,'ShortCode__insert_field'),1 );

		add_action('init',function(){			
			add_shortcode( 'post_features', array($this,'ShortCodeAppend'));
		});
	}
}
(new features_short_code)->Setup();