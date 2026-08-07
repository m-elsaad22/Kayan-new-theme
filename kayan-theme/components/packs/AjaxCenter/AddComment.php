<?php
header("Content-type: application/json");
$json = array();
$GLOBPOST = $_POST;
if( isset($GLOBPOST['postID']) and isset($GLOBPOST['user_name']) && isset($GLOBPOST['email']) ) {
	$time = current_time('mysql');

	$approve = 0;
	if( get_option("default_comments_approval") == 'on' ) {
		$approve = 1;
	}

	$data = array(
	    'comment_post_ID' => $GLOBPOST['postID'],
	    'comment_author' => $GLOBPOST['user_name'],
	    'comment_author_email' => $GLOBPOST['email'],
	    'comment_content' => $GLOBPOST['comment'],
	    'comment_date' => $time,
	    'comment_approved' => $approve,
	);

	if( isset( $GLOBPOST['parent'] ) ){
		$data['comment_parent'] = $GLOBPOST['parent'];
	}

	if( is_user_logged_in() ) { global $current_user;
		$data['user_id'] = $current_user->ID;
		if( in_array('administrator', $current_user->roles) or in_array('author', $current_user->roles) or in_array('editor', $current_user->roles) ) $data['comment_approved'] = 1;
	}

	$cid = wp_insert_comment($data);
	if( !empty( $GLOBPOST['rate'] ) && $GLOBPOST['rate'] != '' ){
		update_comment_meta($cid, 'rating', $GLOBPOST['rate']);
	}
	update_comment_meta($cid, 'type', $GLOBPOST['customtype']);
	if( $GLOBPOST['rate'] >= 3 ) {
		update_post_meta($GLOBPOST['postID'], 'wp_review_comments_positive_count', (INT) get_post_meta($GLOBPOST['postID'], 'wp_review_comments_positive_count', true) + 1);
	}else {
		update_post_meta($GLOBPOST['postID'], 'wp_review_comments_negative_count', (INT) get_post_meta($GLOBPOST['postID'], 'wp_review_comments_negative_count', true) + 1);
	}
	$json['comment_ID'] = $cid;
	#

	$RatingData = array();

	$arguments = array(
		'status' => 'approve',
		#'number' => -1,
		'post_id' => $GLOBPOST['postID'],
		'parent'  => 0,
		"order"		=> 'ASC'
	);
	$comments = get_comments($arguments);
	$max = 0;
	foreach ($comments as $comment) {
		$rating = get_comment_meta($comment->comment_ID, 'rating', true);
		$max = $max+$rating;
		$RatingData[ $rating ] = $RatingData[ $rating ] + 1;
	}
	#
	update_post_meta( $GLOBPOST['postID'], '_wc_average_rating', mb_substr(($max / count($comments)), 0, 3) );
	update_post_meta(  $_POST['id'], '_wc_average_data',$RatingData );
	#
	ob_start();
	$this->ThemeStatic->Part("CommentItem", array("comment"=>get_comment($cid), "post"=>$GLOBPOST['postID']));
	$json['output'] = ob_get_clean();
}else {
	$json['error'] = true;
}
echo json_encode($json);