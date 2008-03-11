<?php
//ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaProductData.php');
include_once('doba/DobaProducts.php');
define('QTY_FMT_NORMAL', 'normal');
define('QTY_FMT_NONE', 'none');
define('QTY_FMT_LIBERAL', 'liberal');
define('QTY_FMT_CONSERVATIVE', 'conservative');

class DobaProductFile {
	var $produts = array();
	/**
	 * Default constructor
	 * @return 
	 */
	function DobaProductFile () {
				
	}
	
	/**
	 * Parse the line by the given delimiter and return an array of elements
	 * @static method
	 * @return array
	 * @param $data string : the line of data to parse
	 * @param $delm string : the delimiter to parse by
	 */
	function parseLine( $data, $delm ) {
		$values = array();		
		$addCom = false;
		$cnt =0;
			
		for ($x=0; $x < strlen($data); $x++) {
			$chr = $data[$x];

			if ($addCom) {
				if ($chr == '"') {
					$addCom = false;
					$values[$cnt] = $values[$cnt].$chr;
				} else if ($chr == $delm) {
					$values[$cnt] = $values[$cnt].$chr;
				} else {
					$values[$cnt] = $values[$cnt].$chr;
				}
			} else {
				if ($chr == '"') {
					$addCom = true;
					$values[$cnt] = $values[$cnt].$chr;	
				} else if ($chr == $delm) {
					$cnt++;
				} else {
					$values[$cnt] = $values[$cnt].$chr;
				}					
			}
		}
		
		return $values;
	}
	
	/**
	 * Take a string and remove the quotes on either end of the string, if they exist.
	 * @static method
	 * @return string
	 * @param $str string
	 */
	function pruneQuotes( $str ) {
		$str = trim($str);
		if (strpos($str, '"') === 0) {
			$str = substr($str, 1, strlen($str)-2);
		}
		
		return $str;
	}
	
	// "123"
	
