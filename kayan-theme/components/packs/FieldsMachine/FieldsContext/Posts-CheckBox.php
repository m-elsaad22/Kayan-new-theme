<?php
if( !isset( $value ) || !isset( $value ) && ( is_string($value) || $value == '' ) ) $value = array();
if( !isset( $require ) ) $require = false;
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

	$options = array();

  	$Founder = new WP_Query($PostsArguments);
  	$CountQuery = $Founder->found_posts;

	foreach ( get_posts( $PostsArguments ) as $post) {
		$options[$post->ID] = $post->post_title;
		$PostsArguments['post__not_in'][] = $post->ID;
	}

	if( !isset( $Ajax ) ){
		foreach ( is_array( $value ) ? $value : array() as $r => $t) {
			if( !isset( $options[$t] ) ){
				$ObjectValue = get_post($t);
				if( isset( $ObjectValue->ID ) ) {
					$options[$t] = $ObjectValue->post_title;
					$PostsArguments['post__not_in'][] = $ObjectValue->ID;
				}
			}
		}
	}
	$vars['options'] = $options;
	#
	if( $CountQuery > count( $options ) ) {
		$LoadMoreAjax = true;
	}
}

$vars['LoadMoreAjax'] = $LoadMoreAjax;

$LoaMoreAttr = (($LoadMoreAjax != false)) ? 'data-loadmore="'.base64_encode(json_encode($PostsArguments)).'"  data-part="'.base64_encode( json_encode( array( 'template'=>'select','field'=>$vars ) ) ).'" data-finish="false"' : 'data-finish="true"';
$vars['LoaMoreAttr'] = $LoaMoreAttr;

$SearchArguments = array('args'=>$PostsArguments,'field'=>$vars);
$vars['SearchArguments'] = base64_encode(json_encode($SearchArguments));
$vars['vars'] = base64_encode(json_encode($vars));
if( !isset( $Ajax ) ){
	echo '<div class="-fix-inputs-area '.( ( isset( $Custom_Class ) ) ? $Custom_Class : '' ).'" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).' '.( ( isset( $Custom_attrs ) ) ? $Custom_attrs : '' ).'>';
		echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3></div>';
		#
		echo '<div class="-CheckBox-Centers">';
			echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '<AjaxHTML_Cut>' : '';
				if( $LoadMoreAjax == true ){
					echo '<div class="AjaxSearchCenter">';
						echo '<input type="text" value="" placeholder="البحث السريع " data-input-search-center="'.$UniqId.'" data-appender-elem="-ScrollerCenter" data-arguments="'.base64_encode( json_encode( $SearchArguments ) ).'">';
					echo '</div>';
				}

				echo '<div class="-CheckBox-Box-InnerArea -ScrollerCenter '.( ( isset( $InsertAppend ) ) ? '-appender-tax-center' : '' ).'" '.( ( isset( $InsertAppend ) ) ? ' data-append-uniq="'.$InsertAppend.'"' : '' ).' '.$LoaMoreAttr.' data-uniqid="'.$UniqId.'">';
					foreach ($options as $fky => $fvlue) {
						echo '<div class="-CheckBox-Box-Item">';
							echo '<input'.(( in_array($fky, $value) ) ? ' checked' : '').' type="checkbox" value="'.$fky.'" name="'.$InputName.'[]" id="'.$id.$fky.'" />';
							echo '<span></span>';
							echo '<em>'.$fvlue.'</em>';
						echo '</div>';
					}
					#
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
		echo '<div class="-CheckBox-Box-Item">';
			echo '<input'.(( in_array($fky, $value) ) ? ' checked' : '').' type="checkbox" value="'.$fky.'" name="'.$InputName.'[]" id="'.$id.$fky.'" />';
			echo '<span></span>';
			echo '<em>'.$fvlue.'</em>';
		echo '</div>';
	}
}
