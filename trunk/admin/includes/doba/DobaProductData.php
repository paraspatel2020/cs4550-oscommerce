<?php
class DobaProductData {
	/**
	 * @var int
	 */
	var $product_id;
	/**
	 * @var int
	 */
	var $item_id;	
	/**
	 * @var string
	 */
	var $title;
	/**
	 * @var string
	 */
	var $product_sku;
	/**
	 * @var string
	 */
	var $upc;
	/**
	 * @var string
	 */
	var $brand;
	/**
	 * @var string
	 */
	var $description;
	/**
	 * @var float
	 */
	var $price;
	/**
	 * @var int
	 */
	var $quantity;
	/**
	 * @var float
	 */
	var $ship_weight;
	/**
	 * @var float
	 */
	var $ship_cost;
	/**
	 * @var string
	 */
	var $image_url;
	/**
	 * @var string
	 */
	var $thumb_url;
	/**
	 * @var int
	 */
	var $image_height;
	/**
	 * @var int
	 */
	var $image_width;
	/**
	 * @var timestamp
	 */
	var $last_update;
	/**
	 * @var string
	 */
	var $date_avail;	
	/**
	 * @var array
	 */
	var $items = array();
	
	function DobaProductData() {}
	
	/**
	 * Getter setter for $product_id
	 * @return int
	 * @param $arg int[optional]
	 */
	function product_id($arg=null) {
		if (!is_null($arg)) {
			$this->product_id = (int)$arg;
		}
		
		return $this->product_id;
	}
	
	/**
	 * Getter setter for $title
	 * @return string
	 * @param $arg string[optional]
	 */
	function title($arg=null) {
		if (!is_null($arg)) {
			$this->title = trim($arg);
		}
		
		return $this->title;
	}
	
	/**
	 * Getter setter for $product_sku
	 * @return string
	 * @param $arg string[optional]
	 */
	function product_sku($arg=null) {
		if (!is_null($arg)) {
			$this->product_sku = trim($arg);
		}
		
		return $this->product_sku;
	}	
	
	/**
	 * Getter setter for $upc
	 * @return string
	 * @param $arg string[optional]
	 */
	function upc($arg=null) {
		if (!is_null($arg)) {
			$this->upc = trim($arg);
		}
		
		return $this->upc;
	}

	/**
	 * Getter setter for $brand
	 * @return string
	 * @param $arg string[optional]
	 */
	function brand($arg=null) {
		if (!is_null($arg)) {
			$this->brand = trim($arg);
		}
		
		return $this->brand;
	}	

	/**
	 * Getter setter for $description
	 * @return string
	 * @param $arg string[optional]
	 */
	function description($arg=null) {
		if (!is_null($arg)) {
			$this->description = trim($arg);
		}
		
		return $this->description;
	}

	/**
	 * Getter setter for $ship_width
	 * @return float
	 * @param $arg float[optional]
	 */
	function ship_width($arg=null) {
		if (!is_null($arg)) {
			$this->ship_width = (float)$arg;
		}
		
		return $this->ship_width;
	}

	/**
	 * Getter setter for $ship_height
	 * @return float
	 * @param $arg float[optional]
	 */
	function ship_height($arg=null) {
		if (!is_null($arg)) {
			$this->ship_height = (float)$arg;
		}
		
		return $this->ship_height;
	}

	/**
	 * Getter setter for $ship_cost
	 * @return float
	 * @param $arg float[optional]
	 */
	function ship_cost($arg=null) {
		if (!is_null($arg)) {
			$this->ship_cost = (float)$arg;
		}
		
		return $this->ship_cost;
	}	

	/**
	 * Getter setter for $image_url
	 * @return string
	 * @param $arg string[optional]
	 */
	function image_url($arg=null) {
		if (!is_null($arg)) {
			$this->image_url = trim($arg);
		}
		
		return $this->image_url;
	}

	/**
	 * Getter setter for $thumb_url
	 * @return string
	 * @param $arg string[optional]
	 */
	function thumb_url($arg=null) {
		if (!is_null($arg)) {
			$this->thumb_url = trim($arg);
		}
		
		return $this->thumb_url;
	}

	/**
	 * Getter setter for $image_height
	 * @return int
	 * @param $arg int[optional]
	 */
	function image_height($arg=null) {
		if (!is_null($arg)) {
			$this->image_height = (int)$arg;
		}
		
		return $this->image_height;
	}

	/**
	 * Getter setter for $image_width
	 * @return int
	 * @param $arg int[optional]
	 */
	function image_width($arg=null) {
		if (!is_null($arg)) {
			$this->image_width = (int)$arg;
		}
		
		return $this->image_width;
	}	

	/**
	 * Getter setter for $last_update
	 * @return datetime
	 * @param $arg datetime[optional]
	 */
	function last_update($arg=null) {
		if (!is_null($arg)) {
			$this->last_update = trim($arg);
		}
		
		return $this->last_update;
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
}
?>