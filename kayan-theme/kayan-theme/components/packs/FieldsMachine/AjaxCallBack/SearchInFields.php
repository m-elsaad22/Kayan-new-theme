<?php
header("Content-Type: application/json");
ob_start();
$arguments = json_decode(base64_decode($Ajax__data['args']), true);

$Parts = json_decode(base64_decode($Ajax__data['part']), true);

$UniqId = $Ajax__data['uniq'];
$old__school = false;
if( isset( $Parts['field'] ) ){

	if( !isset( $Parts['field']['object__type'] ) ) $old__school = true;

	if( isset( $Parts['field']['object__type'] ) ){

		$Parts['field']['SearchQuery'] = $Ajax__data['SearchQuery'];
		$Parts['field']['PostsArguments'] = $arguments;
		$Parts['field']['Ajax'] = true;

		unset($Parts['field']['options']);
		ob_start();
		$this->Fields__Part($Parts['field']['type'],$Parts['field']);
		#
		$field_output = ob_get_clean();
		$new_data = explode('<EX_NewField>', $field_output)[1];
		$new_data = explode('</EX_NewField>', $new_data)[0];
		$new_data = json_decode( $new_data,true );
		#$json['lela'] = $new_data;
		$arguments = $new_data;
		
		$i = explode('<EX_EndMoreAjax>', $field_output)[1];
		$i = explode('</EX_EndMoreAjax>', $i)[0];
		
		$EX_loadmore__ajax = explode('<EX_loadmore__ajax>', $field_output)[1];
		$EX_loadmore__ajax = explode('</EX_loadmore__ajax>', $EX_loadmore__ajax)[0];
		$json['end'] = ( ( $EX_loadmore__ajax == false || $EX_loadmore__ajax == 'false' ) ) ? true : false;


		$OutPutHTML = explode('</EX_NewField>', $field_output)[1];
		if( strpos( $OutPutHTML , '<EX_EndMoreAjax>') !== FALSE ) $OutPutHTML = explode('<EX_EndMoreAjax>', $OutPutHTML)[0];
		echo $OutPutHTML;
	}


}

