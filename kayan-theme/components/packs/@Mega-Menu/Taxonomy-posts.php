<?php
/**
 * YourColor MegaMenu posts
 * أُصلح هيكل if/elseif المكسور (كان يسبب Parse error).
 */
$ObjectTerm = get_term_by( 'id', $ObjectID, $ObjectValue );

if ( 'on' === $Objeccheck ) {
	echo '<div class="-Your-color-main-menu-"></div>';
} elseif ( 'on' === $Objeccheck2 ) {
	$PostArguments = array(
		'post_type'      => 'post',
		'posts_per_page' => 1,
	);

	foreach ( get_posts( $PostArguments ) as $product1 ) {
		$url     = get_the_permalink( $product1->ID );
		$img     = get_the_post_thumbnail_url( $product1->ID, 'default' );
		$title   = $product1->post_title;
		$content = $product1->post_content;
		echo '<div class="-YourColor-Menu-post">';
			echo '<div class="-YourColor-Menu-post-info">';
				echo '<div class="-YourColor-Menu-title">';
					echo '<a href="' . esc_url( $url ) . '">';
						echo '<h3>' . esc_html( $title ) . '</h3>';
					echo '</a>';
					echo '<div class="-YourColor-Menu-content">';
						echo wp_kses_post( $content );
					echo '</div>';
				echo '</div>';
			echo '</div>';
			if ( ! empty( $img ) ) {
				echo '<div class="-YourColor-Menu-img">';
					echo '<a href="' . esc_url( $url ) . '">';
						echo '<img src="' . esc_url( $img ) . '" alt="' . esc_attr( $title ) . '" />';
					echo '</a>';
				echo '</div>';
			}
		echo '</div>';
	}
}
