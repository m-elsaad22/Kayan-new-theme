<?php 

$obj = get_queried_object();

$Styles = array();
$UniqId = uniqid();

$CategoryContent = $obj->description;
$CategoryContent = str_replace('<br/>', PHP_EOL, $CategoryContent);
$CategoryContent = str_replace('&nbsp;', ' ', $CategoryContent);
$CategoryContent = strip_tags($CategoryContent);
$MoreClass 		= '';
if( strlen($CategoryContent) > 500 ) {
	$CategoryContent = mb_substr($CategoryContent, 0, 500, 'utf-8').'... <a href="javascript:void(0);" data-button="readmore-objects" data-object-type="taxonomeis" data-object-name="'.$obj->taxonomy.'" data-object-id="'.$obj->term_id.'" class="readmore--category-item -BTN--hoverable">قراءة المزيد</a>';
}else{
	$CategoryContent = $CategoryContent;
}

$videoID = get_term_meta( $obj->term_id,'videoID',true );
$archive__posts_per_page = (INT) get_option('posts_per_page');
if( $archive__posts_per_page == 0 ) $archive__posts_per_page = 20;


$this->Part('header',array('Styles'=>$Styles));

echo '<div class="-primary-body">';

	echo '<div class="--primary--intro--pages">';
		echo '<div class="container">';

			echo '<div class="container-pages-head'.( ( !empty( $videoID ) ) ? ' --is--video-category' : '').'">';

				echo '<div class="--container--category--info">';

					echo '<h1>'.$obj->name.'</h1>';
					echo '<div class="--archive--be-content">'.( ( empty( $CategoryContent ) ) ? 'جميع مقالات '.$obj->name : $CategoryContent ).'</div>';

				echo '</div>';
				if( !empty( $videoID ) ){
					echo '<div class="-intro--page--category">';
						echo '<div class="--inner--intro--video">';
							echo'<iframe class="iframe" title="'.$obj->name.'" src="https://www.youtube.com/embed/'.$videoID.'?autoplay=1" frameborder="0"></iframe>';
						echo '</div>';
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
	echo '<div class="-Yc-breadcrumb-">';
		echo '<div class="container">';
			echo '<div class="YC-BreadCrumb -BreadCrumb-PT-'.$obj->taxonomy.'">';
				Breadcrumb();
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="-YC-Widgets-Inner-Row">';
		echo '<div class="container">';
			echo '<div class="-archive--container">';
				echo '<div class="-archivePage-Posts-Grid">';
					$this->Part(
				        'Posts',
				        array(
				            'object__type'=>'posts', # posts or taxonomy or users
				            'object__name'=>'post', # post_type or taxonomy name.
				            'part_object__name'=>'post',
				            'part__name'=>'Post-box',
				            'ObjectTerms'=>array($obj),
				            'ScrollLoader'=>true,
				            'per'=>8,
				            'show__empty_part'=>'object--empty',
				            'data__empty_part'=>array(
				                '__empty_icon'=>'<i class="fa-solid fa-ban"></i>',
				                '__empty_title'=>'لن يتم العثور على المقالات اخري',
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
$this->Part('footer',array('Styles'=>$Styles));