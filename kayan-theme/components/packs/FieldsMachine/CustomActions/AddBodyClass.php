<?php
function wpdocs_admin_classes( $classes ) {
	global $pagenow;

	//if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
		$classes .= ' YourColorEdits-Class-Style';
	//}

	return $classes;
}

add_filter( 'admin_body_class', 'wpdocs_admin_classes' );