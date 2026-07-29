<?php if( !isset( $UniqId ) ) $UniqId = uniqid();
if( !isset( $vars['UniqId'] ) ) $vars['UniqId'] = $UniqId;

# SELECT ITEMS BOX .
	if( !isset( $box__part ) ) $box__part = 'Compo-Select-Box';
	if( !isset( $part__object__data ) ) $part__object__data = array();

# SHOW PERVIEW ITEMS -> DEFULT FALSE
	if( !isset( $show__perview__items ) ) $show__perview__items = false;

# OBJECT TYPES ( 'posts' || 'taxonomy' || 'users'  )
	if( !isset( $object__type ) ) $object__type = 'posts';

# OBJECT NAME -> 'post_type' OR 'taxonomy' => name ... IF 'users' DEFULT -> false;
	if( !isset( $object__name ) && $object__type != 'users' ) $object__name = 'post';
	if( !isset( $object__name ) && $object__type == 'users' ) $object__name = false;

# PER -> POSTS PER PAGE OR NUMBER (INTEGER) .
	if( !isset( $per ) ) $per = 50;

# MULTIPLE SELECT ITEMS ? DEFULT = FALSE;
	if( !isset( $multiple ) ) $multiple = false;

# SAVE TAXONOMY BY -> DEFULT 'term_id'
	if( !isset( $taxonomy_field ) ) $taxonomy_field = 'term_id';

# SAVE DATABASE FIELD BY ?! ..  DEFULT 'id'
	if( !isset( $database_field ) ) $database_field = 'id';

# SEND OBJECT TO PART $YOUR_NAME .
	if( !isset( $part_object__name ) ) $part_object__name = 'post';

# OPEN LOAD MORE 'TRUE' OR 'FALSE' .
	if( !isset( $ScrollLoader ) ) $ScrollLoader = false;

# AUTO LOAD MORE 'TRUE' OR 'FALSE' && IMPORTANT SET $ScrollLoader 'TRUE' .
	if( !isset( $AutoLoadmore ) ) $AutoLoadmore = false;

# ADD CUSTOM MORE BUTTON .
	if( !isset( $custom___more__btn ) ) $custom___more__btn = array();

# SEARCHING FIELDS .
	if( !isset( $seaching___fields ) ) $seaching___fields = '';	


