<?php 
/**
 * Homepage v3 — early template_redirect takeover (runs before ThemeStatic::Locate).
 */

if ( ! function_exists( 'kayan_stabilization_homepage_v3_redirect' ) ) {
	function kayan_stabilization_homepage_v3_redirect() {
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}
		if ( ! function_exists( 'kayan_homepage_v3_active_request' ) || ! kayan_homepage_v3_active_request() ) {
			return;
		}
		if ( ! function_exists( 'kayan_homepage_v3_render' ) ) {
			return;
		}
		kayan_homepage_v3_render();
		exit;
	}
}
add_action( 'template_redirect', 'kayan_stabilization_homepage_v3_redirect', 0 );
