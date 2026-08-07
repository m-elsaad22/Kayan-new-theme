<?php 
$UniqID = uniqid();
# POST CONTENT.
	ob_start();
	the_content();
	$post_content = ob_get_clean();

# STYLES.
	$Styles = array();
	$Styles['shortcodes'] = 'shortcodes.css';
	$Styles[$post->post_type] = 'singular/single.css';

# DATA (Database).
	$permalink       = get_the_permalink($post->ID);
	$post_thumb      = get_the_post_thumbnail_url($post->ID, 'large');
	$service_price   = get_post_meta($post->ID, 'service_price', true);
	$service_icon    = get_post_meta($post->ID, 'service_icon', true);
	$phonenumber     = get_option('phonenumber');
	$whatsapp_number = get_option('whatsapp_number');

# RELATED SERVICES.
	$related__services = get_posts(array(
		'post_type'      => 'services',
		'posts_per_page' => 3,
		'post_status'    => 'publish',
		'post__not_in'   => array($post->ID),
	));

$this->Part('header',array('Styles'=>$Styles));

echo '<div class="-primary-body">';

	echo '<div class="--primary--intro--pages">';
		echo '<div class="container">';
			echo '<div class="container-pages-head">';
				echo '<div class="--container--category--info">';
					echo '<h1>'.$post->post_title.'</h1>';
					if( !empty( $service_price ) ) {
						echo '<span class="kayan-badge kayan-badge--gold">'.$service_price.'</span>';
					}
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

				echo '<div class="kayan-booking-wrap">';

					echo '<div class="kayan-booking-form">';
						if( !empty( $post_thumb ) ) {
							echo '<div class="--thumb--blog-image--" style="margin-bottom:24px;"><img src="'.esc_url($post_thumb).'" alt="'.esc_attr($post->post_title).'" loading="lazy" /></div>';
						}
						echo '<div class="--archive--be-content" style="color:var(--k-text2);">'.$post_content.'</div>';
					echo '</div>';

					echo '<aside class="kayan-booking-side">';
						echo '<h3>'.get_bloginfo('name').'</h3>';
						if( !empty( $phonenumber ) ) {
							echo '<a class="kayan-side-item" href="tel:'.esc_attr($phonenumber).'" rel="nofollow"><i class="fa-solid fa-phone"></i><span>'.$phonenumber.'</span></a>';
						}
						if( !empty( $whatsapp_number ) ) {
							echo '<a class="kayan-side-item" href="https://wa.me/'.esc_attr($whatsapp_number).'" target="_blank" rel="nofollow noopener"><i class="fa-brands fa-whatsapp"></i><span>'.$whatsapp_number.'</span></a>';
						}
					echo '</aside>';

				echo '</div>';

				if( !empty( $related__services ) ) {
					echo '<div class="kayan-grid" style="margin-top:50px;">';
					foreach ( $related__services as $service ) {
						$rel_excerpt = strip_tags($service->post_content);
						$rel_excerpt = mb_substr($rel_excerpt, 0, 120, 'utf-8');
						echo '<div class="kayan-card">';
							echo '<h3 class="kayan-card__title"><a href="'.get_permalink($service->ID).'">'.$service->post_title.'</a></h3>';
							echo '<p class="kayan-card__text">'.$rel_excerpt.'...</p>';
							echo '<a class="kayan-btn kayan-btn--ghost" href="'.get_permalink($service->ID).'">'.__('عرض الخدمة','yourcolor').'</a>';
						echo '</div>';
					}
					echo '</div>';
				}

			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));
