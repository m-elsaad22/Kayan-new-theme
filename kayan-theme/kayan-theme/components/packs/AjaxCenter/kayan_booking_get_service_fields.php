<?php
header( "Content-Type: application/json" );
$_POST = YC_stripslashes_deep( $_POST );
$json  = array( 'fields' => array(), 'services' => array() );

$ids_raw = isset( $_POST['service_ids'] ) ? $_POST['service_ids'] : '';
if ( is_string( $ids_raw ) ) {
	$ids = array_filter( array_map( 'absint', explode( ',', $ids_raw ) ) );
} else if ( is_array( $ids_raw ) ) {
	$ids = array_filter( array_map( 'absint', $ids_raw ) );
} else {
	$ids = array();
}

foreach ( $ids as $sid ) {
	$post = get_post( $sid );
	if ( ! $post || 'services' !== $post->post_type || 'publish' !== $post->post_status ) continue;

	$json['services'][ $sid ] = get_the_title( $post );
	$fields = Kayan_Booking::get_service_fields( $sid );
	if ( ! empty( $fields ) ) {
		$json['fields'][ $sid ] = $fields;
	}
}

echo json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
