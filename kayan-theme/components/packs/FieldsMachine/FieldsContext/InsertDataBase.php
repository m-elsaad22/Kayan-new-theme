<?php  $UniqId = uniqid();
if( !isset( $UniqId ) ) $UniqId = uniqid();
if( isset( $InsertElements ) ){
	$InputName = 'Insert_'.$id;
}else if( isset( $parent_key ) && $parent_key != false && isset( $parent_id ) ){
	$InputName = $parent_id.'['.$parent_key.']['.$id.']';

}else if( isset( $parent_id ) ){
	$InputName = $parent_id.'['.$id.']';
}else{
	$InputName = $id;
}
if( !isset( $insert_button_text ) ) $insert_button_text = 'اضافة عنصر جديد ';
if( !isset( $insert_button_icon ) ) $insert_button_icon = '<i class="fa-solid fa-plus"></i>';
if( !isset( $SaveDB__field ) ) $SaveDB__field = 'id';
if( !isset( $fields__parent ) ) $fields__parent = 'db__metakey__values_update_';

$Current_Value_singular__field = false;
if( isset( $_GET['post'] ) ){

	$Current_Value_singular__field = $_GET['post'];
	$vars['Current_Value_singular__field'] = $Current_Value_singular__field;
}else if( isset( $_GET['tag_ID'] ) ){

	$Current_Value_singular__field = $_GET['tag_ID'];
	$vars['Current_Value_singular__field'] = $Current_Value_singular__field;
}

if( !isset( $value ) ) $value = array();

# SELECT ITEMS BOX .
	if( !isset( $box__part ) ) $box__part = 'Insert-DataBase-Defult';

if( class_exists( $TableName ) ){
	$DB_Table = new $TableName;
	echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '<AjaxHTML_Cut>' : '';

		echo '<div class="-fix-inputs-area" '.( ( isset($parent_id ) ) ? 'data-field-argums="'.base64_encode(json_encode($vars)).'" ' : 'data-vars="'.base64_encode(json_encode($vars)).'"' ).'>';

			echo '<div class="--DataBaseInsert--Js--box-area">';
				echo '<div class="Title-MoreForms"><i class="fa-solid fa-store"></i><h2>'.$title.'</h2></div>';

				echo '<div class="--InsertDB--fields--area" data-uniq-db-each="'.$UniqId.'">';
					echo '<div class="Title-MoreForms"><i class="fa-solid fa-plus"></i><h2>إضافة عنصر جديد </h2></div>';
					foreach ( $fields as $insert__value ) {
						$insert__value['parent_id'] = $InputName;
						$insert__value['InsertElements'] = true;
						$this->Fields__Part($insert__value['type'],$insert__value);
					}
				echo '</div>';

				echo '<div class="--BTN--insert--DB-Area">';
					echo '<div class="--Submit--insert--DB-item" data-insert-db-row="'.$UniqId.'">'.$insert_button_icon.'<span>'.$insert_button_text.'</span></div>';
				echo '</div>';

			echo '</div>';

			echo '<div class="-DBFields-Insert-Area">';

				echo '<div class="-DBFieldsTitle-MoreForms" data-db-insert-count="'.$UniqId.'">';
					echo ( ( isset( $Ajax__mode ) && $Ajax__mode == true ) ) ? '<count--Ajax--Items>' : '';
						if( isset( $value ) && !empty( $value ) ){
							echo '<div class="Title-MoreForms">';
								echo '<i class="fa-solid fa-sitemap"></i>';
								echo '<h2>لقد قُمت بتحديد <count-items>'.count( $value ).'</count-items> عنصر حتي الأن </em></h2>';
							echo '</div>';
						}
					echo ( ( isset( $Ajax__mode ) && $Ajax__mode == true ) ) ? '</count--Ajax--Items>' : '';
				echo '</div>';

				echo '<div class="Append--DataBaseInsert--Boxses--Area" data-db-insert-append="'.$UniqId.'">';

					echo ( ( isset( $Ajax__mode ) && $Ajax__mode == true ) ) ? '<exp--Ajax--Items>' : '';

						if( isset( $value ) && !empty( $value ) ){

							foreach ( $value as $it__value ) {
								$get_items = $DB_Table->get( array( $SaveDB__field=>$it__value ) );
								if( $get_items != false && isset( $get_items[0] ) && isset( $get_items[0]->$SaveDB__field ) ){

									if( !isset( $Current__id ) || ( isset( $Current__id ) && $Current__id == $get_items[0]->$SaveDB__field ) ){
										$this->AdminPart(
											$box__part,
											array(
												'fields__parent'=>$fields__parent,
												'box__part'=>$box__part,
												'fields'=>$fields,
												'InputName'=>$InputName,
												'SaveDB__field'=>$SaveDB__field,
												'object'=>$get_items[0],
												'TableName'=>$TableName,
												'UniqId'=>$UniqId,
											) 
										);
									}

								}
							}
						}
					echo ( ( isset( $Ajax__mode ) && $Ajax__mode == true ) ) ? '</exp--Ajax--Items>' : '';				

				echo '</div>';
			echo '</div>';

		echo '</div>';
	echo ( ( isset( $AjaxHTML_Cut ) ) ) ? '</AjaxHTML_Cut>' : '';
}