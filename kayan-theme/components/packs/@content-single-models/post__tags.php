<?php 

echo '<div class="-post-tag-boxarea">';
	echo '<div class="--widget--sidebar--title --single-tags-posts-title">الكلمات الدلائلية</div>';
	echo '<div class="-post-tag-items">';
		foreach ( $post_tag as $tag) {
			echo '<a href="'.get_term_link($tag).'" class="hoverable activable">'.$tag->name.'</a>';
			$ShareHastags[] = $tag->name;
		}
	echo '</div>';
echo '</div>';