	/**
	 * Takes the headers and values array and adjusts the quantity to be provided to the
	 * 		DobaProductData object
	 * @return integer representing the new quantity or the existing one if no changes are made.
	 * @param $tempHeaders array
	 * @param $tempValues array
	 * @param $temp_supplied_qty int
	 */
	function setQuantity($tempHeaders, $tempValues, $temp_supplied_qty) {
		$headers = $tempHeaders;
		$values = $tempValues;
		$new_quantity = $temp_supplied_qty;
		if (in_array('OSC_QUANTITY_AUTOADJUST', $headers)) {
			$temp = array_keys($headers,'OSC_QUANTITY_AUTOADJUST');
			if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
				$level = $values[$temp[0]];
				
				if (strtolower(trim($level) === QTY_FMT_NORMAL)) {
					$new_quantity = $temp_supplied_qty * .5; 
				}
				elseif (strtolower(trim($level) === QTY_FMT_LIBERAL)) {
					$new_quantity = $temp_supplied_qty * .75; 
				}
				elseif (strtolower(trim($level) === QTY_FMT_CONSERVATIVE)) {
					$new_quantity = $temp_supplied_qty * .25; 
				}
				elseif (strtolower(trim($level) === QTY_FMT_NONE)) {
					$new_quantity = $temp_supplied_qty; 
				}
			}
		}
		elseif (in_array('OSC_QUANTITY_EXACT', $headers)) {
			$temp = array_keys($headers,'OSC_QUANTITY_EXACT');
			if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
				$new_quantity = $values[$temp[0]];
			}
		}
		return round($new_quantity);
	}
	
	/**
	 * Adjusts the price as necessary and returns the new wholesale cost to be supplied to the 
	 * 		DobaProductData object
	 * @return the wholesale cost
	 * @param $tempHeaders array
	 * @param $tempValues array
	 * @param $tempWholesale float
	 * @param $tempMap float
	 * @param $tempMSRP float
	 */
	function setPrice($tempHeaders, $tempValues, $tempWholesale, $tempMap, $tempMSRP) {
		$wholesale = $tempWholesale;
		$map = $tempMap;
		$msrp = $tempMSRP;
		$new_cost = $wholesale;	
		$headers = $tempHeaders;
		$values = $tempValues;	
		if (in_array('OSC_WHOLESALE_MARKUP_PERCENT', $headers)) {
			$temp = array_keys($headers, 'OSC_WHOLESALE_MARKUP_PERCENT');
			if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
				$percent = $values[$temp[0]];
				if ($percent > 1) {
					$percent = $percent / 100;
				}
				$new_cost = $wholesale * (1 + $percent);
				
				if ($map > 0 && $new_cost < $map) {
					$new_cost = $map;
				}
			}				
		}
		elseif (in_array('OSC_WHOLESALE_MARKUP_DOLLAR', $headers)) {
			$temp = array_keys($headers, 'OSC_WHOLESALE_MARKUP_DOLLAR');
			if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
				$markup = $values[$temp[0]];
				$new_cost = $wholesale + $markup;
				
				if ($map > 0 && $new_cost < $map) {
					$new_cost = $map;
				}
			}
		}
		elseif (in_array('OSC_MSRP_MARKUP_PERCENT', $headers)) {
			$temp = array_keys($headers, 'OSC_MSRP_MARKUP_PERCENT');
			if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
				$percent = $values[$temp[0]];
				if ($percent > 1) {
					$percent = $percent / 100;
				}
				$new_cost = $msrp * (1 + $percent);
				
				if ($map > 0 && $new_cost < $map) {
					$new_cost = $map;
				}
			}
		}
		elseif (in_array('OSC_MSRP_MARKUP_DOLLAR', $headers)) {
			$temp = array_keys($headers, 'OSC_MSRP_MARKUP_DOLLAR');
			if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
				$markup = $values[$temp[0]];
				$new_cost = $msrp + $markup;
				
				if ($map > 0 && $new_cost < $map) {
					$new_cost = $map;
				}
			}
		}
		
		return $new_cost;
	}
	
	/**
	 * Proccess a file and store the files elements in a DobaProduts object
	 * @static method
	 * @return 
	 * @param $file string : full path to file on server 
	 * @param $type string : "tab" or "csv"
	 */
	function processFile($file, $type )
	{
		$delm = '';
		$DobaProds = new DobaProducts();
		$headers;

		$fp = fopen($file, 'r');	
		
		$delm = ($type == 'csv') ? ',' : "\t";

		if (!feof($fp))
		{		
			$data = fgets($fp);
			
			$tHeaders = explode($delm, $data);	

			foreach($tHeaders as $item)
			{
			    $headers[] = strtoupper(trim($item));
			}	
		}
		
		while(!feof($fp)) 
		{ 
			$values = DobaProductFile::parseLine( fgets($fp), $delm );
			
			/* Fields to fill in on the osCommerce add product page
			 * Products Status:   In Stock,  Out of Stock		. Passing the current available in the object, needs to be processed before being added to database
			 * Date Available									/
			 * Products Manufacturer							. Leaving blank
			 * Products Name (English, Spanish, Ducth)			/
			 * Tax Class: none, taxable goods					. Default to Taxable, will need setting in the osCommerce config files
			 * Products Price (Net)								. Will use the MSRP for now.
			 * Products Description (English, Spanish, Ducth)	/
			 * Products Quantity								. Will just use the amount in the file till a better solution is found
			 * Products Model									/ = Product SKU
			 * Products Image									. Will need more processing while the object in being added to the database
			 * Products URL (English, Spanish, Ducth)			/ Leaveing blank
			 * Products Weight									/
			 */

			$tempDPD =  new DobaProductData();
			
			$temp = array_keys($headers, 'PRODUCT_ID');
			$tempDPD->product_id($values[$temp[0]]);
			
			$temp = array_keys($headers, 'ITEM_ID');
			$tempDPD->item_id($values[$temp[0]]);
			
			$temp = array_keys($headers, 'TITLE');
			$tempDPD->title(DobaProductFile::pruneQuotes($values[$temp[0]]));
			
			$temp = array_keys($headers, 'MSRP');
			$tempDPD->price($values[$temp[0]]);
			
			$temp = array_keys($headers, 'DESCRIPTION');
			$descr = DobaProductFile::pruneQuotes($values[$temp[0]]);
			$temp = array_keys($headers, 'DETAILS');
			$details = DobaProductFile::pruneQuotes($values[$temp[0]]);
			if (!empty($descr) && !empty($details)) {
				$descr .= '<br><br>' . $details;
			} else {
				$descr .= $details;
			}
			$tempDPD->description($descr);
			
			$temp = array_keys($headers, 'QTY_AVAIL');
		    $supplied_qty = $values[$temp[0]];
			$tempDPD->quantity(DobaProductFile::setQuantity($headers, $values, $supplied_qty));
			
			$temp = array_keys($headers, 'IMAGE_URL');
			$tempDPD->image_url(DobaProductFile::pruneQuotes($values[$temp[0]]));
			
			$temp = array_keys($headers, 'WEIGHT');
			$tempDPD->ship_weight($values[$temp[0]]);
			
			$temp = array_keys($headers, 'SKU');
			$tempDPD->product_sku(DobaProductFile::pruneQuotes($values[$temp[0]]));
			
			$temp = array_keys($headers, 'PRICE');
			$wholesale = $values[$temp[0]];
			//$tempDPD->price($wholesale);
			
			$temp = array_keys($headers, 'MAP');
			$map = $values[$temp[0]];
			
			$temp = array_keys($headers, 'MSRP');
			$msrp = $values[$temp[0]];
			$tempDPD->price(DobaProductFile::setPrice($headers, $values, $wholesale, $map, $msrp));
			
			if (in_array('OSC_PRODUCT_LINK', $headers)) {
				$temp = array_keys($headers, 'OSC_PRODUCT_LINK');
				if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
					$tempDPD->product_url($values[$temp[0]]);
				}
			}
			
			$DobaProds->addProduct($tempDPD);
		} 
		
		fclose($fp);
		
		return $DobaProds;
	}		
}
?>
