<?php
ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaProductData.php');
include_once('doba/DobaProducts.php');

$tempData = array(
	'product_id' => 1234,
	'title' => 'the title of the product',
	'product_sku' => 'the sku of the product',
	'upc' => 'the upc of the product',
	'brand' => 'the brand name of the product',
	'description' => 'a description of the product',
	'ship_width' => 12,
	'ship_height' => 12,
	'ship_weight' => 12,
	'ship_cost' => 12,
	'image_url' => 'www.photodumby.foo',
	'thumb_url' => 'www.photodumby2.foo',
	'image_height' => 12,
	'image_width' => 2,
	'last_update' => '2008-01-02 11:54:02',
	'items' => array(),
);

$dpd = new DobaProductData();
echo "<ul>";
echo "<li>\"".$dpd->product_id($tempData['product_id'])."\" should be \"".$tempData['product_id']."\"</li>";
echo "<li>\"".$dpd->title($tempData['title'])."\" should be \"".$tempData['title']."\"</li>";
echo "<li>\"".$dpd->product_sku($tempData['product_sku'])."\" should be \"".$tempData['product_sku']."\"</li>";
echo "<li>\"".$dpd->upc($tempData['upc'])."\" should be \"".$tempData['upc']."\"</li>";
echo "<li>\"".$dpd->brand($tempData['brand'])."\" should be \"".$tempData['brand']."\"</li>";
echo "<li>\"".$dpd->description($tempData['description'])."\" should be \"".$tempData['description']."\"</li>";
echo "</ul>";
echo "<p>Done</p>";

$dpdAry = new DobaProducts();
echo "<p>".($dpdAry->productExists($tempData['product_id']) ? 'Product added' : 'Product not added')."</p>";
$dpdAry->addProduct($dpd);
echo "<p>".($dpdAry->productExists($tempData['product_id']) ? 'Product added' : 'Product not added')."</p>";
?>