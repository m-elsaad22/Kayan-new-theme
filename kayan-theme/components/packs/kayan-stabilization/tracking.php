<?php 
/**
 * Single tracking engine: KAYAN Track (REST). Legacy admin-ajax trackers are disabled when active.
 */

if ( ! function_exists( 'kayan_stabilization_unregister_legacy_tracking_ajax' ) ) {
	function kayan_stabilization_unregister_legacy_tracking_ajax() {
		if ( ! kayan_stabilization_legacy_tracking_disabled() ) {
			return;
		}

		$actions = array(
			'kt_register_visit',
			'kt_track_click',
			'kt_session_end',
			'kt_heatmap_batch',
			'rukn_track_click',
			'rukn_track_pv',
		);

		foreach ( $actions as $action ) {
			remove_all_actions( 'wp_ajax_' . $action );
			remove_all_actions( 'wp_ajax_nopriv_' . $action );
		}
	}
}
add_action( 'init', 'kayan_stabilization_unregister_legacy_tracking_ajax', 999 );

// Output sanitization: kayan-stabilization/lockdown.php (single OB buffer).
