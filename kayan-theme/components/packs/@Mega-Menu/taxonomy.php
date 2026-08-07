<?php 
if( isset( $ObjectValue['button_mode'] ) && isset( $ObjectValue[ $ObjectValue['button_mode'] ] ) && isset( $ObjectValue[ $ObjectValue['button_mode'] ]['taxonomy_option'] ) && !empty( $ObjectValue[ $ObjectValue['button_mode'] ]['taxonomy_option'] ) ){
	echo '<div class="--Yr-color-maga-menu-">';
		echo '<div class="Yourcolor_title_button">';
			if( isset($ObjectValue['taxonomy']['main_menu_title'] ) && !empty ( $ObjectValue['taxonomy']['main_menu_title'] ) ) echo '<p class="--mega-menu-title--">' . $ObjectValue['taxonomy']['main_menu_title'] . '</p>';
			
			# button page
			if( isset( $ObjectValue['taxonomy']['button_main_page_tax'] ) && !empty( $ObjectValue['taxonomy']['button_main_page_tax'] ) && ( !isset( $ObjectValue['taxonomy']['Button__show_tax'] ) || isset( $ObjectValue['taxonomy']['Button__show_tax'] ) && !empty( $ObjectValue['taxonomy']['Button__show_tax'] ) ) ){
           		$button_page_title = ( ( isset( $ObjectValue['taxonomy']['button_main_Text_tax'] ) && !empty( $ObjectValue['taxonomy']['button_main_Text_tax'] ) ) ) ? $ObjectValue['taxonomy']['button_main_Text_tax'] : get_the_title( $ObjectValue['taxonomy']['button_main_page_tax'] );
               	echo '<div class="--mega-menu--main-list-short">';
                 	echo '<a href="'.get_the_permalink($ObjectValue['taxonomy']['button_main_page_tax']).'" class="--mega-menu--link-short main-ket-button_1">';
                   		echo '<span class="-mega-menu-button-des-short">'.$button_page_title.'</span>';
                        if( isset( $ObjectValue['taxonomy']['FirstButtonIcon_main_tax'] ) && !empty( $ObjectValue['taxonomy']['FirstButtonIcon_main_tax'] ) )echo'<span class="mega-menu-short-icon-">' .$ObjectValue['taxonomy']['FirstButtonIcon_main_tax']. '</span>';
                   	echo '</a>';
               	echo '</div>';
           	}
			# end button
		echo '</div>';
		echo '<div class="--mega-menu-list--">';
			echo'<div class="--all-mega-menu-in--">';

				if( isset( $ObjectValue['taxonomy']['short_main_menu_title'] ) && !empty( $ObjectValue['taxonomy']['short_main_menu_title'] ) ) echo'<p class="--short-mega-menu-title--">' .$ObjectValue['taxonomy']['short_main_menu_title']. '</p>';
			 	echo'<hr class="bg-white">';
				# taxonomy list
				echo '<ul class="Yourcolor_sub_menu">';
					$printed_categories = array(); 

						foreach ($ObjectValue[$ObjectValue['button_mode']]['taxonomy_option'] as $cat__id) {
						    $category__object = get_term_by('id', $cat__id, 'category');
						    $caticon = get_term_meta( $category__object ->term_id , 'icon' , true);
						    if (isset($category__object->term_id)) {
						        if (!in_array($category__object->term_id, $printed_categories)) {
						            echo '<li class="Yourcolor_sub_point">';
						            	if( !empty($caticon) ){
						            		echo '<span class="term-item-in-">' .$caticon. '</span>';
					            		}
						            	echo '<a href="' . get_term_link($category__object) . '" class="Yourcolor_sub_link">' . $category__object->name . '</a>';
						            echo '</li>';
						            $printed_categories[] = $category__object->term_id; 
						        }
						    }
						}

				echo '</ul>';
				# end list
			echo '</div>';
		echo '</div>';
	echo '</div>';
}