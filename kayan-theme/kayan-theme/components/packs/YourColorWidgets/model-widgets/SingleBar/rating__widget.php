<?php /**
 * 
 */
class rating__widget extends YC__WidgetsMachine{
	
	function __construct(){

		# WIDGET INFO 
			$this->widget__name = 'rating__widget';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);		
	}

	public function widget__ui($vars){
		extract($vars);

		global $post;

		if( isset( $title ) ){
			if( empty( $title_color ) ) $title_color = 'var(--uicolor)';

			$title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$title);
			$title = str_replace('%}','</c--color>',$title);
		}

		if( $Rating__Type == 'comments__rating' ){
			$CurrentRateValue = (INT) get_post_meta( $post->ID,'_wc_average_rating',true );
			if( empty( $CurrentRateValue ) || !empty( $CurrentRateValue ) && !is_numeric($CurrentRateValue) ) $CurrentRateValue = (INT) $CurrentRateValue;

			$RatingCounter = get_comments_number($post->ID);
			$RateValues = get_post_meta( $post->ID, '_wc_average_data', true);

		}
		if( $Rating__Type == 'post__rating' ){

			$CurrentRateValue = get_post_meta($post->ID,'TotalRate_v1',true);
			if( empty( $CurrentRateValue ) || !empty( $CurrentRateValue ) && !is_numeric($CurrentRateValue) ) $CurrentRateValue = (INT) $CurrentRateValue;

			$RatingCounter = (INT) get_post_meta($post->ID,'RateUserCount_v1',true);
			$RateValues = ( is_array( get_post_meta( $post->ID, 'RateUsersData_v1', true) ) ) ? get_post_meta($post->ID, 'RateUsersData_v1', true) : array();

		}

		if( $Rating__Type == 'post_meta' || $Rating__Type == 'category' || $Rating__Type == 'defualt' ){
			if( $Rating__Type == 'category' ){

				$defualt__rating = array();
				$category = get_the_terms( $post->ID,'category',true );
				$category = ( ( is_array( $category ) ) ) ? $category : array();
				if( isset( $category[0] ) ) {
					$defualt__rating = get_term_meta( $category[0]->term_id,'defualt__rating',true );
				}

			}else if( $Rating__Type == 'post_meta' ){
				$defualt__rating = get_post_meta( $post->ID,'defualt__rating',true );
				$defualt__rating = ( ( is_array( $defualt__rating ) ) ) ? $defualt__rating : array();
			}else{
				$defualt__rating = ( ( isset( $defualt__rating ) && is_array( $defualt__rating ) ) ) ? $defualt__rating : array();
			}

			$CurrentRateValue = ( ( isset( $defualt__rating['ratingValue'] ) && is_numeric( $defualt__rating['ratingValue'] ) ) ) ? $defualt__rating['ratingValue'] : 5;

			$RateValues = array();
			$RatingCounter = 0;
			for ($et=1; $et < 6; $et++) { 
				$RateValues[ $et ] = ( ( isset( $defualt__rating["ratingUsers_{$et}"] ) && is_numeric( $defualt__rating["ratingUsers_{$et}"] ) ) ) ? $defualt__rating["ratingUsers_{$et}"] : 200;
				$RatingCounter = $RatingCounter + $RateValues[ $et ];
			}
		}

		$RateValues = ( is_array( $RateValues ) ) ? $RateValues : array();

		echo '<div class="--rating--widgets--box">';

            echo '<div class="-sidebar-related-title-section --rating--widgets-title">'.$title.'</div>';
            echo '<div class="--YC-single-rating-box--">';

	            echo'<div class="--rating--widgets--result--box">';

		            echo'<div class="--rating--widgets--stars-result">';
		            	echo '<div class="SB--Stars">';
			                for ($i=1; $i < 6 ; $i++) { 
			                    echo'<i class="fa-solid fa-star"></i>';
			                }
		                echo '</div>';

		                $ActivePerc = $CurrentRateValue * 100 / 5;
		                $ActivePerc = round($ActivePerc,2);
		            	echo '<div class="Active--Stars" style="--bevalue:'.$ActivePerc.'%">';
			                for ($i=1; $i < 6 ; $i++) { 
			                    echo'<i class="fa-solid fa-star"></i>';
			                }
		            	echo'</div>';
		            echo'</div>';

	                echo'<div class="ratingServise--stars-value -rating-value" data-post-id="'.$post->ID.'">'.$CurrentRateValue.'</div>';

	            echo'</div>';

	            echo'<div class="--rating--widgets--stars-averageList">';

					echo '<div class="-Rate-Average-Items -Js-Rate-AverageItems" data-post-id="'.$post->ID.'">';
						for ($i=5; $i >= 1; $i--) { 
							if( isset( $RateValues[ $i ] ) ){
								$AverageCalc = $RateValues[ $i ] * 100 / $RatingCounter;
								$AverageCalc = round($AverageCalc,1);
								echo '<div class="-Rate-Average-element">';
									echo '<em>'.$i.'</em>';
									echo '<div class="-Rate-Average-Label"><div class="-Average--progress" data-progressload="'.$AverageCalc.'"></div></div>';
									echo '<span>'.$AverageCalc.'%</span>';
								echo '</div>';
							}
						}
					echo '</div>';
	            echo'</div>';
            echo '</div>';
        echo'</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$insert_fields = array(
			array(
				'title'  => 'التقييم الافتراضي',
				'type'  => 'Number',
				'id'    => 'ratingValue',
			),
		);
		for ($i=1; $i < 6; $i++) { 
			$insert_fields[] = array(
				'title'  => 'اجمالى عدد الاشخاص المختارة ('.$i.')',
				'type'  => 'Number',
				'id'    => 'ratingUsers_'.$i,
			);
		}

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,			
			'title'=>'التقيمات',
			'description'=>' # شكل ',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'title'  => 'العنوان',
					'type'  => 'Text',
					'id'    => 'title',
				),				
				array(
					'title'  => 'تحديد نوع التقييم',
					'type'  => 'Select',
					'id'    => 'Rating__Type',
					'options'=>array(
						'post_meta'=>'حسب حقل التقييم بالمقال',
						'comments__rating'=>'حسب تقيمات التعليقات',
						'post__rating'=>'حسب تقيمات المقال العامة',
						'category'=>'حسب تصنيف المقال',
						'defualt'=>'القيمة الافتراضية',
					)
				),

				array(
					'title'  =>'إعدادات التقييم الافتراضية',
					'type'  => 'SingleGroup',
					'id'    => 'defualt__rating',
					'is__open'=>true,
					'fields'=> $insert_fields
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
(new rating__widget)->Setup();