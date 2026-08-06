<?php 
class post_gallery_short_code{
	function __construct($argument=array() ){
		
	}

	public function ShortCodeAppend(){

      	global $post;
      
		$hide_post_gallery = get_post_meta( $post->ID,'hide_post_gallery',true );
		if( !empty( $hide_post_gallery ) ) return ;

	    $post_gallery = get_post_meta($post->ID,'post_gallery',true);
	    $post_gallery = ( ( is_array( $post_gallery ) ) ) ? $post_gallery : array();

		$title_post_gallery = get_post_meta( $post->ID,'title_post_gallery',true );
		$content_post_gallery = get_post_meta( $post->ID,'content_post_gallery',true );

	    if( empty( $post_gallery ) ) return ;
		# SHORTCODE OUTPUT .
     		ob_start();

     			echo '<div class="yc-shortcode--box yc-shortcode--post--gallery">';
					echo ( ( !empty( $title_post_gallery ) ) ) ? '<h2 class="--short--code--title --short--code--title">'.$title_post_gallery.'</h2>' : '';
					
					echo ( ( !empty( $content_post_gallery ) ) ) ? '<p class="--short--code--content">'.$content_post_gallery.'</p>' : '';

					echo '<div class="yc-shortcode--single-image" pswp>';
	     				foreach ($post_gallery as $image__id => $image__url) {

	     					$image_alt = get_post_meta($image__id, '_wp_attachment_image_alt', true);
	     					$total_attch_data = wp_get_attachment_metadata( $image__id );
	     					if( isset( $total_attch_data['width'] ) ){
								echo '<div class="yc-shortcode-gallry--image">';
									echo '<a href="'.$image__url.'" aria-label="'.$post->post_title.'" data-pswp-width="'.$total_attch_data['width'].'" data-pswp-height="'.$total_attch_data['width'].'">';
							        	echo YC_get_attachment( array( 'id'=>$image__id,'size'=>'single__gallery','alt'=>$post->post_title) );
						        	echo '</a>';
					        	echo '</div>';
	     					}
	     				}
			        echo '</div>';

     			echo '</div>';

      		$content = ob_get_clean();

	    return $content;
	}

	public function ShortCode__insert_field(){
		global $YC__CFM__global_setup_fields;

		$YC__CFM__global_setup_fields['Post_Types']['yourcolor__post_gallery_short_code'] = array(
			'title'=>  'البوم صور ',
			'ObjectType'=>'post',
			'id'=>'yourcolor__post_gallery_short_code',
			'ObjectType'=>array('post','page'),
            'context' => 'normal',
            'priority' => 'high',
            'MetaBox__Action'=>'fields_metabox',
			'fields'=>array(
                array(
                    'id'=> 'title_post_gallery_short_code',
                    'type'=>'Title',
                    'title'=>'قم باضافة المميزات في المكان المراد اضافته ف المحتوى ',
                    'disc'=>'انسخ هذا الكود   <code data-copy-action="[post_gallery]"><input type="hidden" value="[post_gallery]">[post_gallery]</code>'
                ),
                array(
                    'id'=> 'hide_post_gallery',
                    'type'=>'SwitchBox',
                    'title'=>'إخفاء البوم الصور',
                ),
                array(
                    'id'=> 'title_post_gallery',
                    'type'=>'Text',
                    'title'=>'عنوان الشريحة',
                ),
                array(
                    'id'=> 'content_post_gallery',
                    'type'=>'TextArea',
                    'title'=>'وصف الشريحة',
                ),

                array(
                    'id'=> 'post_gallery',
                    'type'=>'File',
                    'title'=>'البوم الصور',
                    'multiple'=>true,
                ),
			)
		);
	}

	public function Setup(){
		add_action( 'YC__CFM__global_setup_fields',array($this,'ShortCode__insert_field'),1 );
		add_action('init',function(){			
			add_shortcode( 'post_gallery', array($this,'ShortCodeAppend'));
		});		
	}
}
(new post_gallery_short_code)->Setup();