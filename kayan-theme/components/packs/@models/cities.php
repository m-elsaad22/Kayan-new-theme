<?php 
$obj = get_queried_object();
$Styles = array();
$UniqId = uniqid();
#
$post_content = $post->post_content;
$post_content = str_replace('<br/>', PHP_EOL, $post_content);
$post_content = str_replace('&nbsp;', ' ', $post_content);
$post_content = strip_tags($post_content);
$MoreClass 		= '';
if( strlen($post_content) > 350 ) {
	$post_content = mb_substr($post_content, 0, 350, 'utf-8').'... <a href="javascript:void(0);" data-button="readmore-objects" data-object-type="post_type" data-object-name="'.$post->post_type.'" data-object-id="'.$post->ID.'" class="readmore--category-item">قراءة المزيد</a>';
}else{
	$post_content = $post_content;
}
$Styles['city__widget'] = 'YourColor__Widgets/city__widget.css';

$PostArguments = array('taxonomy'=>'city','number'=>30,'hide_empty'=>0);
$defualt__city_icon = get_option('defualt__city_icon');
$page_background = get_post_meta($post->ID, 'page_back_image', true);

if (empty($page_background)) {
    $page_background = get_option('background_image');
}
$this->Part('header',array('Styles'=>$Styles));

echo '<div class="-primary-body">';

	echo '<div class="--primary--intro--pages">';
		echo '<div class="container">';

			echo '<div class="container-pages-head">';

				echo '<div class="--container--category--info">';

					echo '<h1>'.$post->post_title.'</h1>';
					echo '<div class="--archive--be-content">'.$post_content.'</div>';

				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	echo '<div class="-Yc-breadcrumb-">';
		echo '<div class="container">';
			echo '<div class="YC-BreadCrumb -BreadCrumb-PT-'.$post->post_type.'">';
				Breadcrumb();
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="-page--container-sidebars">';
		echo '<div class="-YC-Widgets-Inner-Row">';
			echo '<div class="container">';
				echo '<div class="-cityBox-widgets-items-s1 -page--cites--boxes">';
					$UNIQ = uniqid();
					$VeDelay = 0;
					$lazyload = get_option('lazyload');
					foreach (get_terms( $PostArguments ) as $k => $city) {
						$VeDelay = $VeDelay + 0.1;
	                    $cityName = $city->name;
	                    $cityURL = get_term_link($city);
	                    $image_id = get_term_meta( $city->term_id,'image_blog_id',true );
	                    $description = $city->description;
						$words = explode(' ', $description);
	                    $firstThreeWords = implode(' ', array_slice($words, 0, 10));
	                     echo '<div class="--single--city--boxitem" data-trigger-action="'.$UNIQ.'">';
                        	echo '<div class="--cites-single-box-">';
                        		echo '<div class="-city-wrap-">';
		                        	echo '<div class="--city--logoIcon">';
		                        		if( isset( $icon ) && !empty( $icon ) ){
		                        			echo '<div class="--citeyes-icon-in">' .$icon. '</div>';
		                        		}else{
		                        			echo '<div class="--citeyes-icon-in"><i class="fa-solid fa-house"></i></div>';
		                        		}
		                        	echo '</div>';
			                        echo '<div class="--city--info-boxitem">';
			                        	echo '<a href="'.$cityURL.'" title="' .$city->name. '" data-trigger-url="'.$UNIQ.'"><h4 class="--city-name--">' .$city->name. '</h4></a>';
			                        	echo '<p class="--city-content--">' .$firstThreeWords. '</p>';
			                        echo'</div>';
		                        echo '</div>';
	                        echo'</div>';
                        echo '</div>';
					}

				echo '</div>';       
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));