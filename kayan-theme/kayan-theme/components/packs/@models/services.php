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

$PostArguments = array('post_type'=>'services','posts_per_page'=>-1,'post_status'=>'publish');
$services__list = get_posts($PostArguments);

$whatsapp_number = get_option('whatsapp_number');
$phonenumber     = get_option('phonenumber');

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

				if( !empty( $services__list ) ) {
					echo '<div class="kayan-grid">';
					foreach ( $services__list as $service ) {

						$service_icon    = get_post_meta($service->ID, 'service_icon', true);
						$service_price   = get_post_meta($service->ID, 'service_price', true);
						$service_excerpt = strip_tags($service->post_content);
						$service_excerpt = mb_substr($service_excerpt, 0, 140, 'utf-8');
						$thumb_url       = get_the_post_thumbnail_url($service->ID, 'medium_large');

						echo '<div class="kayan-card">';

							if( !empty( $thumb_url ) ) {
								echo '<div class="kayan-card__thumb"><a href="'.get_permalink($service->ID).'" title="'.esc_attr($service->post_title).'"><img src="'.esc_url($thumb_url).'" alt="'.esc_attr($service->post_title).'" loading="lazy" /></a></div>';
							}else{
								echo '<div class="kayan-card__icon">';
									echo ( !empty( $service_icon ) ) ? kayan_icon_html( $service_icon ) : '<i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i>';
								echo '</div>';
							}

							echo '<h3 class="kayan-card__title"><a href="'.get_permalink($service->ID).'">'.$service->post_title.'</a></h3>';
							echo '<p class="kayan-card__text">'.$service_excerpt.'...</p>';

							echo '<div class="kayan-card__meta">';
								if( !empty( $service_price ) ) {
									echo '<span class="kayan-badge kayan-badge--gold">'.$service_price.'</span>';
								}
								echo '<a class="kayan-btn" href="'.get_permalink($service->ID).'">'.__('تفاصيل الخدمة','yourcolor').' <i class="fa-solid fa-arrow-left"></i></a>';
								if( !empty( $whatsapp_number ) ) {
									echo '<a class="kayan-btn kayan-btn--wa" href="https://wa.me/'.esc_attr($whatsapp_number).'" target="_blank" rel="nofollow noopener"><i class="fa-brands fa-whatsapp"></i></a>';
								}
							echo '</div>';

						echo '</div>';
					}
					echo '</div>';
				}else{
					echo '<div class="kayan-empty">';
						echo '<i class="fa-regular fa-folder-open"></i>';
						echo '<p>'.get_option('search_title').'</p>';
						if( !empty( $phonenumber ) ) {
							echo '<a class="kayan-btn" href="tel:'.esc_attr($phonenumber).'"><i class="fa-solid fa-phone"></i> '.$phonenumber.'</a>';
						}
					echo '</div>';
				}

			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));
