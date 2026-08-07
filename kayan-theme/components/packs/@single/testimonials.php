<?php 
$UniqID = uniqid();
# POST CONTENT.
	ob_start();
	the_content();
	$post_content = ob_get_clean();

$Styles = array();

$rating      = (int) get_post_meta($post->ID, 'rating', true);
$rating      = ( $rating >= 1 && $rating <= 5 ) ? $rating : 5;
$client_name = get_post_meta($post->ID, 'client_name', true);
$client_name = ( !empty( $client_name ) ) ? $client_name : $post->post_title;
$client_city = get_post_meta($post->ID, 'client_city', true);
$initial     = mb_substr($client_name, 0, 1, 'utf-8');

$this->Part('header',array('Styles'=>$Styles));

echo '<div class="-primary-body">';

	echo '<div class="--primary--intro--pages">';
		echo '<div class="container">';
			echo '<div class="container-pages-head">';
				echo '<div class="--container--category--info">';
					echo '<h1>'.$post->post_title.'</h1>';
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

				echo '<div class="kayan-card kayan-review" style="max-width:760px;margin:0 auto;">';

					echo '<div class="kayan-review__stars">';
					for ( $s = 1; $s <= 5; $s++ ) {
						echo ( $s <= $rating ) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
					}
					echo '</div>';

					echo '<div class="kayan-review__quote">'.$post_content.'</div>';

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

			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));
