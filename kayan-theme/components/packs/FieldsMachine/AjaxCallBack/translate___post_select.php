<?php header("Content-Type: application/json");
ob_start();

$json = array();

$Ajax__data = YC_stripslashes_deep($Ajax__data);

$Ajax__data['args'] = json_decode (base64_decode( $Ajax__data['args'] ) , true);
$relation__success = true;

$RandomBytes = random_bytes(16);
$Bin2hex = bin2hex($RandomBytes);

if( isset( $Ajax__data['connecter__id'] ) && $Ajax__data['args']['ObjectID'] ){
	$Theme__LanguageMachine = new Theme__LanguageMachine;
	$languages___lists = $Theme__LanguageMachine->LanguagesSelectList();

	$Relations__languages__locate = new Relations__languages__locate;

	$YC__CFM = (new YC__CFM);
	# HOW TO GET REMOVER LACT ACTIONS.
		if(  isset( $Ajax__data['args']['remover__data'] ) ){

			if( isset( $Ajax__data['args']['remover__data']['remove_relation_post_meta'] ) ){
				foreach ( is_array( $Ajax__data['args']['remover__data']['remove_relation_post_meta'] ) ? $Ajax__data['args']['remover__data']['remove_relation_post_meta'] : array() as $remove_meta__id ) {
					if( $Ajax__data['args']['ObjectAction'] == 'taxonomy' ){
						delete_term_meta( $remove_meta__id,'relationDB__id' );
					}else{
						delete_post_meta( $remove_meta__id,'relationDB__id' );
					}
				}
			}

			if( isset( $Ajax__data['args']['remover__data']['relation__id'] ) ){
				foreach ( is_array( $Ajax__data['args']['remover__data']['relation__id'] ) ? $Ajax__data['args']['remover__data']['relation__id'] : array() as $remove_relation__id ) {
					$Relations__languages__locate->RemoveID( $remove_relation__id );
				}
			}
		}

	# EXTRACT OBJECT TYPES .
		if( $Ajax__data['args']['ObjectAction'] == 'taxonomy' ){

			$PostTypeArguments = TaxonomyesObject( array( 'getIn'=>$Ajax__data['args']['ObjectType'] ) )[$Ajax__data['args']['ObjectType']];
			$wp__object__singular_name = $PostTypeArguments->labels->singular_name;
		}else{

			$PostTypeArguments = PostTypeArguments( array( 'getIn'=>$Ajax__data['args']['ObjectType'] ) )[$Ajax__data['args']['ObjectType']];
			$wp__object__singular_name = $PostTypeArguments->labels->singular_name;
		}

	# GET CURRENT RELATION ID .
		if( $Ajax__data['args']['ObjectAction'] == 'taxonomy' ){
			$current__object = get_term_by('id',$Ajax__data['args']['ObjectID'],$Ajax__data['args']['ObjectType']);
			$current__objID = $current__object->term_id;

			$current___relationDB__id = get_term_meta( $current__objID,'relationDB__id', true);
			$current___post__language = get_term_meta( $current__objID,'post__language', true);
			#
			$current__title = $current__object->name;

			$current__adminURL = admin_url('term.php?taxonomy='.$current__object->taxonomy.'&tag_ID='.$current__object->term_id);

		}else{
			$current__object = get_post($Ajax__data['args']['ObjectID']);
			$current__objID = $current__object->ID;

			$current___relationDB__id = get_post_meta( $current__objID,'relationDB__id', true);
			$current___post__language = get_post_meta( $current__objID,'post__language', true);

			$current__title = $current__object->post_title;

			$current__adminURL = admin_url('post.php?post='.$current__object->ID.'&action=edit');
		}

		$mini_current__title = wp_trim_words($current__title,5,'..');
	# GET CONNECTER RELATION ID .
		if( $Ajax__data['args']['ObjectAction'] == 'taxonomy' ){
			$connecter__object = get_term_by('id',$Ajax__data['connecter__id'],$Ajax__data['args']['ObjectType']);
			$connecter__objID = $connecter__object->term_id;

			$connecter___relationDB__id = get_term_meta( $connecter__objID,'relationDB__id', true);
			$connecter___post__language = get_term_meta( $connecter__objID,'post__language', true);
			#
			$connecter__title = $connecter__object->name;

			$connecter__adminURL = admin_url('term.php?taxonomy='.$connecter__object->taxonomy.'&tag_ID='.$connecter__object->term_id);

		}else{
			$connecter__object = get_post($Ajax__data['connecter__id']);
			$connecter__objID = $connecter__object->ID;

			$connecter___relationDB__id = get_post_meta( $connecter__objID,'relationDB__id', true);
			$connecter___post__language = get_post_meta( $connecter__objID,'post__language', true);
			#
			$connecter__title = $connecter__object->post_title;

			$connecter__adminURL = admin_url('post.php?post='.$connecter__object->ID.'&action=edit');

		}

		$mini_connecter__title = wp_trim_words($connecter__title,5,'..');

		$title_current___post__language = $languages___lists[$current___post__language]['title'];
		$title_connecter___post__language = $languages___lists[$connecter___post__language]['title'];


		$error__log = array(
			'languages_empty'=> array(
				'message'=>"تغذر إتمام عملية الربط .. تعذر الوصول الى اللغة الاساسية للـ  <p>{$wp__object__singular_name}</p> الذي قد قًمت بتحديده ",
				'alert'=>false
			),
			'languages_equal'=> array(
				'message'=>"تعذر اتمام عميلة ربط<p>{$wp__object__singular_name}</p><span><a href='{$current__adminURL}' target='_blank'>{$mini_current__title}</a></span>بـ<p>{$wp__object__singular_name}</p><span><a href='{$connecter__adminURL}' target='_blank'>{$mini_connecter__title}</a></span>برجاء التأكد من اختيار<p>{$wp__object__singular_name}</p>بلغة أخرى غير<strong>{$title_current___post__language}</strong>",
				'alert'=>false
			),
			'found_last_language_AND_equal'=> array(
				'message'=>"لقد قًمت بتنفيذ هذه العملية في وقت سابق .. الـ<p>{$wp__object__singular_name}</p><span><a href='{$connecter__adminURL}' target='_blank'>{$mini_connecter__title}</a></span>بالفعل هو النسخة<strong>{$title_connecter___post__language}</strong>من الـ<p>{$wp__object__singular_name}</p><span><a href='{$current__adminURL}' target='_blank'>{$mini_current__title}</a></span>باللغة<strong>{$title_current___post__language}</strong>",
				'alert'=>false
			),
			'current__found_last_language'=> array(
				'title'=>"تعذر اتمام عميلة ربط",
				'message'=>"<div class='--pp--alert'><custom---p-alert>تعذر اتمام عميلة ربط<p>{$wp__object__singular_name}</p><span><a href='{$current__adminURL}' target='_blank'>{$mini_current__title}</a></span>بـ<p>{$wp__object__singular_name}</p><span><a href='{$connecter__adminURL}' target='_blank'>{$mini_connecter__title}</a></span></custom---p-alert><custom---p-alert>لقد تم العثور على<p>{$wp__object__singular_name}</p>اخر قُمت بتحديده للـ<p>{$wp__object__singular_name}</p> الحالى يحمل نفس لغة <span><a href='{$connecter__adminURL}' target='_blank'>{$mini_connecter__title}</a></span>وهي اللغة<strong>{$title_connecter___post__language}</strong></custom---p-alert><custom---p-alert>اذا كنت تريد الاستمرار سيتم استبدال الـ<p>{$wp__object__singular_name}</p><span><a href='%REMOVE_RELATION_ADMIN_URL%' target='_blank'>%REMOVED_RELATION_POST_TITLE%</a></span>بـ<p>{$wp__object__singular_name}</p><span><a href='{$connecter__adminURL}' target='_blank'>{$mini_connecter__title}</a></span>حيث يكون<span><a href='{$connecter__adminURL}' target='_blank'>{$mini_connecter__title}</a></span>هو النسخة<strong>{$title_connecter___post__language}</strong>لـ<p>{$wp__object__singular_name}</p><span><a href='{$current__adminURL}' target='_blank'>{$mini_current__title}</a></span>باللغة<strong>{$title_current___post__language} </strong> .</div></custom---p-alert>",
				'alert'=>true
			),
			'connecter__found_last_language'=> array(
				'title'=>"تعذر اتمام عميلة ربط",
				'message'=>"<div class='--pp--alert'><custom---p-alert>تعذر اتمام عميلة ربط<p>{$wp__object__singular_name}</p><span><a href='{$current__adminURL}' target='_blank'>{$mini_current__title}</a></span>بـ<p>{$wp__object__singular_name}</p><span><a href='{$connecter__adminURL}' target='_blank'>{$mini_connecter__title}</a></span></custom---p-alert><custom---p-alert>لقد تم العثور على<p>{$wp__object__singular_name}</p>اخر قُمت بتحديده للـ<p>{$wp__object__singular_name}</p><span><a href='{$connecter__adminURL}' target='_blank'>{$mini_connecter__title}</a></span>يحمل نفس لغة الـ<p>{$wp__object__singular_name}</p>الحالى وهي اللغة<strong>{$title_current___post__language}</strong></custom---p-alert><custom---p-alert>اذا كنت تريد الاستمرار سيتم استبدال الـ<p>{$wp__object__singular_name}</p><span><a href='%REMOVE_RELATION_ADMIN_URL%' target='_blank'>%REMOVED_RELATION_POST_TITLE%</a></span>بـ<p>{$wp__object__singular_name}</p><span><a href='{$current__adminURL}' target='_blank'>{$mini_current__title}</a></span>حيث يكون<span><a href='{$current__adminURL}' target='_blank'>{$mini_current__title}</a></span>هو النسخة<strong>{$title_current___post__language}</strong>لـ<p>{$wp__object__singular_name}</p><span><a href='{$connecter__adminURL}' target='_blank'>{$mini_connecter__title}</a></span>واللذي يحمل اللغة<strong>{$title_connecter___post__language}</strong>.</div></custom---p-alert>",
				'alert'=>true
			),

		);

	# CURRENT LANGUAGE == CONNECTER LANGUAGE .
		if( empty( $connecter___post__language ) ){
			$relation__success = false;
			$json['error'] = $error__log['languages_empty'];

		}

		if( $connecter___post__language == $current___post__language  && $relation__success != false )	{
			$relation__success = false;
			$json['error'] = $error__log['languages_equal'];
		}

	# GET CURRENT OBJECT DATA .	
		if( !empty( $current___relationDB__id ) && $relation__success != false ){

			
			$currentAppended__RelationsDB = $Relations__languages__locate->get(
				array(
					'RelationDB__id'=>$current___relationDB__id,
					'ObjectLanguage'=>$connecter___post__language,
					'ObjectAction'=>$Ajax__data['args']['ObjectAction']
				)
			);

			if( !empty( $currentAppended__RelationsDB ) && isset( $currentAppended__RelationsDB[0] ) ){ 
				$relation__success = false;

				if( $currentAppended__RelationsDB[0]->ObjectID == $connecter__objID ){
					$json['error'] = $error__log['found_last_language_AND_equal'];
				}else{
					# APPEND IMPORTANT REPORT IN '$error__log' .
						$error__log['current__found_last_language']['args'] = $Ajax__data['args'];
						$error__log['current__found_last_language']['args']['remover__data'] = array('relation__id'=>array($currentAppended__RelationsDB[0]->id),'remove_relation_post_meta'=>array($currentAppended__RelationsDB[0]->ObjectID));
						$error__log['current__found_last_language']['args'] = base64_encode( json_encode( $error__log['current__found_last_language']['args'] ) );
						$error__log['current__found_last_language']['connecter__id'] = $connecter__objID;


					# REPLACE POST TITLE { %REMOVED_RELATION_POST_TITLE% } AND POST ID { %REMOVED_RELATION_POST_ID% } IN ERROR MESSAGE .
						if( $currentAppended__RelationsDB[0]->ObjectAction == 'taxonomy' ){
							$found_curr__object = get_term_by('id',$currentAppended__RelationsDB[0]->ObjectID,$currentAppended__RelationsDB[0]->ObjectType);
							$found__curr__objID = $found_curr__object->term_id;
							$found__curr__title = $found_curr__object->name;

							$found__curr__adminURL = admin_url('term.php?taxonomy='.$found_curr__object->taxonomy.'&tag_ID='.$found_curr__object->term_id);
						}else{
							$found_curr__object = get_post($currentAppended__RelationsDB[0]->ObjectID);
							$found__curr__objID = $found_curr__object->ID;
							$found__curr__title = $found_curr__object->post_title;

							$found__curr__adminURL = admin_url('post.php?post='.$found_curr__object->ID.'&action=edit');
						}

						$mini_found__curr__title = wp_trim_words($found__curr__title,5,'..');

						$error__log['current__found_last_language']['message'] = str_replace('%REMOVED_RELATION_POST_TITLE%',$mini_found__curr__title , $error__log['current__found_last_language']['message']);
						$error__log['current__found_last_language']['message'] = str_replace('%REMOVE_RELATION_ADMIN_URL%',$found__curr__adminURL , $error__log['current__found_last_language']['message']);

					# SEND IN $JSON DATA .	
						$json['error'] = $error__log['current__found_last_language'];
				}
			}
		}

	# GET CONNECTER OBJECT DATA	.
		if( !empty( $connecter___relationDB__id ) && $relation__success != false ){

			$connecterAppended__RelationsDB = $Relations__languages__locate->get(
				array(
					'RelationDB__id'=>$connecter___relationDB__id,
					'ObjectLanguage'=>$current___post__language,
					'ObjectAction'=>$Ajax__data['args']['ObjectAction']
				)
			);

			if( !empty( $connecterAppended__RelationsDB ) && isset( $connecterAppended__RelationsDB[0] ) ){ 

				$relation__success = false;

				$connecte__rremove__RelationsDB = $Relations__languages__locate->get(
					array(
						'RelationDB__id'=>$connecter___relationDB__id,
						'ObjectID'=>$connecter__objID,
						'ObjectAction'=>$Ajax__data['args']['ObjectAction']
					)
				);

				if(  !empty( $connecte__rremove__RelationsDB ) && isset( $connecte__rremove__RelationsDB[0] ) ){

					# APPEND IMPORTANT REPORT IN '$error__log' .
						$error__log['connecter__found_last_language']['args'] = $Ajax__data['args'];
						$error__log['connecter__found_last_language']['args']['remover__data'] = array('relation__id'=>array($connecte__rremove__RelationsDB[0]->id),'remove_relation_post_meta'=>array($connecte__rremove__RelationsDB[0]->ObjectID) );
						$error__log['connecter__found_last_language']['connecter__id'] = $connecter__objID;

					# REPLACE POST TITLE { %REMOVED_RELATION_POST_TITLE% } AND POST ID { %REMOVED_RELATION_POST_ID% } IN ERROR MESSAGE .
						if( $connecterAppended__RelationsDB[0]->ObjectAction == 'taxonomy' ){
							$found_connec__object = get_term_by('id',$connecterAppended__RelationsDB[0]->ObjectID,$connecterAppended__RelationsDB[0]->ObjectType);
							$found__curr__objID = $found_connec__object->term_id;
							$found__curr__title = $found_connec__object->name;

							$found__connec__adminURL = admin_url('term.php?taxonomy='.$found_connec__object->taxonomy.'&tag_ID='.$found_connec__object->term_id);

						}else{
							$found_connec__object = get_post($connecterAppended__RelationsDB[0]->ObjectID);
							$found__connec__objID = $found_connec__object->ID;
							$found__connec__title = $found_connec__object->post_title;
							$found__connec__adminURL = admin_url('post.php?post='.$found_connec__object->ID.'&action=edit');
						}

						$mini_found__connec__title = wp_trim_words($found__connec__title,5,'..');

						$error__log['connecter__found_last_language']['message'] = str_replace('%REMOVED_RELATION_POST_TITLE%',$mini_found__connec__title , $error__log['connecter__found_last_language']['message']);
						$error__log['connecter__found_last_language']['message'] = str_replace('%REMOVE_RELATION_ADMIN_URL%',$found__connec__adminURL , $error__log['connecter__found_last_language']['message']);

					# SEND IN $JSON DATA .	

					
					$count__remover__relations = $Relations__languages__locate->count(
						array(
							'RelationDB__id'=>$connecter___relationDB__id,
							'ObjectAction'=>$Ajax__data['args']['ObjectAction']
						)
					);

					$count__remover__relations = $count__remover__relations - 1;
					if( $count__remover__relations <= 1 ){
						$error__log['connecter__found_last_language']['args']['remover__data']['relation__id'][] = $connecterAppended__RelationsDB[0]->id;
						$error__log['connecter__found_last_language']['args']['remover__data']['remove_relation_post_meta'][] =  $connecterAppended__RelationsDB[0]->ObjectID;
					}
					$error__log['connecter__found_last_language']['args'] = base64_encode( json_encode( $error__log['connecter__found_last_language']['args'] ) );
					#
					$json['error'] = $error__log['connecter__found_last_language'];

				}
				
			}
		}


	# INSERT DATA ACTIONS .	
		if( $relation__success == true ){

			# UPDATE CURRENT OBJECT 'relationDB__id '
				if( empty( $current___relationDB__id ) ) {

					if( !empty( $connecter___relationDB__id ) )  $current___relationDB__id = $connecter___relationDB__id;

					if( empty( $connecter___relationDB__id ) )  $current___relationDB__id = $Bin2hex;

					if( $Ajax__data['args']['ObjectAction'] == 'taxonomy' ){
						update_term_meta($current__objID,'relationDB__id',$current___relationDB__id);
					}else{
						update_post_meta($current__objID,'relationDB__id',$current___relationDB__id);
					}
				}

			# UPDATE CONNECTER OBJECT 'relationDB__id '

				if( $Ajax__data['args']['ObjectAction'] == 'taxonomy' ){
					update_term_meta($connecter__objID,'relationDB__id',$current___relationDB__id);
				}else{
					update_post_meta($connecter__objID,'relationDB__id',$current___relationDB__id);
				}

			# INSERT ROW IN DB ARGUMENTS .

				# INSERT CURRENT ROW .
					$current__row = $Relations__languages__locate->update(
						array(
							'ObjectID'=>$current__objID,
							'ObjectAction'=>$Ajax__data['args']['ObjectAction'],
							'ObjectType'=>$Ajax__data['args']['ObjectType'],
							'ObjectLanguage'=>$current___post__language,
							'RelationDB__id'=>$current___relationDB__id
						)
					);

				# INSERT CONNECTER ROW .
					$connecter__row = $Relations__languages__locate->update(
						array(
							'ObjectID'=>$connecter__objID,
							'ObjectAction'=>$Ajax__data['args']['ObjectAction'],
							'ObjectType'=>$Ajax__data['args']['ObjectType'],
							'ObjectLanguage'=>$connecter___post__language,
							'RelationDB__id'=>$current___relationDB__id
						)
					);

			# PRINT FINAL ITEMS .
				$json['message'] = 'تم التحديث بنجاح ';

				$RelationsDB = $Relations__languages__locate->get(
					array(
						'RelationDB__id'=>$current___relationDB__id,
						'ObjectAction'=>$Ajax__data['args']['ObjectAction']
					)
				);
				#
				if( !empty( $RelationsDB ) && isset( $RelationsDB[0] ) ){
					$json['counter'] = count($RelationsDB);

					foreach ( $RelationsDB as $relation__db_item ) {

						if( $relation__db_item->ObjectAction == 'taxonomy' ){
							$relationObject = get_term_by('id',$relation__db_item->ObjectID,$relation__db_item->ObjectType );
						}else{
							$relationObject = get_post( $relation__db_item->ObjectID );
						}

						$YC__CFM->AdminPart('Translate-Select-Box', array( 'post'=>$relationObject,'object__type'=> $relation__db_item->ObjectAction ,'DBValue'=>$relation__db_item ) );

					}

				}
				$output = ob_get_clean();
				$json['output'] = $output;
		}

}

echo json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);