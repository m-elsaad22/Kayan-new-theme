<?php 
require_once __DIR__ . '/helpers.php';

function kayan_ui_enqueue_fixes() {
	if ( is_admin() ) {
		return;
	}
	wp_enqueue_script(
		'kayan-ui-fixes',
		get_template_directory_uri() . '/components/packs/kayan-ui/kayan-ui-fixes.js',
		array( 'jquery', 'yourcolor-init' ),
		'2027.1.4',
		true
	);
}
add_action( 'wp_enqueue_scripts', 'kayan_ui_enqueue_fixes', 20 );
