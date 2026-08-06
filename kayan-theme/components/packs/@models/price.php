<?php 
$Styles = array();
$UniqId = uniqid();

$YC__WidgetsMachine = new YC__WidgetsMachine;

$widgets_price_page__meta = ( is_array( get_option( 'widgets_price_page__meta' ) ) ) ? get_option( 'widgets_price_page__meta' ) : array();
$widgets_price_page__meta = ( is_array( $widgets_price_page__meta ) ) ? $widgets_price_page__meta : array();

if( !empty( $widgets_price_page__meta ) ){
	$widgets__Enqueues = $YC__WidgetsMachine->widgets__Enqueues($widgets_price_page__meta);
	$Styles = array_merge($Styles,$widgets__Enqueues);
}				

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
$Styles['contact__form'] = 'YourColor__Widgets/contact__form.css';

$contactus_page__data = get_option('contactus_page__data');
$contactus_page__data = ( ( is_array( $contactus_page__data ) ) ) ? $contactus_page__data : array();
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
		# WIDGETS UI
			if( empty( $hide__sidebar__single ) && !empty( $widgets_price_page__meta ) ) {
				$YC__WidgetsMachine->widgets___UI(
					array(
						'Widgets_data'=>$widgets_price_page__meta,
						'WidgetID'=>'widgets_price_page__meta',
					)
				);
			}



	echo '</div>';
echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));