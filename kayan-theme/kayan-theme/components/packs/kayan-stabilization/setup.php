<?php 
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/lockdown.php';
require_once __DIR__ . '/tracking.php';
require_once __DIR__ . '/cache.php';
require_once __DIR__ . '/homepage.php';

add_action( 'init', 'kayan_stabilization_register_deploy_check', 2 );
