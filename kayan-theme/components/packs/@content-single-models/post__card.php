<?php 
$hide__post_card = get_post_meta( $post->ID,'hide__post_card',true );
if( empty( $hide__post_card ) ) {

	$post__card__data = get_post_meta( $post->ID,'post__card__data',true );
	$post__card__data = ( ( is_array( $post__card__data ) ) ) ? $post__card__data : array();

	if( !isset( $whatsapp_number ) ){
		$whatsapp_number = get_post_meta( $post->ID,'whatsapp_number',true );
		if( empty( $whatsapp_number ) ) $whatsapp_number = get_option('whatsapp_number');
	}

	if( !isset( $phonenumber ) ){
		$phonenumber = get_post_meta( $post->ID,'phonenumber',true );
		if( empty( $phonenumber ) ) $phonenumber = get_option('phonenumber');
	}

	if( !empty( $post__card__data ) && isset( $post__card__data['post_card_title'] ) && !empty( $post__card__data['post_card_title'] ) ){
		echo '<div class="yc--post--models--post-card">';
		    echo '<div class="-single-parent-flexes--content-bar">';
		        echo ( ( isset( $post__card__data['post_card_title'] ) && !empty( $post__card__data['post_card_title'] ) ) ) ? '<span>'.$post__card__data['post_card_title'].'</span>' : '';
		        echo ( ( isset( $post__card__data['post_card_content'] ) && !empty( $post__card__data['post_card_content'] ) ) ) ? '<p>'.$post__card__data['post_card_content'].'</p>' : '';

		        echo '<div class="-post-card--burrons--area">';

		        	if( !isset( $post__card__data['hide__card__callbutton'] ) || isset( $post__card__data['hide__card__callbutton'] ) && empty( $post__card__data['hide__card__callbutton'] ) ){
		                echo '<a class="post-card-buttons -callbutton--post-card -BTN--hoverable" href="tel:'.$phonenumber.'" rel="nofollow">';
		                    echo '<i class="fa-solid fa-phone-volume"></i>';
		                    echo '<strong>اتصل بنا</strong>';
		                echo '</a>';
		        	}

		        	if( !isset( $post__card__data['hide__card__whatsapp'] ) || isset( $post__card__data['hide__card__whatsapp'] ) && empty( $post__card__data['hide__card__whatsapp'] ) ){
		                echo '<a target="_blank" rel="nofollow" class="post-card-buttons whatsapp--callbutton--post-card -BTN--hoverable" href="https://wa.me/'.$whatsapp_number.'">';
		                    echo '<i class="fa-brands fa-whatsapp"></i>';
		                    echo '<strong>   الواتساب</strong>';
		                echo '</a>';
		            }
		        echo '</div>';
		    echo '</div>';
		echo '</div>';
	}

}
