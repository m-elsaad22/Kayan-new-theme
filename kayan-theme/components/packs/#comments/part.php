<?php
$comment_num = get_comments_number($post->ID);
$comments__section__title = get_option('comments__section__title');
$comments__section__title = ( ( !empty( $comments__section__title ) ) ) ? $comments__section__title : 'اترك تعليقاً ';

$comments__section__content = get_option('comments__section__content');
$comments__section__content = ( ( !empty( $comments__section__content ) ) ) ? $comments__section__content : 'لن يتم نشر عنوان بريدك الإلكتروني. الحقول الإلزامية مشار إليها بـ *';


echo '<div class="single-post-parent-container-comments">';
	echo '<div class="-singular-comments-post">';
		echo '<div class="-comments-titlecontext-inner">';
			echo '<div class="--widget--sidebar--title">'.$comments__section__title.'</div>';
			echo '<p>'.$comments__section__content.'</p>';
		echo '</div>';

		##echo '<comment-counter>'.( ( $comment_num > 0 ) ? '<em>'.$comment_num.'</em>' : '' ).'</comment-counter>';
	echo '</div>';	

	echo '<form excludeform action="'.home_url().'" data-parent="0" data-id="'.$post->ID.'" presestform class="CommentsFormInner" method="POST" onsubmit="SubmitComment(this);return false;">';

		echo '<div class="alerts"></div>';
		if( is_user_logged_in() ) { global $current_user;
			echo '<div class="-comments-form-inputs-area" data-comment-field="user_name"><input type="text" disabled name="user_name" value="'.$current_user->display_name.'" placeholder="إسمك الكريم *" /></div>';
			echo '<div class="-comments-form-inputs-area" data-comment-field="email"><input type="email" disabled name="email" value="'.$current_user->user_email.'" placeholder="بريدك الإلكتروني *" /></div>';
		}else{
			echo '<div class="-comments-form-inputs-area" data-comment-field="user_name"><input type="text" name="user_name" data-input-cook="user_name" placeholder="إسمك الكريم *" /></div>';
			echo '<div class="-comments-form-inputs-area" data-comment-field="email"><input type="email" name="email"  data-input-cook="email" placeholder="بريدك الإلكتروني *" /></div>';
		}

		$keyCode = "this.closest('form')";
		echo '<div class="-comment-contentarea d-flex">';
			echo '<div class="-comments-form-inputs-area -comments--textarea" data-comment-field="comment"><textarea name="comment" placeholder="أكتب التعليق هنا .." onkeypress="return SubmitComment('.$keyCode.',event);"></textarea></div>';
			echo '<div class="RateComment d-flex -flex-gutter -flex-gutter-small">';
				echo '<div class="RatingReview">';
					echo '<input type="hidden" class="RateValue" name="rate" />';
					echo '<i data-rate="1" class="fas fa-star"></i>';
					echo '<i data-rate="2" class="fas fa-star"></i>';
					echo '<i data-rate="3" class="fas fa-star"></i>';
					echo '<i data-rate="4" class="fas fa-star"></i>';
					echo '<i data-rate="5" class="fas fa-star"></i>';
				echo '</div>';
				echo '<div class="product-item-info-stats-ratings">';
					echo '<p class="d-flex -space-between -flex-center">';
						echo '<span class="-rating-label">التقييم</span>';
						echo '<span class="-rating-value">5.0</span>';
					echo '</p>';
				echo '</div>';
			echo '</div>';
			
		echo '</div>';

		echo '<div class="-comments-form-Button-area"><button class="hoverable activable" type="submit">إرســال التعليق <i class="fa-solid fa-arrow-left"></i></button></div>';
	echo '</form>';
	$arguments = array(
		'status' => 'approve',
		'number' => '15',
		'post_id' => $post->ID,
		'parent'  => 0
	);
	$comments = get_comments($arguments);
	$totalcomments = wp_count_comments($post->ID)->approved;
	if( count($comments) > 0 ) {
		echo '<div class="CommentsList" data-id="'.$post->ID.'">';
			echo '<div class="CommentsList__Title">'.NumberReader($totalcomments).' تعليقات</div>';
			echo '<div class="CommentsListInner">';
				foreach ($comments as $comment) {
					$this->Part("CommentItem", array("comment"=>$comment, "post"=>$post));
				}
			echo '</div>';
		echo '</div>';
	}else {
		echo '<div class="CommentsList" data-id="'.$post->ID.'">';
			echo '<div class="CommentsListInner">';
				echo '<div class="NoComments">';
					echo '<i class="fas fa-info-circle"></i>';
					echo 'لم يتم إضافة تعليقات لهذا المقال.';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
echo '</div>';