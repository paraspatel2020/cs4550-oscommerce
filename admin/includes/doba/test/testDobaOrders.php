<?php
ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaOrderInfo.php');
include_once('doba/DobaOrders.php');

$tempData = array(
	'orders_id' => 12345,
	'first_name' => 'Candice',
	'last_name' => 'Johnson',
	'address' => '4958 W 495 S',
	'city' => 'Orem',
	'state' => 'UT',
	'postal' => '84097',
	'country' => 'USA',
	'items' => array(),
);

$dpo = new DobaOrderInfo();
echo "<ul>";
echo "<li>\"".$dpo->orders_id($tempData['orders_id'])."\" should be \"".$tempData['orders_id']."\"</li>";
echo "<li>\"".$dpo->first_name($tempData['first_name'])."\" should be \"".$tempData['first_name']."\"</li>";
echo "<li>\"".$dpo->last_name($tempData['last_name'])."\" should be \"".$tempData['last_name']."\"</li>";
echo "<li>\"".$dpo->address($tempData['address'])."\" should be \"".$tempData['address']."\"</li>";
echo "<li>\"".$dpo->city($tempData['city'])."\" should be \"".$tempData['city']."\"</li>";
echo "<li>\"".$dpo->state($tempData['state'])."\" should be \"".$tempData['state']."\"</li>";
echo "<li>\"".$dpo->postal($tempData['postal'])."\" should be \"".$tempData['postal']."\"</li>";
echo "<li>\"".$dpo->country($tempData['country'])."\" should be \"".$tempData['country']."\"</li>";
echo "</ul>";
echo "<p>Done</p>";

$dpoAry = new DobaOrders();
echo "<p>".($dpoAry->orderExists($tempData['orders_id']) ? 'Order added' : 'Order not added')."</p>";
$dpoAry->addOrder($dpo);
echo "<p>".($dpoAry->orderExists($tempData['orders_id']) ? 'Order added' : 'Order not added')."</p>";

?>