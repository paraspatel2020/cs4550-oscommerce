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
			$this->products[$objDobaProductData->product_id()] = $objDobaProductData;
			return true;
		}
		return false;
	}
	
	/**
	 * Check to see if the specified product exists
	 * @return bool
	 * @param $product_id int
	 */
	function productExists($product_id) {
		return (isset($this->products[intval($product_id)]));
	}
	
	/**
	 * Remove the specified product if it exists
	 * @return bool
	 * @param $product_id int
	 */
	function removeProduct($product_id) {
		if ($this->productExists($product_id)) {
			unset($this->products[$product_id]);
			return true;
		}
		return false;
	}

}
?>