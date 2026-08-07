<?php header("Content-Type: application/json");
ob_start();
$json = array();
$json['output'] = '';
$Ajax__data = YC_stripslashes_deep($Ajax__data);
$Theme__LanguageMachine = new Theme__LanguageMachine;
$YC__CFM = new YC__CFM;

if( $Ajax__data['ObjectAction'] == 'taxonomy' ){
	$CurrentObject = get_term_by( 'id',$Ajax__data['ObjectID'], $Ajax__data['ObjectType']);
	$CurrentID = $CurrentObject->term_id;

	update_term_meta($CurrentID,'post__language',$Ajax__data['CurrentValue']);

}else{
	$CurrentObject = get_post($Ajax__data['ObjectID']);
	$CurrentID = $CurrentObject->ID;

	update_post_meta($CurrentID,'post__language',$Ajax__data['CurrentValue']);
}


$example_callBack = array(
	'title'=>'إعدادات اللغات ',
	'fields'=>array(),
	'id'=>'SelectLanguage',
	'metaBox__path'=>$Theme__LanguageMachine->LanguagePacks_Path.'/CallBack/SelectLanguage.php',
	'MetaBox__Action'=>'callback_metabox',
    'context' => 'normal',
    'priority' => 'high',
    'Activable__Object'=>$CurrentObject,
    'ObjectType'=>array($Ajax__data['ObjectType']),
    'Values'=>array(
    	'post__language'=>$Ajax__data['CurrentValue']
    )					
);

$YC__CFM->Require($example_callBack['metaBox__path'],$example_callBack);
#
$output = ob_get_clean();
$json['output'] = $output;
echo json_encode( $json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);