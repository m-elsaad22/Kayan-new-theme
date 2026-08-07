<?php 
header("Content-Type: text/css");
##
ob_start();
require('main.css');
$css = ob_get_length();
$css = str_replace(PHP_EOL, '', $css);
echo $css;