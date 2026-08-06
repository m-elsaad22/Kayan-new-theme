<?php
function YC_get_attachment($args) {

    /*
        'id'=>'ATTACHMENT ID',
        'alt'=>'ATTACHMENT CUSTOM ALT',
        'size'=>'ATTACHMENT SIZE NAME',
        'return__output'=>true || false
        'LazyLoad'=>true
    */

    extract( $args );

    if( !isset( $id ) ) return false;
    if( !isset( $return__output ) ) $return__output = true;
    if( !isset( $LazyLoad ) ) $LazyLoad = true;

    $SrcAttr = ( ( $LazyLoad != false && $return__output != false ) ) ? 'data-loader-src' : 'src';
    
    $imageAttributes = array(
        'class' => 'YourColor--Theme--image',
    );


    $attch_data  = wp_get_attachment_metadata($id);
    if( empty( $attch_data ) ) return false;

    $current__size = ( ( isset( $attch_data['sizes'][$size] ) ) ) ? $attch_data['sizes'][$size] : $attch_data;

    $imageAttributes['width'] = $current__size['width'];
    $imageAttributes['height'] = $current__size['height'];

    $attachment_src = wp_get_attachment_image_url($id,$size);
    $imageAttributes[$SrcAttr] = $attachment_src;

    $default__alt = ( ( !isset( $alt ) && isset( $attch_data['image_meta']['alt'] ) ) ) ? $attch_data['image_meta']['alt'] : '';
    $imageAttributes['alt'] = ( ( isset( $alt ) && !empty( $alt ) ) ) ? $alt : $default__alt;

    if( $return__output == false ) return $imageAttributes;

    $imageTag = '<img ';
    foreach ($imageAttributes as $attribute => $value) {
        $imageTag .= $attribute . '="' . $value . '" ';
    }
    $imageTag .= '/>';
    
    return $imageTag;

}