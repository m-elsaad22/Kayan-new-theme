<?php
# يمنح nonce طازجاً لنموذج التواصل لحظة الإرسال — لا يُخزَّن في كاش الصفحات
nocache_headers();
header("Content-Type: application/json");
echo json_encode( array( 'nonce' => wp_create_nonce( 'yc_contact_form' ) ) );
