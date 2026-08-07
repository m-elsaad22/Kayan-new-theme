<?php
if( !isset( $value ) ) $value = '';
if( !isset( $per ) ) $per = 50;

#
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

	if( !isset( $PostsArguments ) ){
		$PostsArguments = array(
			'post_type'=>$post_type_name,
			'posts_per_page'=>$per,
		);
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

$vars['LoadMoreAjax'] = $LoadMoreAjax;

$LoaMoreAttr = (($LoadMoreAjax != false)) ? 'data-loadmore="'.base64_encode(json_encode($PostsArguments)).'"  data-part="'.base64_encode( json_encode( array( 'template'=>'radio','field'=>$vars ) ) ).'" data-finish="false"' : 'data-finish="true"';
$vars['LoaMoreAttr'] = $LoaMoreAttr;

$SearchArguments = array('args'=>$PostsArguments,'field'=>$vars);
$vars['SearchArguments'] = base64_encode(json_encode($SearchArguments));
$vars['vars'] = base64_encode(json_encode($vars));
if( !isset( $Ajax ) ){
	echo '<div class="-fix-inputs-area" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';
		echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3></div>';
		#
		echo '<div class="-Radio-Inner-Box">';
			echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '<AjaxHTML_Cut>' : '';
				if( $LoadMoreAjax == true ){
					echo '<div class="AjaxSearchCenter">';
						echo '<input type="text" value="" placeholder="البحث السريع " data-input-search-center="'.$UniqId.'" data-appender-elem="-ScrollerCenter" data-arguments="'.base64_encode( json_encode( $SearchArguments ) ).'">';
					echo '</div>';
				}

				echo '<div class="-Radio-Box-InnerArea -ScrollerCenter" '.$LoaMoreAttr.' data-uniqid="'.$UniqId.'">';
					foreach ($options as $fky => $fvlue) {
						echo '<div class="-Radio-Box-Item" '.(( isset( $Custom_says ) ) ? ' data-argums-say="'.$fky.'"' : '' ).'>';
							echo '<input'.(($fky == $value) ? ' checked' : '').' type="radio" value="'.$fky.'" name="'.$InputName.'" id="'.$id.$fky.'" />';
							echo '<span></span>';
							echo '<em>'.$fvlue.'</em>';
						echo '</div>';
					}
				echo '</div>';

				echo '<LoadMore--InpuArea><PostsScrollLoader data-more-click="'.$UniqId.'" class="PostsScrollLoader LoadMorePostsBTN" '.(($LoadMoreAjax != false) ? '' : 'style="display:none"').'><i class="fa-solid fa-square-plus"></i><span>تحميل المزيد</span></PostsScrollLoader></LoadMore--InpuArea>';
			echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '</AjaxHTML_Cut>' : '';

		echo '</div>';
		echo ( ( isset( $disc ) ) ) ? '<descor>'.$disc.'</descor>' : '';
	echo '</div>';
}else{
	echo '<EX_NewField>';
		$json = $PostsArguments;
		echo json_encode( $json );
	echo '</EX_NewField>';
	foreach ($options as $fky => $fvlue) {
		echo '<div class="-Radio-Box-Item" '.(( isset( $Custom_says ) ) ? ' data-argums-say="'.$fky.'"' : '' ).'>';
			echo '<input'.(($fky == $value) ? ' checked' : '').' type="radio" value="'.$fky.'" name="'.$InputName.'" id="'.$id.$fky.'" />';
			echo '<span></span>';
			echo '<em>'.$fvlue.'</em>';
		echo '</div>';
	}
}