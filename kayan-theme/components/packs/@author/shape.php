<?php 

$Styles = array();
$UniqId = uniqid();
#
$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));

$this->Part('header',array('Styles'=>$Styles));

echo '<div class="-primary-body">';

	echo '<div class="--primary--intro--pages">';
		echo '<div class="container">';

			echo '<div class="container-pages-head">';

				echo '<div class="--container--category--info">';

					echo '<h1>'.$curauth->display_name.'</h1>';
					echo '<div class="--archive--be-content">صفحة مقالات الكاتب '.$curauth->display_name.'</div>';

				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
	echo '<div class="-Yc-breadcrumb-">';
		echo '<div class="container">';
			echo '<div class="YC-BreadCrumb -BreadCrumb-PT-author">';
				Breadcrumb();
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="-YC-Widgets-Inner-Row">';
		echo '<div class="container">';

			echo '<div class="-archive--container">';

				echo '<div class="-archivePage-Posts-Grid">';
					$this->Part('Posts',array('AutoLoadmore'=>false,'UniqId'=>$UniqId,'AutoLoadmore'=>true,'author'=>$curauth->ID));
				echo '</div>';

			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));