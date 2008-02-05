<?php
ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaOrderInfo.php');
include_once('doba/DobaOrders.php');
include_once('doba/DobaOrderFile.php');

$do = new DobaOrders();
$dboi = new DobaOrderInfo();
$dof = new DobaOrderFile();

$tempData = array(
	'orders_id' => 12345,
	'first_name' => 'Candice',
	'last_name' => 'Johnson',
	'address' => '4958 W 495 S',
	'address2' => 'none',
	'city' => 'Orem',
	'state' => 'UT',
	'postal' => '84097',
	'country' => 'USA',
	'items_id' => '25',
	'quantity' => '30',
	'max' => '1025',
);

$dboi->po_number($tempData['orders_id']);
$dboi->first_name($tempData['first_name']);
$dboi->last_name($tempData['last_name']);
$dboi->address1($tempData['address']);
$dboi->address2($tempData['address2']);
$dboi->city($tempData['city']);
$dboi->state($tempData['state']);
$dboi->postal($tempData['postal']);
$dboi->country($tempData['country']);
$dboi->item_id($tempData['items_id']);
$dboi->quantity($tempData['quantity']);
$dboi->max_expected_total($tempData['max']);

$do->addOrder($dboi);

echo "<pre>";
$dof->processData($do);
echo "</pre>";
?>