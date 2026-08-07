<?php 
function Breadcrumb() {
	$position = 1;
	echo '<ol itemscope itemtype="http://schema.org/BreadcrumbList" class="BreadcrumbsFilters">';
		echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
			echo '<a class="unline" itemprop="item" href="'.home_url().'">';
			echo '<span itemprop="name">'.((is_single()) ? '<i class="fa-solid fa-house-chimney"></i>' : '<i class="fa-solid fa-house-chimney"></i>').get_option('sitename').'</span></a>';
			echo '<meta itemprop="position" content="'.$position.'" />';
		echo '</li>';
		if( is_page() ) {
			global $post;
			if( $post->post_parent == 0 ) {
				$position++;
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
					echo '<a class="unline" itemprop="item" href="'.get_the_permalink($post->ID).'">';
					echo '<span itemprop="name">'.$post->post_title.'</span></a>';
					echo '<meta itemprop="position" content="'.$position.'" />';
				echo '</li>';
			}else {
				$position++;
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
					echo '<a class="unline" itemprop="item" href="'.get_the_permalink($post->post_parent).'">';
					echo '<span itemprop="name">'.get_the_title($post->post_parent).'</span></a>';
					echo '<meta itemprop="position" content="'.$position.'" />';
				echo '</li>';
				$position++;
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
					echo '<a class="unline" itemprop="item" href="'.get_the_permalink($post->ID).'">';
					echo '<span itemprop="name">'.$post->post_title.'</span></a>';
					echo '<meta itemprop="position" content="'.$position.'" />';
				echo '</li>';
			}
		}else if( is_single() ) {
			global $post;
			$category = (is_array(get_the_terms($post->ID, 'category', ''))) ? get_the_terms($post->ID, 'category', '') : array();
			foreach ( array_slice($category,0,2) as $cat) {
				$position++;
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
					echo '<a class="unline" itemprop="item" href="'.get_term_link($cat).'">';
					echo '<span itemprop="name">'.$cat->name.'</span></a>';
					echo '<meta itemprop="position" content="'.$position.'" />';
				echo '</li>';
			}
			$services = (is_array(get_the_terms($post->ID, 'services', ''))) ? get_the_terms($post->ID, 'services', '') : array();
			if( !empty( $services ) ){
				foreach ( array_slice($services,0,2) as $serv) {
					echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
						echo '<a class="unline" itemprop="item" href="'.get_term_link($serv).'">';
						echo '<span itemprop="name">'.$serv->name.'</span></a>';
						echo '<meta itemprop="position" content="'.$position.'" />';
					echo '</li>';
				}
			}
			$position++;
			echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem" style="display:none">';
				echo '<a class="unline" itemprop="item" href="'.get_the_permalink($post->ID).'">';
				echo '<span itemprop="name">'.$post->post_title.'</span></a>';
				echo '<meta itemprop="position" content="'.$position.'" />';
			echo '</li>';
		}else if( is_search() ) {
			$SearchQuery = urldecode(get_search_query());
			$position++;
			echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
				echo '<a class="unline" itemprop="item" href="'.(new ThemeStatic)->cleanURL(home_url('/search/'.str_replace(' ', '+', $SearchQuery))).'">';
				echo '<span itemprop="name">نتائج البحث عن : '.$SearchQuery.'</span></a>';
				echo '<meta itemprop="position" content="'.$position.'" />';
			echo '</li>';
		}else if( is_category() ) {
			$obj = get_queried_object();
			$parentID = 0;
			if( $obj->parent > 0 )  $parentID = $obj->parent;

			if( $parentID == 0 ) {
				$position++;
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
					echo '<a class="unline" itemprop="item" href="'.get_term_link($obj).'">';
					echo '<span itemprop="name">'.$obj->name.'</span></a>';
					echo '<meta itemprop="position" content="'.$position.'" />';
				echo '</li>';
			}else {
				if( $parentID != $obj->term_id ) {
					$position++;
					$parent = get_term($parentID, 'category');
					echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
						echo '<a class="unline" itemprop="item" href="'.get_term_link($parent).'">';
						echo '<span itemprop="name">'.$parent->name.'</span></a>';
						echo '<meta itemprop="position" content="'.$position.'" />';
					echo '</li>';
				}
				$position++;
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
					echo '<a class="unline" itemprop="item" href="'.get_term_link($obj).'">';
					echo '<span itemprop="name">'.$obj->name.'</span></a>';
					echo '<meta itemprop="position" content="'.$position.'" />';
				echo '</li>';
			}
		}else if( is_tag() or is_tax('services') or is_tax('filters') or is_tax('ratingbars') or is_tax('stores') or is_tax('statistics') or is_tax('customers') ) {
			$obj = get_queried_object();
			$parentID = 0;
			if( $obj->parent > 0 ) {
				$parentID = $obj->parent;
			}else if( get_term_meta($obj->term_id, 'parentc', true) > 0 ) {
				$parentID = get_term_meta($obj->term_id, 'parentc', true);
			}

			if( $parentID == 0 ) {
				$position++;
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
					echo '<a class="unline" itemprop="item" href="'.get_term_link($obj).'">';
					echo '<span itemprop="name">'.$obj->name.'</span></a>';
					echo '<meta itemprop="position" content="'.$position.'" />';
				echo '</li>';
			}else {
				$position++;
				$parent = get_term($parentID, $obj->taxonomy);
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
					echo '<a class="unline" itemprop="item" href="'.get_term_link($parent).'">';
					echo '<span itemprop="name">'.$parent->name.'</span></a>';
					echo '<meta itemprop="position" content="'.$position.'" />';
				echo '</li>';
				$position++;
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
					echo '<a class="unline" itemprop="item" href="'.get_term_link($obj).'">';
					echo '<span itemprop="name">'.$obj->name.'</span></a>';
					echo '<meta itemprop="position" content="'.$position.'" />';
				echo '</li>';
			}
		}else if( is_author() ) {
			$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
				$position++;
				echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
					echo '<a class="unline" itemprop="item" href="'.get_author_posts_url($curauth->ID).'">';
					echo '<span itemprop="name">'.$curauth->display_name.'</span></a>';
					echo '<meta itemprop="position" content="'.$position.'" />';
				echo '</li>';

		}
		if( (new ThemeStatic)->Paged() > 1 ) {
			$position++;
			echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
				echo '<a class="unline" itemprop="item" href="'.(new ThemeStatic)->GetCurrentURL().'">';
				echo '<span itemprop="name">صفحة '.(new ThemeStatic)->Paged().'</span></a>';
				echo '<meta itemprop="position" content="'.$position.'" />';
			echo '</li>';
		}
	echo '</ol>';
}