<?php
ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaApi.php');

$api = new DobaApi();

// change the following variables to have the data you wish to submit
$action = DOBA_API_ACTION_CREATEORDER;
$data = array(
	'po_number' => '95738',
	'first_name' => 'Brittany',
	'last_name' => 'Kearns',
	'address1' => '6894 W 49th Street',
	'address2' => '',
	'city' => 'Eugene',
	'state' => 'OR',
	'postal'=> '97563',
	'country' => 'US',
	'items' => array(
		array(
			'item_id' => '623746',
			'quantity' => '1'
		)
	)
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