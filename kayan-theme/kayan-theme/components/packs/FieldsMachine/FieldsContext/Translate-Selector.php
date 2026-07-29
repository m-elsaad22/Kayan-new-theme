<?php  if( class_exists('Relations__languages__locate') ) {
	$Relations__languages__locate = new Relations__languages__locate;

	if( !isset( $UniqId ) ) $UniqId = uniqid();
	# SELECT ITEMS BOX .
		if( !isset( $box__part ) ) $box__part = 'Translate-Select-Box';

	# SHOW PERVIEW ITEMS -> DEFULT FALSE
		if( !isset( $show__perview__items ) ) $show__perview__items = false;

	# OBJECT TYPES ( 'posts' || 'taxonomy' || 'users'  )
		if( !isset( $object__type ) ) $object__type = 'posts';

	# OBJECT NAME -> 'post_type' OR 'taxonomy' => name ... IF 'users' DEFULT -> false;
		if( !isset( $object__name ) && $object__type != 'users' ) $object__name = 'post';
		if( !isset( $object__name ) && $object__type == 'users' ) $object__name = false;

	# PER -> POSTS PER PAGE OR NUMBER (INTEGER) .
		if( !isset( $per ) ) $per = 50;

	# SAVE TAXONOMY BY -> DEFULT 'term_id'
		if( !isset( $taxonomy_field ) ) $taxonomy_field = 'term_id';

	# BUILDING INPUT NAME .
		unset($vars['InsertElements']);

		if( isset( $InsertElements ) ){
			$InputName = 'Insert_'.$id;
		}else if( isset( $parent_id ) ){
			$InputName = $parent_id.'['.$id.']';
		}else{
			$InputName = $id;
		}

	# BULIDING QUERY ARGUMENTS .
		
		# IF NOT SEND '$PostsArguments';

			if( !isset( $PostsArguments ) ){

				if( $object__type == 'posts' ){

					$PostsArguments = array(
						'post_type'=>$object__name,
						'posts_per_page'=>$per,
					);

					# GET POSTS IN SELECTED TERM .
						if( isset( $ObjectTerms ) ){
							$PostsArguments['tax_query']['relation'] = 'AND';
							
							foreach ($ObjectTerms as $s => $mm) {
								$PostsArguments['tax_query'][] = array(
								    'taxonomy'  => $mm->taxonomy,
								    'field'   => ($mm->taxonomy == 'category') ? 'term_id' : 'slug',
								    'terms'   => ($mm->taxonomy == 'category') ? $mm->term_id : $mm->slug,
								    'operator'  => 'IN'
								);
							}
						}


					if( isset( $post__not_in ) ){
						$PostsArguments['post__not_in']	= ( ( is_array( $post__not_in ) ) ) ? $post__not_in : array($post__not_in);
					}

				}

				if( $object__type == 'taxonomy' ){

					$PostsArguments = array(
						'hide_empty'=>0,
						'taxonomy'=>$object__name,
						'number'=>$per,
					);

					# GET POSTS IN SELECTED TERM .
						if( isset( $ObjectTerms ) ){
							$PostsArguments['tax_query']['relation'] = 'AND';
							
							foreach ($ObjectTerms as $s => $mm) {
								$PostsArguments['tax_query'][] = array(
								    'taxonomy'  => $mm->taxonomy,
								    'field'   => ($mm->taxonomy == 'category') ? 'term_id' : 'slug',
								    'terms'   => ($mm->taxonomy == 'category') ? $mm->term_id : $mm->slug,
								    'operator'  => 'IN'
								);
							}
						}


					if( isset( $post__not_in ) ){
						$PostsArguments['exclude'] = ( ( is_array( $post__not_in ) ) ) ? array_values($post__not_in) : array($post__not_in);
					}


				}
			}
		# FOUND TOTAL COUNT .
			if( $object__type == 'posts' ){
				$Founder = new WP_Query($PostsArguments);
				$CountQuery = $Founder->found_posts;
			}
			if( $object__type == 'taxonomy' ){
				$CountQuery = wp_count_terms($PostsArguments);
			}

	# VALUE CHECK .

		$Final__values = array();
		$FinalObject__values = array();

		if( isset( $action__argums['ObjectID'] ) ){

			if( $action__argums['ObjectAction'] == 'taxonomy' ){
				$current___relationDB__id = get_term_meta( $action__argums['ObjectID'],'relationDB__id', true);
			}else{
				$current___relationDB__id = get_post_meta( $action__argums['ObjectID'],'relationDB__id', true);
			}

			$RelationsDB = $Relations__languages__locate->get(
				array(
					'RelationDB__id'=>$current___relationDB__id,
					'ObjectAction'=>$action__argums['ObjectAction']
				)
			);

			if( !empty( $RelationsDB ) && isset( $RelationsDB[0] ) ){
				foreach ( $RelationsDB as $rel__item ) {

					# IF $object__type == 'posts'
						if( $rel__item->ObjectAction == 'posts' ){
							$ObjectValue = get_post($rel__item->ObjectID);
							if( isset( $ObjectValue->ID ) ) $Final__values[$ObjectValue->ID]['ObjectValue'] = $ObjectValue;
							$Final__values[ $ObjectValue->ID ]['DBValue'] = $rel__item;
						}

					# IF $object__type == 'taxonomy'	
						if( $rel__item->ObjectAction == 'taxonomy' ){
							$ObjectValue = get_term_by('id',$rel__item->ObjectID,$rel__item->ObjectType);
							if( isset( $ObjectValue->term_id ) ) $Final__values[ $ObjectValue->term_id ]['ObjectValue'] = $ObjectValue;
							$Final__values[ $ObjectValue->term_id ]['DBValue'] = $rel__item;
						}

				}
			}

		}
		$count_Final__values = count($Final__values);


	# SEARCHING AREA .
		if( !isset( $searching__placeholde ) ){

			# IF $object__type == 'posts'
				if( $object__type == 'posts' ){
					$PostTypeArguments = PostTypeArguments( array( 'getIn'=>$object__name ) )[$object__name];
					$searching__placeholde = $PostTypeArguments->labels->search_items;
					$ob__type__name = $PostTypeArguments->labels->singular_name;
				}

			# IF $object__type == 'taxonomy'	
				if( $object__type == 'taxonomy' ){
					$PostTypeArguments = TaxonomyesObject( array( 'getIn'=>$object__name ) )[$object__name];
					$searching__placeholde = $PostTypeArguments->labels->search_items;
					$ob__type__name = $PostTypeArguments->labels->singular_name;
				}

		}


	# LOAD MORE OIPTIONS .
		$LoadMoreAjax = false;

		# CHECK QUERY COUNT .
			if( $CountQuery > $per ) {
				$LoadMoreAjax = true;
			}

		$LoaMoreAttr = ( ( $LoadMoreAjax != false ) ) ? ' data-loadmore="'.base64_encode( json_encode($PostsArguments) ).'"  data-part="'.base64_encode( json_encode( array( 'template'=>'Compo-Select-Field','field'=>$vars ) ) ).'" data-finish="false"' : ' data-finish="true"';

	# SEARCHING OPTIONS .
		$SearchingAttr = ( ( $show__perview__items != false  && $LoaMoreAttr != false ) ) ? ' data-arguments="'.base64_encode( json_encode( array('args'=>$PostsArguments,'field'=>$vars) ) ).'"' : ' data-part="'.base64_encode( json_encode( array( 'template'=>'Compo-Select-Field','field'=>$vars ) ) ).'" data-arguments="'.base64_encode( json_encode( $PostsArguments ) ).'"';


	$vars['vars'] = base64_encode(json_encode($vars));
	$vars['LoaMoreAttr'] = $LoaMoreAttr;
	$action__argums = base64_encode( json_encode( $action__argums ) );
	if( !isset( $Ajax ) ){
		echo '<div class="-fix-inputs-area -fix-inputs-area-translate" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';

			echo '<div class="-result-searching-translate-ajax --result-my-fix-translate" data-uniqid="'.$UniqId.'" data-argums="'.$action__argums.'"'.( ( empty( $Final__values ) ) ? ' style="display:none;"' : '' ).'>';
				
				echo "<div class='-Your-selected-title'>لقد قٌمت بتحديد عدد <em>{$count_Final__values}</em> لغة للـ <p>{$ob__type__name}</p></div>";

				echo '<div class="--relations--current--obj--list">';

					foreach ( $Final__values as $Value__item ) {
						$this->AdminPart($box__part,array('post'=>$Value__item['ObjectValue'],'active'=>true,'object__type'=>$Value__item['DBValue']->ObjectType,'DBValue'=>$Value__item['DBValue']));
					}

				echo '</div>';

			echo '</div>';


			echo '<div class="-select--posts--top-area">';

				echo '<div class="-fix-forms-field-title">';
					echo '<h3>'.$title.'</h3>';
					echo ( ( isset( $disc ) ) ) ? '<descor>'.$disc.'</descor>' : '';
				echo '</div>';

				echo '<div class="selected-input--serch">';
					echo '<div class="-fix---searching--input">';
						echo '<input type="text" placeholder="'.$searching__placeholde.'" data-input-search-center="'.$UniqId.'" data-appender-elem="-ScrollerCenter"'.$SearchingAttr.'>';
					echo '</div>';
				echo '</div>';
			echo '</div>';


			echo '<div class="--translate--too-alerts --translate--append-alerts" data-uniqid="'.$UniqId.'"></div>';

			echo '<div class="-searching--result--selected -ScrollerCenter" data-uniqid="'.$UniqId.'"'.( ( $show__perview__items == false ) ? ' style="display:none;"' : '' ).$LoaMoreAttr.'>';

				# IF $object__type == 'posts'
					if( $object__type == 'posts' ){

						foreach ( get_posts( $PostsArguments ) as $post ) {
							$this->AdminPart($box__part,array('post'=>$post,'object__type'=>$object__type));
						}
					}

				# IF $object__type == 'taxonomy'
					if( $object__type == 'taxonomy' ){

						foreach ( get_terms( $PostsArguments ) as $post ) {
							$this->AdminPart($box__part,array('post'=>$post,'object__type'=>$object__type));

						}

					}

				# IF $object__type == 'users'
					if( $object__type == 'users' ){
						if( !isset( $user_query ) ) $user_query = new WP_User_Query($PostsArguments);

						foreach ( $user_query->get_results() as $post ) {
							$this->AdminPart($box__part,array('post'=>$post,'object__type'=>$object__type));
						}
					}


			echo '</div>';
			
			echo '<LoadMore--InpuArea><PostsScrollLoader data-more-click="'.$UniqId.'" class="PostsScrollLoader LoadMorePostsBTN" '.( ( $LoadMoreAjax != false && $show__perview__items != false ) ? '' : 'style="display:none"').'><i class="fa-solid fa-square-plus"></i><span>تحميل المزيد</span></PostsScrollLoader></LoadMore--InpuArea>';

		echo '</div>';
	}

	if( isset( $Ajax ) ){


		echo '<EX_NewField>';
			$json = $PostsArguments;
			echo json_encode( $json );
		echo '</EX_NewField>';


		# IF $object__type == 'posts'
			if( $object__type == 'posts' ){
				$i=0;
				foreach ( get_posts( $PostsArguments ) as $post ) {$i++;
					$this->AdminPart($box__part,array('post'=>$post,'object__type'=>$object__type));
				}
			}

		# IF $object__type == 'taxonomy'
			if( $object__type == 'taxonomy' ){
				$i=0;
				foreach ( get_terms( $PostsArguments ) as $post ) {$i++;
					$this->AdminPart($box__part,array('post'=>$post,'object__type'=>$object__type));

				}

			}

		# IF $object__type == 'users'
			if( $object__type == 'users' ){
				if( !isset( $user_query ) ) $user_query = new WP_User_Query($PostsArguments);
				$i=0;
				foreach ( $user_query->get_results() as $post ) {$i++;
					$this->AdminPart($box__part,array('post'=>$post,'object__type'=>$object__type));
				}
			}

		echo '<EX_EndMoreAjax>';
			$more__json = $i;
			echo json_encode( $more__json );
		echo '</EX_EndMoreAjax>';

	}
}