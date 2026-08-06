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
$vars['vars'] = base64_encode(json_encode($vars));

if( !isset( $hide__title ) ) $hide__title = false;

$disc__info = '';
if( isset( $desc ) && isset( $i_desc ) ) $disc__info = "<descor data-tooltip='{$desc}' class='--for-tooltip-descor'><i class='fa-solid fa-circle-info'></i></descor>";
if( isset( $desc ) && !isset( $i_desc ) ) $disc__info = "<descor>{$desc}</descor>";

if( !isset( $Icon ) ) $Icon = '';
if( !isset( $max ) ) $max = '999';
if( !isset( $min ) ) $min = '1';
echo '<div class="-fix-inputs-area'.( ( isset( $parent_div_class ) ) ? " {$parent_div_class}" : "" ).( ( $Icon != '' ) ? ' --is-Icon--title-' : '' ).'" data-field-id="'.$id.'">';
	echo ( ( $hide__title == false ) ) ? '<div class="-fix-forms-field-title">'.$Icon.'<h3>'.$title.'</h3>'.$disc__info.'</div>' : '';
	echo '<input type="number" value="'.$value.'" '.( ( isset( $disabled ) && $disabled == true ) ? 'disabled' : '' ).' '.(( isset( $pattern ) ) ? 'pattern="'.$pattern.'"' : '' ).' placeholder="'.( ( isset( $desc ) ) ? $desc : $title ).'"'.( ( !isset( $off_min_max ) ) ? ' max="'.$max.'" min="'.$min.'"' : '' ).' name="'.$InputName.'" id="'.$id.'">';
	

echo '</div>';