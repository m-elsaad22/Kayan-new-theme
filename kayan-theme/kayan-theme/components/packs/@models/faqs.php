<?php 
$Styles = array();
$UniqId = uniqid();
#
$post_content = $post->post_content;
$post_content = str_replace('<br/>', PHP_EOL, $post_content);
$post_content = str_replace('&nbsp;', ' ', $post_content);
$post_content = strip_tags($post_content);
$MoreClass 		= '';
if( strlen($post_content) > 350 ) {
	$post_content = mb_substr($post_content, 0, 350, 'utf-8').'... <a href="javascript:void(0);" data-button="readmore-objects" data-object-type="post_type" data-object-name="'.$post->post_type.'" data-object-id="'.$post->ID.'" class="readmore--category-item">قراءة المزيد</a>';

}else{
	$post_content = $post_content;
}
$Styles['Faqs__simple2'] = 'YourColor__Widgets/Faqs__simple2.css';

$PostArguments = array('post_type'=>'faq','posts_per_page'=>10);
$page_background = get_post_meta($post->ID, 'page_back_image', true);

if (empty($page_background)) {
    $page_background = get_option('background_image');
}
$this->Part('header',array('Styles'=>$Styles));

echo '<div class="-primary-body">';

	echo '<div class="--primary--intro--pages">';
		echo '<div class="container">';

			echo '<div class="container-pages-head">';

				echo '<div class="--container--category--info">';

					echo '<h1>'.$post->post_title.'</h1>';
					echo '<div class="--archive--be-content">'.$post_content.'</div>';

				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	echo '<div class="-Yc-breadcrumb-">';
		echo '<div class="container">';
			echo '<div class="YC-BreadCrumb -BreadCrumb-PT-'.$post->post_type.'">';
				Breadcrumb();
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	echo '<div class="-page--container-sidebars">';

		echo '<div class="-YC-Widgets-Inner-Row">';
			echo '<div class="container">';
				echo '<div class="-YC-FaqsSimple-Center-v1">';
					echo '<div class="-YC-FaqsSimple-ItemsCenter-v1">';
						$v__s = 0;
						$VeDelay=0;
						foreach ( get_posts( $PostArguments ) as $post ) { $v__s++;
							$VeDelay = $VeDelay + 0.1;
							$UN = uniqid();
							echo '<div class="--YC-faq-classes-in-- '.( ( $v__s == 1 ) ? ' active' : '').' animation-hidden"  data-animation-id="fadeInUpBig" data-animation-delay="'.$VeDelay.'s">';
								echo '<div class="-YC-FaqsSimple-Item-v1">';
									echo '<div class="-YC-FaqsSimple-Title" data-toggle-faqs="'.$UN.'">';
										echo '<h2>'.$post->post_title.'</h2>';
										echo '<div class="--YC-icon-faq-">';
											echo '<i class="fa-solid fa-plus"></i>';
										echo '</div>';
									echo '</div>';
									echo '<div class="-FaqsSimple-Content-Row-v1 -Toggle-Content">';
										echo '<div class="-p-FaqsSimple-ContentValue-v1 -ToggleContentValue">'.$post->post_content.'</div>';
									echo '</div>';
								echo '</div>';
							echo '</div>';
						}  
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

	echo '</div>';
echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));