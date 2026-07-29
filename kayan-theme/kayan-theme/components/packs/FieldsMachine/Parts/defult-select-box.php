<?php  $Theme__LanguageMachine = new Theme__LanguageMachine;
$post__language = get_post_meta( $post->ID,'post__language',true );
# !! TEST CODE 
	if( !empty( $post__language ) ) update_post_meta( $post->ID,'post__language','ar');
##########

$CurrentLanguage = $Theme__LanguageMachine->getLanguagesList( $post__language );

$thumb__url = get_the_post_thumbnail_url($post->ID);

echo '<div class="-currrent-single-elements -search-select-single-item '.( ( isset( $active ) ) ? ' active' : '' ).'">';
	echo ( ( isset( $hideInput ) ) ) ? '<div class="-aps--tools-action" data-select-custom-items="'.$post->ID.'" data-multiple="'.$multiple.'" data-hide-input="'.base64_encode( json_encode( $hideInput ) ).'"></div>' : '';
	echo ( ( isset( $Input ) ) ) ? $Input : '';
	echo '<div class="Thumb--II">'.( ( !empty( $thumb__url ) ) ? '<img src="'.$thumb__url.'">' : '<i class="fa-solid fa-ban"></i>').'</div>';
	echo '<div class="-currrent-single-elements-box-title"><a href="'.admin_url('post.php?post='.$post->ID.'&action=edit').'" target="_blank">'.$post->post_title.'</a></div>';
	
	echo '<div class="-remove-custom-post-select-tools hoverable" data-button="remove-post-incustom-select-posts"><i class="fa-solid fa-xmark"></i></div>';
echo '</div>';