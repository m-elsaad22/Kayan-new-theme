<?php 
class post_services_short_code{
	function __construct($argument=array() ){
		
	}

	public function ShortCodeAppend(){
		global $post;

		$hide_services_section = get_post_meta( $post->ID,'hide_services_section',true );
		if( !empty( $hide_services_section ) ) return ;

		$post__services__data = get_post_meta( $post->ID,'post__services__data',true );
		$post__services__data = ( ( is_array( $post__services__data ) ) ) ? $post__services__data : array();
		if( empty( $post__services__data ) ) return ;

		# SHORTCODE OUTPUT .
     		ob_start();
				echo '<div class="yc-shortcode--box yc-shortcode--post-services">';

					echo ( ( isset( $post__services__data['services__title'] ) && !empty( $post__services__data['services__title'] ) ) ) ? '<h2 class="--short--code--title --short--code--title">'.$post__services__data['services__title'].'</h2>' : '';
					
					echo ( ( isset( $post__services__data['services__content'] ) && !empty( $post__services__data['services__content'] ) ) ) ? '<p class="--short--code--content">'.$post__services__data['services__content'].'</p>' : '';

					if( isset( $post__services__data['post_services_items'] ) && !empty( $post__services__data['post_services_items'] ) ){
						$post__services__data['post_services_items'] = ( ( is_array( $post__services__data['post_services_items'] ) ) ) ? $post__services__data['post_services_items'] : array();
						echo '<div class="yc-shortcode--services--items">';
							foreach ( $post__services__data['post_services_items'] as $step__item ) {

								echo '<div class="yc-shortcode--single-services-item">';

									if( isset( $step__item['image_id'] ) && !empty( $step__item['image_id'] ) ){
										echo '<div class="yc-shortcode-step--image">';
									        echo YC_get_attachment(
									        	array(
										            'id'=>$step__item['image_id'],
										            'size'=>'services__shortcode',
										        )
										    );
							        	echo '</div>';
									}

									echo '<div class="yc-shortcode--services--info">';
										echo ( ( isset( $step__item['title'] ) && !empty( $step__item['title'] ) ) ) ? '<h3>'.$step__item['title'].'</h3>' : '';
										echo ( ( isset( $step__item['content'] ) && !empty( $step__item['content'] ) ) ) ? '<p>'.$step__item['content'].'</p>' : '';
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

		$YC__CFM__global_setup_fields['Post_Types']['yourcolor__post_services_short_code'] = array(
			'title'=>  'شريحة الخدمات',
			'ObjectType'=>'post',
			'id'=>'yourcolor__post_services_short_code',
			'ObjectType'=>array('post','page'),
            'context' => 'normal',
            'priority' => 'high',	
            'MetaBox__Action'=>'fields_metabox',		
			'fields'=>array(
                array(
                    'id'=> 'title_post_services_short_code',
                    'type'=>'Title',
                    'title'=>'قم باضافة المميزات في المكان المراد اضافته ف المحتوى ',
                    'disc'=>'انسخ هذا الكود   <code data-copy-action="[post_services]"><input type="hidden" value="[post_services]">[post_services]</code>'
                ),

                array(
                    'id'=> 'hide_services_section',
                    'type'=>'SwitchBox',
                    'title'=>'إخفاء الخدمات من المقال',
                ),
				array(
					'title'  =>'إعدادات الخدمات ',
					'type'  => 'SingleGroup',
					'id'    => 'post__services__data',
					'is__open'=>true,
					'fields'=> array(
		               	array(
		                    'id'=> 'services__title',
		                    'type'=>'Text',
		                    'title'=>'عنوان الشريحة',
		                ),
		                array(
		                    'id'=> 'services__content',
		                    'type'=>'TextArea',
		                    'title'=> 'محتوى الشريحة',
		                ),
				        array(
				            'title'=>'الخدمات',
				            'id'=> 'post_services_items',
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
				                    'title'=> 'المحتوي ',
				                ),
				                array(
				                    'id'=> 'image',
				                    'type'=>'File',
				                    'title'=>'الصورة ',
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
			add_shortcode( 'post_services', array($this,'ShortCodeAppend'));
		});

	}
}
(new post_services_short_code)->Setup();