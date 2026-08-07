<?php 
if( isset( $ObjectValue['button_mode'] ) && isset( $ObjectValue[ $ObjectValue['button_mode'] ] ) && isset( $ObjectValue[ $ObjectValue['button_mode'] ]['post__mapItems_list'] ) && !empty( $ObjectValue[ $ObjectValue['button_mode'] ]['post__mapItems_list'] ) ){
	echo '<div class="--Yr-color-maga-menu-">';
		echo '<div class="Yourcolor_title_button">';
			if( isset($ObjectValue['post']['main_menu_title_post'] ) && !empty ( $ObjectValue['post']['main_menu_title_post'] ) ) echo '<p class="--mega-menu-title--">' . $ObjectValue['post']['main_menu_title_post'] . '</p>';
			
			# button page
			if( isset( $ObjectValue['post']['button_main_page_posts'] ) && !empty( $ObjectValue['post']['button_main_page_posts'] ) && ( !isset( $ObjectValue['post']['Button__show_posts'] ) || isset( $ObjectValue['post']['Button__show_posts'] ) && !empty( $ObjectValue['post']['Button__show_posts'] ) ) ){
           		$button_page_title = ( ( isset( $ObjectValue['post']['button_main_Text_posts'] ) && !empty( $ObjectValue['post']['button_main_Text_posts'] ) ) ) ? $ObjectValue['post']['button_main_Text_posts'] : get_the_title( $ObjectValue['post']['button_main_page_posts'] );
               	echo '<div class="--mega-menu--main-list-short">';
                 	echo '<a href="'.get_the_permalink($ObjectValue['post']['button_main_page_posts']).'" class="--mega-menu--link-short main-ket-button_1">';
                   		echo '<span class="-mega-menu-button-des-short">'.$button_page_title.'</span>';
                        if( isset( $ObjectValue['post']['FirstButtonIcon_main_posts'] ) && !empty( $ObjectValue['post']['FirstButtonIcon_main_posts'] ) )echo'<span class="mega-menu-short-icon-">' .$ObjectValue['post']['FirstButtonIcon_main_posts']. '</span>';
                   	echo '</a>';
               	echo '</div>';
           	}
			# end button

		echo '</div>';
		echo '<div class="--mega-menu-list--">';
			echo'<div class="--all-mega-menu-in--">';

				if( isset( $ObjectValue['post']['short_main_menu_title_post'] ) && !empty( $ObjectValue['post']['short_main_menu_title_post'] ) ) echo'<p class="--short-mega-menu-title--">' .$ObjectValue['post']['short_main_menu_title_post']. '</p>';
			 	echo'<hr class="bg-white">';
				# post list
				echo '<ul class="Yourcolor_sub_menu">';
					foreach ( $ObjectValue[ $ObjectValue['button_mode'] ]['post__mapItems_list'] as $post__id ) {
						$post__object = get_post($post__id);
						$icons = get_post_meta($post__id, 'articon', true);
						if( isset( $post__object->ID ) ){
							echo '<li class="Yourcolor_sub_point">';
								if( !empty($icons) ){
									echo '<span class="term-item-in-">' .$icons. '</span>';
								}
								echo' <a href="' .get_the_permalink($post__object). '" class="Yourcolor_sub_link">' .$post__object->post_title. '</a>';
							echo '</li>';
						}
					}
				echo '</ul>';
				# end list
			echo '</div>';
		echo '</div>';
	echo '</div>';
}