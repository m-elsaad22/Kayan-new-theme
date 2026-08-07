<?php global $yc__widgets__center;
$widget_type = get_post_meta( $widget__post,'widget_type' ,true);
$widget_post_meta = ( is_array( get_post_meta( $widget__post, 'widget_post_meta',true ) ) ) ? get_post_meta( $widget__post, 'widget_post_meta',true ) : array();

# FOUND WIDGET ID IN VALUES .	
	$Widget__Key = '';
	foreach ($value as $k => $v) {
		if( $v['widget_post__id'] == $widget__post ){
			$Widget__Key = $k;
		}
	}

if( isset( $yc__widgets__center[$ModelCenter] ) && isset( $yc__widgets__center[$ModelCenter]['Packs'][$widget_type] ) && $Widget__Key != '' ){
  	echo  '<div class="widget-fields-InnerElemnt">';
		echo  '<div class="widget-fields-area">';
			if( isset( $yc__widgets__center[$ModelCenter]['Packs'][$widget_type]['fields'] ) ){
				foreach ( $yc__widgets__center[$ModelCenter]['Packs'][$widget_type]['fields'] as $k => $single_field) {
					$single_field['parent_id'] = 'widget_post_meta_'.$single_widget__uniq.'_'.$id;
					#
					if( isset( $widget_post_meta[ $single_field['id'] ] ) && !empty( $widget_post_meta[ $single_field['id'] ] ) ) {
						if( isset( $widget_post_meta[ $single_field['id'].'_id' ] ) && !empty( $widget_post_meta[ $single_field['id'].'_id' ] ) ){
							$single_field['value']['url'] = $widget_post_meta[ $single_field['id'] ];
							$single_field['value']['id'] = $widget_post_meta[ $single_field['id'].'_id' ];
						}else{
							$single_field['value'] = $widget_post_meta[ $single_field['id'] ];
						}

						# HIDE FIELDS .
						if( isset( $single_field['create_hide_fields'] ) ){
							foreach ( $single_field['create_hide_fields'] as $ek => $innerField) {
								if( $ek == $single_field['value'] && !empty( $innerField['fields'] ) ){
									foreach ( $innerField['fields'] as $e => $newfld) {
										if( isset( $widget_post_meta[ $newfld['id'] ] ) && !empty( $widget_post_meta[ $newfld['id'] ] ) ) {
											if( isset( $widget_post_meta[ $newfld['id'].'_id' ] ) && !empty(  $widget_post_meta[ $newfld['id'].'_id' ] ) ){
												$single_field['create_hide_fields'][$ek]['fields'][$e]['value']['url'] = $widget_post_meta[ $newfld['id'] ];
												$single_field['create_hide_fields'][$ek]['fields'][$e]['value']['id'] = $widget_post_meta[ $newfld['id'].'_id' ];
											}else{
												$single_field['create_hide_fields'][$ek]['fields'][$e]['value'] = $widget_post_meta[ $newfld['id'] ];
											}

										}															
									}

								}
							}
						}
					}

					$this->Fields__Part($single_field['type'],$single_field);
				}
			}
		echo  '</div>';
  		echo '<div class="-YC-widget-save-area">';
  			echo '<div class="-YC-widget-action-button -YC-widget-undo-button activable disable" data-un-key="'.$single_widget__uniq.'"><span>تراجع </span><i class="fa-solid fa-rotate-left"></i></div>';
  			echo '<div class="-YC-widget-action-button -YC-widget-save-button activable disable" data-uniq="'.$Widget__Key.'" data-un-key="'.$single_widget__uniq.'" data-save-widget="widget_post_meta_'.$single_widget__uniq.'_'.$id.'"><span>حفظ</span><i class="fa-solid fa-arrow-left"></i></div>';
		echo '</div>';
	echo '</div>';
}