if( $old__school == true ){

	if( isset( $arguments['taxonomy'] ) ){

	    $i=0;
		if( isset( $Parts['field'] ) ){
			unset($arguments['offset']);
			$arguments['name__like'] = $Ajax__data['SearchQuery'];
			$Parts['field']['PostsArguments'] = $arguments;
			$Parts['field']['Ajax'] = true;

			unset($Parts['field']['options']);
			ob_start();
			$this->Fields__Part($Parts['field']['type'],$Parts['field']);
			#
			$field_output = ob_get_clean();
			$new_data = explode('<EX_NewField>', $field_output)[1];
			$new_data = explode('</EX_NewField>', $new_data)[0];
			$new_data = json_decode( $new_data,true );
			$json['lela'] = $new_data;
			$arguments = $new_data;
			
			$i = explode('<EX_EndMoreAjax>', $field_output)[1];
			$i = explode('</EX_EndMoreAjax>', $i)[0];
			
			$OutPutHTML = explode('</EX_NewField>', $field_output)[1];
			if( strpos( $OutPutHTML , '<EX_EndMoreAjax>') !== FALSE ) $OutPutHTML = explode('<EX_EndMoreAjax>', $OutPutHTML)[0];
			echo $OutPutHTML;
		}else{
	    	foreach (get_terms( $NewArguments ) as $cat) {$i++;
	    		#
	    	}
		}

		$json['end'] = false;
		if($i == 0 || $i < $arguments['number'] ) $json['end'] = true;

	}else if( isset( $arguments['YC_Current_Users'] ) ){
		#
		$i = 0;
		if( isset( $Parts['field'] ) ){
			unset($arguments['paged']);
			$arguments['search'] = '*'.$Ajax__data['SearchQuery'].'*';
			$Parts['field']['PostsArguments'] = $arguments;
			$Parts['field']['Ajax'] = true;

			unset($Parts['field']['options']);
			
			ob_start();
			$this->Fields__Part($Parts['field']['type'],$Parts['field']);
			#
			$field_output = ob_get_clean();
			$new_data = explode('<EX_NewField>', $field_output)[1];
			$new_data = explode('</EX_NewField>', $new_data)[0];
			$new_data = json_decode( $new_data,true );
			$arguments = $new_data;
			
			$i = explode('<EX_EndMoreAjax>', $field_output)[1];
			$i = explode('</EX_EndMoreAjax>', $i)[0];
			
			$OutPutHTML = explode('</EX_NewField>', $field_output)[1];
			if( strpos( $OutPutHTML , '<EX_EndMoreAjax>') !== FALSE ) $OutPutHTML = explode('<EX_EndMoreAjax>', $OutPutHTML)[0];
			echo $OutPutHTML;
		}else{
			foreach ( get_posts( $arguments )  as $post ) {$i++;
				if( $Parts['template'] == 'yc-froms' ){
					$AdminUrl = admin_url('admin.php?page=edit-forms&id='.$post->ID.'&action=edit');
					echo '<div class="-contain-MiniBox" data-post-id="'.$post->ID.'">';
						echo '<div class="-checkBox-post-fixed" data-selected-postactions="'.$post->ID.'" data-uniqid="'.$UniqId.'"></div>';
						echo '<div class="-mini-bx-info">';
							echo '<div class="-fix-selected-btn" data-selected-postactions="'.$post->ID.'" data-uniqid="'.$UniqId.'"></div>';
							echo '<h2><a href="'.$AdminUrl.'" target="_blank">'.wp_trim_words($post->post_title,10,'..').'</a></h2>';
							echo '<div class="-mini-Actions">';
								echo '<a href="'.$AdminUrl.'" target="_blank"><i class="fa-solid fa-pen-to-square"></i><span>تعديل النموذج </span></a>';
								echo '<div class="RemovePost" data-remove-post-id="'.$post->ID.'"><i class="fa-solid fa-trash-can"></i><span>حذف النموذج </span></div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				}
			}
		}
		$json['end'] = false;
		if($i == 0 || $i < $arguments['number'] ) $json['end'] = true;	

	}else if( isset( $arguments['post_type'] ) ){	
		#
		$i = 0;
		if( isset( $Parts['field'] ) ){
			unset($arguments['paged']);
			$arguments['s'] = $Ajax__data['SearchQuery'];
			$Parts['field']['PostsArguments'] = $arguments;
			$Parts['field']['Ajax'] = true;

			unset($Parts['field']['options']);
			
			ob_start();
			$this->Fields__Part($Parts['field']['type'],$Parts['field']);
			#
			$field_output = ob_get_clean();
			$new_data = explode('<EX_NewField>', $field_output)[1];
			$new_data = explode('</EX_NewField>', $new_data)[0];
			$new_data = json_decode( $new_data,true );
			$arguments = $new_data;
			
			$i = explode('<EX_EndMoreAjax>', $field_output)[1];
			$i = explode('</EX_EndMoreAjax>', $i)[0];
			
			$OutPutHTML = explode('</EX_NewField>', $field_output)[1];
			if( strpos( $OutPutHTML , '<EX_EndMoreAjax>') !== FALSE ) $OutPutHTML = explode('<EX_EndMoreAjax>', $OutPutHTML)[0];
			echo $OutPutHTML;
		}else{
			foreach ( get_posts( $arguments )  as $post ) {$i++;
				if( $Parts['template'] == 'yc-froms' ){
					$AdminUrl = admin_url('admin.php?page=edit-forms&id='.$post->ID.'&action=edit');
					echo '<div class="-contain-MiniBox" data-post-id="'.$post->ID.'">';
						echo '<div class="-checkBox-post-fixed" data-selected-postactions="'.$post->ID.'" data-uniqid="'.$UniqId.'"></div>';
						echo '<div class="-mini-bx-info">';
							echo '<div class="-fix-selected-btn" data-selected-postactions="'.$post->ID.'" data-uniqid="'.$UniqId.'"></div>';
							echo '<h2><a href="'.$AdminUrl.'" target="_blank">'.wp_trim_words($post->post_title,10,'..').'</a></h2>';
							echo '<div class="-mini-Actions">';
								echo '<a href="'.$AdminUrl.'" target="_blank"><i class="fa-solid fa-pen-to-square"></i><span>تعديل النموذج </span></a>';
								echo '<div class="RemovePost" data-remove-post-id="'.$post->ID.'"><i class="fa-solid fa-trash-can"></i><span>حذف النموذج </span></div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				}
			}
		}
		$json['end'] = false;
		if($i == 0 || $i < $arguments['posts_per_page'] ) $json['end'] = true;	
	}
}


$html = ob_get_clean();
if(empty($html)){
	$html = '<div class="NothingFoundFilter">';
		if( isset( $arguments['post_type'] ) ){
			$PostTypeArguments = PostTypeArguments( array( 'getIn'=>$arguments['post_type'] ) )[$arguments['post_type']];
			$html .= '<i class="fa-solid fa-users-slash"></i>';
			$html .= '<p>لم يتم العثور على نتائج لبحثك "'.$Ajax__data['SearchQuery'].'" في '.$PostTypeArguments->labels->not_found.'</p>';
		}else if( isset( $arguments['taxonomy'] ) ){
			$html .= '<i class="fa-solid fa-battery-empty"></i>';
			$html .= '<p>لم يتم العثور على نتائج اخري </p>';
			$html .= '<p>لقد شاهدت كل العناصر التي تُعرض فى هذه الصفحة</p>';			
		}
	$html .= '</div>';
	$json['end'] = true;
}

$json['output'] = $html;
$json['arguments'] = base64_encode(json_encode($arguments));

echo json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);