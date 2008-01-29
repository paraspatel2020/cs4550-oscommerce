<?php

class DobaOrders {
	var $orders = array();
	
	function DobaOrders () {}
	
	/**
	 * Add a DobaOrderInfo object to the orders array
	 * @return bool
	 * @param $objDobaOrderInfo DobaOrderInfo
	 */
	function addOrder($objDobaOrderInfo)
	{
		if (!$this->orderExists($objDobaOrderInfo->orders_id))
		{
			$this->orders[$objDobaOrderInfo->orders_id()] = $objDobaOrderInfo;
			return true;
		}
		return false;
	}
	
	/**
	 * Determine if a specified order exists
	 * @return bool
	 * @param $orders_id int
	 */
	function orderExists($orders_id)
	{
		return isset($this->orders[intval($orders_id)]);
	}
	
	/**
	 * Remove a specified order
	 * @return bool
	 * @param $orders_id int
	 */
	function removeOrder($orders_id)
	{
		if ($this->orderExists($objDobaOrderInfo->orders_id))
		{
			unset($this->orders[intval($orders_id)]);
			return true;
		}
		return false;
	}
	
	/**
	 * @todo make work with db
	 * Pulls orders by sent to Doba status
	 * @static method
	 * @return bool
	 * @param $status string
	 */
	function loadOrders($status)
	{
		return true;
	}
}
?>