<?php 
#
$post___faqs = get_post_meta($post->ID,'yourcolor__faqs',true);
$post___faqs = ( is_array($post___faqs) ) ? $post___faqs : array();
if( !empty( $post___faqs ) ){
	echo '<div class="-YC-FaqsSimple-vsingle">';
		echo '<h2 class="--widget--sidebar--title">الاسئلة الشائعة </h2>';
		echo '<div class="-YC-FaqsSimple-vsingle-items">';
			$v__s=0;
			foreach ($post___faqs as $ke_s => $v) {$v__s++;

				echo '<div class="-YC-FaqsSimple-vsingle-Item-v2'.( ( $v__s == 1 ) ? ' active' : '').'">';

					echo '<div class="-YC-FaqsSimple-vsingle-Title" data-toggle-faqs="'.$ke_s.'">';
						echo '<div class="--fq-count">'.str_pad($v__s, 2, "0", STR_PAD_LEFT).'</div>';
						echo '<h2>'.$v['question'].'</h2>';
						echo '<i class="fa-solid fa-plus"></i>';
					echo '</div>';

					echo '<div class="-FaqsSimple-vsingle-Content-Row-v1 -Toggle-Content">';
						echo '<div class="-p-FaqsSimple-vsingle-ContentValue-v1 -ToggleContentValue">'.$v['answer'].'</div>';
					echo '</div>';

				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
}