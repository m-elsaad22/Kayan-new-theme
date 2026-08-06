<?php /**
 * 
 */
class single__blog extends YC__WidgetsMachine{
	
	function __construct(){

		# WIDGET INFO 
			$this->widget__name = 'single__blog';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);

		# POSTS 
			$this->Field_SelectOptions = array(
				'latest'=>'بدون تحديد  ( الاحدث ) ',
				'pin'=>'المثبت ',
				'most_views'=>'الاكثر مشاهدة'
			);					
	}

	public function widget__ui($vars){
		extract($vars);

		global $post;
		$category = get_the_terms( $post->ID,'category',true );
		$category = ( ( is_array( $category ) ) ) ? $category : array();

		$city = get_the_terms( $post->ID,'city',true );
		$city = ( ( is_array( $city ) ) ) ? $city : array();

		if( isset( $title ) ){
			if( empty( $title_color ) ) $title_color = 'var(--uicolor2)';

			$title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$title);
			$title = str_replace('%}','</c--color>',$title);
		}


		if( !isset( $sidebar__filters__per_single ) || isset( $sidebar__filters__per_single ) && !is_numeric( $sidebar__filters__per_single ) ) $sidebar__filters__per_single = 30;

		if( !isset( $sidebar__filters__single ) ) $sidebar__filters__single = array();
		$sidebar__filters__single = ( ( is_array( $sidebar__filters__single ) ) ) ? $sidebar__filters__single : array();
		#
	    $filters__argums = array(
	    	'posts_per_page'=> $sidebar__filters__per_single,
	    	'post_type'=>'post',
	    	'post__not_in'=>array($post->ID),
	    );

	    $insert__in_title = false;
	    if( !empty( $sidebar__filters__category_single ) && $sidebar__filters__category_single == 'on' && isset( $category[0] ) ){
	    	$insert__in_title = true;
	    	$filters__argums['tax_query'] = array(
	    		'relation'=>'AND',
	    	);
	    	#
	    	$filters__argums['tax_query'][] = array(
				'taxonomy'=>$category[0]->taxonomy,
				'field'=>(($category[0]->taxonomy == 'category')) ? 'term_id' : 'slug',
				'terms'=>(($category[0]->taxonomy == 'category')) ? $category[0]->term_id : $category[0]->slug,
				'operator'=>'IN'
	    	);
	    	
	    }
	    #
	    $insert__in_title = false;
	    if( !empty( $sidebar__filters__city_single ) && $sidebar__filters__city_single == 'on' && isset( $city[0] ) ){
	    	$insert__in_title = true;
	    	$filters__argums['tax_query'] = array(
	    		'relation'=>'AND',
	    	);
	    	#
	    	$filters__argums['tax_query'][] = array(
				'taxonomy'=>$city[0]->taxonomy,
				'field'=>(($city[0]->taxonomy == 'category')) ? 'term_id' : 'slug',
				'terms'=>(($city[0]->taxonomy == 'city')) ? $city[0]->term_id : $city[0]->slug,
				'operator'=>'IN'
	    	);
	    	
	    }

		foreach ( $sidebar__filters__single as $metakey ) {
			$filtersArgums = $filters__argums;
			if( $metakey == 'most_views' ) {
			    $filtersArgums['meta_key'] = 'views';
			    $filtersArgums['orderby'] = 'meta_value_num';

			}else if( $metakey == 'pin' ) {
		    	$filtersArgums['meta_key'] = 'pin';
			}

			$Founder = new WP_Query($filtersArgums);
			$Count = $Founder->found_posts;
			if( $Count > 0 ){								
				echo '<div class="-sidebar-related-Single -sidebar-related-'.$metakey.'">';
					echo '<h3 class="-sidebar-related-title-section"> '.( ( isset ( $vars["sidebar__filters__title_{$metakey}"] ) ) ? '<span>'.$vars["sidebar__filters__title_{$metakey}"].'</span>' : '' ).( ( $insert__in_title == true ) ? ' <p><a href="'.get_term_link($category[0]).'">'.$category[0]->name.'</a></p>' : '' ).'</h3>';

					echo '<div class="-sidebar-related-title-posts-items">';
						$faii= 0;
						foreach ( get_posts( $filtersArgums ) as $rpost ) {$faii++;
							if( $faii == 1 ){
					    		$this->ThemeStatic->Part('Post-box',array('post'=>$rpost));
							}else{
					    		$this->ThemeStatic->Part('Post-side-box',array('post'=>$rpost));
							}
						}
					echo '</div>';
				echo '</div>';
			}
		}

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,			
			'title'=>'كومبو فلاتر المقالات',
			'description'=>' # شكل ',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'title'  => 'الفلاتر ',
					'en_title'=> 'select filters',
					'type'  => 'CheckBox',
					'id'    => 'sidebar__filters__single',
					'options'=>$this->Field_SelectOptions
				),

				array(
					'title'  => 'عدد المقالات  ',
					'en_title'=> 'posts per page filters',
					'type'  => 'Number',
					'id'    => 'sidebar__filters__per_single',
				),
				array(
					'title'  => 'مقالات من نفس القسم ',
					'en_title'=> 'show posts category',
					'type'  => 'SwitchBox',
					'id'    => 'sidebar__filters__category_single',
				),
				array(
					'title'  => 'مقالات من نفس المدينة',
					'en_title'=> 'show posts category',
					'type'  => 'SwitchBox',
					'id'    => 'sidebar__filters__city_single',
				),

			),
		);

		foreach ( $this->Field_SelectOptions as $metakey => $metavalue ) {
			$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ]['fields'][] = array(
				'title'  => 'عنوان '.$metavalue,
				'en_title'=> 'posts per page filters',
				'type'  => 'Text',
				'id' => 'sidebar__filters__title_'.$metakey,
			);
		}

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ]['fields'][] = array(
			'type'=>'Title',
			'id' => 'wsedewdfd',
			'title' =>'إعدادات الظهور ',
		);
		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ]['fields'][] = array(
			'type'=>'SwitchBox',
			'id' => 'hide_section__switch',
			'title' =>'هل تريد إخفاء هذه الشريحة مؤقتاً',
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new single__blog)->Setup();