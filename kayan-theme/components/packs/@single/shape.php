<?php 
wp_reset_query();
global $post;

$this->Blade('single', false, $post->post_type);