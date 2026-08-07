<?php  
if( $object__type == 'posts' ){
	$thumb__url = get_the_post_thumbnail_url($post->ID);
	$Title = $post->post_title;
	$EditURL = admin_url('post.php?post='.$post->ID.'&action=edit');
	$ObjectID = $post->ID;
}

if( $object__type == 'taxonomy' ){
	$Title = $post->name;
	$EditURL = admin_url('term.php?taxonomy='.$post->taxonomy.'&tag_ID='.$post->term_id);
	$ObjectID = $post->term_id;
}

if( $object__type == 'users' ){
	$Title = $post->display_name;
	$EditURL = admin_url('https://elboshy.com/StarAds/wp-admin/user-edit.php?user_id='.$post->ID);
	$ObjectID = $post->ID;
}

echo '<div class="-currrent-single-elements -selected-item-byme'.( ( isset( $active ) ) ? ' active' : '' ).'" data-title="'.$Title.'">';
	echo ( ( isset( $hideInput ) ) ) ? '<div class="-aps--tools-action" data-select-custom-items="'.$ObjectID.'" data-multiple="'.$multiple.'" data-hide-input="'.base64_encode( json_encode( $hideInput ) ).'"></div>' : '';
	echo ( ( isset( $Input ) ) ) ? $Input : '';
	if( isset( $thumb__url ) ){
		echo '<div class="Thumb--II">'.( ( !empty( $thumb__url ) ) ? '<img src="'.$thumb__url.'">' : '<i class="fa-solid fa-ban"></i>').'</div>';
	}else{
		echo '<div class="Thumb--II Thumb--II--ID">'.$ObjectID.'</div>';
	}
	echo '<div class="-currrent-single-elements-box-title"><a href="'.$EditURL.'" target="_blank">'.$Title.'</a></div>';
	
	echo '<div class="-remove-custom-post-select-tools hoverable" data-remove-compo-select-item="'.$post->ID.'"><i class="fa-solid fa-xmark"></i></div>';
echo '</div>';