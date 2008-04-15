<?php
define('QTY_FMT_NORMAL', 'normal');
define('QTY_FMT_NONE', 'none');
define('QTY_FMT_LIBERAL', 'liberal');
define('QTY_FMT_CONSERVATIVE', 'conservative');
define('QTY_FMT_AUTOADJUST', 'osc_quantity_autoadjust');
define('QTY_FMT_EXACT','osc_quantity_exact');
define('PRICE_FMT_WHOLESALE_PERCENT', 'osc_wholesale_markup_percent');
define('PRICE_FMT_WHOLESALE_DOLLAR', 'osc_wholesale_markup_dollar');
define('PRICE_FMT_EXACT', 'osc_markup_exact');
define('PRICE_FMT_MSRP_PERCENT', 'osc_msrp_markup_percent');
define('PRICE_FMT_MSRP_DOLLAR', 'osc_msrp_markup_dollar');
define('PRICE_FMT_NONE', 'none');

$img_data = array();
$brand_data = array();
$category_data = array();

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
			$data = @file_get_contents($src, FILE_BINARY);
			if (!empty($data)) {
				$i_parts = explode('/', $src);
				$ref = 'doba___' . $i_parts[count($i_parts)-2] . '_' . $i_parts[count($i_parts)-1];
				$new_file = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $ref;
				if (($fh = @fopen($new_file, 'w')) !== false) {
					if (@fwrite($fh, $data) === false) {
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
				$products_quantity = intval($prod->quantity());
				$products_model = $prod->product_sku();
				$img_url = $prod->image_url();
				if (empty($img_url)) {
					$img_url = $prod->thumb_url();
				}
				$products_image = DobaInteraction::processImage($img_url, $products_id);
				$products_price = floatval($prod->price());
				$products_last_modified = 'now()';
				$products_weight = $prod->ship_weight();
				$products_status = ($prod->quantity() > 0) ? 1 : 0;
				$products_tax_class_id = 1;
				$manufacturers_id = DobaInteraction::setBrandName($prod->brand());
				$categories_id = DobaInteraction::setCategoryName($prod->category_name());
				$language_id = 1;
				$products_name = $prod->title();
				$products_description = $prod->description();
				
				
				$sql_prod .= '(' . $products_id . ', ' . $products_quantity . ', "' . addslashes(tep_db_prepare_input($products_model)) . '", 
							"' . addslashes(tep_db_prepare_input($products_image)) . '", ' . $products_price . ', ' . $products_last_modified . ', 
							' . $products_weight . ', ' . $products_status . ', ' . $products_tax_class_id . ', ' . $manufacturers_id . ')';
				$sql_cat .= '(' . $products_id . ', ' . $categories_id . ')';
				$sql_descr .= '(' . $products_id . ', ' . $language_id . ', "' . addslashes(tep_db_prepare_input($products_name)) . '", 
							"' . addslashes(tep_db_prepare_input($products_description)) . '")';
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
	
	/**
	 * Takes takes the column header name as a string and the value of the column and
	 * adjusts the quantity. The adjusted quantity will be returned to the DobaProductsFile
	 * to be loaded into the DobaProducts data object
	 * @return integer representing the new quantity or the existing one if no changes are needed.
	 * @param $header. representing the name of the column in the file
	 * @param $value. representing the value, if populated, at the header column. The value corresponds
	 * 					to the adjustment level desired (normal, liberal, conservative or none)
	 * @param $temp_supplied_qty int. representing the quantity initally supplied in the uploaded file
	 * 					for the product. This is the quantity that will be adjusted according to the
	 * 					level indicated in $value.
	 */
	function setQuantity($header, $value, $supplied_qty) {
		$new_quantity = intval($supplied_qty);
		$column = strval(strtolower(trim($header)));
		$level = strval(strtolower(trim($value)));
		
		if ($column === QTY_FMT_AUTOADJUST) {			
			if ($level === QTY_FMT_NORMAL) {
				$new_quantity = $supplied_qty * .5; 
			}
			elseif ($level === QTY_FMT_LIBERAL) {
				$new_quantity = $supplied_qty * .75; 
			}
			elseif ($level === QTY_FMT_CONSERVATIVE) {
				$new_quantity = $supplied_qty * .25; 
			}
			elseif ($level === QTY_FMT_NONE) {
				$new_quantity = $supplied_qty; 
			}
		}
		elseif ($column === QTY_FMT_EXACT) {
			if (intval($value) < intval($supplied_qty)) {
				$new_quantity = intval($value);
			}
			else {
				$new_quantity = intval($supplied_qty);
			}
			
			
		}
		elseif ($column === QTY_FMT_NONE) {
			$new_quantity = $supplied_qty;
		}
		
		return intval(round($new_quantity));
	}
	
	/**
	 * Takes takes the column header name as a string and the value of the column and
	 * adjusts the price. The adjusted price will be returned to the DobaProductsFile
	 * to be loaded into the DobaProducts data object
	 * @return integer representing the new price or the existing one if no changes are needed.
	 * @param $header string. representing the name of the column header in the file
	 * @param $value string. representing the customized pricing structure desired. This value,
	 * 					if it exists, will adjust the price to display a new price represented 
	 * 					by the value. 
	 * @param $wholesale float. The supplied wholesale cost.
	 * @param $map float. The supplied map price, if it exits.
	 * @param $msrp float. The supplied msrp price if it exists. 
	 */
	function setPrice($header, $value, $wholesale, $map, $msrp) {
		$new_cost = $wholesale;
		$column = strval(strtolower(trim($header)));
		if (floatval($value) > 0) {
			if ($column === PRICE_FMT_WHOLESALE_PERCENT) {	
				$percent = $value;
				if ($percent > 1) {
					$percent = $percent / 100;
				}
				$new_cost = $wholesale * (1 + $percent);
			}
			elseif ($column === PRICE_FMT_WHOLESALE_DOLLAR) {
				$markup = $value;
				$new_cost = $wholesale + $markup;
			}
			elseif ($column === PRICE_FMT_MSRP_PERCENT) {
				$percent = $value;
				if ($percent > 1) {
					$percent = $percent / 100;
				}
				$new_cost = $msrp * (1 + $percent);
			}
			elseif ($column === PRICE_FMT_MSRP_DOLLAR) {
				$markup = $value;
				$new_cost = $msrp + $markup;
			}							
			elseif ($column === PRICE_FMT_EXACT) {
				$new_cost = $value;
			}
			elseif ($column === PRICE_FMT_NONE) {
				$new_cost = $wholesale;
			}			
		}
		
		if ($map > 0 && $new_cost < $map) {
				$new_cost = $map;
			}
		
		return floatval($new_cost);
	}
	
	/**
	 * If the OSC_CATEGORY field exists, this function will load the new category name into the 
					categories_description table and the last modified data into the categories table.
	 * @return integer. The category id in the database.
	 * @param $str string [optional]. The category name to be created. 
	 */
	function setCategoryName($str='') {
		$str = trim($str);
	
		if ($str == '') {
			$str = PRODUCT_DEFAULT_CATEGORY_NAME;
		}
		
		error_log("Category: ".$str);
		global $category_data;
		
		// return the id if we have already dealt with it in the current load
		if (isset($category_data[$str])) {
			return $category_data[$str];
		}
		
		$sql = 'select categories_id as id from ' . TABLE_CATEGORIES_DESCRIPTION . ' 
				where language_id=1 and 
					  categories_name="' . addslashes(tep_db_prepare_input($str)) . '" 
				limit 1';
		$res = tep_db_query($sql);
		
		if (is_array(($arr = tep_db_fetch_array($res)))) {
			$category_data[$str] = $arr['id'];
			return $arr['id'];
		}
		
		$sql = 'insert ignore into ' . TABLE_CATEGORIES . ' 
					(sort_order, date_added, last_modified) 
				values 
					(0, now(), now())';
		$tc_insert_query = tep_db_query($sql);
		
		$id = intval(tep_db_insert_id());
		
		if ($id > 0) {
			$sql = 'insert into ' . TABLE_CATEGORIES_DESCRIPTION . ' 
						(categories_id, language_id, categories_name)
					values 
						(' . intval(tep_db_insert_id()) . ', 1, "' . addslashes(tep_db_prepare_input($str)) . '"), 
						(' . intval(tep_db_insert_id()) . ', 2, "' . addslashes(tep_db_prepare_input($str)) . '"), 
						(' . intval(tep_db_insert_id()) . ', 3, "' . addslashes(tep_db_prepare_input($str)) . '")';
			$tcd_insert_query = tep_db_query($sql);
		}			
		
		$category_data[$str] = $id;
		return $id;
	}
	
	/**
	 * If the OSC_BRAND field exists, this function will load the new brand name into the 
					manufacturers_info table and the last modified data into the manufacturers table.
	 * @return integer. The manufacturers_id in the database.
	 * @param $str string.  The brand name to be created. 
	 * @param $url string [optional]. The url, if provided, to the manufacture's website.
	 */
	function setBrandName($str, $url='') {
		$str = trim($str);
		$url = trim($url);
	
		// if the brand has already been created, do nothing. 
		if (in_array($str, array('','N/A'))) {
			return 0;
		}
		
		error_log("Brand: ".$str);
		
		global $brand_data;
		if (isset($brand_data[$str])) {
			return $brand_data[$str];
		}
		
		$sql = 'select manufacturers_id as id from ' . TABLE_MANUFACTURERS . '
				where manufacturers_name="' . addslashes(tep_db_prepare_input($str)) . '"';
		$res = tep_db_query($sql);

		if (is_array(($arr = tep_db_fetch_array($res)))) {
			$brand_data[$str] = $arr['id'];
			return $arr['id'];
		}
		
		$sql = 'insert ignore into ' . TABLE_MANUFACTURERS . '	
					(manufacturers_name, date_added, last_modified)
				values
					("' . addslashes(tep_db_prepare_input($str)) . '", now(), now())';
		$tm_insert_query = tep_db_query($sql);

		$id = intval(tep_db_insert_id());

		if ($id > 0) {
			$sql = 'insert into ' . TABLE_MANUFACTURERS_INFO . ' 
						(manufacturers_id, languages_id, manufacturers_url)
					values 
						(' . intval(tep_db_insert_id()) . ', 1, "' . addslashes(tep_db_prepare_input($url)) . '"), 
						(' . intval(tep_db_insert_id()) . ', 2, "' . addslashes(tep_db_prepare_input($url)) . '"), 
						(' . intval(tep_db_insert_id()) . ', 3, "' . addslashes(tep_db_prepare_input($url)) . '")';
			$tmi_insert_query = tep_db_query($sql);
		}			

		$brand_data[$str] = $id;
		return $id;
	}
}
?>
