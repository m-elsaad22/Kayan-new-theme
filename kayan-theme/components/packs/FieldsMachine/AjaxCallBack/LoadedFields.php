<?php
header("Content-Type: application/json");
ob_start();
$json = array();
$argums = json_decode(base64_decode($Ajax__data['Argums']), true);
$argums['AjaxHTML_Cut'] = true;

$this->Fields__Part($argums['type'],$argums);

$HTML = ob_get_clean();

$HTML = explode('<AjaxHTML_Cut>', $HTML)[1];
$HTML = explode('</AjaxHTML_Cut>', $HTML)[0];
$json['output'] = $HTML;
$json['type'] = $argums['type'];
echo json_encode($json);