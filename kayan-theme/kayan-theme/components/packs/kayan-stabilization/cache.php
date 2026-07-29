<?php 
if ( ! function_exists( 'kayan_stabilization_purge_caches' ) ) {
	function kayan_stabilization_purge_caches() {
		if ( function_exists( 'litespeed_purge_all' ) ) {
			litespeed_purge_all();
		}
		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}
		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
		}

		wp_cache_flush();

		global $wpdb;
		$wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_kayan_%' OR option_name LIKE '_transient_timeout_kayan_%'"
		);
	}
}

if ( ! function_exists( 'kayan_stabilization_after_theme_switch' ) ) {
	function kayan_stabilization_after_theme_switch() {
		kayan_stabilization_purge_caches();
		if ( function_exists( 'kayan_track_bootstrap' ) ) {
			kayan_track_bootstrap();
		}
	}
}
add_action( 'after_switch_theme', 'kayan_stabilization_after_theme_switch' );
