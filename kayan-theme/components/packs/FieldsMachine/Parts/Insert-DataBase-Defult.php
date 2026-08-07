<?php 
echo '<div class="--single--DB-item --Js-nocss-important-db-item-class" data-db-insert-itemid="'.$object->$SaveDB__field.'" data-db-arguments="'.base64_encode( json_encode( $vars ) ).'" data-db-uniq-itemid="'.$UniqId.'">';
	echo '<input type="hidden" name="'.$InputName.'['.$object->$SaveDB__field.']" value="'.$object->$SaveDB__field.'">';
	echo '<div class="DB-item-Fields--Edits">';
		foreach ( $fields as $field__value ) {
			$field__value['parent_id'] = $fields__parent.'['.$object->$SaveDB__field.']';
			$field__value['value'] = ( ( isset( $object->{$field__value['id']} ) ) ) ? $object->{$field__value['id']} : '';

			$this->Fields__Part($field__value['type'],$field__value);
		}
	echo '</div>';

	echo '<div class="--RemoveThisDBItem">';
		echo '<div class="-YC-Current-DB-action-button activable disable Remove--Current-DB-item-id" data-remove-db-insert-item="'.$object->$SaveDB__field.'" data-uniq="'.$UniqId.'"><span>حذف</span><i class="fa-solid fa-xmark"></i></div>';
		echo '<div class="-YC-Current-DB-action-button activable disable Save--Current-DB-item-id" data-edit-db-insert-item="'.$object->$SaveDB__field.'" data-uniq="'.$UniqId.'"><span>حفظ</span><i class="fa-solid fa-arrow-left"></i></div>';
	echo '</div>';
echo '</div>';
