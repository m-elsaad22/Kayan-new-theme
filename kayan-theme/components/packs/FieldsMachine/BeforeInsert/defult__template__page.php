<?php

if( !empty( $_POST ) ){
	if( isset( $_POST['submitForm'] ) && isset( $_POST['post_id'] ) ){

		header("Content-Type: application/json");

		$json = array();
		$PostUpdated = array(
			'ID'=>$_POST['post_id'],		
			'post_status'=> $set__post_status,
		);

		if( isset( $_POST['post_title'] ) ){
			$PostUpdated['post_title'] = $_POST['post_title'];
		}
		wp_update_post($PostUpdated);


		foreach ( $setup__fields as $wid__id => $wid__data ) {

			foreach ( is_array( $wid__data['fields'] ) ? $wid__data['fields'] : array() as $field__key => $field__data) {

				if( isset( $_POST[ $field__data['id'] ] ) ) {

					update_post_meta($_POST['post_id'],  $field__data['id'] , $_POST[ $field__data['id'] ]);

					# FIELD TYPE FILE __ ID .
						if( isset( $_POST[ $field__data['id'].'_id' ] ) ){
							update_post_meta($_POST['post_id'], $field__data['id'].'_id' , $_POST[ $field__data['id'].'_id' ] );
						}else{
							delete_post_meta($_POST['post_id'], $field__data['id'].'_id' );
						}

				}else{
					delete_post_meta($_POST['post_id'],  $field__data['id'] );
				}
			}
		}

		foreach ( $setup__taxonomy as $taxonomy_name => $taxonomy__data ) {
			if( isset( $_POST[ $taxonomy_name ] ) ) {
				wp_set_post_terms( $_POST['post_id'],$_POST[ $taxonomy_name ], $taxonomy_name );
			}else{
				wp_set_object_terms( $_POST['post_id'], null, 'category' );
			}			
		}

		do_action("{$object__name}__after__insert_save_fields",$_POST['post_id']);

		$json = array(
			'type'=>'sucsses',
			'post_url'=>admin_url( 'post.php?post='.$_POST['post_id'].'&action=edit'),
			'alert'=>'جاري التحويل الى الخطوة التالية ',
		);
	}else{
		$json = array(
			'type'=>'error',
			'alert'=>'لقد حدث خطا .. يرجي اعادة تحميل الصفحة والمحاولة مرة اخري',
		);
	}
	$output = ob_get_clean();
	echo json_encode($json);
	die();
}else{
	$post_id = wp_insert_post(
		array(
			'post_type'=> $object__name,
			'post_status'=>'auto-draft',
			'post_content'=>'',
			'post_title'=>'مسودة تلقائية ',
		)
	);
	$post = get_post($post_id);
	$action = 'new';
}
#
$currentURL = (new ThemeStatic)->GetCurrentURL();
#
$PostTypeArguments = PostTypeArguments( array( 'getIn'=>$object__name ) )[ $object__name ];

if( !isset( $page__title ) ){
	$page__title = $PostTypeArguments->labels->add_new_item;
}
if( !isset( $page__sub_title ) ){
	$page__sub_title = ' قم بتحديد البيانات الإلزامية لإنشاء '.$PostTypeArguments->labels->singular_name.' وأضغط أستمرار ومتابعة ';
}

$uniqid = uniqid();
echo '<yourcolorapi--conatiner>';
	echo '<Inseder--Appender>';
		echo '<header class="YS_Header">';
			echo '<div class="PluginName">';
				echo '<div class="Head--Text">';
					echo '<h2>'.$page__title.'</h2><p>'.$page__sub_title.'</p>';
				echo '</div>';
				echo '<div class="Header Icon">'.LoardIcons( $page__icon,'250px','250px').'</div>';
			echo '</div>';
		echo '</header>';

		echo '<div class="PagesModelConainer -PageIs--CreateForms">';


			echo '<form action="'.$currentURL.'" method="POST" enctype="multipart/form-data" data-form-ajax="true" data-uniq="'.$uniqid.'" data-form-result="beforeinsert_products">';

				echo '<input type="hidden" name="submitForm" value="">';
				echo '<input type="hidden" name="post_id" value="'.$post->ID.'">';

				echo '<div class="-FormsBuilding-Main">';
					if( $show__post_title__field != false ){

						echo '<div class="-Page-FormsBox">';
							echo '<div class="-FormsPostTitle">';
								$setup__post_title = array(
									'type'=>'Text',
									'id' => 'post_title',
									'title' =>'ادخل عنواناََ مميزاََ للمقال ',
									'value'=>'',
									'require'=>true,
								);
								$this->Fields__Part($setup__post_title['type'],$setup__post_title);
							echo '</div>';
						echo '</div>';
					}
				echo '</div>';
				
				do_action("{$object__name}__before__insert_fields");
				

				echo '<div class="-f--page-intry">';
					if( isset( $setup__fields ) ){
						foreach ( $setup__fields as $wid__id => $wid__data ) {
							echo '<div class="-widgets-before-inser-post">';
								echo '<div class="Title-MoreForms">'.( ( isset( $wid__data['icon'] ) ) ? $wid__data['icon'] : '<i class="fa-solid fa-circle-info"></i>' ).'<h2>'.$wid__data['title'].'</h2></div>';

								echo '<div class="-widgets-before-insert-fields">';

									foreach ( is_array( $wid__data['fields'] ) ? $wid__data['fields'] : array()  as $field__data ) {
										$this->Fields__Part( $field__data['type'], $field__data );
									}

								echo '</div>';

							echo '</div>';
						}
					}


					if( isset( $setup__taxonomy ) ){
						foreach ( $setup__taxonomy as $taxonomy_name => $taxonomy__data ) {
							echo '<div class="-widgets-before-inser-post">';
								echo '<div class="Title-MoreForms"><i class="fa-solid fa-circle-info"></i><h2>'.$taxonomy__data['title'].'</h2></div>';

								echo '<div class="-widgets-before-insert-fields">';
									$select_category = array(
										'title' => $taxonomy__data['title'],
										'type'=>'Compo-Select-Field',
										'id' => $taxonomy_name,
										'object__type'=>'taxonomy',
										'object__name'=>$taxonomy_name,
										'show__perview__items'=>false,
										'per'=>10,
										'multiple'=>true,
										'require'=> $taxonomy__data['require'],
										'disc'=> $taxonomy__data['desctiption']
									);
									$this->Fields__Part($select_category['type'],$select_category);
								echo '</div>';

							echo '</div>';
						}
					}

				echo '</div>';


				do_action("{$object__name}__after__insert_fields");

				echo '<div class="-row-create-button"><button type="submit" class="activable hoverable"><span>متابعة واستمرار </span><i class="fa-solid fa-arrow-left"></i></button></div>';

			echo '</form>';

		echo '</div>';
	echo '</Inseder--Appender>';
echo '</yourcolorapi--conatiner>';