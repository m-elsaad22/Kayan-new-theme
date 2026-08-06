<?php 
$works_gallery = get_post_meta( $post->ID,'works_gallery',true );
$works_gallery = ( ( is_array( $works_gallery ) ) ) ? $works_gallery : array();
$thumbnail_id = get_post_thumbnail_id($post->ID);
if( empty( $thumbnail_id ) && !empty( $works_gallery ) ) {
	$i = 0;
	foreach ( $works_gallery as $att_id => $att_url ) {$i++;
		if( $i == 1 ) $thumbnail_id = $att_id;
	}
}

if( !isset( $stop_lazyload ) ) $stop_lazyload = true;
$client__name = get_post_meta( $post->ID,'client__name',true );
$services__type = get_post_meta( $post->ID,'services__type',true );
$services__rate = get_post_meta( $post->ID,'services__rate',true );
$lazyload = get_option('lazyload');
$final__gallery_items = array();
if( !empty( $works_gallery ) ){
	foreach ( $works_gallery as $img_att_id => $att_url ) {
		$YC_get_attachment = YC_get_attachment(
			array(
				'id'=>$img_att_id,
				'size'=>'full',
				'return__output'=>false,
			)
		);
		if( $YC_get_attachment != false ){
			$final__gallery_items[] = $YC_get_attachment;
		}
	}
}
echo '<div class="--single--work-post-box">';
	if( !empty( $thumbnail_id ) ){
		echo '<div class="--single--word-thumnail"'.( ( !empty( $final__gallery_items ) ) ? 'data-gallery-popover="'.base64_encode( json_encode( $final__gallery_items ) ).'"' : '').'>';
			
			echo '<div class="--single--thumb-image --is--larger--thumb-image'.( ( empty( $final__gallery_items ) )  ? ' --gallery--is-full-w' : '').'">';
				if( $stop_lazyload == true ) echo '<div class="-YC-Loader-Cover" style="background-image: url(' .$lazyload. ');"></div>';
				echo YC_get_attachment(
					array(
						'LazyLoad'=>$stop_lazyload,
						'id'=>$thumbnail_id,
						'alt'=>$post->post_title,
						'size'=> 'thumb__work__box',
					)
				);
			echo '</div>';
			echo '<div class="--work_top">';
				echo '<div class="--work--title--h3">'.$post->post_title.'</div>';
				if( !empty( $works_gallery ) ){
					echo '<div class="--minithumb-image">';
						$GI = 0;
						foreach ( $works_gallery as $att_id => $att_url ) {$GI++;
							if( $GI <= 3 ) {
								echo '<div class="--single--thumb-image">';
									if( $GI == 3 ){
										$other__count = count( $works_gallery ) - 3;
										echo '<div class="-more--work--gallery--button-">';
											echo '<em>+</em><strong>'.$other__count.'</strong>';
										echo '</div>';
									}
									if( $stop_lazyload == true ) echo '<div class="-YC-Loader-Cover" style="background-image: url(' .$lazyload. ');"></div>';
									echo YC_get_attachment(
										array(
											'LazyLoad'=>$stop_lazyload,
											'id'=>$att_id,
											'alt'=>$post->post_title,
											'size'=>'mini__work__box'
										)
									);
								echo '</div>';
							}
						}
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';
	}

	echo '<div class="--single--work--info">';
		if( !empty( $client__name ) || !empty( $services__type ) || !empty( $services__rate ) ) {
        	echo '<div class="single--work--table-items">';
                if( !empty( $client__name ) ){
                    echo '<div class="--single--table-item">';
                      	echo '<p>اسم العميل :</p>';
                      	echo '<span>'.$client__name.'</span>';
                    echo '</div>';
                }
                if( !empty( $services__type ) ){
                    echo '<div class="--single--table-item">';
                      	echo '<p>نوع الخدمة :</p>';
                      	echo '<span>'.$services__type.'</span>';
                    echo '</div>';
                }

			    if( !empty( $services__rate ) ){
 					echo '<div class="--single--table-item">';
                      	echo '<p>تقييم الخدمة :</p>';
			    		echo '<div class="--single--work-rating-stars">';
			            	echo '<div class="--single--work--Stars">';
				                for ($i=1; $i < 6 ; $i++) { 
				                    echo'<i class="fa-solid fa-star"></i>';
				                }
			                echo '</div>';

			                $ActivePerc = $services__rate * 100 / 5;
			                $ActivePerc = round($ActivePerc,2);
			            	echo '<div class="--single--work-Active--Stars" style="--bevalue:'.$ActivePerc.'%">';
				                for ($i=1; $i < 6 ; $i++) { 
				                    echo'<i class="fa-solid fa-star"></i>';
				                }
			            	echo'</div>';
			    		echo '</div>';
                    echo '</div>';
				}
        	echo'</div>';
		}


	echo '</div>';
echo '</div>';