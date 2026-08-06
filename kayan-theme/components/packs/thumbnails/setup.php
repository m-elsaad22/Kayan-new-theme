<?php 
function SetupThumbnails() {

	$logo__data = get_option( 'logo__data' );
	$logo__data = ( ( is_array( $logo__data ) ) ) ? $logo__data : array();
	$image_logo_width = '90';
	$image_logo_height = '40';
	if( !empty( $logo__data ) && isset( $logo__data['logo__mode'] ) && $logo__data['logo__mode'] == 'Image' && isset( $logo__data[ $logo__data['logo__mode'] ] ) && isset( $logo__data[$logo__data['logo__mode']]['image_logo_width'] )  ) $image_logo_width = $logo__data[$logo__data['logo__mode']]['image_logo_width'];
	if( !empty( $logo__data ) && isset( $logo__data['logo__mode'] ) && $logo__data['logo__mode'] == 'Image' && isset( $logo__data[ $logo__data['logo__mode'] ] ) && isset( $logo__data[ $logo__data['logo__mode']]['image_logo_height'] ) ) $image_logo_height = $logo__data[$logo__data['logo__mode']]['image_logo_height'];


	$footer__logo = get_option( 'footer__logo' );
	$footer__logo = ( ( is_array( $footer__logo ) ) ) ? $footer__logo : array();
	$footer_logo_width = '200';
	$footer_logo_height = '60';
	if( !empty( $footer__logo ) && isset( $footer__logo['logo__mode'] ) && $footer__logo['logo__mode'] == 'Image' && isset( $footer__logo[ $footer__logo['logo__mode'] ] ) && isset( $footer__logo[$footer__logo['logo__mode']]['image_logo_width'] )  ) $footer_logo_width = $footer__logo[$footer__logo['logo__mode']]['image_logo_width'];
	if( !empty( $footer__logo ) && isset( $footer__logo['logo__mode'] ) && $footer__logo['logo__mode'] == 'Image' && isset( $footer__logo[ $footer__logo['logo__mode'] ] ) && isset( $footer__logo[ $footer__logo['logo__mode']]['image_logo_height'] ) ) $footer_logo_height = $footer__logo[$footer__logo['logo__mode']]['image_logo_height'];


	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'logo__size', $image_logo_width, $image_logo_height, false );
	add_image_size( 'footer_sizelogo', $footer_logo_width, $footer_logo_height, false );
	add_image_size( 'category__size', 60, 60, false );
	add_image_size( 'city__image1', 610, 450, false );
	add_image_size( 'city__image2', 360, 280, false );
	add_image_size( 'gallery_features', 330, 250, false );
	add_image_size( 'contactimg', 550, 550, false );
	add_image_size( 'gallery', 480 , 370, false );
	add_image_size( 'services__shortcode', 160, 160, false );
	add_image_size( 'single__gallery', 195, 195, false );
	add_image_size( 'watch__more', 110, 110, false );
	add_image_size( 'mini__work__box', 150, 100, false );
	add_image_size( 'panner', 600, 750, false );
	add_image_size( 'posts__box', 394, 265, false );
	add_image_size( 'mobile_post_size', 300, 225, false );
	add_image_size( 'post_single_size', 800, 600, false );
	add_image_size( 'intro__cover_size', 300, 225, false );	
	add_image_size( 'faqs__image', 434, 358, false );	
	add_image_size( 'medium', 190, 230, false );
	add_image_size( 'thumb__work__box', 473 , 400 , false );
	add_image_size( 'default', 370, 520, false );
	add_image_size( 'pinsize', 180, 270, false );
	add_image_size( 'small_size',50,50, true );
	add_image_size( 'small_post',65,65, false );
	add_image_size( 'about__us', 616 , 552 , false );
	add_image_size( 'image_city', 354 , 215 , false );
	add_image_size( 'secondary_about__us', 560 , 540 , false );
}
add_action('init', 'SetupThumbnails');