<?php
if( !isset( $value ) ) $value = '';

if( isset( $InsertElements ) ){
	unset($vars['InsertElements']);
	$InputName = 'Insert_'.$id;
}else if( isset( $parent_id ) ){
	$InputName = $parent_id.'['.$id.']';
}else{
	$InputName = $id;
}

$disc__info = '';
if( isset( $desc ) && isset( $i_desc ) ) $disc__info = "<descor data-tooltip='{$desc}' class='--for-tooltip-descor'><i class='fa-solid fa-circle-info'></i></descor>";
if( isset( $desc ) && !isset( $i_desc ) ) $disc__info = "<descor>{$desc}</descor>";

if( !isset( $Icon ) ) $Icon = '';

echo '<div class="-fix-inputs-area'.( ( isset( $parent_div_class ) ) ? " {$parent_div_class}" : "" ).( ( $Icon != '' ) ? ' --is-Icon--title-' : '' ).'" data-field-id="'.$id.'">';
	echo '<div class="-fix-forms-field-title">'.$Icon.'<h3>'.$title.'</h3>'.$disc__info.'</div>';
	echo '<textarea style="height:100px" placeholder="'.((isset( $desc ) ) ? strip_tags( $desc ) : strip_tags( $title ) ).'" name="'.$InputName.'" id="'.$id.'">'.$value.'</textarea>';
echo '</div>';