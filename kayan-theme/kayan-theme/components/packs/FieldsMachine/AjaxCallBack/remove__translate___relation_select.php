<?php header("Content-Type: application/json");
ob_start();
$json = array();
$json['output'] = '';
$Ajax__data = YC_stripslashes_deep($Ajax__data);

if( isset( $Ajax__data['remove__id'] ) ){
	$YC__CFM = (new YC__CFM);
	$Relations__languages__locate = new Relations__languages__locate;
	$Remover____list = array();

	$remove__relation__get = $Relations__languages__locate->get(
		array(
			'id'=>$Ajax__data['remove__id']
		)
	);

	if( !empty( $remove__relation__get ) && isset( $remove__relation__get[0] ) ){
		$Remover____list['DB__ids'][] = $remove__relation__get[0]->id;
		$Remover____list['remove_meta__ids'][] = $remove__relation__get[0]->ObjectID;
		#
		$count__remover__relations = $Relations__languages__locate->count(
			array(
				'RelationDB__id'=>$remove__relation__get[0]->RelationDB__id,
				'ObjectAction'=>$remove__relation__get[0]->ObjectAction
			)
		);

		$count__remover__relations = $count__remover__relations - 1;


		if( $count__remover__relations <= 1 ){
			$once__relation__get = $Relations__languages__locate->get(
				array(
					'RelationDB__id'=>$remove__relation__get[0]->RelationDB__id,
					'ObjectAction'=>$remove__relation__get[0]->ObjectAction

				)
			);

			foreach ( $once__relation__get as $r ) {
				if( !in_array( $r->id,$Remover____list['DB__ids'] ) ) $Remover____list['DB__ids'][] = $r->id;
				if( !in_array( $r->ObjectID,$Remover____list['remove_meta__ids'] ) ) $Remover____list['remove_meta__ids'][] = $r->ObjectID;
			}

		}

		if( isset( $Remover____list['DB__ids'] ) ){
			foreach ( is_array( $Remover____list['DB__ids'] ) ? $Remover____list['DB__ids'] : array() as $idsDB ) {
				$Relations__languages__locate->RemoveID($idsDB);
			}
		}

		if( isset( $Remover____list['remove_meta__ids'] ) ){
			foreach ( is_array( $Remover____list['remove_meta__ids'] ) ? $Remover____list['remove_meta__ids'] : array() as $objmeta__id ) {
				if( $remove__relation__get[0]->ObjectAction == 'taxonomy' ){
					delete_term_meta($objmeta__id,'relationDB__id');
				}else{
					delete_post_meta($objmeta__id,'relationDB__id');
				}
			}
		}

		$RelationsDB = $Relations__languages__locate->get(
			array(
				'RelationDB__id'=>$remove__relation__get[0]->RelationDB__id,
				'ObjectAction'=>$remove__relation__get[0]->ObjectAction
			)
		);
		#
		$json['counter'] = 0;
		if( !empty( $RelationsDB ) && isset( $RelationsDB[0] ) ){
			$json['counter'] = count( $RelationsDB );

			foreach ( $RelationsDB as $relation__db_item ) {

				if( $relation__db_item->ObjectAction == 'taxonomy' ){
					$relationObject = get_term_by('id',$relation__db_item->ObjectID,$relation__db_item->ObjectType );
				}else{
					$relationObject = get_post( $relation__db_item->ObjectID );
				}

				$YC__CFM->AdminPart('Translate-Select-Box', array( 'post'=>$relationObject,'object__type'=> $relation__db_item->ObjectAction ,'DBValue'=>$relation__db_item ) );

			}

			$output = ob_get_clean();
			$json['output'] = $output;
		}
	}

	echo json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
}
