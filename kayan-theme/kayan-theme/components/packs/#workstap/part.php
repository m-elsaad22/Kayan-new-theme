<?php
if( !isset( $UniqID ) )  $UniqID = uniqid();
if( !isset( $Button__show ) ) $Button__show = '';
if( !isset( $Button__text ) || isset( $Button__text ) && empty( $Button__text ) ) $Button__text = '';


$currentURL = $this->GetCurrentURL();
$currentURL = str_replace('www.', '',$currentURL);
echo '<div class="-YC-Products-TabHead">';

	if( isset( $items__in__filter ) ){

		echo '<div class="-DropChevrons-UL '.( ( !isset( $Ajax_Cut ) ) ? 'animation-hidden" data-animation-id="fadeInUpBig"' : '"' ).' data-uniq="'.$UniqID.'">';
			echo '<ul>';
				$AllAjaxArgums = base64_encode( json_encode( array( 'taxonomy'=>'category','post_type'=>'works','BoxPart'=>'Works-box','Button__show'=>$Button__show,'Button__text'=>$Button__text) ) );
				echo '<li data-uniq="'.$UniqID.'" class="-Products-Filter-LI '.( ( !isset( $CurrentFilters[ 'category' ] ) ) ? 'active' : '' ).'" data-filter-tabs="'.$AllAjaxArgums.'">';
					echo '<span href="#" class="hoverable activable">';
						echo '<span>الكل</span>';
					echo '</span>';
				echo '</li>';
				foreach ( $items__in__filter as $item ) {
					$get__type = get_term_by('id',$item,'category' );
					if( isset( $get__type->term_id ) ){
						$Counters_transienst_id = $get__type->taxonomy.'products-counts-'.$get__type->term_id;
						$Check__productsArgums = $PostsArguments;
						unset( $Check__productsArgums['tax_query'] );
						$Check__productsArgums['tax_query']['relation'] = 'AND';
						$Check__productsArgums['tax_query'][] = array(
						    'taxonomy'  => $get__type->taxonomy,
						    'field'   => ($get__type->taxonomy == 'category') ? 'term_id' : 'slug',
						    'terms'   => ($get__type->taxonomy == 'category') ? $get__type->term_id : $get__type->slug,
						    'operator'  => 'IN'
						);
						$type__products__count = get_transient($Counters_transienst_id);
						if( empty( $type__products__count ) || isset( $_GET['force'] ) ) {
							$Founder__chields = new WP_Query($Check__productsArgums);
							$type__products__count = $Founder__chields->found_posts;
							set_transient($Counters_transienst_id,$type__products__count,3600);
						}
						if( $type__products__count > 0 ){
							$FixAjaxArgums = base64_encode( json_encode( array( 'taxonomy'=>$get__type->taxonomy,'id'=>$get__type->term_id,'post_type'=>'works','BoxPart'=>'Works-box','Button__show'=>$Button__show,'Button__text'=>$Button__text) ) );
							if( empty( $Icon ) ) $Icon = '';
							echo '<li data-uniq="'.$UniqID.'" class="-Products-Filter-LI '.( ( isset( $CurrentFilters[ $get__type->taxonomy ]->term_id ) && $CurrentFilters[ $get__type->taxonomy ]->term_id == $get__type->term_id ) ? 'active' : '' ).'" data-filter-tabs="'.$FixAjaxArgums.'">';
								echo '<span class="hoverable activable">';
									echo '<span>'.$get__type->name.'</span>';
								echo '</span>';
							echo '</li>';
						}
					}
				}
			echo '</ul>';
		echo '</div>';		
	}
echo '</div>';

echo '<div class="-posts-fix-boxes-items">';
	echo '<div class="-Taps-AppendCenter" data-arguments="'. base64_encode( json_encode( $PostsArguments ) ) .'" data-uniq="'.$UniqID.'">';
		echo ( ( isset( $Ajax_Cut ) ) ) ? '<Filter-Posts-Cut>' : '';
			$VeDelay = 0;
			foreach ( get_posts( $PostsArguments ) as $post) {
				$VeDelay = $VeDelay + 0.1;
				if( isset( $Ajax_Cut ) ){
					$this->Part('Works-box',array('post'=>$post ,'stop_lazyload'=>false));
				}else{
					$this->Part('Works-box',array('post'=>$post,'animation'=>$VeDelay));
				}
			}

		echo ( ( isset( $Ajax_Cut ) ) ) ? '</Filter-Posts-Cut>' : '';
	echo '</div>';
	if( $Button__show != '' ){
		echo ( ( isset( $Ajax_Cut ) ) ) ? '<Filter-Button-Cut>' : '';
			echo '<div class="more-btn-blog-posts">';
			
				echo '<a href="'.(( isset( $CurrentFilters[ 'types' ] ) ) ? get_term_link($CurrentFilters[ 'types' ]) : home_url('products') ).'">';
					echo '<i class="fa-solid fa-square-plus"></i>';
					echo ( ( $Button__text != '' ) ) ? $Button__text : 'المزيد من المقالات';
				echo '</a>';
			echo '</div>';
		echo ( ( isset( $Ajax_Cut ) ) ) ? '</Filter-Button-Cut>' : '';
	}
echo '</div>';