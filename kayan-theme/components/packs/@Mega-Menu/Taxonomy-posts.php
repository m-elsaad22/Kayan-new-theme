<?php 
// # YourColor MegaMenu posts
$ObjectTerm = get_term_by('id',$ObjectID,$ObjectValue,$Objeccheck,$Objeccheck2,$ObjecURL,$_menu_item_icon);

if  ($Objeccheck == 'on' ) {
echo'fsdfj';
}

elseif  ($Objeccheck2 == 'on' ) {
		$PostArguments = array(
			'post_type'=>'post',
			'posts_per_page'=>1,
		);

			foreach (get_posts($PostArguments) as $product1) {
				$url = get_the_permalink($product1->ID);
				$img = get_the_post_thumbnail_url($product1->ID, 'default');
				$title = $product1->post_title;
				$content = $product1->post_content;
				echo'<div class="-YourColor-Menu-post">';
		        	echo '<div class="-YourColor-Menu-post-info">';
			            echo '<div class="-YourColor-Menu-title">';
							echo'<a href="'.$url.'">';
								echo '<h3>'.$title.'</h3>';
				        	echo '</a>';
			            	echo '<div class="-YourColor-Menu-content">';
	  							echo' '.$content.' ';
	     		        	echo'</div>';	
			        	echo'</div>';	
			        echo'</div>';	
			        echo'<div class="-YourColor-Menu-img">';
						echo'<a href="'.$url.'">';
			            	echo '<img  src="'.$img.'"/>';
			        	echo '</a>';
		            echo '</div>';
				echo'</div>';
			}
if  ($Objeccheck == 'on' ) {
	echo '<div class="-Your-color-main-menu-">';
	
	echo '</div>';
	foreach (get_posts($PostArguments) as $product1) {
		$url = get_the_permalink($product1->ID);
		$img = get_the_post_thumbnail_url($product1->ID, 'default');
		$title = $product1->post_title;
		$content = $product1->post_content;
		echo'<div class="-YourColor-Menu-post">';
        	echo '<div class="-YourColor-Menu-post-info">';
        		echo '<div class="-YourColor-Menu-taxonpmy--title-">';
        		echo '</div>';
    		echo '</div>';
		echo '</div>';
	}
}

elseif  ($Objeccheck2 == 'on' ) {

}