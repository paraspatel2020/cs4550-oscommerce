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
			
			$tHeaders = explode($delm, $data);	

			foreach($tHeaders as $item)
			{
			    $headers[] = strtoupper($item);
			}	
		}
		
		while(!feof($fp)) 
		{ 
			$data = fgets($fp);
				
			$values = explode($delm, $data);

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

/* Known bug!!! ITEM_ID is not being found correctly. This is likely because ITEM_ID is at the end of a line and therefore has the \r\n and these are confusing the search algorithim.*/
//echo 'values: '.var_dump($headers).'<br><br>';

//$temp = array_keys($headers, 'ITEM_ID ');
//echo 'Keys \'ITEM_ID\': '.$temp[0].'<br>';

			$tempDPD =  new DobaProductData();
			
			$temp = array_keys($headers, 'PRODUCT_ID');
			$tempDPD->product_id($values[$temp[0]]);
			$temp = array_keys($headers, 'ITEM_ID ');
			$tempDPD->item_id($values[$temp[0]]);
			$temp = array_keys($headers, 'TITLE');
			$tempDPD->title($values[$temp[0]]);
			$temp = array_keys($headers, 'MSRP');
			$tempDPD->price($values[$temp[0]]);
			$temp = array_keys($headers, 'DESCRIPTION');
			$tempStr = ''.$values[$temp[0]].'<br>';
			$temp = array_keys($headers, 'DETAILS');
			$tempStr = $tempStr.''.$values[$temp[0]];
			$tempDPD->description($tempStr);
			$temp = array_keys($headers, 'QTY_AVAIL');
			$tempDPD->quantity($values[$temp[0]]);
			$temp = array_keys($headers, 'IMAGE_URL');
			$tempDPD->image_url($values[$temp[0]]);
			$temp = array_keys($headers, 'WEIGHT');
			$tempDPD->ship_weight($values[$temp[0]]);
			$tempDPD->date_avail(date('Y-m-d'));
			$temp = array_keys($headers, 'SKU');
			$tempDPD->product_sku($values[$temp[0]]);
					
			$DobaProds->addProduct($tempDPD);
		} 
		
		fclose($fp);
	
		return $DobaProds;
	}
}
?>