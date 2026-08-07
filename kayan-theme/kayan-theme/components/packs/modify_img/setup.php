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
    $id = absint( $id );
    if ( ! $id ) return false;
    if( !isset( $return__output ) ) $return__output = true;
    if( !isset( $LazyLoad ) ) $LazyLoad = true;
    if( !isset( $size ) ) $size = 'full';

    $SrcAttr = ( ( $LazyLoad != false && $return__output != false ) ) ? 'data-loader-src' : 'src';

    $imageAttributes = array(
        'class' => 'YourColor--Theme--image',
    );

    $attch_data  = wp_get_attachment_metadata($id);
    if( empty( $attch_data ) ) {
        $fallback_url = wp_get_attachment_url( $id );
        if ( ! $fallback_url ) return false;
        if ( $return__output == false ) {
            return array( 'src' => $fallback_url, 'alt' => isset( $alt ) ? $alt : '' );
        }
        return '<img class="YourColor--Theme--image kayan-logo-img" src="'.esc_url( $fallback_url ).'" alt="'.esc_attr( isset( $alt ) ? $alt : '' ).'" loading="eager" decoding="async" />';
    }

    $current__size = ( ( isset( $attch_data['sizes'][$size] ) ) ) ? $attch_data['sizes'][$size] : $attch_data;

    if ( isset( $current__size['width'] ) ) $imageAttributes['width'] = $current__size['width'];
    if ( isset( $current__size['height'] ) ) $imageAttributes['height'] = $current__size['height'];

    $attachment_src = wp_get_attachment_image_url($id, $size);
    if ( ! $attachment_src ) {
        $attachment_src = wp_get_attachment_image_url( $id, 'full' );
    }
    if ( ! $attachment_src ) {
        $attachment_src = wp_get_attachment_url( $id );
    }
    if ( ! $attachment_src ) return false;

    $imageAttributes[$SrcAttr] = $attachment_src;
    # دائماً ضع src حقيقي حتى مع LazyLoad — يمنع أيقونة الصورة المكسورة قبل تشغيل JS
    if ( 'src' !== $SrcAttr ) {
        $imageAttributes['src'] = $attachment_src;
    }

    $default__alt = ( ( !isset( $alt ) && isset( $attch_data['image_meta']['alt'] ) ) ) ? $attch_data['image_meta']['alt'] : '';
    $imageAttributes['alt'] = ( ( isset( $alt ) && !empty( $alt ) ) ) ? $alt : $default__alt;
    $imageAttributes['loading'] = ( $LazyLoad ) ? 'lazy' : 'eager';
    $imageAttributes['decoding'] = 'async';
    if ( ! $LazyLoad ) {
        $imageAttributes['data-no-lazy'] = '1';
        $imageAttributes['data-skip-lazy'] = '1';
        $imageAttributes['class'] = trim( ( isset( $imageAttributes['class'] ) ? $imageAttributes['class'] : '' ) . ' skip-lazy' );
    }

    if( $return__output == false ) return $imageAttributes;

    $imageTag = '<img ';
    foreach ($imageAttributes as $attribute => $value) {
        if ( '' === $value || null === $value ) continue;
        $imageTag .= $attribute . '="' . esc_attr( $value ) . '" ';
    }
    $imageTag .= '/>';

    return $imageTag;

}
