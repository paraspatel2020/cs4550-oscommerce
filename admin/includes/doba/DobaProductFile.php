<?php
//ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaProductData.php');
include_once('doba/DobaProducts.php');

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
			$tempDPD->quantity($values[$temp[0]]);
			
			$temp = array_keys($headers, 'IMAGE_URL');
			$tempDPD->image_url(DobaProductFile::pruneQuotes($values[$temp[0]]));
			
			$temp = array_keys($headers, 'WEIGHT');
			$tempDPD->ship_weight($values[$temp[0]]);
			
			$temp = array_keys($headers, 'SKU');
			$tempDPD->product_sku(DobaProductFile::pruneQuotes($values[$temp[0]]));
					
			$DobaProds->addProduct($tempDPD);
		} 
		
		fclose($fp);
		
		return $DobaProds;
	}		
}
?>
