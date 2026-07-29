<?php 
$search_query = get_search_query();
$search_query = trim( $search_query );
$Styles = array();
$UniqId = uniqid();
#
$args1 = array(
	"post_type"			=> 'post',
	"posts_per_page"	=> 28,
	"s"			=> $search_query,
);
global $wpdb;
$Value_search_query = '%' . $wpdb->esc_like( $search_query ) . '%';
$myposts = $wpdb->get_results( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title LIKE %s AND post_status = 'publish'", $Value_search_query) );


$this->Part('header',array('Styles'=>$Styles));

echo '<div class="-primary-body">';

	echo '<div class="--primary--intro--pages">';
		echo '<div class="container">';
			echo '<div class="container-pages-head">';
				echo '<div class="--container--category--info">';
					echo '<div class="YC-BreadCrumb">';
						Breadcrumb();
					echo '</div>';
					echo '<h1>نتائج البحث عن '.esc_html( $search_query ).'</h1>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="-YC-Widgets-Inner-Row">';
		echo '<div class="container">';

			if( isset( $myposts ) && !empty( $myposts )  ){
				$post_ids = array(); // Create an array to store post IDs

				foreach ( $myposts as $mypost ) {
				    $post_ids[] = $mypost->ID; // Store each post ID in the array
				}

				$args = array(
				    "post_type"         => 'post',
				    "posts_per_page"    => 28,
				    "post__in"          => array_values($post_ids), // Correctedsyntax
				);
								echo '<div class="-archive--container">';

					echo '<div class="-archivePage-Posts-Grid">';
						$this->Part(
					        'Posts',
					        array(
					            'object__type'=>'posts', # posts or taxonomy or users
					            'object__name'=>'post', # post_type or taxonomy name.
					            'part_object__name'=>'post',
					            'part__name'=>'Post-box',
					            'ScrollLoader'=>true,
					             'PostsArguments'=>$args,
					            'per'=>20,
					            'show___empty__part'=>'object--empty',
					            'data___empty__part'=>array(
					                '__empty_icon'=>'<i class="fa-solid fa-ban"></i>',
					                '__empty_title'=>'لم يتم العثور  علي  "'.esc_html( $search_query ).'"',
					                '__empty_description'=>'<a href="'.home_url().'">الرئيسية </a>',
					                '__Ajax_empty_title'=>'لم يتم العثور  علي  "'.esc_html( $search_query ).'"',
					                '__Ajax_empty_description'=>'تم عرض جميع المقالات قسم <strong></strong><a href="'.home_url().'">الرئيسية </a>',

					            ),
					        )
					    );
					echo '</div>';

				echo '</div>';
			}else{
				print_r($myposts);
				print(1);
		        echo '<div class="--remove-insert--post">';
		            $this->Blade( 'empty__objects', array(
			                '__empty_icon'=>'<i class="fa-solid fa-ban"></i>',
			                '__empty_title'=>'لم يتم العثور  علي  "'.esc_html( $search_query ).'"',
			                '__empty_description'=>'<a href="'.home_url().'">الرئيسية </a>',
			                '__Ajax_empty_title'=>'لم يتم العثور  علي  "'.esc_html( $search_query ).'"',
			                '__Ajax_empty_description'=>'تم عرض جميع المقالات قسم <strong></strong><a href="'.home_url().'">الرئيسية </a>',

			            )
		            , 'object--empty');
		        echo '</div>';
			}
		echo '</div>';
	echo '</div>';

echo '</div>';

$this->Part('footer',array('Styles'=>$Styles));