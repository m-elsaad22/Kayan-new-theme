<?php 
global $yc__widgets__center;
if( isset( $yc__widgets__center[$ModelCenter] ) && isset( $yc__widgets__center[$ModelCenter]['Packs'] ) ){

	if( !isset($value) || isset( $value ) && !is_array($value) ) $value = array();
	#
	if( isset( $InsertElements ) ){
		unset($vars['InsertElements']);
		$InputName = 'Insert_'.$id;
	}else if( isset( $parent_id ) ){
		$InputName = $parent_id.'['.$id.']';
	}else{
		$InputName = $id;
	}
	$vars['InputName'] = $InputName;
	# 
	foreach ( $yc__widgets__center[$ModelCenter]['Packs'] as $model__name => $fields ){

		if( is_array( $fields ) && isset( $fields['fields'] ) ){
			$FieldsSetup[ $model__name ] = $fields;
		}else{
			$FieldsSetup[ $model__name ] = $fields;
		}
	}
	#
	$KeysValues = array();
	foreach ( $value as $t => $wq) {
		$KeysValues[] = $t;
	}
	#
	$widget_UniqKey = uniqid();
	echo '<div class="Master-Widgets_selected">';
		echo '<div class="Title-MoreForms">';
			echo '<i class="fa-solid fa-square-plus"></i>';
			echo '<h2>'.$title.'</h2>';
			echo '<div class="-show-models-selected">';
				echo LoardIcons('jvucoldz','160px','160px',array('primary'=>'#041c36','secondary'=>'#1269eb'));
				echo '<span>إضافة شريحة جديدة </span>';
			echo '</div>';
		echo '</div>';

		echo '<div class="-Master-Selected-Icon">';

			echo '<div class="RightWidgets-Select" data-argums-keys="'.base64_encode( json_encode( $KeysValues ) ).'" data-fields-argums="'.base64_encode( json_encode( $vars ) ).'">';

				echo '<Widgets-select>';
					echo '<input type="text" id="widgets_'.$id.'_Searcher" data-searching-widgets="'.$widget_UniqKey.'" style="" placeholder="البحث في الودجات " autocomplete="off" autocorrect="off" name="widgets_'.$id.'_Searcher" spellcheck="false">';
				echo '</Widgets-select>';
				#
				echo '<ul>';
					foreach ($FieldsSetup as $k => $f) {
						if( isset( $f['fields'] ) ){
							$f['fields'] = FieldsJson($f['fields']);
						}
						echo '<li class="widgets-Selected-Items" data-argums-fields="'.base64_encode( json_encode( $f ) ).'" data-insert-widget="'.$k.'">';
							echo '<div class="WidgetInfo">';
								echo '<h2>'.$f['title'].'</h2>';
								echo ( ( isset( $f['description'] ) ) ) ? '<p>'.$f['description'].'</p>' : '';
							echo '</div>';

							echo ( ( isset( $f['screen-shoot'] ) ) ) ? '<div class="Info-show-Screen-Shoot"><i class="fa-solid fa-camera"></i></div>' : '';

						echo '</li>';
					}
				echo '</ul>';
			echo '</div>';

			echo '<div class="-Selcted-widget-items apbsortable" data-uniq="'.$widget_UniqKey.'" data-connect-with="sortbyme">';

				foreach ( $value as $skey => $widget_data) {

					if( isset( $FieldsSetup[ $widget_data['widget_id'] ] ) && isset( $widget_data['widget_post__id'] ) ){
						$single_widget__uniq = uniqid();

						$ActivableWidgets = $FieldsSetup[ $widget_data['widget_id'] ];

						echo '<div class="-widget-item -widget-type-'.$widget_data['widget_id'].'" data-uniq-key="'.$skey.'" data-un-key="'.$single_widget__uniq.'" data-widget-post-id="'.$widget_data['widget_post__id'].'" data-fields-argums="'.base64_encode( json_encode( $vars ) ).'">';

							echo '<sortbyme class="-widget-item-title-">';
								echo  '<h2>'.$ActivableWidgets['title'].( ( isset( $ActivableWidgets['description'] ) ) ? '<p>'.$ActivableWidgets['description'].'</p>' : '' ).'</h2>';
								echo '<i class="fa-solid fa-brush hoverable activable -widget-open" data-uniq="'.$single_widget__uniq.'"></i>';
								echo '<i class="fa-solid fa-xmark hoverable activable -widget-remove" data-remove-widgets="'.$single_widget__uniq.'"></i>';
								echo '<input type="hidden" name="'.$InputName.'['.$skey.'][widget_id]" value="'.$widget_data['widget_id'].'">';
								echo '<input type="hidden" name="'.$InputName.'['.$skey.'][widget_post__id]" value="'.$widget_data['widget_post__id'].'">';
							echo '</sortbyme>';

							echo '<div class="widget-fields-appender" data-uniq="'.$single_widget__uniq.'" style="display:none;"></div>';
						echo '</div>';
					}
					
				}
			echo '</div>';

		echo '</div>';

	echo '</div>';
}else{
	echo '<h2>لم يتم العثور على ودجات حتى الأن </h2>';
}