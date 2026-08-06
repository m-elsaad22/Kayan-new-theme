<?php /**
 * 
 */
class page_url extends YC__WidgetsMachine{
	
	function __construct(){

		# WIDGET INFO 
			$this->widget__name = 'page_url';
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
				$meta_pages__value = get_term_meta( $category[0]->term_id,'Pages__List__URL',true );
				if( !empty( $meta_pages__value ) ) $Pages__List__URL = $meta_pages__value;
			}
		}else{
			$meta_pages__value = get_post_meta( $post->ID,'Pages__List__URL',true );
			if( !empty( $meta_pages__value ) ) $Pages__List__URL = $meta_pages__value;
		}

	  	if( !isset( $Pages__List__URL ) || isset( $Pages__List__URL ) && empty( $Pages__List__URL ) ) $Pages__List__URL = array();
	  	$Pages__List__URL = Sort__this__list( $Pages__List__URL );

	  	if( !empty( $Pages__List__URL ) ){
			echo '<div class="-side--bar-widgets--pages--URL">';
				echo ( ( isset( $title ) ) ) ? '<div class="--widget--sidebar--title --sidebar-widget-pages--title">'.$title.'</div>' : '';
				echo '<div class="--inners--page--items">';
					foreach ( $Pages__List__URL as $single__page_item ) {
					    if( isset( $single__page_item['button_page'] ) && !empty( $single__page_item['button_page'] ) ) {
					    	$button_page_title = ( ( isset( $single__page_item['button_Text'] ) && !empty( $single__page_item['button_Text'] ) ) ) ? $single__page_item['button_Text'] : get_the_title( $single__page_item['button_page'] );
					    	echo '<a href="'.get_the_permalink($single__page_item['button_page']).'" title="'.$button_page_title.'">';
					    		echo '<i class="fa-solid fa-angles-left"></i>';
					    		echo '<span>'.$button_page_title.'</span>';
				    		echo '</a>';
					    }
					}
				echo '</div>';
			echo '</div>';
	  	}

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,			
			'title'=>'الصفحات',
			'description'=>' # شكل ',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'title'  => 'العنوان',
					'type'  => 'Text',
					'id'    => 'title',
				),
				array(
					'type'=>'GroupsField',
					'id' => 'Pages__List__URL',
					'title' =>'اختر الصفحات المراد عرضها ',
					'disc'=>'في حالة تحديدها من صفحة المقالة يتم اظهار الصفحات المختارة في المقالة',
					'fields'=> array(
			            array(
			                'type'=>'Posts-Select',
			                'id' => 'button_page',
			                'post_type_name'=>'page',
			                'title' =>'تحديد الصفحة',
			            ),
			            array(
			                'type'=>'Text',
			                'id' => 'button_Text',
			                'title' =>'إضافة عنوان اخر للصفحة',
			            ),
						array(
							'type'=>'Text',
							'id'=>'number',
							'title'=>'رقم الترتيب ',
							'require'=>true,
						),
					)
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
(new page_url)->Setup();