<?php
$thumbnail_url = get_the_post_thumbnail_url($post->ID);

$thumbnail_id = get_post_thumbnail_id($post->ID);

$PermaLink = get_the_permalink($post->ID);
echo '<div class="-Post-sidebar-box-single-item '.( ( isset( $animation ) ) ? 'animation-hidden" data-animation-id="fadeInUpBig" data-animation-delay="'.$animation.'s"' : '"' ).'>';

	echo '<div class="-Post-sidebar-box-item-Thumb">';
		if( !empty( $thumbnail_id ) ){
			echo YC_get_attachment(
				array(
					'id'=>$thumbnail_id,
					'alt'=>$post->post_title,
					'size'=>'small_post'
				)
			);
		}
	echo '</div>';

	echo '<div class="-Post-sidebar-box-item-Info">';
		echo '<div><a href="'.$PermaLink.'">'.wp_trim_words( $post->post_title,8,'..' ).'</a></div>';
	echo '</div>';

echo '</div>';