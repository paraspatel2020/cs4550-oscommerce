<?php
//ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaProductData.php');
include_once('doba/DobaProducts.php');

	/**
	 * Default constructor
	 * @return 
	 */
	function DobaProductFile () {
				
	}

	/**
	 * Proccess a file and store the files elements in a DobaProduts object
	 * @return 
	 * @param $file file handle 
	 * @param $type string
	 */
	function processFile($file, $type )
	{
		$delm = '';
		$tempDPD =  new DobaProductData();
		$DobaProds = new DobaProducts();
		$headers;
		
		if ($type == 'csv')
		{
			$delm = ',';			
		}
		else 
		{
			$delm = '\t';
		}
		
		if (!feof($file))
		{
			$data = fgets($file);
			
			$headers = explode($delm, $data);			
		}
		
		while(!feof($file)) 
		{ 
			$data = fgets($file);
			
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
			
			$tempDPD->$product_id = $values[(int)array_keys($headers, 'PRODUCT_ID')];
			$DobaProds .= $tempDPD;
		} 
		return $DobaProds;
	}

?>