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

	$phonenumber = get_post_meta( $post->ID,'phonenumber',true );
	if( empty( $phonenumber ) ) $phonenumber = get_option('phonenumber');


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

	# SETUP DATA LIST .
		$SiteMapList = array();

		$post__mapItems_list = get_option( 'post__mapItems_list' );
		if( !empty( $post__mapItems_list ) ){
			$post__mapItems_title = get_option( 'post__mapItems_title','مقالات' );
			$post__mapItems_sort = get_option( 'post__mapItems_sort');
			if( empty( $post__mapItems_sort ) ) $post__mapItems_sort = uniqid();
			$SiteMapList[ $post__mapItems_sort ] = array(
				'Items'=>$post__mapItems_list,
				'Title'=>$post__mapItems_title,
				'Object'=>'posts',
				'Type'=>'post'
			);
		}

		$page__mapItems_list = get_option( 'page__mapItems_list' );
		if( !empty( $page__mapItems_list ) ){
			$page__mapItems_title = get_option( 'page__mapItems_title','صفحات' );
			$page__mapItems_sort = get_option( 'page__mapItems_sort');
			if( empty( $page__mapItems_sort ) ) $page__mapItems_sort = uniqid();
			$SiteMapList[ $page__mapItems_sort ] = array(
				'Items'=>$page__mapItems_list,
				'Title'=>$page__mapItems_title,
				'Object'=>'posts',
				'Type'=>'page'
			);
		}

		$category__mapItems_list = get_option( 'category__mapItems_list' );
		if( !empty( $category__mapItems_list ) ){
			$category__mapItems_title = get_option( 'category__mapItems_title','التصنيفات' );
			$category__mapItems_sort = get_option( 'category__mapItems_sort');
			if( empty( $category__mapItems_sort ) ) $category__mapItems_sort = uniqid();
			$SiteMapList[ $category__mapItems_sort ] = array(
				'Items'=>$category__mapItems_list,
				'Title'=>$category__mapItems_title,
				'Object'=>'taxonomy',
				'Type'=>'category'
			);
		}

		$country__mapItems_list = get_option( 'country__mapItems_list' );
		if( !empty( $country__mapItems_list ) ){
			$country__mapItems_title = get_option( 'country__mapItems_title','المدن' );
			$country__mapItems_sort = get_option( 'country__mapItems_sort');
			if( empty( $country__mapItems_sort ) ) $country__mapItems_sort = uniqid();
			$SiteMapList[ $country__mapItems_sort ] = array(
				'Items'=>$country__mapItems_list,
				'Title'=>$country__mapItems_title,
				'Object'=>'taxonomy',
				'Type'=>'city'
			);
		}


	# HEADER .
	$this->Part('header',array('Styles'=>$Styles));	
	echo '<div class="YC-single-title">';
		echo '<div class="container">';
			echo '<div class="--YC-title-breadcrhumb-">';
				echo '<div class="single-post-title single-'.$post->post_type.'-post-title">';
					echo '<h1>'.$post->post_title.'</h1>';
				echo '</div>';
				echo '<div class="-single-post-content single-'.$post->post_type.'-post-content">';
					echo $post_content ;
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
		echo '<div class="-singular-pages-container">';
			echo '<div class="container">';

				echo '<div class="-Yc-single-main -YC-singleType-'.$post->post_type.'">';
					# SERVICE REQUST

					global $post;
					$category = get_the_terms( $post->ID,'category',true );
					$category = ( ( is_array( $category ) ) ) ? $category : array();

					if( isset( $title ) ){
						if( empty( $title_color ) ) $title_color = 'var(--uicolor2)';

						$title = str_replace('{%','<c--color style="--cword-color:'.$title_color.'">',$title);
						$title = str_replace('%}','</c--color>',$title);
					}

					$hide__sidebar__service_request = get_post_meta( $post->ID,'hide__sidebar__service_request',true );

					$post__service_request__data = get_post_meta( $post->ID,'post__service_request__data',true );
					$post__service_request__data = ( ( is_array( $post__service_request__data ) ) ) ? $post__service_request__data : array();

					if( ( !isset( $post__service_request__data['orderservicesy'] ) || ( isset( $post__service_request__data['orderservicesy'] ) && empty( $post__service_request__data['orderservicesy'] ) ) ) && isset( $defualt__title ) ) $post__service_request__data['orderservicesy'] = $defualt__title;
					if( ( !isset( $post__service_request__data['orderservicesy'] ) || ( isset( $post__service_request__data['orderservicesy'] ) && empty( $post__service_request__data['orderservicesy'] ) ) ) && !isset( $defualt__title ) ) $post__service_request__data['orderservicesy'] = 'اطلب الخدمة الان ';

					if( ( !isset( $post__service_request__data['contentservicesy'] ) || ( isset( $post__service_request__data['contentservicesy'] ) && empty( $post__service_request__data['contentservicesy'] ) ) ) && isset( $defualt__content ) ) $post__service_request__data['contentservicesy'] = $defualt__content;
					if( ( !isset( $post__service_request__data['contentservicesy'] ) || ( isset( $post__service_request__data['contentservicesy'] ) && empty( $post__service_request__data['contentservicesy'] ) ) ) && !isset( $defualt__content ) ) $post__service_request__data['contentservicesy'] = 'خدمة علي مدار 24 ساعه';

					$phonenumber = get_post_meta( $post->ID,'phone_number',true );
					if( empty( $phonenumber ) ) $phonenumber = get_option('phonenumber');
					# (نظام الطلب العائم القديم أُزيل — CTA الآن من fab-stack فقط)
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

							# APPEND MAP ITEMS .
								echo '<div class="--map--items--list">';
									if( !empty( $SiteMapList ) ){
										foreach ( $SiteMapList as $items__data ) {
											
								            echo '<div class="links--sitemap-items--box --box-type'.$items__data['Type'].'">';
									            echo '<div class="links--sitemap-items-title">';
									                echo'<h2>'.$items__data['Title'].'</h2>';
									            echo'</div>';
									            echo '<div class="links--sitemap-items-lists">';
									                foreach( $items__data['Items'] as $single__item ) {
								                       	if( $items__data['Object'] == 'taxonomy' ){
								                       		$Object = get_term_by( 'id',$single__item, $items__data['Type']);
								                       		if( isset( $Object->term_id ) ){
								                       			$single__link = get_term_link($Object);
								                       			$single__name = $Object->name;
										                        echo '<a href="'.$single__link.'" aria-label="'.$single__name.'">';
										                            echo '<p>'.$single__name.'</p>';
										                        echo '</a>';								                       			
								                       		}
								                       	}else if( $items__data['Object'] == 'posts' ){
								                       		$Object = get_post( $single__item);
								                       		if( isset( $Object->ID ) ){
								                       			$single__link = get_the_permalink($Object);
								                       			$single__name = $Object->post_title;
										                        echo '<a href="'.$single__link.'" aria-label="'.$single__name.'">';
										                            echo '<p>'.$single__name.'</p>';
										                        echo '</a>';								                       			
								                       		}
								                       	}								                        
								                    }
									            echo '</div>';
								        	echo '</div>';
										}
									}

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