<?php 
global $post;
$model = get_post_meta($post->ID, 'template', true);
if( !empty( $model ) && isset( $model['SelectedModel'] ) && !empty( $model['SelectedModel'] ) ){
    $model = $this->packsPath.'@models/'.$model['SelectedModel'].'.php';
    if( file_exists($model) ) {
    	require($model);
    }else {
    	$model = $this->packsPath.'@models/standard-page.php';
    }
}else {
	$model = $this->packsPath.'@models/standard-page.php';
	require($model);
}