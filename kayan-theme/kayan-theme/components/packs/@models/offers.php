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

$offers_data = get_option('offers_data');
$offers_data = ( is_array( $offers_data ) ) ? $offers_data : array();

$whatsapp_number = get_option('whatsapp_number');
$phonenumber     = get_option('phonenumber');
$offers_badge    = get_option('offers_badge');

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

				if( !empty( $offers_data ) ) {
					echo '<div class="kayan-grid">';
					foreach ( $offers_data as $offer ) {
						if( !is_array( $offer ) ) continue;

						$offer_title   = ( isset( $offer['offer_title'] ) )   ? $offer['offer_title']   : '';
						$offer_content = ( isset( $offer['offer_content'] ) ) ? $offer['offer_content'] : '';
						$offer_price   = ( isset( $offer['offer_price'] ) )   ? $offer['offer_price']   : '';
						$offer_old     = ( isset( $offer['offer_old_price'] ) ) ? $offer['offer_old_price'] : '';
						$offer_icon    = ( isset( $offer['offer_icon'] ) )    ? $offer['offer_icon']    : '';

						if( empty( $offer_title ) ) continue;

						echo '<div class="kayan-card kayan-offer">';

							if( !empty( $offers_badge ) ) {
								echo '<span class="kayan-offer__ribbon">'.$offers_badge.'</span>';
							}

							echo '<div class="kayan-card__icon">';
								echo ( !empty( $offer_icon ) ) ? $offer_icon : '<i class="fa-solid fa-gift"></i>';
							echo '</div>';

							echo '<h3 class="kayan-card__title">'.$offer_title.'</h3>';

							if( !empty( $offer_content ) ) {
								echo '<p class="kayan-card__text">'.strip_tags($offer_content).'</p>';
							}

							if( !empty( $offer_price ) ) {
								echo '<div class="kayan-price">'.$offer_price;
									if( !empty( $offer_old ) ) {
										echo '<span class="kayan-offer__old">'.$offer_old.'</span>';
									}
								echo '</div>';
							}

							echo '<div class="kayan-card__meta">';
								if( !empty( $phonenumber ) ) {
									echo '<a class="kayan-btn" href="tel:'.esc_attr($phonenumber).'"><i class="fa-solid fa-phone"></i></a>';
								}
								if( !empty( $whatsapp_number ) ) {
									echo '<a class="kayan-btn kayan-btn--wa" href="https://wa.me/'.esc_attr($whatsapp_number).'" target="_blank" rel="nofollow noopener"><i class="fa-brands fa-whatsapp"></i></a>';
								}
							echo '</div>';

						echo '</div>';
					}
					echo '</div>';
				}else{
					echo '<div class="kayan-empty">';
						echo '<i class="fa-solid fa-gift"></i>';
						echo '<p>'.get_option('search_title').'</p>';
					echo '</div>';
				}

			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));
