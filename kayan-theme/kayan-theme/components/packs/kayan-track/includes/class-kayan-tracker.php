<?php 
if ( ! class_exists( 'Kayan_Tracker' ) ) {

	class Kayan_Tracker {

		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'inject_tracker' ), 99 );
		}

		public function inject_tracker() {
			if ( is_admin() || ! kayan_track_is_enabled() ) {
				return;
			}

			$service = kayan_track_get_page_service();
			$path    = kayan_track_pack_path() . 'assets/tracker.js';
			$ver     = file_exists( $path ) ? (string) filemtime( $path ) : KAYAN_TRACK_VERSION;

			wp_enqueue_script(
				'kayan-tracker',
				kayan_track_pack_url() . 'assets/tracker.js',
				array(),
				$ver,
				true
			);

			$config = array(
				'restUrl'    => rest_url( 'kayan/v1/track' ),
				'dniUrl'     => rest_url( 'kayan/v1/dni' ),
				'dniEnabled' => (bool) kayan_track_get_option( 'kayan_dni_enabled', false ),
				'cooldown'   => (int) kayan_track_get_option( 'kayan_cooldown_mins', 30 ),
				'pageService'=> $service,
			);

			wp_add_inline_script(
				'kayan-tracker',
				'window.KayanTracker=' . wp_json_encode( $config ) . ';',
				'before'
			);
		}
	}
}
