<?php
$UniqID = uniqid();
if( !isset( $ShareTags ) ) $ShareTags = ''; 

if( is_array( $ShareTags ) ){
	$FinalShareTags = ( ( !empty( $ShareTags ) ) ) ? implode(',', $ShareTags) : '';
}else{
	$FinalShareTags = $ShareTags;
}

$Description = wp_trim_words($Description,10,'..');
if( !isset( $Picture ) || empty( $Picture ) ) $Picture = '';

if( !isset( $section__title ) ) $section__title = 'مشاركة المقال';

echo '<div class="-single-share-posts-area">';

	echo '<div class="-single-share-header">';
		echo '<div class="--widget--sidebar--title --single-share-posts-title">'.$section__title.'</div>';
	echo '</div>';


	echo '<div class="-share-icons-list">';

		echo '<div class="-YC-owl-navs-items">';
			echo "<div class='-YC-owl-Slides-prev -custom-owl-Slides-prev' data-owlnavs-change='".$UniqID."' data-type='prev'><i class='fa-solid fa-arrow-right'></i></div>";
			echo "<div class='-YC-owl-Slides-next -custom-owl-Slides-next' data-owlnavs-change='".$UniqID."' data-type='next'><i class='fa-solid fa-arrow-left'></i></div>";
		echo '</div>';

		echo '<div class="-itemslist-share-icons-list" data-uniq="'.$UniqID.'" data-count="23">';
			echo '<div class="--sp-social-item facebook" data-sharer="facebook" data-url="'.$Permalink.'" data-hashtag="#'.str_replace(' ', '_', $Title).'" data-quote="'.$Title.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fab fa-facebook"></i>';
					echo '<span>facebook</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item messenger" data-sharer="messenger" data-url="'.$Permalink.'" data-title="'.$Title.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fab fa-facebook-messenger"></i>';
					echo '<span>messenger</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item whatsapp" data-sharer="whatsapp" data-url="'.$Permalink.'"'.( ( !wp_is_mobile() ) ? ' data-web="true"' : '' ).' data-link="true" data-blank="true" data-title="'.$Title.'"b" data-custom-class="socialTips">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fab fa-whatsapp"></i>';
					echo '<span>whatsapp</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item telegram" data-sharer="telegram" data-url="'.$Permalink.'" data-title="'.$Title.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fab fa-telegram"></i>';
					echo '<span>telegram</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item twitter" data-sharer="twitter" data-url="'.$Permalink.'" data-hashtag="'.$FinalShareTags.'" data-title="'.$Title.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fab fa-twitter"></i>';
					echo '<span>twitter</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item linkedin" data-sharer="linkedin" data-url="'.$Permalink.'" data-title="'.$Title.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-linkedin"></i>';
					echo '<span>linkedin</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item viber" data-sharer="viber" data-title="'.$Title.'" data-url="'.$Permalink.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-viber"></i>';
					echo '<span>viber</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item pinterest" data-sharer="pinterest" data-url="'.$Permalink.'" data-title="'.$Title.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-pinterest"></i>';
					echo '<span>pinterest</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item tumblr" data-sharer="tumblr" data-caption="'.$Title.'" data-title="'.$Title.'" data-tags="'.$FinalShareTags.'" data-url="'.$Permalink.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-tumblr"></i>';
					echo '<span>tumblr</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item hackernews" data-sharer="hackernews"  data-title="'.$Title.'" data-url="'.$Permalink.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-hacker-news"></i>';
					echo '<span>hackernews</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item reddit" data-sharer="reddit" data-title="'.$Title.'" data-url="'.$Permalink.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-reddit"></i>';
					echo '<span>reddit</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item vk" data-sharer="vk" data-caption="'.$Title.'" data-title="'.$Title.'" data-url="'.$Permalink.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-vk"></i>';
					echo '<span>vk</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item buffer" data-sharer="buffer" data-title="'.$Title.'" data-via="ellisonleao" data-picture="'.$Picture.'" data-url="'.$Permalink.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-buffer"></i>';
					echo '<span>buffer</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item xing" data-sharer="xing" data-title="'.$Title.'" data-url="'.$Permalink.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-xing"></i>';
					echo '<span>xing</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item line" data-sharer="line" data-title="'.$Title.'" data-url="'.$Permalink.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-line"></i>';
					echo '<span>line</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item pocket" data-sharer="pocket" data-url="'.$Permalink.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-get-pocket"></i>';
					echo '<span>pocket</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item flipboard" data-sharer="flipboard" data-title="'.$Title.'" data-url="'.$Permalink.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-flipboard"></i>';
					echo '<span>flipboard</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item weibo" data-sharer="weibo" data-url="'.$Permalink.'" data-title="'.$Title.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-weibo"></i>';
					echo '<span>weibo</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item blogger" data-sharer="blogger" data-url="'.$Permalink.'" data-title="'.$Title.'" data-description="'.$Description.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-blogger"></i>';
					echo '<span>blogger</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item okru" data-sharer="okru" data-url="'.$Permalink.'" data-title="'.$Title.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-odnoklassniki"></i>';
					echo '<span>okru</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item evernote" data-sharer="evernote" data-url="'.$Permalink.'" data-title="'.$Title.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-evernote"></i>';
					echo '<span>evernote</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item skype" data-sharer="skype" data-url="'.$Permalink.'" data-title="'.$Title.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-skype"></i>';
					echo '<span>skype</span>';
				echo '</a>';
			echo '</div>';

			echo '<div class="--sp-social-item trello" data-sharer="trello" data-url="'.$Permalink.'" data-title="'.$Title.'" data-description="'.$Description.'">';
				echo '<a href="'.$Permalink.'" data-navigate-off="true">';
					echo '<i class="fa-brands fa-trello"></i>';
					echo '<span>trello</span>';
				echo '</a>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="-share-popover-boxed-copy">';
		echo '<input type="text" readonly value="'.$Permalink.'" aria-label="copylink" />';
		echo '<button class="-copy activable"  aria-label="نسخ الرابط  "><em>نسخ الرابط  </em><span>تم النسخ </span></button>';
	echo '</div>';

echo '</div>';