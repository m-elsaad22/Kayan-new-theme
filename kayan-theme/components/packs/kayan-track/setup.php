<?php 
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/includes/class-kayan-tracker-db.php';
require_once __DIR__ . '/includes/class-kayan-dni.php';
require_once __DIR__ . '/includes/class-kayan-tracker-rest.php';
require_once __DIR__ . '/includes/class-kayan-tracker.php';
require_once __DIR__ . '/includes/class-kayan-report-share.php';
require_once __DIR__ . '/includes/class-kayan-tracker-admin.php';

function kayan_track_bootstrap() {
	Kayan_Tracker_DB::install();
}
add_action( 'after_switch_theme', 'kayan_track_bootstrap' );
add_action( 'init', 'kayan_track_maybe_install', 1 );

function kayan_track_maybe_install() {
	$ver = kayan_track_get_option( 'kayan_track_db_version', '' );
	if ( $ver !== KAYAN_TRACK_VERSION ) {
		Kayan_Tracker_DB::install();
		kayan_track_update_option( 'kayan_track_db_version', KAYAN_TRACK_VERSION );
	}
}

add_action( 'kayan_track_daily_cleanup', array( 'Kayan_Tracker_DB', 'cleanup_old_data' ) );

if ( kayan_track_is_enabled() || is_admin() ) {
	new Kayan_Tracker_REST();
	new Kayan_Tracker();
	new Kayan_Report_Share();
}

if ( is_admin() ) {
	new Kayan_Tracker_Admin();
}