# BUILDING INPUT NAME .

	if( isset( $InsertElements ) ){
		$InputName = 'Insert_'.$id;
	}else if( isset( $parent_key ) && $parent_key != false && isset( $parent_id ) ){
		$InputName = $parent_id.'['.$parent_key.']['.$id.']';

	}else if( isset( $parent_id ) ){
		$InputName = $parent_id.'['.$id.']';
	}else{
		$InputName = $id;
	}
	#unset($vars['InsertElements']);

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
						
						foreach ( $ObjectTerms as $s => $mm ) {
							$PostsArguments['tax_query'][] = array(
							    'taxonomy'  => $mm->taxonomy,
							    'field'   => ($mm->taxonomy == 'category') ? 'term_id' : 'slug',
							    'terms'   => ($mm->taxonomy == 'category') ? $mm->term_id : $mm->slug,
							    'operator'  => 'IN'
							);
						}
					}

				# GET POSTS IN CustomMetaQuery .
					if( isset( $custom__meta_query ) ){
						$PostsArguments['meta_query'] = $custom__meta_query;
					}
			}

			if( $object__type == 'taxonomy' ){

				$PostsArguments = array(
					'hide_empty'=>0,
					'taxonomy'=>$object__name,
					'number'=>$per,
				);
			}

			if( $object__type == 'users' ){

				$PostsArguments = array(
					'order' => 'ASC',
					'number' => $per,
					'YC_Current_Users' =>true,
				);

				# GET USERS IN JUST THIS ROLE .
					if( isset( $role__in ) ){
						$PostsArguments['role__in'] = $role__in;
					}
			}
			if( $object__type == 'database' ){
				$PostsArguments = array();
			}
		}

	# INSERT OBJECT COUNT .

		if( isset( $PostsArguments['paged'] ) && $PostsArguments['paged'] > 1 && ( $object__type == 'users' || $object__type == 'taxonomy' ) ){
			$offset = ( $PostsArguments['paged']-1 ) * $per;
			$PostsArguments['offset'] = $offset;
		}



	# SEARCHING QUERY .
		if( isset( $SearchQuery ) )	{

			if( $object__type == 'posts' ){
				$PostsArguments['s'] = $SearchQuery;
			}

			if( $object__type == 'taxonomy' ){
				$PostsArguments['name__like'] = $SearchQuery;
			}

			if( $object__type == 'users' ){
				$PostsArguments['search'] = '*'.$SearchQuery.'*';
			}

			if( $object__type == 'database' ){

				$PostsArguments[ $seaching___fields ] = '%'.$SearchQuery.'%';
				$PostsArguments['compare'] = array(
					$seaching___fields => 'LIKE'
				);

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

		if( $object__type == 'users' ){
			$user_query = new WP_User_Query($PostsArguments);
			$users = $user_query->get_results();
			$CountQuery = $user_query->get_total();
		}

		if( $object__type == 'database' ){
			$database = new $object__name;
			$CountQuery = $database->count($PostsArguments);
			if( !is_integer( $CountQuery ) && !is_array( $CountQuery ) ) $CountQuery = (INT) $CountQuery;
		}

# VALUE CHECK .
/*	$Final__values = array();

	if( !isset( $value ) ) $value = array();

	if( isset( $value ) && !empty( $value ) ){

		if( $multiple == false ){
			if( is_array( $value ) ) $value = $value[0];

			# IF $object__type == 'posts'
				if( $object__type == 'posts' ){
					$ObjectValue = get_post($value);
					if( isset( $ObjectValue->ID ) ) $Final__values[$ObjectValue->ID] = $ObjectValue;
				}

			# IF $object__type == 'taxonomy'	
				if( $object__type == 'taxonomy' ){
					$ObjectValue = get_term_by($taxonomy_field,$value,$object__name);
					if( isset( $ObjectValue->term_id ) ) $Final__values[ $ObjectValue->$taxonomy_field ] = $ObjectValue;
				}

			# IF $object__type == 'users'	
				if( $object__type == 'users' ){
					$ObjectValue = get_userdata($value);
					if( isset( $ObjectValue->ID ) ) $Final__values[$ObjectValue->ID] = $ObjectValue;
				}

		}else{

			foreach ( ( is_array( $value ) ) ? $value : array() as $value__id ) {

				# IF $object__type == 'posts'
					if( $object__type == 'posts' ){
						$ObjectValue = get_post($value__id);
						if( isset( $ObjectValue->ID ) ) $Final__values[$ObjectValue->ID] = $ObjectValue;
					}

				# IF $object__type == 'taxonomy'	
					if( $object__type == 'taxonomy' ){
						$ObjectValue = get_term_by($taxonomy_field,$value__id,$object__name);
						if( isset( $ObjectValue->term_id ) ) $Final__values[ $ObjectValue->$taxonomy_field ] = $ObjectValue;
					}

				# IF $object__type == 'users'	
					if( $object__type == 'users' ){
						$ObjectValue = get_userdata($value__id);
						if( isset( $ObjectValue->ID ) ) $Final__values[$ObjectValue->ID] = $ObjectValue;
					}
			}

		}
	}*/

# SEARCHING AREA .
	if( !isset( $searching__placeholde ) ){

		# IF $object__type == 'posts'
			if( $object__type == 'posts' ){
				$PostTypeArguments = PostTypeArguments( array( 'getIn'=>$object__name ) )[$object__name];
				$searching__placeholde = $PostTypeArguments->labels->search_items;
			}

		# IF $object__type == 'taxonomy'	
			if( $object__type == 'taxonomy' ){
				$PostTypeArguments = TaxonomyesObject( array( 'getIn'=>$object__name ) )[$object__name];
				$searching__placeholde = $PostTypeArguments->labels->search_items;
			}

		# IF $object__type == 'users'	
			if( $object__type == 'users' ){
				$searching__placeholde = 'البحث في الاعضاء ';
			}

		# IF $object__type == 'database'	
			if( $object__type == 'database' ){
				$searching__placeholde = 'البحث';
			}
	}

$LoadMoreAjax = true;
$object____lists = array();
if( $show__perview__items == true || isset( $Ajax ) ){

	$current__result__count = 0;

	# IF $object__type == 'posts'
		if( $object__type == 'posts' ){


			foreach ( get_posts( $PostsArguments ) as $post ) {$current__result__count++;
				$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$post->ID.']' ).'" id="'.$InputName.'" value="'.$post->ID.'">';
				$object____lists[ $post->ID ] = array($part_object__name=>$post,'hideInput'=>$Box___input,'multiple'=>$multiple,'object__type'=>$object__type,'part__object__data'=>$part__object__data);
			}
		}

	# IF $object__type == 'taxonomy'
		if( $object__type == 'taxonomy' ){

			foreach ( get_terms( $PostsArguments ) as $term ) {$current__result__count++;

				$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$term->$taxonomy_field.']' ).'" id="'.$InputName.'" value="'.$term->$taxonomy_field.'">';
				$object____lists[ $term->{$taxonomy_field} ] = array($part_object__name=>$term,'hideInput'=>$Box___input,'multiple'=>$multiple,'object__type'=>$object__type,'part__object__data'=>$part__object__data);

			}

		}

	# IF $object__type == 'users'
		if( $object__type == 'users' ){
			if( !isset( $user_query ) ) $user_query = new WP_User_Query($PostsArguments);

			foreach ( $user_query->get_results() as $user ) {$current__result__count++;

				$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$user->ID.']' ).'" id="'.$InputName.'" value="'.$user->ID.'">';
				$object____lists[ $user->ID ] = array($part_object__name=>$user,'hideInput'=>$Box___input,'multiple'=>$multiple,'object__type'=>$object__type,'part__object__data'=>$part__object__data);

			}
		}

	# IF $object__type == 'database'
		if( $object__type == 'database' ){

			if( !isset( $database ) ) $database = new $object__name;

			$item__request = $database->get($PostsArguments,0,$per);
			if( $item__request != false ){

				foreach ( $item__request as $single___obj ) {$current__result__count++;

					$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$single___obj->{$database_field}.']' ).'" id="'.$InputName.'" value="'.$single___obj->{$database_field}.'">';
					$object____lists[ $single___obj->{$database_field} ] = array($part_object__name=>$single___obj,'hideInput'=>$Box___input,'multiple'=>$multiple,'object__type'=>$object__type,'part__object__data'=>$part__object__data);

				}
			}
		}

	# LOAD MORE OIPTIONS .
		#echo ( ( $ScrollLoader == false ) ) ? 'FALSE' : 'TRUE'; die;
		if( $current__result__count == 0 
			|| ( isset( $PostsArguments['offset'] ) && $CountQuery <= ( $PostsArguments['offset'] + $current__result__count ) ) 
			|| ( isset( $PostsArguments['paged'] ) && $CountQuery <= ( ( ( $PostsArguments['paged']-1 ) * $per ) + $current__result__count ) ) 
			|| ( $current__result__count < $per || ( $current__result__count + 1 <= $per ) )
		) $LoadMoreAjax = false;
}
# VALUE CHECK .
	$Final__values = array();

	if( !isset( $value ) ) $value = array();

	if( isset( $value ) && !empty( $value ) ){
		if( $multiple == false ){
			if( is_array( $value ) ) $value = $value[0];

			# IF $object__type == 'posts'
				if( $object__type == 'posts' ){

					if( isset( $object____lists[$value] ) ){
						$object____lists[$value]['active'] = true;
						#
						$v__item = $object____lists[$value];
						$v__item['Input'] = $object____lists[$value]['hideInput'];
						unset( $v__item['hideInput'] );
						$Final__values[$value] = $v__item;

					}else{					
						$ObjectValue = get_post($value);
						if( isset( $ObjectValue->ID ) ){
							$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$value.']' ).'" id="'.$InputName.'" value="'.$value.'">';
							$Final__values[$ObjectValue->ID] = array($part_object__name=>$ObjectValue,'Input'=>$Box___input,'multiple'=>$multiple,'active'=>true,'object__type'=>$object__type,'part__object__data'=>$part__object__data);
						}
					}
				}

			# IF $object__type == 'taxonomy'	
				if( $object__type == 'taxonomy' ){
					if( isset( $object____lists[$value] ) ){
						$object____lists[$value]['active'] = true;
						#
						$v__item = $object____lists[$value];
						$v__item['Input'] = $object____lists[$value]['hideInput'];
						unset( $v__item['hideInput'] );
						$Final__values[$value] = $v__item;
					}else{					
						$ObjectValue = get_term_by($taxonomy_field,$value,$object__name);
						if( isset( $ObjectValue->{$taxonomy_field} ) ){
							$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$ObjectValue->{$taxonomy_field}.']' ).'" id="'.$InputName.'" value="'.$ObjectValue->{$taxonomy_field}.'">';
							$Final__values[$ObjectValue->{$taxonomy_field}] = array($part_object__name=>$ObjectValue,'Input'=>$Box___input,'multiple'=>$multiple,'active'=>true,'object__type'=>$object__type,'part__object__data'=>$part__object__data);
						}
					}
				}

			# IF $object__type == 'users'	
				if( $object__type == 'users' ){
					if( isset( $object____lists[$value] ) ){
						$object____lists[$value]['active'] = true;
						#
						$v__item = $object____lists[$value];
						$v__item['Input'] = $object____lists[$value]['hideInput'];
						unset( $v__item['hideInput'] );
						$Final__values[$value] = $v__item;
					}else{					
						$ObjectValue = get_userdata($value);
						if( isset( $ObjectValue->ID ) ){
							$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$ObjectValue->ID.']' ).'" id="'.$InputName.'" value="'.$ObjectValue->ID.'">';
							$Final__values[$ObjectValue->ID] = array($part_object__name=>$ObjectValue,'Input'=>$Box___input,'multiple'=>$multiple,'active'=>true,'object__type'=>$object__type,'part__object__data'=>$part__object__data);
						}
					}
				}

			# IF $object__type == 'database'	
				if( $object__type == 'database' ){

					if( isset( $object____lists[$value] ) ){
						$object____lists[$value]['active'] = true;
						#
						$v__item = $object____lists[$value];
						$v__item['Input'] = $object____lists[$value]['hideInput'];
						unset( $v__item['hideInput'] );
						$Final__values[$value] = $v__item;
					}else{

						$get__ObjectValue = $database->get(array( $database_field=>$value ),0,$per);
						if( $get__ObjectValue != false && isset( $get__ObjectValue[0] ) ){

							if( isset( $get__ObjectValue[0]->{$database_field} ) ){
								$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$get__ObjectValue[0]->{$database_field}.']' ).'" id="'.$InputName.'" value="'.$get__ObjectValue[0]->{$database_field}.'">';
								$Final__values[$get__ObjectValue[0]->{$database_field}] = array($part_object__name=>$get__ObjectValue[0],'Input'=>$Box___input,'multiple'=>$multiple,'active'=>true,'object__type'=>$object__type,'part__object__data'=>$part__object__data);
							}
						}

					}

				}

		}else{

			foreach ( ( is_array( $value ) ) ? $value : array() as $value__id ) {
				#print_r($value__id);die
				# IF $object__type == 'posts'
					if( $object__type == 'posts' ){

						if( isset( $object____lists[$value__id] ) ){
							$object____lists[$value__id]['active'] = true;
							#
							$v__item = $object____lists[$value__id];
							$v__item['Input'] = $object____lists[$value__id]['hideInput'];
							unset( $v__item['hideInput'] );
							$Final__values[$value__id] = $v__item;

						}else{					
							$ObjectValue = get_post($value__id);
							if( isset( $ObjectValue->ID ) ){
								$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$ObjectValue->ID.']' ).'" id="'.$InputName.'" value="'.$ObjectValue->ID.'">';
								$Final__values[$ObjectValue->ID] = array($part_object__name=>$ObjectValue,'Input'=>$Box___input,'multiple'=>$multiple,'active'=>true,'object__type'=>$object__type,'part__object__data'=>$part__object__data);
							}
						}
					}

				# IF $object__type == 'taxonomy'
					if( $object__type == 'taxonomy' ){
						if( isset( $object____lists[$value__id] ) ){
							$object____lists[$value__id]['active'] = true;
							#
							$v__item = $object____lists[$value__id];
							$v__item['Input'] = $object____lists[$value__id]['hideInput'];
							unset( $v__item['hideInput'] );
							$Final__values[$value__id] = $v__item;
						}else{					
							$ObjectValue = get_term_by($taxonomy_field,$value__id,$object__name);
							if( isset( $ObjectValue->{$taxonomy_field} ) ){
								$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$ObjectValue->{$taxonomy_field}.']' ).'" id="'.$InputName.'" value="'.$ObjectValue->{$taxonomy_field}.'">';
								$Final__values[$ObjectValue->{$taxonomy_field}] = array($part_object__name=>$ObjectValue,'Input'=>$Box___input,'multiple'=>$multiple,'active'=>true,'object__type'=>$object__type,'part__object__data'=>$part__object__data);
							}
						}
					}

				# IF $object__type == 'users'
					if( $object__type == 'users' ){
						if( isset( $object____lists[$value__id] ) ){
							$object____lists[$value__id]['active'] = true;
							#
							$v__item = $object____lists[$value__id];
							$v__item['Input'] = $object____lists[$value__id]['hideInput'];
							unset( $v__item['hideInput'] );
							$Final__values[$value__id] = $v__item;
						}else{					
							$ObjectValue = get_userdata($value__id);
							if( isset( $ObjectValue->ID ) ){
								$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$ObjectValue->ID.']' ).'" id="'.$InputName.'" value="'.$ObjectValue->ID.'">';
								$Final__values[$ObjectValue->ID] = array($part_object__name=>$ObjectValue,'Input'=>$Box___input,'multiple'=>$multiple,'active'=>true,'object__type'=>$object__type,'part__object__data'=>$part__object__data);
							}
						}
					}

				# IF $object__type == 'database'
					if( $object__type == 'database' ){

						if( isset( $object____lists[ $value__id ] ) ){

							$object____lists[ $value__id ]['active'] = true;
							#
							$v__item = $object____lists[$value__id];
							$v__item['Input'] = $object____lists[$value__id]['hideInput'];
							unset( $v__item['hideInput'] );
							$Final__values[$value__id] = $v__item;
						}else{

							$get__ObjectValue = $database->get(array( $database_field=>$value__id ),0,$per);
							if( $get__ObjectValue != false && isset( $get__ObjectValue[0] ) ){

								if( isset( $get__ObjectValue[0]->{$database_field} ) ){
									$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$get__ObjectValue[0]->{$database_field}.']' ).'" id="'.$InputName.'" value="'.$get__ObjectValue[0]->{$database_field}.'">';
									$Final__values[$get__ObjectValue[0]->{$database_field}] = array($part_object__name=>$get__ObjectValue[0],'Input'=>$Box___input,'multiple'=>$multiple,'active'=>true,'object__type'=>$object__type,'part__object__data'=>$part__object__data);
								}
							}
						}
					}
			}

		}
	}
