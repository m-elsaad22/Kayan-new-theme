<?php /**
 * 
 */
class posts__video extends YC__WidgetsMachine{
	
	function __construct(){

		# WIDGET INFO 
			$this->widget__name = 'posts__video';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
				
	}

	public function widget__ui($vars){
		extract($vars);

		global $post;
		$category = get_the_terms( $post->ID,'category',true );
		$category = ( ( is_array( $category ) ) ) ? $category : array();

		if( isset( $title ) ){
			if( empty( $title_color ) ) $title_color = 'var(--uicolor)';

			$title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$title);
			$title = str_replace('%}','</c--color>',$title);
		}

		if( isset( $sidebar__filters__category_single ) && !empty( $sidebar__filters__category_single ) ){
			$category = get_the_terms( $post->ID,'category',true );
			$category = ( ( is_array( $category ) ) ) ? $category : array();
			if( isset( $category[0] ) ) {
				$meta_pages__value = get_term_meta( $category[0]->term_id,'VideoID',true );
				if( !empty( $meta_pages__value ) ) $VideoID = $meta_pages__value;
			}
		}else{
			$meta_pages__value = get_post_meta( $post->ID,'VideoID',true );
			if( !empty( $meta_pages__value ) ) $VideoID = $meta_pages__value;
		}

	  	if( isset( $VideoID ) && !empty( $VideoID ) ){
			echo '<div class="-side--bar-widgets--videos-Ids">';
				echo '<div class="--inners--videos-Ids--items">';
					echo '<div class="--image--video-append-src" data-click-video="'.base64_encode( json_encode( '<iframe class="iframe" title="'.$post->post_title.'" src="https://www.youtube.com/embed/'.$VideoID.'?autoplay=1" frameborder="0"></iframe>' ) ).'">';
						echo '<img alt="'.$post->post_title.'" data-loader-src="https://img.youtube.com/vi/'.$VideoID.'/0.jpg">';
						echo '<i class="fa-solid fa-play"></i>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
	  	}

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,			
			'title'=>'الفيديوهات',
			'description'=>' # شكل ',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
	            array(
	                'type'=>'Text',
	                'id' => 'VideoID',
	                'title' =>'الفيديو الافتراضي',
	            ),
				array(
					'title'  => 'حسب قائمة تصنيف المقال',
					'en_title'=> 'show posts category',
					'type'  => 'SwitchBox',
					'id'    => 'sidebar__filters__category_single',
				),
				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'wsedewdfd',
					'title' =>'إعدادات الظهور ',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'hide_section__switch',
					'title' =>'هل تريد إخفاء هذه الشريحة مؤقتاً',
				)
			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new posts__video)->Setup();