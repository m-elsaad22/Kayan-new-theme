<?php 
class work_steps_short_code{
	function __construct($argument=array() ){
		
	}

	public function ShortCodeAppend(){
      	global $post;
      
		$hide_work_steps = get_post_meta( $post->ID,'hide_work_steps',true );
		if( !empty( $hide_work_steps ) ) return ;

		$post__work_steps__data = get_post_meta( $post->ID,'post__work_steps__data',true );
		$post__work_steps__data = ( ( is_array( $post__work_steps__data ) ) ) ? $post__work_steps__data : array();
		if( empty( $post__work_steps__data ) ) return ;

		# SHORTCODE OUTPUT .
     		ob_start();
				echo '<div class="yc-shortcode--box yc-shortcode--work-steps">';

					echo ( ( isset( $post__work_steps__data['work_steps__title'] ) && !empty( $post__work_steps__data['work_steps__title'] ) ) ) ? '<h2 class="--short--code--title">'.$post__work_steps__data['work_steps__title'].'</h2>' : '';
					
					echo ( ( isset( $post__work_steps__data['work_steps__content'] ) && !empty( $post__work_steps__data['work_steps__content'] ) ) ) ? '<p class="--short--code--content">'.$post__work_steps__data['work_steps__content'].'</p>' : '';

					if( isset( $post__work_steps__data['work_steps_items'] ) && !empty( $post__work_steps__data['work_steps_items'] ) ){
						$post__work_steps__data['work_steps_items'] = ( ( is_array( $post__work_steps__data['work_steps_items'] ) ) ) ? $post__work_steps__data['work_steps_items'] : array();
						echo '<div class="yc-shortcode--steps--items">';
							$v1=0;
							foreach ( $post__work_steps__data['work_steps_items'] as $step__item ) {$v1++;

								echo '<div class="yc-shortcode--single-worksteps-item">';

									echo '<div class="yc-shortcode-worksteps--image">'.str_pad($v1, 2, "0", STR_PAD_LEFT).'</div>';									

									echo '<div class="yc-shortcode--worksteps--info">';
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

		$YC__CFM__global_setup_fields['Post_Types']['yourcolor__work_steps_short_code'] = array(
			'title'=>  'خطوات العمل',
			'ObjectType'=>'post',
			'id'=>'yourcolor__work_steps_short_code',
			'ObjectType'=>array('post','page'),
            'context' => 'normal',
            'priority' => 'high',
            'MetaBox__Action'=>'fields_metabox',
			'fields'=>array(
                array(
                    'id'=> 'title_work_steps_short_code',
                    'type'=>'Title',
                    'title'=>'قم باضافة المميزات في المكان المراد اضافته ف المحتوى ',
                    'disc'=>'انسخ هذا الكود   <code data-copy-action="[post_steps]"><input type="hidden" value="[post_steps]">[post_steps]</code>'
                ),
                array(
                    'id'=> 'hide_work_steps',
                    'type'=>'SwitchBox',
                    'title'=>'إخفاء خطوات العمل من المقال',
                ),
				array(
					'title'  =>'إعدادات خطوات العمل ',
					'type'  => 'SingleGroup',
					'id'    => 'post__work_steps__data',
					'is__open'=>true,
					'fields'=> array(
		                array(
		                    'id'=> 'work_steps__title',
		                    'type'=>'Text',
		                    'title'=>'العنوان',
		                ),
		                array(
		                    'id'=> 'work_steps__content',
		                    'type'=>'TextArea',
		                    'title'=> 'وصف قصير',
		                ),
				        array(
				            'title'=>'خطوات العمل',
				            'id'=> 'work_steps_items',
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
				                )		                
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
			add_shortcode( 'post_steps', array($this,'ShortCodeAppend'));
		});			
	}
}
(new work_steps_short_code)->Setup();