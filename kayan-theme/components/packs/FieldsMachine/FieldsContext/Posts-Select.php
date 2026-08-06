<?php if( !isset( $value ) ) $value = '';
if( !isset( $per ) ) $per = 50;

unset($vars['InsertElements']);
if( isset( $InsertElements ) ){
	$InputName = 'Insert_'.$id;
}else if( isset( $parent_id ) ){
	$InputName = $parent_id.'['.$id.']';
}else{
	$InputName = $id;
}

$UniqId = uniqid();
$LoadMoreAjax = false;
#
if( !isset( $options ) ){
	$options = array();

	if( !isset( $Ajax ) ) $options[''] = 'بدون';

	if( !isset( $PostsArguments ) ){
		$PostsArguments = array(
			'post_type'=>$post_type_name,
			'posts_per_page'=>$per,
		);

		// # TEST CODE.
		//$PostsArguments['post_status'] = 'pending';

		#
		if(isset($ObjectTerms)){
			$TotalTerms = array();
			$PostsArguments['tax_query'] = array(
				'relation'=>  'AND',
			);
			//
			foreach ($ObjectTerms as $s => $mm) {
				$PostsArguments['tax_query'][] = array(
				    'taxonomy'  => $mm->taxonomy,
				    'field'   => ($mm->taxonomy == 'category') ? 'term_id' : 'slug',
				    'terms'   => ($mm->taxonomy == 'category') ? $mm->term_id : $mm->slug,
				    'operator'  => 'IN'
				);
			}
		}
		
	}

    $Founder = new WP_Query($PostsArguments);
    $CountQuery = $Founder->found_posts;

	foreach ( get_posts( $PostsArguments ) as $post) {
		$options[$post->ID] = $post->post_title;
		$PostsArguments['post__not_in'][] = $post->ID;
	}

	if( !isset( $Ajax ) ){
		if( !isset( $options[$value] ) ){
			$ObjectValue = get_post($value);
			if( isset( $ObjectValue->ID ) ) {
				$options[$value] = $ObjectValue->post_title;
				$PostsArguments['post__not_in'][] = $ObjectValue->ID;
			}else{
				$value = '';
			}
		}
	}

	$vars['options'] = $options;
	#
	if( $CountQuery > $per ) {
		$LoadMoreAjax = true;
	}
}

if( !isset($PostsArguments) ){
	echo '<pre>';
		//print_r($vars);
	echo '</pre>';
}

//print_r($PostsArguments);

$vars['LoadMoreAjax'] = $LoadMoreAjax;

$LoaMoreAttr = (($LoadMoreAjax != false)) ? 'data-loadmore="'.base64_encode(json_encode($PostsArguments)).'"  data-part="'.base64_encode( json_encode( array( 'template'=>'select','field'=>$vars ) ) ).'" data-finish="false"' : 'data-finish="true"';
$vars['LoaMoreAttr'] = $LoaMoreAttr;

$SearchArguments = array('args'=>$PostsArguments,'field'=>$vars);
$vars['SearchArguments'] = base64_encode(json_encode($SearchArguments));
$vars['vars'] = base64_encode(json_encode($vars));
if( !isset( $Ajax ) ){
	echo '<div class="-fix-inputs-area" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';
		echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3></div>';
		echo '<div class="Select-Options-Items">';
			echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '<AjaxHTML_Cut>' : '';
				echo '<input type="text" name="'.$InputName.'" id="'.$InputName.'" value="'.$value.'" style="display:none" class="Selected-Value">';
				echo '<h2 data-select-open="'.$InputName.'">';
					echo '<span>'.( ( $value != '' ) ? $options[$value] : 'بدون تحديد ').'</span><i class="fas fa-angle-down"></i>';
				echo '</h2>';
				echo '<div class="-Select-DropDown">';

					echo '<div class="AjaxSearchCenter">';
						echo '<input type="text" value="" placeholder="البحث السريع " data-input-search-center="'.$UniqId.'" data-appender-elem="-ScrollerCenter" '.( ( $LoadMoreAjax == false ) ? 'data-arguments="'.base64_encode( json_encode( $SearchArguments ) ).'"' : 'data-arguments="'.base64_encode( json_encode( $PostsArguments ) ).'" data-part="'.base64_encode( json_encode( array( 'template'=>'select','field'=>$vars ) ) ).'"' ).'>';
					echo '</div>';
					

					echo '<ul class="Lists-Select-Items -ScrollerCenter" '.$LoaMoreAttr.' data-uniqid="'.$UniqId.'">';	
						foreach ( $options as $skey => $meky) {
							echo '<li '.( ( isset( $selected_shows ) ) ? 'data-shows-selected="'.$skey.'" data-meta-key="'.$id.'"' : '' ).' data-title="'.$meky.'" data-selected="'.$skey.'" '.(($skey == $value) ? 'class="active"' : '').'>'.$meky.'</li>';
						}
					echo '</ul>';
					echo '<LoadMore--InpuArea><PostsScrollLoader data-more-click="'.$UniqId.'" class="PostsScrollLoader LoadMorePostsBTN" '.(($LoadMoreAjax != false) ? '' : 'style="display:none"').'><i class="fa-solid fa-square-plus"></i><span>تحميل المزيد</span></PostsScrollLoader></LoadMore--InpuArea>';
				echo '</div>';
			echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '</AjaxHTML_Cut>' : '';
		echo '</div>';
		echo ( ( isset( $disc ) ) ) ? '<descor>'.$disc.'</descor>' : '';
	echo '</div>';
}else{
	echo '<EX_NewField>';
		$json = $PostsArguments;
		echo json_encode( $json );
	echo '</EX_NewField>';

	foreach ( $options as $skey => $meky) {
		echo '<li '.( ( isset( $selected_shows ) ) ? 'data-shows-selected="'.$skey.'" data-meta-key="'.$id.'"' : '' ).' data-selected="'.$skey.'" '.(($skey == $value) ? 'class="active"' : '').' data-title="'.$meky.'">'.$meky.'</li>';
	}
}

