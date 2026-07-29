<?php
if( !isset( $value ) || !isset( $value ) && ( is_string($value) || $value == '' ) ) $value = array();
if( !isset( $require ) ) $require = false;
if( !isset( $per ) ) $per = 50;


$value = ( ( is_array( $value ) ) ) ? $value : array();

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
			'order'        => 'ASC',
			'number'       => $per,
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
		foreach ($value as $r => $t) {
			if( !isset( $options[$t] ) ){
				$ObjectValue = get_userdata($t);
				if( isset( $ObjectValue->ID ) ) {
					$options[$t] = $ObjectValue->display_name;
					$PostsArguments['exclude'][] = $ObjectValue->ID;
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
