<?php
header("Content-Type: application/json");
ob_start();

$json = array();
if( isset( $_POST['Arguments'] ) && isset( $_POST['Values'] ) ){

	$_POST['Arguments'] = json_decode( base64_decode( $_POST['Arguments'] ),true );
	$TableName = new $_POST['Arguments']['TableName'];

	if( isset( $_POST['Arguments']['singular__field'] ) && isset( $_POST['Arguments']['Current_Value_singular__field'] ) ){
		$_POST['Values'][ $_POST['Arguments']['singular__field'] ] = $_POST['Arguments']['Current_Value_singular__field'];
	}

	$_POST['Values']['insert__db_manual'] = true;

	$New_CourseID = $TableName->update($_POST['Values']);

	$_POST['Arguments']['value'][ $New_CourseID ] = $New_CourseID;

	$_POST['Arguments']['Ajax__mode'] = true;
	$_POST['Arguments']['Current__id'] = $New_CourseID;
	$this->Fields__Part($_POST['Arguments']['type'],$_POST['Arguments']);

	$html = ob_get_clean();
	$Itemshtml = explode('<exp--Ajax--Items>', $html)[1];
	$Itemshtml = explode('</exp--Ajax--Items>', $Itemshtml)[0];

	if( strpos($html, '<count--Ajax--Items>') !== FALSE ){
		$Counterhtml = explode('<count--Ajax--Items>', $html)[1];
		$Counterhtml = explode('</count--Ajax--Items>', $Counterhtml)[0];
		$json['CounterOutput'] = $Counterhtml;
	}else{
		$json['CounterOff'] = true;
	}

	$json['output'] = $Itemshtml;
}

echo json_encode($json);