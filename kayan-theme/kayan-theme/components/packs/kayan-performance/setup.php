<?php 
require_once __DIR__ . '/helpers.php';

add_filter( 'wp_get_attachment_image_attributes', 'kayan_perf_attachment_image_attributes', 20, 3 );
add_action( 'wp_head', 'kayan_perf_render_resource_hints', 0 );

function kayan_perf_defer_noncritical_scripts( $tag, $handle, $src ) {
	if ( ! kayan_perf_is_enabled() || ( function_exists( 'IsSpeed' ) && IsSpeed() ) ) {
		return $tag;
	}

	$defer_handles = array( 'photoswipe', 'owl-carousel', 'yourcolor-owlcarousel', 'yourcolor-setup-carousel', 'yourcolor-setup-lazy' );
	foreach ( $defer_handles as $needle ) {
		if ( false !== strpos( $handle, $needle ) || false !== strpos( $src, $needle ) ) {
			if ( false === strpos( $tag, ' defer' ) ) {
				$tag = str_replace( ' src', ' defer src', $tag );
			}
			break;
		}
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'kayan_perf_defer_noncritical_scripts', 20, 3 );
