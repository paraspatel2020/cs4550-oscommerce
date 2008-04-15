<?php

class DobaOrders {
	/**
	 * array of DobaOrderInfo objects
	 */
	var $orders = array();
	
	/**
	 * default constructor
	 */
	function DobaOrders () {}
	
	/**
	 * Add a DobaOrderInfo object to the orders array
	 * @return bool
	 * @param $objDobaOrderInfo DobaOrderInfo
	 */
	function addOrder($objDobaOrderInfo)
	{
		$key = $objDobaOrderInfo->po_number().'_'.$objDobaOrderInfo->item_id();
		if (!$this->orderExists($key))
		{
			$this->orders[$key] = $objDobaOrderInfo;
			return true;
		}
		return false;
	}
	
	/**
	 * Determine if a specified order exists
	 * @return bool
	 * @param $key string
	 */
	function orderExists($key)
	{
		return isset($this->orders[$key]);
	}
	
	/**
	 * Remove a specified order
	 * @return bool
	 * @param $key string
	 */
	function removeOrder($key)
	{
		if ($this->orderExists($key))
		{
			unset($this->orders[$key]);
			return true;
		}
		return false;
	}
	
	/**
	 * Pulls orders by sent to Doba status
	 * @return bool
	 * @param $status string
	 */
	function loadOrders($status)
	{
		require_once('includes/configure.php');
		require_once(DIR_WS_FUNCTIONS . 'database.php');

		$orders = array();
  		$orders_query = tep_db_query("select * from " . TABLE_ORDERS);
  		while ($order = tep_db_fetch_array($orders_query)) {
    		$orders[] = $order;
  		}
		
		return true;
	}
	
	function toArray() {
		$ret = array();
		
		foreach ($this->orders as $o) {
			$ret[] = $o->toArray();
		}
		
		return $ret;
	}
}
?>
