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

				echo '<div class="-archive--container">';

					echo '<div class="-archivePage-Posts-Grid">';
						$this->Part(
					        'Posts',
					        array(
					            'object__type'=>'posts', 
					            'object__name'=>'post', 
					            'part_object__name'=>'post',
					            'part__name'=>'Post-box',
					            'ScrollLoader'=>true,
					            'per'=>9,
					            'show___empty__part'=>'object--empty',
					            'data___empty__part'=>array(
					                '__empty_icon'=>'<i class="fa-solid fa-ban"></i>',
					                '__empty_title'=>'لن يتم العثور على المقالات ',
					                '__empty_description'=>'<a href="'.home_url().'">الرئيسية </a>',
					                '__Ajax_empty_title'=>'لقد شاهدت جميع الالمقالات',
					                '__Ajax_empty_description'=>'تم عرض جميع المقالات قسم <strong></strong><a href="'.home_url().'">الرئيسية </a>',

					            ),
					        )
					    );
					echo '</div>';

				echo '</div>';
			echo '</div>';
		echo '</div>';

	echo '</div>';

echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));