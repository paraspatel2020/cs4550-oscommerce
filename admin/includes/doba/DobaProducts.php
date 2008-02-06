<?php

class DobaProducts {
	var $products = array();
	
	function DobaProducts () {
				
	}
	/**
	 * Add a DobaProductData object to the products array
	 * @return bool
	 * @param $objDobaProductData DobaProductData
	 */
	function addProduct($objDobaProductData) {
		if (is_a($objDobaProductData, 'DobaProductData')) {
			$this->products[$objDobaProductData->item_id()] = $objDobaProductData;
			return true;
		}
		return false;
	}
	
	/**
	 * Check to see if the specified product exists
	 * @return bool
	 * @param $key int
	 */
	function productExists($key) {
		return (isset($this->products[intval($key)]));
	}
	
	/**
	 * Remove the specified product if it exists
	 * @return bool
	 * @param $product_id int
	 */
	function removeProduct($key) {
		if ($this->productExists($key)) {
			unset($this->products[$key]);
			return true;
		}
		return false;
	}

}
?>