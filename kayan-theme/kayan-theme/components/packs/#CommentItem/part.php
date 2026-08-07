<?php
$UniqID = uniqid();
$class = ''; 
$status = 'زائر';
$verived = false;

if( $comment->user_id > 0 ) {
	$user = get_userdata($comment->user_id);
	if( isset( $user->ID ) ){
		if( in_array('administrator', $user->roles) or in_array('editor', $user->roles) or in_array('author', $user->roles) ) {
			$status = 'الدعم';
			$class = 'featured';
			$verived = true;
		}else {
			$status = 'عضو';
		}		
	}


}

$CommentContent = $comment->comment_content;
$CommentContent = str_replace('<br/>', PHP_EOL, $CommentContent);
$CommentContent = str_replace('&nbsp;', ' ', $CommentContent);
$CommentContent = strip_tags($CommentContent);

$date = 'منذ '.human_time_diff( date('U', strtotime($comment->comment_date)), current_time('timestamp') );
$rating = (INT) get_comment_meta($comment->comment_ID, 'rating', true);
$show_date = get_option( 'show_date' );
echo '<div class="-comment--single-item" id="comment-'.$comment->comment_ID.'">';
	echo '<div class="CommentContent">';
		echo '<div class="-comments-head-area">';
			echo '<div class="UserAvatar '.$class.'"><i class="fa-solid fa-user"></i></div>';
			echo '<div class="-comment-user-area">';
				echo '<div class="--user--comment-name">'.$comment->comment_author.'</div>';
				if( !empty( $show_date ) && $show_date == 'on' ){
					echo '<div class="comment-status">';
						echo '<div class="comment-bottom-bar-item CommentDate">'.$date.'</div>';
					echo '</div>';
				}
			echo '</div>';

			if( !empty( $rating ) ){
				$avg = $rating*100/5;
				echo '<div class="-productBox-rate-bar-average">';
					echo '<div class="stars-avg">';
						echo '<div class="d-flex -flex-center stars-avg-back">';
							echo '<i class="fa fa-star"></i>';
							echo '<i class="fa fa-star"></i>';
							echo '<i class="fa fa-star"></i>';
							echo '<i class="fa fa-star"></i>';
							echo '<i class="fa fa-star"></i>';
						echo '</div>';
						echo '<div class="d-flex -flex-center stars-avg-front" style="--percent:'.$avg.'%;">';
							echo '<i class="fa fa-star"></i>';
							echo '<i class="fa fa-star"></i>';
							echo '<i class="fa fa-star"></i>';
							echo '<i class="fa fa-star"></i>';
							echo '<i class="fa fa-star"></i>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}

		echo '</div>';

		echo '<div class="Context-Comments">';
			echo '<p>'.$CommentContent.'</p>';
		echo '</div>';

	echo '</div>';
echo '</div>';
$arguments = array(
	'status' => 'approve',
	'number' => '10',
	'post_id' => $post->ID,
	'parent'  => $comment->comment_ID
);
$comments = get_comments($arguments);
if( !empty($comments) ) {
	echo '<ul class="ChildComments">';
		foreach ($comments as $comment) {
			$this->Part("CommentItem", array("comment"=>$comment, "post"=>$post));
		}
	echo '</ul>';
}