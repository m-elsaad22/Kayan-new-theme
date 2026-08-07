<?php 

$related__sections_data = get_option('related__sections_data');
$related__sections_data = ( ( is_array( $related__sections_data ) ) ) ? $related__sections_data : array();

$related__sections_data['related__per_category'] = ( ( isset( $related__sections_data['related__per_category'] ) && is_numeric( $related__sections_data['related__per_category'] ) ) ) ? $related__sections_data['related__per_category'] : 10;
$related__sections_data['related__title'] = ( ( isset( $related__sections_data['related__title'] ) ) ) ? $related__sections_data['related__title'] : 'مقالات اخري من قسم';
$related__sections_data['related__per_page'] = ( ( isset( $related__sections_data['related__per_page'] ) ) ) ? $related__sections_data['related__per_page'] : 4;

# RELATED .
	if( $related__sections_data['related__per_category'] > 0 ){

		$taxonomy = 'category';
		if( isset( $Related_Terms[ $taxonomy ] ) && !empty( $Related_Terms[ $taxonomy ] ) ){

			echo '<div class="-YC-related-posts">';
				$color__icon = [1,3,5,7,9,11,13,15,17,19,21];
				$category__counter = 0;
				$color_counter = 0;
				foreach ( $Related_Terms[$taxonomy] as $term) {$category__counter++;

					if( $category__counter <= $related__sections_data['related__per_category'] ){
					    $args = array(
					    	'posts_per_page'=>$related__sections_data['related__per_page'],
					    	'post_type'=>'post',
					    	'orderby'=>'rand',
					    	'post__not_in'=>array($post->ID),
					    	'tax_query'=>array(
					    		'relation'=>'AND',
					    		array(
					    			'taxonomy'=>$term->taxonomy,
					    			'field'=>(($term->taxonomy == 'category')) ? 'term_id' : 'slug',
					    			'terms'=>(($term->taxonomy == 'category')) ? $term->term_id : $term->slug,
					    			'operator'=>'IN'
					    		),
					    	)
					    );
					    #
						$Founder = new WP_Query($args);
						$Count = $Founder->found_posts;
						if( $Count > 0 ){

							$color_counter++;
							echo '<div class="-Related-Single -Box-SingleItem'.( ( in_array( $color_counter, $color__icon ) ) ? ' --related--singular-insert' : '' ).'">';		
								echo '<div class="container">';
									echo '<div class="-TitleContent-section">';
										echo '<span>'.$related__sections_data['related__title'].' </span>';
										echo '<p>'.$term->name.'</p>';
										echo ( ( !isset( $related__sections_data['hide__related__more'] ) || isset( $related__sections_data['hide__related__more'] ) && empty( $related__sections_data['hide__related__more'] ) ) ) ? '<a href="'.get_term_link($term).'" class="-BTN--hoverable"><span>عرض المزيد </span><i class="fa-solid fa-arrow-left"></i></a>' : '';
									echo '</div>';

									echo '<div class="-Posts-RelatedBoxes">';
										foreach (get_posts($args) as $rpost) {
										    	$this->Blade('Box',array('post'=>$rpost),'Post-box');
										}
									echo '</div>';
								echo '</div>';

							echo '</div>';
						}
					}
				}

			echo '</div>';
		}
				
	}
