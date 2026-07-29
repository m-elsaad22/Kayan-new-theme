<?php 
add_action("BeforeBlade_single", function(){
	wp_reset_query();
	global $post;
	update_post_meta($post->ID, 'views', (INT) get_post_meta($post->ID, 'views', true) + 1);
});