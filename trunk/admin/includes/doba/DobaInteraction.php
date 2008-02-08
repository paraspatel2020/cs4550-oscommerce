<?php
$img_data = array();

class DobaInteraction {
	
	/**
	 * Take the requested image source and save it to the server, then return the 
	 * path to the image.
	 * @return $str : the relative path to the image in the images directory
	 * @param $src string : the url to the image
	 * @param $product_id int : the id of the product in the oscommerce db
	 */
	function processImage( $src ) {
		global $img_data;
		
		$ref = '';
		
		if (isset($img_data[$src])) {
			return $img_data[$src];
		}
		
		if (!empty($src)) {
			$data = file_get_contents($src, FILE_BINARY);
			if (!empty($data)) {
				$i_parts = explode('/', $src);
				$ref = 'doba___' . $i_parts[count($i_parts)-2] . '_' . $i_parts[count($i_parts)-1];
				$new_file = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $ref;
				if (($fh = fopen($new_file, 'w')) !== false) {
					if (fwrite($fh, $data) === false) {
						$ref = '';
					}
				} else {
					$ref = '';
				}
			}
		}
		
		$img_data[$src] = $ref;		
		
		return $ref;
	}
	
	/**
	 * Load all of the supplied products into the database.  This will update any 
	 * existing entries and add any new entries.  It is up to the user to manage 
	 * the discontinuance of products.
	 * @static : this is a static method
	 * @return bool : True if the products were succssfully loaded.
	 * @param $products DobaProducts
	 */
	function loadDobaProductsIntoDB( $products ) {
		if (is_a($products, 'DobaProducts')) {
			$sql_prod = 'replace into ' . TABLE_PRODUCTS . ' 
							(products_id, products_quantity, products_model, products_image, products_price, products_last_modified, products_weight, 
							products_status, products_tax_class_id, manufacturers_id) 
						values ';
			$sql_cat = 'replace into ' . TABLE_PRODUCTS_TO_CATEGORIES . ' 
							(products_id, categories_id) 
						values';
			$sql_descr = 'replace into ' . TABLE_PRODUCTS_DESCRIPTION . ' 
							(products_id, language_id, products_name, products_description) 
						values ';

			$can_insert = false;
			foreach ($products->products as $prod) {
				if ($can_insert) {
					$sql_prod .= ', ';
					$sql_cat .= ', ';
					$sql_descr .= ', ';
				}
				$can_insert = true;
				$products_id = $prod->item_id();
				$products_quantity = $prod->quantity();
				$products_model = $prod->product_sku();
				$img_url = $prod->image_url();
				if (empty($img_url)) {
					$img_url = $prod->thumb_url();
				}
				$products_image = DobaInteraction::processImage($img_url, $products_id);
				$products_price = $prod->price();
				$products_last_modified = 'now()';
				$products_weight = $prod->ship_weight();
				$products_status = ($prod->quantity() > 0) ? 1 : 0;
				$products_tax_class_id = 1;
				$manufacturers_id = 'NULL';
				$categories_id = 0;
				$language_id = 1;
				$products_name = $prod->title();
				$products_description = $prod->description();
				
				$sql_prod .= '(' . $products_id . ', ' . $products_quantity . ', "' . tep_db_prepare_input($products_model) . '", 
							"' . tep_db_prepare_input($products_image) . '", ' . $products_price . ', ' . $products_last_modified . ', 
							' . $products_weight . ', ' . $products_status . ', ' . $products_tax_class_id . ', ' . $manufacturers_id . ')';
				$sql_cat .= '(' . $products_id . ', ' . $categories_id . ')';
				$sql_descr .= '(' . $products_id . ', ' . $language_id . ', "' . tep_db_prepare_input($products_name) . '", 
							"' . tep_db_prepare_input($products_description) . '")';
			}
			
			if ($can_insert) {
				tep_db_query($sql_prod);
				tep_db_query($sql_cat);
				tep_db_query($sql_descr);
				
				$sql = 'update ' . TABLE_PRODUCTS . ' set products_date_added=now(), products_date_available=now() where products_date_added is NULL';
				tep_db_query($sql);
					
				return true;
			}
		}
		
		return false;
	}
	
	/**
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
						
		$cnt_query = tep_db_query($sql);
  		$cnt = tep_db_num_rows($cnt_query);
			
		return $cnt;
	}
}
?>