/*# LOAD MORE OIPTIONS .
	$LoadMoreAjax = false;

	# CHECK QUERY COUNT .
		if( $CountQuery > $per ) {
			$LoadMoreAjax = true;
		}
*/

	$LoaMoreAttr = ( ( $LoadMoreAjax != false ) ) ? ' data-loadmore="'.base64_encode( json_encode($PostsArguments) ).'"  data-part="'.base64_encode( json_encode( array( 'template'=>'Compo-Select-Field','field'=>$vars ) ) ).'" data-finish="false"' : ' data-finish="true"';

# SEARCHING OPTIONS .
	$SearchingAttr = ( ( $show__perview__items != false  && $LoadMoreAjax != false ) ) ? ' data-part="'.base64_encode( json_encode( array( 'template'=>'Compo-Select-Field','field'=>$vars ) ) ).'" data-arguments="'.base64_encode( json_encode( $PostsArguments ) ).'"' : ' data-arguments="'.base64_encode( json_encode( array('args'=>$PostsArguments,'field'=>$vars) ) ).'"';


$vars['vars'] = base64_encode(json_encode($vars));
$vars['LoaMoreAttr'] = $LoaMoreAttr;



if( !isset( $Ajax ) ){
	echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '<AjaxHTML_Cut>' : '';
		echo '<div class="-fix-inputs-area" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';

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

			echo '<div class="--result-my-fix-posts-select --JS-result--select" data-uniqid="'.$UniqId.'"'.( ( empty( $Final__values ) ) ? ' style="display:none;"' : '' ).'>';
				
				echo '<div class="-Your-selected-title">لقد قٌمت بتحديد </div>';

				echo '<div class="-scroller-slider-findors-UL" data-customslider="true" data-ulfind=".-fix-selcted-items" data-findelements=".-currrent-single-elements">';
					echo '<div class="-fix-selcted-items --JS--Appended--Selector">';
						foreach ( $Final__values as $k__id => $value___argums) {
							$this->AdminPart($box__part,$value___argums);
						}
						/*foreach ( $Final__values as $k__id => $value___id) {
							$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$k__id.']' ).'" id="'.$InputName.'" value="'.$k__id.'">';
							$this->AdminPart($box__part,array('post'=>$value___id,'Input'=>$Box___input,'multiple'=>$multiple,'active'=>true,'object__type'=>$object__type));
						}*/
					echo '</div>';
				echo '</div>';

			echo '</div>';

			echo '<div class="-result-searching-too" data-uniqid="'.$UniqId.'"></div>';

			echo '<div class="-searching--result--selected -ScrollerCenter" data-uniqid="'.$UniqId.'"'.( ( $show__perview__items == false ) ? ' style="display:none;"' : '' ).$LoaMoreAttr.'>';
				# IF $object__type == 'posts'
					if( !empty( $object____lists ) ){

						#print_r($box__part);die;
						foreach ( $object____lists as $obj___item ) {
							$this->AdminPart($box__part,$obj___item);
						}
					}

				/*# IF $object__type == 'posts'
					if( $object__type == 'posts' ){

						foreach ( get_posts( $PostsArguments ) as $post ) {

							$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$post->ID.']' ).'" id="'.$InputName.'" value="'.$post->ID.'">';
							$this->AdminPart($box__part,array('post'=>$post,'hideInput'=>$Box___input,'multiple'=>$multiple,'object__type'=>$object__type));
						}
					}

				# IF $object__type == 'taxonomy'
					if( $object__type == 'taxonomy' ){

						foreach ( get_terms( $PostsArguments ) as $post ) {

							$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$post->$taxonomy_field.']' ).'" id="'.$InputName.'" value="'.$post->$taxonomy_field.'">';
							$this->AdminPart($box__part,array('post'=>$post,'hideInput'=>$Box___input,'multiple'=>$multiple,'object__type'=>$object__type));

						}

					}

				# IF $object__type == 'users'
					if( $object__type == 'users' ){
						if( !isset( $user_query ) ) $user_query = new WP_User_Query($PostsArguments);

						foreach ( $user_query->get_results() as $post ) {
							$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$post->ID.']' ).'" id="'.$InputName.'" value="'.$post->ID.'">';
							$this->AdminPart($box__part,array('post'=>$post,'hideInput'=>$Box___input,'multiple'=>$multiple,'object__type'=>$object__type));
						}
					}*/


			echo '</div>';
			
			echo '<LoadMore--InpuArea><PostsScrollLoader data-more-click="'.$UniqId.'" class="PostsScrollLoader LoadMorePostsBTN" '.( ( $LoadMoreAjax != false && $show__perview__items != false ) ? '' : 'style="display:none"').'><i class="fa-solid fa-square-plus"></i><span>تحميل المزيد</span></PostsScrollLoader></LoadMore--InpuArea>';

		echo '</div>';
	echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '</AjaxHTML_Cut>' : '';
}

