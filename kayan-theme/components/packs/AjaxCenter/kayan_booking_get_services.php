<?php
header( "Content-Type: application/json" );
$_POST = YC_stripslashes_deep( $_POST );
$json  = array( 'services' => array() );

$category_id = isset( $_POST['category_id'] ) ? absint( $_POST['category_id'] ) : 0;
$service_id  = isset( $_POST['service_id'] ) ? absint( $_POST['service_id'] ) : 0;

if ( $service_id ) {
	$post = get_post( $service_id );
	if ( $post && 'services' === $post->post_type && 'publish' === $post->post_status ) {
		$json['services'][] = Kayan_Booking::format_service( $post );
	}
} else if ( $category_id ) {
	$json['services'] = Kayan_Booking::get_linked_services( $category_id );
}

echo json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
