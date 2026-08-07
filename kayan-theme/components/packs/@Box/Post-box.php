<?php
$uniqid = uniqid();
$thumbnail_id = get_post_thumbnail_id($post->ID);

$PermaLink = get_the_permalink($post->ID);
#
$mini__post_content = wp_trim_words($post->post_content,10);
$lazyload = get_option('lazyload');
echo '<div class="-Post-box-single-item" data-trigger-action="'.$uniqid.'">';
	echo '<div class="--thumb--blog-image--">';
		echo '<div class="-Post-box-item-Thumb">';
			if( !empty( $thumbnail_id ) ){
				echo '<div class="-YC-Loader-Cover" style="background-image: url(' .$lazyload. ');"></div>';
				echo YC_get_attachment(
					array(
						'id'=>$thumbnail_id,
						'alt'=>$post->post_title,
						'size'=>'posts__box'
					)
				);
			}
		echo '</div>';
	echo '</div>';
	echo '<div class="--blog-one-single--">';
		echo '<div class="posts_title"><a href="'.$PermaLink.'" data-trigger-url="'.$uniqid.'">'.$post->post_title.'</a></div>';
		echo '<div class="-P-content">'.$mini__post_content.'</div>';
	echo '</div>';
echo '</div>';