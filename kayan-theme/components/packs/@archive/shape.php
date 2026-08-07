<?php
global $current_user;
$obj = get_queried_object();

# ═══ v1.3.0: توجيه آمن يدعم أرشيف التصنيفات وأرشيف أنواع المنشورات ═══
$template = 'default';

if ( is_object( $obj ) && isset( $obj->taxonomy ) && ! empty( $obj->taxonomy ) ) {
	# أرشيف تصنيف: @archive/{taxonomy}.php إن وُجد
	if ( file_exists( $CurrentDir . $obj->taxonomy . '.php' ) ) {
		$template = $obj->taxonomy;
	}
} else if ( is_post_type_archive() ) {
	# أرشيف نوع منشور: @archive/archive-{post_type}.php إن وُجد
	$pt = get_query_var( 'post_type' );
	if ( is_array( $pt ) ) { $pt = reset( $pt ); }
	if ( $pt && file_exists( $CurrentDir . 'archive-' . $pt . '.php' ) ) {
		$template = 'archive-' . $pt;
	}
}

require( $CurrentDir . $template . '.php' );
