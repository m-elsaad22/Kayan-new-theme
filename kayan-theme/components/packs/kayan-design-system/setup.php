<?php 
require_once __DIR__ . '/helpers.php';

add_action( 'AfterWPHead', 'kayan_render_global_gradient_styles', 15 );
add_action( 'AfterWPHead', 'kayan_render_global_shadow_styles', 16 );
