<?php
class DobaOrderInfo
{
	/**
	 * @var int
	 */
	var $orders_id;
	
	/**
	 * @var string
	 */
	var $first_name;
	
	/**
	 * @var string
	 */
	var $last_name;

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
	 */
	var $items = array();
	
	function DobaOrderInfo() {}
	
	/**
	 * Getter setter for $orders_id
	 * @return int
	 * @param $arg int[optional]
	 */
	function orders_id($arg = null) 
	{
		if (!is_null($arg)) 
		{
			$this->orders_id = intval($arg);
		}
		
		return $this->orders_id;
	}
	
	/**
	 * Getter setter for $first_name
	 * @return string
	 * @param $arg string[optional]
	 */
	function first_name($arg = null) 
	{
		if (!is_null($arg)) 
		{
			$this->first_name = trim($arg);
		}
		
		return $this->first_name;
	}

	/**
	 * Getter setter for $last_name
	 * @return string
	 * @param $arg string[optional]
	 */
	function last_name($arg = null)
	{
		if (!is_null($arg))
		{
			$this->last_name = trim($arg);
		}
		
		return $this->last_name;
	}

	/**
	 * Getter setter for $address
	 * @return string
	 * @param $arg string[optional]
	 */
	function address($arg = null)
	{
		if (!is_null($arg))
		{
			$this->address = trim($arg);
		}
		
		return $this->address;
	}

	/**
	 * Getter setter for $city
	 * @return string
	 * @param $arg string[optional]
	 */
	function city($arg = null)
	{
		if (!is_null($arg))
		{
			$this->city = trim($arg);
		}
		
		return $this->city;
	}

	/**
	 * Getter setter for $state
	 * @return string
	 * @param $arg string[optional]
	 */
	function state($arg = null)
	{
		if (!is_null($arg))
		{
			$this->state = trim($arg);
		}
		
		return $this->state;
	}

	/**
	 * Getter setter for $postal
	 * @return string
	 * @param $arg string[optional]
	 */
	function postal($arg = null)
	{
		if (!is_null($arg))
		{
			$this->postal = trim($arg);
		}
		
		return $this->postal;
	}

	/**
	 * Getter setter for $country
	 * @return string
	 * @param $arg string[optional]
	 */
	function country($arg = null)
	{
		if (!is_null($arg))
		{
			$this->country = trim($arg);
		}
		
		return $this->country;
	}

	/**
	 * Getter setter for $items
	 * @return array
	 * @param $arg array[optional]
	 */
	function items($arg=null) {
		if (!is_null($arg) && is_array($arg)) {
			$this->items = $arg;
		}
		
		return $this->items;
	}	
}//end class DobaOrderInfo

?>