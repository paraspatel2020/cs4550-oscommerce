<?php
ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaApi.php');

$api = new DobaApi();

// change the following variables to have the data you wish to submit
$action = DOBA_API_ACTION_CREATEORDER;
$data = array(
	'firstname' => '',
	'lastname' => '',
	'address1' => '',
	'city' => '',
	'state' => '',
	'postal'=> ''
);
// end fields to be changed

echo "<strong>Testing:</strong> ".$action."<br><strong>With data:</strong><pre>";
print_r($data);
echo "</pre><strong>Result:</strong>";
if ($api->compileRequestXml($action, $data) && $api->sendRequest()) {
	echo " Successfully submitted<br><strong>Response XML:</strong><pre>";
	echo htmlentities($api->getResponseXml());
} else if ($api->hasErrors()) {
	echo " Submission Failure, with errors<br><strong>Errors:</strong><pre>";
	print_r($api->getErrors());
} else {	
	echo " Submission Failure, no errors<br><strong>Object dump:</strong><pre>";
	var_dump($api);
}
echo "</pre>";
?>