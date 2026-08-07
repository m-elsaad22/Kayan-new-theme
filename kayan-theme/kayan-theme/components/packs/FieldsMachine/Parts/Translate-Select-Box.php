<?php   $Theme__LanguageMachine = new Theme__LanguageMachine;
$languages___lists = $Theme__LanguageMachine->LanguagesSelectList();

if( isset( $post->taxonomy ) ){
	$Title = $post->name;
	$EditURL = admin_url('term.php?taxonomy='.$post->taxonomy.'&tag_ID='.$post->term_id);
	$ObjectID = $post->term_id;
	$Current__language = get_term_meta($ObjectID,'post__language',true);
}else{
	$thumb__url = get_the_post_thumbnail_url($post->ID);
	$Title = $post->post_title;
	$EditURL = admin_url('post.php?post='.$post->ID.'&action=edit');
	$ObjectID = $post->ID;
	$Current__language = get_post_meta($ObjectID,'post__language',true);

}
echo '<div class="-currrent-single-elements -selected-item-byme'.( ( isset( $DBValue ) ) ? ' active' : '' ).'">';
	echo ( ( !isset( $DBValue ) ) ) ? '<div class="-aps--tools-action" data-select-custom-ajax="'.$ObjectID.'"></div>' : '';

	if( isset( $thumb__url ) ){
		echo '<div class="Thumb--II">'.( ( !empty( $thumb__url ) ) ? '<img src="'.$thumb__url.'">' : '<i class="fa-solid fa-ban"></i>').'</div>';
	}else{
		echo '<div class="Thumb--II Thumb--II--ID">'.$ObjectID.'</div>';
	}

	echo '<div class="-currrent-single-elements-box-title"><a href="'.$EditURL.'" target="_blank">'.$Title.'</a></div>';
	echo '<div class="-post---language"><span>'.$languages___lists[ $Current__language ]['title'].'</span><img src="'.$languages___lists[ $Current__language ]['image'].'"></div>';

	echo ( ( isset( $DBValue  ) ) ) ? '<div class="-remove-custom-post-select-tools hoverable" data-remove-translate-select-item="'.$DBValue->id.'"><i class="fa-solid fa-xmark"></i></div>' : '';
echo '</div>';