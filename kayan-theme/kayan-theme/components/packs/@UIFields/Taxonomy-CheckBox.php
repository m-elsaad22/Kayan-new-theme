<?php 
if( !isset( $value ) || !isset( $value ) && ( is_string($value) || $value == '' ) ) $value = array();
if( !isset( $require ) ) $require = false;
if( !isset( $per ) ) $per = 5;


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

	$options = array();
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
		foreach ($value as $r => $t) {
			if( !isset( $options[$t] ) ){
				$ObjectValue = get_term_by($taxonomy_field,$t,$taxonomy_name);
				if( isset( $ObjectValue->term_id ) ) {
					$options[$t] = $ObjectValue->name;
					$PostsArguments['exclude'][] = $ObjectValue->term_id;
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

$LoaMoreAttr = (($LoadMoreAjax != false)) ? 'data-loadmore="'.base64_encode(json_encode($PostsArguments)).'"  data-part="'.base64_encode( json_encode( array( 'template'=>'checkbox','field'=>$vars ) ) ).'" data-finish="false"' : 'data-finish="true"';
$vars['LoaMoreAttr'] = $LoaMoreAttr;


$SearchArguments = array('args'=>$PostsArguments,'field'=>$vars);
$vars['SearchArguments'] = base64_encode(json_encode($SearchArguments));
$vars['vars'] = base64_encode(json_encode($vars));
if( !isset( $Ajax ) ){
	echo '<div class="-fix-inputs-area '.( ( isset( $Custom_Class ) ) ? $Custom_Class : '' ).'" data-field-id="'.$id.'" '.( ( isset( $Custom_attrs ) ) ? $Custom_attrs : '' ).'>';
		echo '<div class="-fix-forms-field-title"><h3>'.$title.'</h3>'.( ( isset( $disc ) ) ? '<descor data-tooltip="'.$disc.'"><i class="fa-solid fa-circle-info"></i></descor>' : '').'</div>';
		#
		echo '<div class="-CheckBox-Centers">';
			if( $LoadMoreAjax == true ){
				echo '<div class="AjaxSearchCenter">';
					echo '<input type="text" value="" placeholder="البحث السريع " data-input-search-center="'.$UniqId.'" data-appender-elem="-ScrollerCenter" data-arguments="'.base64_encode( json_encode( $SearchArguments ) ).'">';
				echo '</div>';
			}

			echo '<div class="-CheckBox-Box-InnerArea -ScrollerCenter '.( ( isset( $taxonomy_parent ) && $taxonomy_parent == 0 ) ? 'Has-ParentChilds ' : '' ).( ( isset( $InsertAppend ) ) ? '-appender-tax-center' : '' ).'" '.( ( isset( $InsertAppend ) ) ? ' data-append-uniq="'.$InsertAppend.'"' : '' ).' '.$LoaMoreAttr.' data-uniqid="'.$UniqId.'">';
				foreach ($options as $fky => $fvlue) {
					echo '<div class="-CheckBox-Box-Item">';
						echo '<input'.(( in_array($fky, $value) ) ? ' checked' : '').' type="checkbox" value="'.$fky.'" name="'.$InputName.'[]" id="'.$id.$fky.'" />';
						echo '<span></span>';
						echo '<em>'.$fvlue.'</em>';

						if( isset( $taxonomy_parent ) && $taxonomy_parent == 0 ){
							$ChildsTerms = get_terms( 
								array(
									'hide_empty'=>0,
									'taxonomy'=>$taxonomy_name,
									'parent'=>$fky,
								)
							);

							if( isset( $ChildsTerms[0] ) ){
								echo '<div class="-DropDown-Fields-Action"><i class="fa-solid fa-chevron-down"></i></div>';
								echo '<div class="Select-Childs">';
									echo '<h2 class="-Fields-DropDown-Title">اقسام ('.$fvlue.') الفرعية </h2>';
									echo '<div class="-Fields-DropDown-Childs">';
										foreach ($ChildsTerms as $child) {
											echo '<div class="-CheckBox-Box-Item">';
												echo '<input'.(( in_array($child->term_id, $value) ) ? ' checked' : '').' type="checkbox" value="'.$child->term_id.'" name="'.$InputName.'[]" id="'.$id.$child->term_id.'" />';
												echo '<span></span>';
												echo '<em>'.$child->name.'</em>';
											echo '</div>';
										}
									echo '</div>';
								echo '</div>';
							}
						}
					echo '</div>';
				}
				#
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
		echo '<div class="-CheckBox-Box-Item">';
			echo '<input'.(( in_array($fky, $value) ) ? ' checked' : '').' type="checkbox" value="'.$fky.'" name="'.$InputName.'[]" id="'.$id.$fky.'" />';
			echo '<span></span>';
			echo '<em>'.$fvlue.'</em>';

			if( isset( $taxonomy_parent ) && $taxonomy_parent == 0 ){
				$ChildsTerms = get_terms( 
					array(
						'hide_empty'=>0,
						'taxonomy'=>$taxonomy_name,
						'parent'=>$fky,
					)
				);

				if( isset( $ChildsTerms[0] ) ){
					echo '<div class="-DropDown-Fields-Action"><i class="fa-solid fa-chevron-down"></i></div>';
					echo '<div class="Select-Childs">';
						echo '<h2 class="-Fields-DropDown-Title">اقسام ('.$fvlue.') الفرعية </h2>';
						echo '<div class="-Fields-DropDown-Childs">';
							foreach ($ChildsTerms as $child) {
								echo '<div class="-CheckBox-Box-Item">';
									echo '<input'.(( in_array($child->term_id, $value) ) ? ' checked' : '').' type="checkbox" value="'.$child->term_id.'" name="'.$InputName.'[]" id="'.$id.$child->term_id.'" />';
									echo '<span></span>';
									echo '<em>'.$child->name.'</em>';
								echo '</div>';
							}
						echo '</div>';
					echo '</div>';
				}
			}
		echo '</div>';
	}
}
