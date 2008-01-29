<?php
//ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaProductData.php');
include_once('doba/DobaProducts.php');

class DobaProductFile {
	/**
	 * Default constructor
	 * @return 
	 */
	function DobaProductFile () {
				
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
		$tempDPD =  new DobaProductData();
		$DobaProds = new DobaProducts();
		$headers;
		
		$fp = fopen($file, 'r');
		
		if ($type == 'csv')
		{
			$delm = ',';			
		}
		else 
		{
			$delm = "\t";
		}
		
		if (!feof($fp))
		{
			$data = fgets($fp);
			
			$headers = explode($delm, $data);			
		}
		
		while(!feof($fp)) 
		{ 
			$data = fgets($fp);
			
			$values = explode($delm, $data);
	
			/* Fields to fill in on the osCommerce add product page
			 * Products Status:   In Stock,  Out of Stock		
			 * Date Available									
			 * Products Manufacturer							? How do I get this info (Not part or Doba file)
			 * Products Name (English, Spanish, Ducth)			/
			 * Tax Class: none, taxable goods					? How do I get this info (Not part or Doba file)
			 * Products Price (Net)								. Will use the MSRP for now.
			 * Products Description (English, Spanish, Ducth)	/
			 * Products Quantity								. Will just use the amount in the file till a better solution is found
			 * Products Model									? 
			 * Products Image									. 
			 * Products URL (English, Spanish, Ducth)			/
			 * Products Weight									/
			 */
			
			$tempDPD->product_id($values[(int)array_keys($headers, 'PRODUCT_ID')]);
			$tempDPD->item_id($values[(int)array_keys($headers, 'ITEM_ID')]);
			$tempDPD->$title($values[(int)array_keys($headers, 'TITLE')]);
			$tempDPD->$price($values[(int)array_keys($headers, 'MSRP')]);
			$tempDPD->$description(''.$values[(int)array_keys($headers, 'DESCRIPTION')].$values[(int)array_keys($headers, 'DETAILS')]);
			$tempDPD->$quantity($values[(int)array_keys($headers, 'QTY_AVAIL')]);
			$tempDPD->$image_url($values[(int)array_keys($headers, 'IMAGE_URL')]);
			$tempDPD->$ship_weight($values[(int)array_keys($headers, 'WEIGHT')]);			

			$DobaProds->addProduct($tempDPD);
		} 
		
		fclose($fp);
		
		return $DobaProds;
	}
}
?>