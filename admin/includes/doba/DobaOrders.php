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
	 * @todo make work with db
	 * Pulls orders by sent to Doba status
	 * @static method
	 * @return DobaOrders
	 * @param $status string (all, new, submitted, unsubmitted)
	 */
	function loadOrders($status)
	{
		$do = new DobaOrders();
		$orders = array();
		$sql = 'select 
					' . TABLE_ORDERS . '.orders_id as ponumber, 
					' . TABLE_ORDERS . '.delivery_name as name,
					' . TABLE_ORDERS . '.delivery_street_address as address1,
					' . TABLE_ORDERS . '.delivery_city as city,
					' . TABLE_ORDERS . '.delivery_state as state,
					' . TABLE_ORDERS . '.delivery_postcode as postal,
					' . TABLE_ORDERS . '.delivery_country as country,
					' . TABLE_ORDERS_PRODUCTS . '.products_id as item_id,
					' . TABLE_ORDERS_PRODUCTS . '.products_quantity as quantity,
					' . TABLE_ORDERS_PRODUCTS . '.final_price as max_expected_total,
					DobaLog.doba_log_id,
					DobaLog.api_response
				from 
					' . TABLE_ORDERS . '
					join ' . TABLE_ORDERS_PRODUCTS . ' on ' . TABLE_ORDERS_PRODUCTS . '.orders_id=' . TABLE_ORDERS . '.orders_id 
					left join DobaLog on DobaLog.local_id=' . TABLE_ORDERS . '.orders_id and DobaLog.datatype="order"';
		if ($status == 'new') {
			$sql .= ' where DobaLog.doba_log_id is NULL';
		} else if ($status == 'submitted') {
			$sql .= ' where DobaLog.api_response is not NULL';
		} else if ($status == 'unsubmitted') {
			$sql .= ' where DobaLog.api_response is NULL';
		}
  		$orders_query = tep_db_query($sql);
  		while ($o = tep_db_fetch_array($orders_query)) {
 			$i = new DobaOrderInfo();
			$i->po_number($o['ponumber']);
			$name = $i->name_to_parts($o['name']);
			$i->first_name($name['FirstName']);
			$i->last_name($name['LastName']);
			$i->address1($o['address1']);
			$i->city($o['city']);
			$i->state($o['state']);
			$i->postal($o['postal']);
			$i->country($o['country']);
			$i->item_id($o['item_id']);
			$i->quantity($o['quantity']);
			$i->max_expected_total($o['max_expected_total']);
			$do->addOrder($i);
 		}
			
		return $do;
	}
	
	/**
	 * Get the number of orders that will be returned by status
	 * @static method
	 * @return int
	 * @param $status string
	 */
	function getOrderCount($status) {
		$sql = 'select 
					distinct concat(' . TABLE_ORDERS_PRODUCTS . '.orders_id, "-", ' . TABLE_ORDERS_PRODUCTS . '.products_id) as idx 
				from 
					' . TABLE_ORDERS . '
					join ' . TABLE_ORDERS_PRODUCTS . ' on ' . TABLE_ORDERS_PRODUCTS . '.orders_id=' . TABLE_ORDERS . '.orders_id 
					left join DobaLog on DobaLog.local_id=' . TABLE_ORDERS . '.orders_id and DobaLog.datatype="order"';
		if ($status == 'new') {
			$sql .= ' where DobaLog.doba_log_id is NULL';
		} else if ($status == 'submitted') {
			$sql .= ' where DobaLog.api_response is not NULL';
		} else if ($status == 'unsubmitted') {
			$sql .= ' where DobaLog.api_response is NULL';
		}
		
		echo "<!-- ".$sql." -->";
				
		$cnt_query = tep_db_query($sql);
  		$cnt = tep_db_num_rows($cnt_query);
			
		return $cnt;
	}
}
?>
