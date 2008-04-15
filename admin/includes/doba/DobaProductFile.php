<?php
//ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaProductData.php');
include_once('doba/DobaProducts.php');
include_once('doba/DobaInteraction.php');
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
			    $headers[] = DobaProductFile::pruneQuotes(strtoupper(trim($item)));
			}	
		}
		
		while(!feof($fp)) 
		{ 
			$values = DobaProductFile::parseLine( fgets($fp), $delm );
			
			/* Fields to fill in on the osCommerce add product page
			 * Products Status:   In Stock,  Out of Stock		. Passing the current available in the object, needs to be processed before being added to database
			 * Date Available									/
			 * Products Manufacturer							/
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
			
			$temp = array_keys($headers, 'IMAGE_URL');
			$tempDPD->image_url(DobaProductFile::pruneQuotes($values[$temp[0]]));
			
			$temp = array_keys($headers, 'WEIGHT');
			$tempDPD->ship_weight($values[$temp[0]]);
			
			$temp = array_keys($headers, 'SKU');
			$tempDPD->product_sku(DobaProductFile::pruneQuotes($values[$temp[0]]));
			
			$temp = array_keys($headers, 'PRICE');
			$wholesale = $values[$temp[0]];
			
			$temp = array_keys($headers, 'MAP');
			$map = $values[$temp[0]];
			
			$temp = array_keys($headers, 'MSRP');
			$msrp = $values[$temp[0]];
			
			/*
			 * This set of if statements checks if the specified pricing manipulation fields exist
			 * in the file. If they do, the column header name and the value of the field are sent
			 * as parameters to DobaInteraction::setPrice(). The supplied wholesale cost, the map price
			 * and the msrp are also sent as parameters and used in setPrice() to ensure the correct 
			 * price is calculated.
			 */
			if (in_array('OSC_WHOLESALE_MARKUP_PERCENT', $headers)) {
				$temp = array_keys($headers, 'OSC_WHOLESALE_MARKUP_PERCENT');
				if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
					$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_WHOLESALE_PERCENT, $values[$temp[0]], $wholesale, $map, $msrp));
				}
				else {
					$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_NONE, $wholesale, $wholesale, $map, $msrp));
				}					
			}
			elseif (in_array('OSC_WHOLESALE_MARKUP_DOLLAR', $headers)) {
				$temp = array_keys($headers, 'OSC_WHOLESALE_MARKUP_DOLLAR');
				if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
					$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_WHOLESALE_DOLLAR, $values[$temp[0]], $wholesale, $map, $msrp));
				}	
				else {
					$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_NONE, $wholesale, $wholesale, $map, $msrp));
				}				
			}	
			elseif (in_array('OSC_MARKUP_EXACT', $headers)) {
				$temp = array_keys($headers, 'OSC_MARKUP_EXACT');
				if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
					$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_EXACT, $values[$temp[0]], $wholesale, $map, $msrp));
				}	
				else {
					$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_NONE, $wholesale, $wholesale, $map, $msrp));
				}				
			}
			elseif (in_array('OSC_MSRP_MARKUP_PERCENT', $headers)) {
				$temp = array_keys($headers, 'OSC_MSRP_MARKUP_PERCENT');
				if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
					$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_MSRP_PERCENT, $values[$temp[0]], $wholesale, $map, $msrp));
				}	
				else {
					$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_NONE, $wholesale, $wholesale, $map, $msrp));
				}				
			}
			elseif (in_array('OSC_MSRP_MARKUP_DOLLAR', $headers)) {
				$temp = array_keys($headers, 'OSC_MSRP_MARKUP_DOLLAR');
				if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
					$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_MSRP_DOLLAR, $values[$temp[0]], $wholesale, $map, $msrp));
				}	
				else {
					$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_NONE, $wholesale, $wholesale, $map, $msrp));
				}				
			}
			else {
				$tempDPD->price(DobaInteraction::setPrice(PRICE_FMT_NONE, $wholesale, $wholesale, $map, $msrp));
			}	
			
			
			/*
			 * This set of if statements checks if the specified quantity manipulation fields exist
			 * in the file. If they do, the column header name and the value of the field are sent
			 * as parameters to DobaInteraction::setQuantity(). The supplied quantity is also 
			 * sent as a parameter and used in setQuantity() to ensure the correct quantity is calculated.
			 */
			if (in_array('OSC_QUANTITY_AUTOADJUST', $headers)) {
				$temp = array_keys($headers,'OSC_QUANTITY_AUTOADJUST');
				if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
					$tempDPD->quantity(DobaInteraction::setQuantity(QTY_FMT_AUTOADJUST, $values[$temp[0]], $supplied_qty));
				}
				else {
					$tempDPD->quantity(DobaInteraction::setQuantity(QTY_FMT_NONE, $supplied_qty, $supplied_qty));
				}	
			}			
			elseif (in_array('OSC_QUANTITY_EXACT', $headers)) {
				$temp = array_keys($headers,'OSC_QUANTITY_EXACT');
				if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
					$tempDPD->quantity(DobaInteraction::setQuantity(QTY_FMT_EXACT, $values[$temp[0]], $supplied_qty));
				}
				else {
					$tempDPD->quantity(DobaInteraction::setQuantity(QTY_FMT_NONE, $supplied_qty, $supplied_qty));
				}	
			}	
			else {
				$tempDPD->quantity(DobaInteraction::setQuantity(QTY_FMT_NONE, $supplied_qty, $supplied_qty));
			}		

			
			/*
			 * if the OSC_CATEGORY field exists, the category name is loaded into the DobaProductsData
			 * object so that it can be processed.
			 */
			if (in_array('OSC_CATEGORY', $headers)) {
				$tempCategory = array_keys($headers, 'OSC_CATEGORY');
				if (isset($values[$tempCategory[0]]) && !empty($values[$tempCategory[0]])) {
					$tempDPD->category_name(DobaProductFile::pruneQuotes($values[$tempCategory[0]]));
				}
			}
			
			$temp = array_keys($headers, 'BRAND');
			$tempDPD->brand(DobaProductFile::pruneQuotes($values[$temp[0]]));
			
			/*
			 * if the OSC_BRAND field exists, the brand name is loaded into the DobaProductsData
			 * object so that it can be processed.
			 */ 
			if (in_array('OSC_BRAND', $headers)) {
				$tempBrand = array_keys($headers, 'OSC_BRAND');
				if (isset($values[$tempBrand[0]]) && !empty($values[$tempBrand[0]])) {
					$tempDPD->brand(DobaProductFile::pruneQuotes($values[$tempBrand[0]]));
				}
			}
				
			
			/*
			 * if the OSC_PRODUCT_LINK field exists, the brand name is loaded into the DobaProductsData
			 * object so that it can be processed.
			 */
			if (in_array('OSC_PRODUCT_LINK', $headers)) {
				$temp = array_keys($headers, 'OSC_PRODUCT_LINK');
				if (isset($values[$temp[0]]) && !empty($values[$temp[0]])) {
					$tempDPD->product_url(DobaProductFile::pruneQuotes($values[$temp[0]]));
				}
			}
			
			$DobaProds->addProduct($tempDPD);
		} 
		
		fclose($fp);
		
		return $DobaProds;
	}		
}
?>
