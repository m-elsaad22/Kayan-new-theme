<?php
function YC__SavePosts__LastUpdate($post){

	if( $post->post_type == 'post' ) {

		foreach( (is_array(get_the_terms($postID, 'category', ''))) ? get_the_terms($postID, 'category', '') : array() as $c ) {
			update_term_meta($c->term_id, 'last_update', time());
			update_term_meta($c->term_id, 'last_update_post', $postID);
		}

	}	
}

add_action('YC__CFM__After_Save_post_metabox','YC__SavePosts__LastUpdate');