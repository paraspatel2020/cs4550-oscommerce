<?php
class DobaOrderStatus {
	/**
	 * @var string 
	 */
	var $status;
	/**
	 * @var float
	 */
	var $subtotal;
	/**
	 * @var float
	 */
	var $service_fees;
	/**
	 * @var float
	 */
	var $shipping_fees;
	/**
	 * @var float
	 */
	var $dropship_fees;
	/**
	 * @var float
	 */
	var $total;
	/**
	 * @var string 
	 */
	var $name;
	/**
	 * @var string 
	 */
	var $address;
	/**
	 * @var string 
	 */
	var $city;
	/**
	 * @var string 
	 */
	var $state;
	/**
	 * @var string 
	 */
	var $postal;
	/**
	 * @var string 
	 */
	var $country;
	/**
	 * @var array
	 * id, distributor_id, status
	 */
	var $items;
	/**
	 * @var array
	 * distributor_id, carrier, tracking
	 */
	var $shipments;
}
?>