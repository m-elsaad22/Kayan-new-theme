<?php
if( !isset( $value ) ) $value = '';
if( !isset( $per ) ) $per = 50;

if( isset( $InsertElements ) ){
	unset($vars['InsertElements']);
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

	if( !isset( $taxonomy_field ) ) $taxonomy_field = 'term_id';

	if( !isset( $PostsArguments ) ){
		$PostsArguments = array(
			'hide_empty'=>0,
			'taxonomy'=>$taxonomy_name,
			'number'=>$per,
		);
		if( isset( $taxonomy_parent ) ){
			$PostsArguments['parent'] = $taxonomy_parent;
		}
	}


	$CountQuery = wp_count_terms($PostsArguments);
	foreach ( get_terms( $PostsArguments ) as $term) {
		if( $taxonomy_field == 'term_id' ){
			$options[$term->term_id] = $term->name;
		}else if( $taxonomy_field == 'slug' ){
			$options[$term->slug] = $term->name;
		}else if( $taxonomy_field == 'name' ){
			$options[$term->name] = $term->name;
		}
		$PostsArguments['exclude'][] = $term->term_id;
	}

	if( !isset( $Ajax ) ){
		if( !isset( $options[$value] ) ){
			$ObjectValue = get_term_by($taxonomy_field,$value,$taxonomy_name);
			if( isset( $ObjectValue->term_id ) ) {
				$options[$value] = $ObjectValue->name;
				$PostsArguments['exclude'][] = $ObjectValue->term_id;
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
					if( $LoadMoreAjax == true ){
						echo '<div class="AjaxSearchCenter">';
							echo '<input type="text" value="" placeholder="البحث السريع " data-input-search-center="'.$UniqId.'" data-appender-elem="-ScrollerCenter" data-arguments="'.base64_encode( json_encode( $SearchArguments ) ).'">';
						echo '</div>';
					}

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

