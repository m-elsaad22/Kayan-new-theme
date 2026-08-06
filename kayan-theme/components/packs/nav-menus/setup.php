<?php 
function SetupMenus() {
	register_nav_menus( array(
		'main-menu'      => __( 'القائمة الرئيسية', 'YourColor' ),
	) );
}
add_action('Initialize', 'SetupMenus');
