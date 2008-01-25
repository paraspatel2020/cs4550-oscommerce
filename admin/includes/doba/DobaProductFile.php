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
			 * Products Manufacturer
			 * Products Name (English, Spanish, Ducth)
			 * Tax Class: none, taxable goods
			 * Products Price (Net)
			 * Products Description (English, Spanish, Ducth)
			 * Products Quantity
			 * Products Model
			 * Products Image
			 * Products URL (English, Spanish, Ducth)
			 * Products Weight
			 */
			
			$tempDPD->product_id($values[(int)array_keys($headers, 'PRODUCT_ID')]);
			$DobaProds->addProduct($tempDPD);
		} 
		
		fclose($fp);
		
		return $DobaProds;
	}
}
?>