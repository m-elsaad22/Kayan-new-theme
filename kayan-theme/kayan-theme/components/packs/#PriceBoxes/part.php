<?php 
$ButtonText = 'اشتري الأن';
$target = false;
$hide__price__button = get_option('hide__price__button');
if( empty( $hide__price__button ) ){
	$price__button__text = get_post_meta( $post->ID,'btn_title',true );
	if( empty( $price__button__text ) ) $price__button__text = get_option('price__button__text');

	if( !empty( $price__button__text ) ) $ButtonText = $price__button__text;
	$price__option_data = get_option('price__option_data');
	if( !empty( $price__option_data ) && isset( $price__option_data['price__mode'] ) && isset( $price__option_data[ $price__option_data['price__mode'] ] ) ){

		if( $price__option_data['price__mode'] == 'manual' ){

			if( !empty( $price__option_data[ $price__option_data['price__mode'] ] ) && isset( $price__option_data[ $price__option_data['price__mode'] ]['button__URL'] ) && !empty( $price__option_data[ $price__option_data['price__mode'] ]['button__URL'] ) ){
				$PermaLink = $price__option_data[ $price__option_data['price__mode'] ]['button__URL'];
			}
			
		}else if( $price__option_data['price__mode'] == 'watshapp' ){

			if( !empty( $price__option_data[ $price__option_data['price__mode'] ] ) && isset( $price__option_data[ $price__option_data['price__mode'] ]['watshapp'] ) && !empty( $price__option_data[ $price__option_data['price__mode'] ]['watshapp'] ) ){
				$PermaLink = "https://wa.me/{$price__option_data[ $price__option_data['price__mode'] ]['watshapp']}";
			}else if( !empty( get_option('whatsapp_number') ) ){
				$whatsapp_number = get_option('whatsapp_number');
				$PermaLink = "https://wa.me/{$whatsapp_number}";
			}
			$target = ' target="_blank"';
		}else if( $price__option_data['price__mode'] == 'phonenumber' ){

			if( !empty( $price__option_data[ $price__option_data['price__mode'] ] ) && isset( $price__option_data[ $price__option_data['price__mode'] ]['phonenumber'] ) && !empty( $price__option_data[ $price__option_data['price__mode'] ]['phonenumber'] ) ){
				$PermaLink = "tel:{$price__option_data[ $price__option_data['price__mode'] ]['phonenumber']}";
			}else if( !empty( get_option('phonenumber') ) ){
				$phonenumber = get_option('phonenumber');
				$PermaLink = "tel:{$phonenumber}";
			}
			$target = ' target="_blank"';

		}else if( $price__option_data['price__mode'] == 'page' ){

			if( !empty( $price__option_data[ $price__option_data['price__mode'] ] ) && isset( $price__option_data[ $price__option_data['price__mode'] ]['button_page'] ) && !empty( $price__option_data[ $price__option_data['price__mode'] ]['button_page'] ) ){

				$PermaLink = get_the_permalink( $price__option_data[ $price__option_data['price__mode'] ]['button_page'] );
			}
		}
	}

	if( !isset( $PermaLink ) || isset( $PermaLink ) && empty( $PermaLink ) ){
		$contact__us__page = get_option('contact__us__page');
		if( !empty( $contact__us__page ) ){
			$PermaLink = get_the_permalink( $contact__us__page );

		}else{
			$PermaLink = home_url();
		}
	}

}
$plan_features = get_post_meta($post->ID,'services_text',true);
$plan_features = ( is_array( $plan_features ) ) ? $plan_features : array();
#
$PriceArguments = array();
#
$currency__shows = get_option('currency__shows');
$currency__shows = ( ( is_array( $currency__shows ) ) ) ? $currency__shows : array();
# PRICE VALUE .
	$PriceArguments = array();
	$price = get_post_meta($post->ID,'price_text',true);
	$discount = get_post_meta( $post->ID,'offer',true );
	$price_icon = get_post_meta( $post->ID,'price_icon',true );
	if( !empty( $discount ) ){
		$PriceArguments['discount'] = $discount;
	}
	if( !empty( $price ) ){
		$PriceArguments['value'] = $price;

		if( !empty( $discount ) ){
			$PriceArguments['discount'] = $discount;

			$discount_mouny = ($price / 100 ) * $discount;
			$discount_val = $price - $discount_mouny;
			$discount_val = round($discount_val,2);
			$PriceArguments['discount_val'] = $discount_val;
		}
	}

	if( empty( $price_icon ) ) $price_icon = get_option('currency');
	if( empty( $price_icon ) ) $price_icon = 'USD';

	$currency__found = false;
	foreach ( $currency__shows as $currency__item ) {
		if( $currency__item['item__id'] == $price_icon ){
			$currency__found = $currency__item;
		}
	}

	if( $currency__found == false ) $currency__found = array('item__id'=>$price_icon,'short'=>'$','title'=>'دولار امريكي ');

	$Content = wp_trim_words( $post->post_content,10 );

	$icon_text = get_post_meta( $post->ID,'icon_text',true );
	if( empty( $icon_text ) ) $icon_text = '<i class="fa-solid fa-bell-concierge"></i>';

	$Activable = false;
	if( isset( $item__data['ActivePlan'] ) && $item__data['ActivePlan'] == 'on' || !isset( $item__data ) && !empty( get_post_meta($post->ID,'feature',true) ) ) $Activable = true;

	# PRICE ITEM
	echo '<div class="-PriceBox-v1-box '.( ( $Activable == true ) ? ' -ActivePlane'  : '' ).'">';

		#Icon And title And content
		echo '<h3>'.( ( isset( $item__data['Title'] ) && !empty( $item__data['Title'] ) ) ? $item__data['Title']  : $post->post_title ).'</h3>';

		echo '<div class="-P-Plane--Content">'.$Content.'</div>';
		if( isset( $icon_text ) && !empty( $icon_text ) ){
			echo '<div class="--prices-space-icon">';
				echo '<div class="--icon-price-in-">' .$icon_text. '</div>';
			echo '</div>';
		}
		# End section

		echo '<div class="price-icon-title">';
			if( isset( $PriceArguments['discount'] ) ){
				echo '<div class="-discout-value">وفِّر <span>'.$PriceArguments['discount'].' %</span></div>';
			}
			echo '<div class="-Price-Selary">';
				if( isset( $PriceArguments[ 'value' ] ) ){
					echo '<div class="-price-app-value"><strong>'.( ( isset( $PriceArguments['discount'] ) ) ? $PriceArguments['discount_val'] : $PriceArguments['value'] ).'</strong> <p>'.$currency__found['item__id'].'</p></div>';
				}
			echo '</div>';
			echo '<div class="boreder"></div>';
			if( !empty( $plan_features ) ){
				echo '<div class="-Price-Items-List">';
					echo '<ul>';
						foreach ( $plan_features as $items) {
							echo '<li class="'.( ( isset( $items['available'] ) && $items['available'] == 'on' ) ? '-Disabled-Features' : '' ).'"><div class="icon_price">'.( ( isset( $items['available'] ) && $items['available'] == 'on' ) ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-check"></i>' ).'</div><span>'.$items['service_info'].'</span>'.( ( isset( $items['description'] ) && !empty( $items['description'] ) ) ? '<div class="-Planes-Info-sowh-In" data-tooltip-base="true" data-tooltip="'.base64_encode($items['description']  ).'"><i class="fa-solid fa-circle-info"></i></div>' : '' ).'</li>';
						}
					echo '</ul>';
				echo '</div>';
			}
			if( isset( $PermaLink ) && !empty( $PermaLink ) ){
				echo '<div class="-Price-Footer-Area">';
					echo ( ( empty( $hide__price__button ) ) ) ? '<div class="-Plane-Button-v1"><a href="'.$PermaLink.'"'.( ( $target != false ) ? $target : '' ).' class="-Twesters-Link btn-ket_2">'.$ButtonText.'</a></div>' : '';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
	# END SECTION