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

	if( !isset( $PostsArguments ) ){
		$PostsArguments = array(
			'order'        => 'ASC',
			'number'       => $per,
			'YC_Current_Users'=>true,
		);
		#
		if( isset( $role__in ) ){
			$PostsArguments['role__in'] = $role__in;
		}
	}


	$user_query = new WP_User_Query($PostsArguments);
	$users = $user_query->get_results();
	$CountQuery = $user_query->get_total();
	#

	foreach ( $user_query->get_results() as $post) {
		$options[$post->ID] = $post->display_name;
		$PostsArguments['exclude'][] = $post->ID;
	}

	if( !isset( $Ajax ) ){
		if( !isset( $options[$value] ) ){
			$ObjectValue = get_userdata($value);
			if( isset( $ObjectValue->ID ) ) {
				$options[$value] = $ObjectValue->display_name;
				$PostsArguments['exclude'][] = $ObjectValue->ID;
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
			echo '<input type="text" name="'.$InputName.'" id="'.$InputName.'" value="'.$value.'" style="display:none" class="Selected-Value">';
			echo '<h2 data-select-open="'.$InputName.'">';
				echo '<span>'.( ( $value != '' ) ? $options[$value] : 'بدون تحديد ').'</span><i class="fas fa-angle-down"></i>';
			echo '</h2>';
			echo '<div class="-Select-DropDown">';
				echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '<AjaxHTML_Cut>' : '';
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
				echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '</AjaxHTML_Cut>' : '';					
			echo '</div>';
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

