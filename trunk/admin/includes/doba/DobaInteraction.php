<?php
class DobaInteraction {
	
	/**
	 * Load all of the supplied products into the database.
	 * @static : this is a static method
	 * @return bool : True if the products were succssfully loaded.
	 * @param $products DobaProducts
	 * @param $action string (replace, update)
	 * 		update : add the supplied products to the current database and update any duplicates
	 * 		replace : remove all products currently in the database and insert the supplied products
	 */
	function loadDobaProductsIntoDB( $products, $action='update' ) {
		
		return true;
	}
}
?>