if( isset( $Ajax ) ){

	echo '<EX_NewField>';
		$json = $PostsArguments;
		echo json_encode( $json );
	echo '</EX_NewField>';

	# IF $object__type == 'posts'
		if( !empty( $object____lists ) ){

			foreach ( $object____lists as $obj___item ) {
				#$this->Blade('UIFields', $obj___item,"Parts/{$box__part}" );
				$this->AdminPart($box__part, $obj___item);
			}
		}

	echo '<EX_EndMoreAjax>';
		$more__json = $current__result__count;
		echo json_encode( $more__json );
	echo '</EX_EndMoreAjax>';

	echo '<EX_loadmore__ajax>';
		$loadmore__ajax = $LoadMoreAjax;
		echo json_encode( $loadmore__ajax );
	echo '</EX_loadmore__ajax>';




	/*echo '<EX_NewField>';
		$json = $PostsArguments;
		echo json_encode( $json );
	echo '</EX_NewField>';*/


	# IF $object__type == 'posts'
		if( $object__type == 'posts' ){
			$i=0;
			foreach ( get_posts( $PostsArguments ) as $post ) {$i++;

				$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$post->ID.']' ).'" id="'.$InputName.'" value="'.$post->ID.'">';
				$this->AdminPart($box__part,array('post'=>$post,'hideInput'=>$Box___input,'multiple'=>$multiple,'object__type'=>$object__type));
			}
		}

	# IF $object__type == 'taxonomy'
		if( $object__type == 'taxonomy' ){
			$i=0;
			foreach ( get_terms( $PostsArguments ) as $post ) {$i++;

				$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$post->$taxonomy_field.']' ).'" id="'.$InputName.'" value="'.$post->$taxonomy_field.'">';
				$this->AdminPart($box__part,array('post'=>$post,'hideInput'=>$Box___input,'multiple'=>$multiple,'object__type'=>$object__type));

			}

		}

	# IF $object__type == 'users'
		if( $object__type == 'users' ){
			if( !isset( $user_query ) ) $user_query = new WP_User_Query($PostsArguments);
			$i=0;
			foreach ( $user_query->get_results() as $post ) {$i++;
				$Box___input = '<input type="hidden" name="'.$InputName.( ( $multiple == false ) ? '' : '['.$post->ID.']' ).'" id="'.$InputName.'" value="'.$post->ID.'">';
				$this->AdminPart($box__part,array('post'=>$post,'hideInput'=>$Box___input,'multiple'=>$multiple,'object__type'=>$object__type));
			}
		}

	/*echo '<EX_EndMoreAjax>';
		$more__json = $i;
		echo json_encode( $more__json );
	echo '</EX_EndMoreAjax>';*/

}