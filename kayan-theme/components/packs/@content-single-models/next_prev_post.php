<?php 
echo '<div class="-next-prev-singular-posts">';
	$prev_post  = get_previous_post();
	$next_post  = get_next_post();
	if(isset($prev_post) and $prev_post != ''){
		echo '<div class="-Chevrons--NextPrev -chevron--prevPost">';
			echo '<a href="'.get_the_permalink($prev_post->ID).'">';
				echo '<div class="-Chevrons--NextPrev-poster"><i class="fa-solid fa-arrow-right"></i></div>';
				echo '<div class="inbox-pos">';
					echo '<span>المقال السابق </span>';
					echo '<h3>'.wp_trim_words($prev_post->post_title,5,'..').'</h3>';
				echo '</div>';
			echo '</a>';
		echo '</div>';
	}
	if(isset($next_post) and $next_post != ''){
		echo '<div class="-Chevrons--NextPrev -chevron--nextPost">';
			echo '<a href="'.get_the_permalink($next_post->ID).'">';
				echo '<div class="inbox-pos">';
					echo '<span>المقال التالى </span>';
					echo '<h3>'.wp_trim_words($next_post->post_title,5,'..').'</h3>';
				echo '</div>';
				echo '<div class="-Chevrons--NextPrev-poster"><i class="fa-solid fa-arrow-left"></i></div>';
			echo '</a>';
		echo '</div>';
	}

echo '</div>';