<?php 
class post_call_short_code{
	function __construct($argument=array() ){
		
	}

	public function ShortCodeAppend(){
      	global $post;
      
		$hide_call_section = get_post_meta( $post->ID,'hide_call_section',true );
		if( !empty( $hide_call_section ) ) return ;


		$post__call_section__data = get_post_meta( $post->ID,'post__call_section__data',true );
		if( empty( $post__call_section__data ) ) return ;

		# SHORTCODE OUTPUT .
     		ob_start();

     			$call_section_phone = '';
     			if( isset( $post__call_section__data['call_section_phone'] ) && !empty( $post__call_section__data['call_section_phone'] ) ) $call_section_phone = $post__call_section__data['call_section_phone'];

     			if( $call_section_phone == '' ){
     				$post_call_phone = get_post_meta( $post->ID,'phone_number',true );
     				if( !empty( $post_call_phone ) ){
     					$call_section_phone = $post_call_phone;
     				}else{
     					$call_section_phone = get_option('phonenumber');
     				}
     			}

     			$call_section_whatsapp = '';
     			if( isset( $post__call_section__data['call_section_whatsapp'] ) && !empty( $post__call_section__data['call_section_whatsapp'] ) ) $call_section_whatsapp = $post__call_section__data['call_section_whatsapp'];

     			if( $call_section_whatsapp == '' ){
     				$whatsapp_number = get_post_meta( $post->ID,'whatsapp_number',true );
     				if( !empty( $whatsapp_number ) ){
     					$call_section_whatsapp = $whatsapp_number;
     				}else{
     					$call_section_whatsapp = get_option('whatsapp_number');
     				}
     			}

				echo '<div class="yc-shortcode--box yc-shortcode--section--contactus">';

					echo '<div class="--contact--post-info">';
						echo ( ( isset( $post__call_section__data['call_section_title'] ) && !empty( $post__call_section__data['call_section_title'] ) ) ) ? '<h2 class="--shortcode--section--contactus--title">'.$post__call_section__data['call_section_title'].'</h2>' : '';
						echo ( ( isset( $post__call_section__data['call_section_content'] ) && !empty( $post__call_section__data['call_section_content'] ) ) ) ? '<p class="--shortcode--section--contactus--content">'.$post__call_section__data['call_section_content'].'</p>' : '';
					echo '</div>';

					echo '<div class="--contact--post-call--buttons">';

						if( !empty( $call_section_phone ) ){
					        echo '<a class="--contact--button-call-link --button-call-link-phone -BTN--hoverable" href="tel:'.$call_section_phone.'" aria-label="phone" role="link">';
					            echo '<i class="fa-solid fa-phone-volume"></i>';
					            echo '<strong>اتصل بنا</strong>';
					        echo '</a>';
						}

						if( !empty( $call_section_whatsapp ) ){
					        echo '<a target="_blank" rel="nofollow" class="--contact--button-call-link --button-call-link-whatsapp -BTN--hoverable" aria-label="whatsapp" role="link" href="https://wa.me/'.$call_section_whatsapp.'">';
					            echo '<i class="fa-brands fa-whatsapp"></i>';
					            echo '<strong>   الواتساب</strong>';
					        echo '</a>';
						}

					echo '</div>';
				echo '</div>';

      		$content = ob_get_clean();

      	return $content;
	}

	public function ShortCode__insert_field(){
		global $YC__CFM__global_setup_fields;

		$YC__CFM__global_setup_fields['Post_Types']['yourcolor__post_call_short_code'] = array(
			'title'=>  'شريحة الاتصال',
			'ObjectType'=>'post',
			'id'=>'yourcolor__post_call_short_code',
			'ObjectType'=>array('post','page'),
            'context' => 'normal',
            'priority' => 'high',
            'MetaBox__Action'=>'fields_metabox',
			'fields'=>array(
                array(
                    'id'=> 'title_post_call_short_code',
                    'type'=>'Title',
                    'title'=>'قم باضافة المميزات في المكان المراد اضافته ف المحتوى ',
                    'disc'=>'انسخ هذا الكود   <code data-copy-action="[post_call]"><input type="hidden" value="[post_call]">[post_call]</code>'
                ),

                array(
                    'id'=> 'hide_call_section',
                    'type'=>'SwitchBox',
                    'title'=>'إخفاء شريحة الاتصال من المقال',
                ),

				array(
					'title'  =>'إعدادات شريحة الاتصال ',
					'type'  => 'SingleGroup',
					'id'    => 'post__call_section__data',
					'is__open'=>true,
					'fields'=> array(
		                array(
		                    'id'=> 'call_section_title',
		                    'type'=>'Text',
		                    'title'=>'عنوان الشريحة',
		                ),
		                array(
		                    'id'=> 'call_section_content',
		                    'type'=>'Text',
		                    'title'=>'عنوان  الثانوي',
		                ),
		                array(
		                    'id'=> 'call_section_phone',
		                    'type'=>'Text',
		                    'title'=> 'رقم  الهاتف ',
		                ),
		                array(
		                    'id'=> 'call_section_whatsapp',
		                    'type'=>'Text',
		                    'title'=> 'رقم الواتساب',
		                ),
					)
				)
			)
		);
	}

	public function Setup(){
		add_action( 'YC__CFM__global_setup_fields',array($this,'ShortCode__insert_field'),1 );
		add_action('init',function(){			
			add_shortcode( 'post_call', array($this,'ShortCodeAppend'));
		});			
	}
}
(new post_call_short_code)->Setup();