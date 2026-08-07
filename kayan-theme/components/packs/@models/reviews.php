<?php 
$Styles = array();
$UniqId = uniqid();
#
$post_content = $post->post_content;
$post_content = str_replace('<br/>', PHP_EOL, $post_content);
$post_content = str_replace('&nbsp;', ' ', $post_content);
$post_content = strip_tags($post_content);
if( strlen($post_content) > 350 ) {
	$post_content = mb_substr($post_content, 0, 350, 'utf-8').'... <a href="javascript:void(0);" data-button="readmore-objects" data-object-type="post_type" data-object-name="'.$post->post_type.'" data-object-id="'.$post->ID.'" class="readmore--category-item">'.__('قراءة المزيد','yourcolor').'</a>';
}

$PostArguments = array('post_type'=>'testimonials','posts_per_page'=>-1,'post_status'=>'publish');
$reviews__list = get_posts($PostArguments);

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

				if( !empty( $reviews__list ) ) {
					echo '<div class="kayan-grid">';
					foreach ( $reviews__list as $review ) {

						$rating      = (int) get_post_meta($review->ID, 'rating', true);
						$rating      = ( $rating >= 1 && $rating <= 5 ) ? $rating : 5;
						$client_name = get_post_meta($review->ID, 'client_name', true);
						$client_name = ( !empty( $client_name ) ) ? $client_name : $review->post_title;
						$client_city = get_post_meta($review->ID, 'client_city', true);
						$quote       = strip_tags($review->post_content);
						$initial     = mb_substr($client_name, 0, 1, 'utf-8');

						echo '<div class="kayan-card kayan-review">';

							echo '<div class="kayan-review__stars">';
							for ( $s = 1; $s <= 5; $s++ ) {
								echo ( $s <= $rating ) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
							}
							echo '</div>';

							echo '<p class="kayan-review__quote">'.$quote.'</p>';

							echo '<div class="kayan-review__author">';
								echo '<span class="kayan-review__avatar">'.$initial.'</span>';
								echo '<div>';
									echo '<div class="kayan-review__name">'.$client_name.'</div>';
									if( !empty( $client_city ) ) {
										echo '<span class="kayan-badge">'.$client_city.'</span>';
									}
								echo '</div>';
							echo '</div>';

						echo '</div>';
					}
					echo '</div>';
				}else{
					echo '<div class="kayan-empty">';
						echo '<i class="fa-regular fa-star"></i>';
						echo '<p>'.get_option('search_title').'</p>';
					echo '</div>';
				}

			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));
