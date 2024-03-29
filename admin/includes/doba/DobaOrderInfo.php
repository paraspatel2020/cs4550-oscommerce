<?php
class DobaOrderInfo
{
	/**
	 * @var int : orders id in this oscommerce implementation
	 */
	var $po_number;
	
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
	var $address1;
	
	/**
	 * @var string
	 */
	var $address2;

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
	 * @var int
	 */
	var $item_id;
	
	/**
	 * @var int
	 */
	var $quantity;
	
	/**
	 * @var float
	 */
	var $max_expected_total;
	
	function DobaOrderInfo() {}
	
	/**
	 * Getter setter for $po_number
	 * @return int
	 * @param $arg int[optional]
	 */
	function po_number($arg = null) 
	{
		if (!is_null($arg)) 
		{
			$this->po_number = intval($arg);
		}
		
		return intval($this->po_number);
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
		
		return trim($this->first_name);
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
		
		return trim($this->last_name);
	}

	/**
	 * Getter setter for $address1
	 * @return string
	 * @param $arg string[optional]
	 */
	function address1($arg = null)
	{
		if (!is_null($arg))
		{
			$this->address1 = trim($arg);
		}
		
		return trim($this->address1);
	}
	
	/**
	 * Getter setter for $address2
	 * @return string
	 * @param $arg string[optional]
	 */
	function address2($arg = null)
	{
		if (!is_null($arg))
		{
			$this->address2 = trim($arg);
		}
		
		return trim($this->address2);
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
		
		return trim($this->city);
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
			$this->state = $this->convertStateToAbbr(trim($arg));
		}
		
		return trim($this->state);
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
		
		return trim($this->postal);
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
			$this->country = $this->convertCountryToAbbr(trim($arg));
		}
		
		return trim($this->country);
	}

	/**
	 * Getter setter for $item_id
	 * @return int
	 * @param $arg int[optional]
	 */
	function item_id($arg=null) {
		if (!is_null($arg)) {
			$this->item_id = intval($arg);
		}
		
		return intval($this->item_id);
	}
	
	/**
	 * Getter setter for $quantity
	 * @return int
	 * @param $arg int[optional]
	 */
	function quantity($arg=null) {
		if (!is_null($arg)) {
			$this->quantity = intval($arg);
		}
		
		return intval($this->quantity);
	}
	
	/**
	 * Getter setter for $max_expected_total
	 * @return float
	 * @param $arg float[optional]
	 */
	function max_expected_total($arg=null) {
		if (!is_null($arg)) {
			$this->max_expected_total = floatval($arg);
		}
		
		return floatval($this->max_expected_total);
	}			
	
	/**
	 * Take a full name and split it into FistName and LastName.
	 * The LastName will always be the last word in the name.
	 * The FirstName will contain all other names.  i.e. FirstName, MiddleInitial, etc.
	 * 
	 * @param string $name
	 * @return array(FirstName, LastName)
	 */
	function name_to_parts($name) {
		$name = trim($name);
		$ret = array(
			'FirstName' => '',
			'LastName' => ''
		);
		$parts = explode(' ', $name);
		if (count($parts) > 0) {
			$ret['LastName'] = $parts[count($parts)-1];
			for ($i=0; $i<count($parts)-1; ++$i) {
				$ret['FirstName'] .= ' '.$parts[$i];
			}
			$ret['FirstName'] = trim($ret['FirstName']);
		}
		
		return $ret;
	}
	
	function convertStateToAbbr($s) {
		if (strlen($s) <= 2) {
			return $s;
		}
		
		include('doba/state_country.inc');
		return isset($states[strtolower($s)]) ? $states[strtolower($s)] : $s;
	}
	
	function convertCountryToAbbr($c) {
		if (strlen($c) <= 2) {
			return $c;
		}
		
		include('doba/state_country.inc');
		return isset($countries[strtolower($c)]) ? $countries[strtolower($c)] : $c;
	}

	/**
	 * 
	 * @return array(po_number, first_name, last_name, address1, address2, city, state, postal, country, item_id, quantitiy, max_expected_total)
	 */
	function toArray() {
		return array(
			'po_number' => $this->po_number(),
			'first_name' => $this->first_name(),
			'last_name' => $this->last_name(),
			'address1' => $this->address1(),
			'address2' => $this->address2(),
			'city' => $this->city(),
			'state' => $this->state(),
			'postal' => $this->postal(),
			'country' => $this->country(),
			'item_id' => $this->item_id(),
			'quantity' => $this->quantity(),
			'max_expected_total' => $this->max_expected_total()
		);		
	}
}//end class DobaOrderInfo

?>