<?php
# يمنح nonce طازجاً لنموذج الحجز — لا يُخزَّن في كاش الصفحات
nocache_headers();
header( "Content-Type: application/json" );
echo json_encode( array( 'nonce' => wp_create_nonce( 'kayan_booking_form' ) ) );
