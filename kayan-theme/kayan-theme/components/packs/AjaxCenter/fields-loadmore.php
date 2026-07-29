<?php
header("Content-Type: application/json");
ob_start();
$arguments = json_decode(base64_decode($_POST['args']), true);

$Parts = json_decode(base64_decode($_POST['part']), true);

if( !isset($arguments["paged"]) ) $arguments["paged"] = 1;
$arguments["paged"] = $arguments["paged"] + 1;

#
$UniqId = $_POST['uniq'];


$old__school = false;
if( isset( $Parts['field'] ) ){

	if( !isset( $Parts['field']['object__type'] ) ) $old__school = true;

	if( isset( $Parts['field']['object__type'] ) ){

		$Parts['field']['PostsArguments'] = $arguments;
		$Parts['field']['Ajax'] = true;

		unset($Parts['field']['options']);
		unset($Parts['field']['vars']);
		
		ob_start();
		$this->ThemeStatic->Blade('UIFields',$Parts['field'],$Parts['field']['type'] );
		#
		$field_output = ob_get_clean();
		$new_data = explode('<EX_NewField>', $field_output)[1];
		$new_data = explode('</EX_NewField>', $new_data)[0];
		$new_data = json_decode( $new_data,true );
		$json['test__argums'] = $new_data;
		$arguments = $new_data;
		
		$i = explode('<EX_EndMoreAjax>', $field_output)[1];
		$i = explode('</EX_EndMoreAjax>', $i)[0];
		
		$OutPutHTML = explode('</EX_NewField>', $field_output)[1];
		if( strpos( $OutPutHTML , '<EX_EndMoreAjax>') !== FALSE ) $OutPutHTML = explode('<EX_EndMoreAjax>', $OutPutHTML)[0];

		echo $OutPutHTML;
	}

}

if( $old__school == true ){

	if( isset( $arguments['taxonomy'] ) ){
		$PagesNum = $arguments["paged"] - 1;
		$Offset = $arguments['number'] * $PagesNum;

		$NewArguments = $arguments;
		$NewArguments['offset'] = $Offset;
		unset($NewArguments['paged']);

	    $i=0;
		if( isset( $Parts['field'] ) ){
			unset($NewArguments['offset']);
			$Parts['field']['TaxonomyArguments'] = $NewArguments;
			$Parts['field']['Ajax'] = true;
			unset($Parts['field']['options']);
			ob_start();
			$this->ThemeStatic->Blade('UIFields',$Parts['field'],$Parts['field']['type'] );
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

			$OutPutHTML = explode('</EX_NewField>', $field_output)[1];
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
			
			$Parts['field']['PostsArguments'] = $arguments;
			$Parts['field']['Ajax'] = true;

			unset($Parts['field']['options']);
			
			ob_start();
			$this->ThemeStatic->Blade('UIFields',$Parts['field'],$Parts['field']['type'] );
			#
			$field_output = ob_get_clean();
			$new_data = explode('<EX_NewField>', $field_output)[1];
			$new_data = explode('</EX_NewField>', $new_data)[0];
			$new_data = json_decode( $new_data,true );
			$json['test__argums'] = $new_data;
			$arguments = $new_data;
			
			$i = explode('<EX_EndMoreAjax>', $field_output)[1];
			$i = explode('</EX_EndMoreAjax>', $i)[0];
			
			$OutPutHTML = explode('</EX_NewField>', $field_output)[1];
			if( strpos( $OutPutHTML , '<EX_EndMoreAjax>') !== FALSE ) $OutPutHTML = explode('<EX_EndMoreAjax>', $OutPutHTML)[0];

			echo $OutPutHTML;
		}else{
			#
		}
		$json['end'] = false;
		if($i == 0 || $i < $arguments['number'] ) $json['end'] = true;

	}else if( isset( $arguments['post_type'] ) ){
		#
		$i = 0;
		if( isset( $Parts['field'] ) ){
			
			$Parts['field']['PostsArguments'] = $arguments;
			$Parts['field']['Ajax'] = true;

			unset($Parts['field']['options']);
			
			ob_start();
			$this->ThemeStatic->Blade('UIFields',$Parts['field'],$Parts['field']['type'] );
			#
			$field_output = ob_get_clean();
			$new_data = explode('<EX_NewField>', $field_output)[1];
			$new_data = explode('</EX_NewField>', $new_data)[0];
			$new_data = json_decode( $new_data,true );
			$json['test__argums'] = $new_data;
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
			if( $arguments['post_type'] == 'videos' ){
				$html .= '<i class="fa-solid fa-display"></i>';
				$html .= '<p>لم يتم العثور على فيديوهات اخري </p>';
				$html .= '<p>لقد شاهدت كل الفيديوهات التي تُعرض فى هذه الصفحة</p>';				
			}else if( $arguments['post_type'] == 'attachment' ){
				$html .= '<i class="fa-solid fa-image"></i>';
				$html .= '<p>لم يتم العثور على صور اخري </p>';
				$html .= '<p>لقد شاهدت كل الصور التي تُعرض فى هذه الصفحة</p>';				
			}else if( $arguments['post_type'] == 'quotes' ){
				$html .= '<i class="fa-solid fa-pen-nib"></i>';
				$html .= '<p>لم يتم العثور على مقتطفات اخري </p>';
				$html .= '<p>لقد شاهدت كل المقتطفات التي تُعرض فى هذه الصفحة</p>';				
			}else if( $arguments['post_type'] == 'post' ){
				$html .= '<i class="fa-solid fa-users-slash"></i>';
				$html .= '<p>لم يتم العثور على شخصيات اخري </p>';
				$html .= '<p>لقد شاهدت كل الشخصيات التي تُعرض فى هذه الصفحة</p>';				

			}else if( $arguments['post_type'] == 'yc-froms' ){
				$html .= '<i class="fa-solid fa-table-cells-large"></i>';
				$html .= '<p>لم يتم العثور على نماذج اخري</p>';
				$html .= '<p>لقد شاهدت كل النماذج التي تُعرض فى هذه الصفحة</p>';
			}
		}else if( isset( $arguments['YC_Current_Users'] ) ){
				$html .= '<i class="fa-solid fa-table-cells-large"></i>';
				$html .= '<p>لم يتم العثور على اعضاء اخري</p>';
				$html .= '<p>لقد شاهدت كل الاعضاء التي تُعرض فى هذه الصفحة</p>';
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