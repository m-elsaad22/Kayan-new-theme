<?php  
if( !isset( $value ) ) $value = '';
if( !isset( $per ) ) $per = 200;

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
if( !isset( $options ) || ( isset( $options ) && empty( $options ) ) ){
	$options = array();
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

$LoaMoreAttr = (($LoadMoreAjax != false)) ? 'data-loadmore="'.base64_encode(json_encode($PostsArguments)).'"  data-part="'.base64_encode( json_encode( array( 'template'=>'radio','field'=>$vars ) ) ).'" data-finish="false"' : 'data-finish="true"';
$vars['LoaMoreAttr'] = $LoaMoreAttr;

$SearchArguments = array('args'=>$PostsArguments,'field'=>$vars);
$vars['SearchArguments'] = base64_encode(json_encode($SearchArguments));
$vars['vars'] = base64_encode(json_encode($vars));
if( !isset( $Ajax ) ){
	echo '<div class="-fix-inputs-area" data-field-id="'.$id.'">';
		echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3>'.( ( isset( $disc ) ) ? '<descor data-tooltip="'.$disc.'"><i class="fa-solid fa-circle-info"></i></descor>' : '').'</div>';
		#
		echo '<div class="-Radio-Inner-Box">';
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

		echo '</div>';
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