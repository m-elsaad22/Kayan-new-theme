<?php 

echo '<div class="-post-reviews-feedback--area">';


	echo '<div class="-post-reviews-area -post-feedback">';

		echo '<div class="-FeedBack-Rating-MasterArea">';
			echo '<div class="--widget--sidebar--title --single-ratingbox-posts-title">قُم بإرسال تقيمك النهائي</div>';
			echo '<div class="-FeedBack-Rating -Rating-Master-Area">';
				echo '<div class="RatingReview" data-save-id="'.$post->ID.'" data-save-type="'.$post->post_type.'">';
					echo '<i data-rate="1" class="fas fa-star"></i>';
					echo '<i data-rate="2" class="fas fa-star"></i>';
					echo '<i data-rate="3" class="fas fa-star"></i>';
					echo '<i data-rate="4" class="fas fa-star"></i>';
					echo '<i data-rate="5" class="fas fa-star"></i>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="-YC-post-reviews-info Rate-New-Mixers"'.( ( $TotalRate_v1 > 0 ) ? '' : ' style="display:none;"' ).'>';

			echo '<div class="product-item-info-stats-ratings">';
				echo '<div class="-icon-info-stats-ratings">';
					echo '<span class="-rating-value" data-post-id="'.$post->ID.'">'.$TotalRate_v1.'</span>';
				echo '</div>';
				echo '<p class="d-flex -space-between -flex-center">';
					echo '<span class="-rating-label">تقييمات المستخدمين</span>';
					echo '<span class="-rating-suptitle" data-post-id="'.$post->ID.'"><em>'.$RatingCounter.'</em>مشترك في التقييم </span>';
				echo '</p>';
			echo '</div>';
		echo '</div>';

	echo '</div>';

	echo '<div class="-PostFeedBack-Rateing-Box Rate-New-Mixers"'.( ( $TotalRate_v1 > 0 ) ? '' : ' style="display:none;"' ).'>';

		echo '<div class="-Rate-Average-Items -Js-Rate-AverageItems" data-post-id="'.$post->ID.'">';
			for ($i=5; $i >= 1; $i--) { 
				if( isset( $RateValues[ $i ] ) ){
					$AverageCalc = $RateValues[ $i ] * 100 / $RatingCounter;
					$AverageCalc = round($AverageCalc,1);
					echo '<div class="-Rate-Average-element">';
						echo '<em>'.$i.'</em>';
						echo '<div class="-Rate-Average-Label"><div class="-Average--progress" data-progressload="'.$AverageCalc.'"></div></div>';
						echo '<span>'.$AverageCalc.'%</span>';
					echo '</div>';
				}
			}
		echo '</div>';

	echo '</div>';
echo '</div>';