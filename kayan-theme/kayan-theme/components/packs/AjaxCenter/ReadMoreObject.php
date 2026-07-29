<?php 
header("Content-Type: application/json");
$json = array();

if( isset( $_POST['Object__type'] ) && $_POST['Object__type'] == 'post_type' ){

	$post = get_post( $_POST['Object__id'] );
	$PostContent = $post->post_content;

}else if( isset( $_POST['Object__type'] ) && $_POST['Object__type'] == 'taxonomeis' ){
	
	$post = get_term_by('id',$_POST['Object__id'],$_POST['Object__name']);
	$PostContent = $post->description;
}
$json['content'] = $PostContent;
echo json_encode($json);
