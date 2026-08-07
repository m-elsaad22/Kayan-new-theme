<?php 

$obj = get_queried_object();

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
$Styles['category'] = 'YourColor__Widgets/category.css';
$bimg = get_template_directory_uri().'/components/styles/img/service_shape_1.png';
$PostArguments = array('taxonomy'=>'category','number'=>30,'hide_empty'=>0);
$defualt__category_icon = get_option('defualt__category_icon');
$page_background = get_post_meta($post->ID, 'page_back_image', true);
if( !isset( $number ) ) $number = 8;
if (empty($page_background)) {
    $page_background = get_option('background_image');
}
if( isset( $taxonomy_option ) ){
	$get_terms = array();
	foreach ( $taxonomy_option as $tx__value ) {
		$s_tems = get_term_by('id',$tx__value,'category');
		if( isset( $s_tems->term_id ) ) $get_terms[] = $s_tems;
	}
}else{
	$TermsArgums =  array(
    'taxonomy' => 'category',
    'number'    =>$number,
);
	$get_terms = get_terms($TermsArgums);
}
$lazyload = get_option('lazyload');
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
				echo '<div class="-YC-Category-all-in">';
					echo '<div class="--YC-category-widget">';
						$VeDelay=0;
	            	 	$i = 0;
					 	foreach( $get_terms as $category){
					 		$uniqid = uniqid();
							$VeDelay = $VeDelay + 0.1;
	                        $CategoryName = $category->name;
	                        $CategoryURL = get_term_link($category);
	                        $hide_category_switch = get_term_meta( $category->term_id,'hide_category_switch',true );
	                        $but_text = get_term_meta( $category->term_id,'but_text',true );
	                        $catimg = get_term_meta( $category->term_id,'Image-Icon_id',true );
	                        echo '<div class="--single--category--boxitem" data-trigger-action="'.$uniqid.'">';
	                        	echo '<div class="YC--service-shabe-style">';
		                        	echo '<div class="Yc-service-item-style">';
		                        		echo '<div class="service--item--icon">';
		                        			echo '<div class="--YC-before-back">';
					                	    	if( !empty( $icon ) ){
						                        	echo ''.$icon.'';
						                        }else{
					                    			echo '<i class="fa-solid fa-broom"></i>';
						                        }
					                        echo '</div>';
				                        echo '</div>';
				                        echo '<div class="--YC-category--">';
				                        	echo '<a href="'.$CategoryURL.'" data-trigger-url="' .$uniqid. '" title="'.$category->name.'"><div class="YC-serice-name">'.$category->name.'</div></a>';
				                        	echo '<div class="-p-category-desc"><p>'.wp_trim_words($category->description,15).'</public></div>';
			                        		if( empty( $hide_category_switch ) ){
			                        			if( isset( $but_text ) && !empty( $but_text) ){
			                        				echo '<div class="-btn--category">';
			                        			 		echo '<span class="-category-button">' .$but_text. '</span>';
					                        			echo'<i class="fa-solid fa-arrow-left-long"></i>';
					                        		echo '</div>';
				                        		}else{
				                        			echo '<div class="-btn--category">';
			                        			 		echo '<span class="-category-button">اقراء اكثر</span>';
					                        			echo'<i class="fa-solid fa-arrow-left-long"></i>';
					                        		echo '</div>';
				                        		}
		                        			}
		                        		echo '</div>';
		                        	echo '</div>';
	                        	echo '</div>';
	                        echo '</div>';
	                        $i ++;
	                    }
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));