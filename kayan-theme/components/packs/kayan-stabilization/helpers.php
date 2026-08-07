<?php 
if ( ! function_exists( 'kayan_stabilization_theme_version' ) ) {
	function kayan_stabilization_theme_version() {
		$theme = wp_get_theme();
		return $theme->get( 'Version' );
	}
}

if ( ! function_exists( 'kayan_stabilization_legacy_tracking_disabled' ) ) {
	function kayan_stabilization_legacy_tracking_disabled() {
		if ( function_exists( 'kayan_track_is_enabled' ) && kayan_track_is_enabled() ) {
			return ! empty( yc_get_option( 'kayan_stabilization_allow_legacy_tracking' ) ) ? false : true;
		}
		return ! empty( yc_get_option( 'kayan_stabilization_disable_legacy_tracking' ) );
	}
}

if ( ! function_exists( 'kayan_stabilization_register_deploy_check' ) ) {
	function kayan_stabilization_register_deploy_check() {
		$version = kayan_stabilization_theme_version();
		$stamp   = yc_get_option( 'kayan_deploy_stamp' );
		if ( $stamp !== $version ) {
			if ( function_exists( 'kayan_stabilization_purge_caches' ) ) {
				kayan_stabilization_purge_caches();
			}
			yc_update_option( 'kayan_deploy_stamp', $version );
		}
	}
}

if ( ! function_exists( 'kayan_stabilization_get_deployment_manifest' ) ) {
	/**
	 * Branch / PR alignment reference for ops (not loaded on frontend logic).
	 */
	function kayan_stabilization_get_deployment_manifest() {
		return array(
			'theme_version'       => kayan_stabilization_theme_version(),
			'phase1_pr'           => '#11 cursor/phase1-seo-perf-cda3',
			'kayan_track_pr'      => '#10 cursor/kayan-track-cda3',
			'homepage_v3_pr'      => '#7 cursor/homepage-v3-cda3',
			'admin_mobile_branch' => 'cursor/admin-mobile-cda3',
			'required_packs'      => array(
				'kayan-performance',
				'kayan-stabilization',
				'kayan-track',
				'Enqueues',
			),
			'legacy_paths_to_retire' => array(
				'components/packs/#footer/js/setup.js'           => 'Use setup-carousel.js + setup-lazy.js on non-v3 pages only',
				'components/packs/YourColorWidgets/model-selector/intro-models/slider_intro_v1.php' => 'Disabled when Homepage v3 active',
				'components/packs/theme-seo/setup.php'           => 'Title() bypassed when KAYAN SEO active',
				'components/packs/schema/setup.php'              => 'insert__schema skipped when modern schema',
			),
			'production_only_legacy' => array(
				'inline_script#kayan-tracking-js' => 'Remove Code Snippets / custom plugin (kt_* ajax)',
				'inline_script rukn_track_*'      => 'Remove custom snippet (rukn_track_click / rukn_track_pv)',
				'inline_script _rsa_sid'          => 'Remove pageview snippet',
			),
		);
	}
}
