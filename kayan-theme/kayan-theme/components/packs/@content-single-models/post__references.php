<?php 
$references = get_post_meta($post->ID, 'references', true);
if( !empty($references) ) {
	$references = explode(PHP_EOL, $references);
	$references = array_filter($references);
}

$references = ( is_array( $references ) ) ? $references : array();
if( !empty( $references ) ){
	echo '<div class="-references-post">';
		echo '<h2 class="-Title-references">';
			echo '<div class="-references-title-context">';
				echo '<span>المراجع  </span>';
			echo '</div>';
			echo '<toggle-toc class="references hoverable activable"><span>عرض الكل </span><i class="fa-solid fa-arrow-down"></i></toggle-toc>';
		echo '</h2>';

		echo '<ul>';
			$nt=0;
			foreach ( $references as $key => $string) {$nt++;
				preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $string, $match);
				foreach( $match[0] as $url ) {
					$string = str_replace($url, '<a dir="auto" href="'.$url.'" rel="nofollow" target="_blank" class="unline hoverable activable">'.mb_substr($url, 0, 60).'<em>'.str_pad($nt, 2, "0", STR_PAD_LEFT).'</em></a>', $string);
				}
				echo '<li>'.$string.'</li>';
			}
		echo '</ul>';
	echo '</div>';
}