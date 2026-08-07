<?php
$UniqID = uniqid();
# POST CONTENT.
	ob_start();
	the_content();
	$post_content = ob_get_clean();

# PAGE SETUP .	
	$YC__WidgetsMachine = new YC__WidgetsMachine;
	# Style FILES.
		$Styles = array();

		$Path_style__Intro = $this->StylesPath.'singular';
		$StyleFields__Intro = array();
		#
		foreach( glob($Path_style__Intro.'/*.css') as $cs__file ) {
			$current_file_name = explode('singular/', $cs__file)[1];
			$current_file_name = explode('/', $current_file_name)[0];
			$current_file_name = explode('.css', $current_file_name)[0];
			$StyleFields__Intro[] = $current_file_name;
		}
		$Styles['shortcodes'] = 'shortcodes.css';
		$Styles[$post->post_type] = 'singular/single.css';

#

$ShareHastags = array();

# PAGE CONTENT.

	$permalink = get_the_permalink($post->ID);

	# IMAGE THUMBNAIL .
		$post_thumb = get_the_post_thumbnail_url($post->ID);
		$Image__size__value = ( wp_is_mobile() ) ? 'mobile_post_size' : 'post_single_size';
		$post_thumb_id = get_post_thumbnail_id($post->ID);
	#
	$publishedAt = date('Y-m-d',strtotime($post->post_date));
	$DayDate = StaticThemeDate($publishedAt,'d F Y');

	$position__post_card = get_post_meta( $post->ID,'position__post_card',true );
	if( empty( $position__post_card ) ) $position__post_card = 'top_content';

	#
	$whatsapp_number = get_post_meta( $post->ID,'whatsapp_number',true );
	if( empty( $whatsapp_number ) ) $whatsapp_number = get_option('whatsapp_number');

	$phonenumber = get_post_meta( $post->ID,'phone_number',true );
	if( empty( $phonenumber ) ) $phonenumber = get_option('phonenumber');


	# POPOVER EDITS
		$hide__page__popover = get_option('hide__page__popover');
		$PopOver__Attr = '';
		if( empty( $hide__page__popover ) ) {
			$post__popover__data = get_post_meta( $post->ID,'post__popover__data',true );
			$post__popover__data = ( ( is_array( $post__popover__data ) ) ) ? $post__popover__data : array();
			if( !empty( $post__popover__data ) ){
				$post__popover__data['whatsapp_number'] = $whatsapp_number;
				$post__popover__data['phonenumber'] = $phonenumber;
				$PopOver__Attr = ' data-scroll-popover="'.base64_encode( json_encode( $post__popover__data ) ).'"';
			}
		}

	# SINGLE OPTIONS .
		$hide__thumbnail__pages = get_option('hide__thumbnail__pages');
		$hide__post__references = get_option('hide__pages__references');
		$hide__post__faqs = get_option('hide__pages__faqs');
		$hide__pages__shares = get_option('hide__pages__shares');

		# SINGLE SIDE BAR .
			$hide__sidebar__single = get_option('hide__sidebar__pages');
			if( empty( $hide__sidebar__single ) ){

				$widgets_pages__meta = ( is_array( get_option( 'widgets_pages__meta' ) ) ) ? get_option( 'widgets_pages__meta' ) : array();
				$widgets_pages__meta = ( is_array( $widgets_pages__meta ) ) ? $widgets_pages__meta : array();

				if( !empty( $widgets_pages__meta ) ){
					$widgets__Enqueues = $YC__WidgetsMachine->widgets__Enqueues($widgets_pages__meta);
					$Styles = array_merge($Styles,$widgets__Enqueues);
				}
			}

	# HEADER .
	$this->Part('header',array('Styles'=>$Styles));	
	
	echo '<div class="YC-single-title">';
		echo '<div class="container">';
			echo '<div class="--YC-title-breadcrhumb-">';
				echo '<div class="single-post-title single-'.$post->post_type.'-post-title">';
					echo '<h1>'.$post->post_title.'</h1>';
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
	# MAIN SINGLE CONTAINER .	
		echo '<div class="-singular-pages-container"'.$PopOver__Attr.'>';
			echo '<div class="container">';

				echo '<div class="-Yc-single-main -YC-singleType-'.$post->post_type.'">';
				
					# CENTER CONTENT.
						echo '<div class="single-content-context-elements">';

							echo '<div class="-secodary-single-post-bar for-content-single">';

								# POST CARD .
									if( $position__post_card == 'top_image' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

								if( !empty( $post_thumb_id ) && empty( $hide__thumbnail__pages ) ){
									echo '<div class="-single-parent-flexes--content-inner-thumb">';
										echo '<div class="-single-parent-flexes--content-inner-thumb--inner">';
											echo YC_get_attachment(
												array(
												    'id' => $post_thumb_id,
												    'size' => $Image__size__value,
												    'alt' => $post->post_title,
												)
											);
										echo '</div>';
									echo '</div>';
								}

								
								# POST CARD .
									if( $position__post_card == 'top_content' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

								# POST CONTENT 	
									echo '<div class="-single-post-content standard-page single-'.$post->post_type.'-post-content">';
										do_action('yc_hook_ad_location_content_above');
										#
										echo $post_content;
										#
										do_action('yc_hook_ad_location_content_below');
									echo '</div>';

								# POST CARD .
									if( $position__post_card == 'bottom_content' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );


								# POST REFERNCES .
									if( empty( $hide__post__references ) ) $this->Blade('content-single-models',array('post'=>$post ),'post__references' );

								# POST CARD .
									if( $position__post_card == 'before__references' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

								# POST FAQS .
									if( empty( $hide__post__faqs ) ) $this->Blade('content-single-models',array('post'=>$post ),'post__faqs' );

								# POST CARD .
									if( $position__post_card == 'before_faqs' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

								# POST CARD .
									if( $position__post_card == 'end__page' ) $this->Blade('content-single-models',array('post'=>$post,'whatsapp_number'=>$whatsapp_number,'phonenumber'=>$phonenumber ),'post__card' );

							echo '</div>';


							# POST SHARE .	
								if( empty( $hide__pages__shares ) ) $this->Blade('content-single-models',array('Title'=>$post->post_title,'Permalink'=>$permalink,'ShareTags'=>$ShareHastags,'Description'=>$post->post_content,'Picture'=>$post_thumb,'section__title'=>'مشاركة' ),'ShareItems' );			
						echo '</div>';

					# SIDEBAR POSTS .
						if( empty( $hide__sidebar__single ) && !empty( $widgets_pages__meta ) ) {
							$YC__WidgetsMachine->widgets___UI(
								array(
									'Widgets_data'=>$widgets_pages__meta,
									'WidgetID'=>'widgets_pages__meta',
									'Parent__section__class'=>'-first-single-post-bar',
									'Single__section__class'=>'--Single--page--widget-item',
									'section_InnerRow_class'=>'Single--page-widget-innerRow',
								)
							);
						}
				echo '</div>';
			echo '</div>';
		echo '</div>';

	# FOOTER .
		$this->Part('footer',array('Styles'=>$Styles));