<?php

include_once('doba/DobaOrders.php');
include_once('doba/DobaOrderInfo.php');

class DobaOrderFile
{	
	/**
	 * default constructor for DobaOrderFile
	 * @return void
	 */	
	function DobaOrderFile() {}
	
	/**
	 * Takes the orders array from DobaOrders and sends DobaOrderInfo data to be echoed 
	 * @return 
	 * @param $orders DobaOrders
	 */
	function processData (DobaOrders $orders)
	{
		/*
		 * Looking at the example order file, it looks like we did not declare all the
		 * required data fields as variables in DobaOrderInfo.  We are missing address2,
		 * quantity, and maxexpectedtotal.  Also, is the PONumber the order_id? What is the
		 * item_id? I can change or add these after clarification.
		 */
		echoHeader();
		for ($i = 0; $i<count($orders->$orders); $i++)
		{
			echo "\n";
			echoData($orders->$order_id);
			echoData($orders->$first_name);
			echoData($orders->$last_name);
			echoData($orders->$address);
			echoData($orders->$city);
			echoData($orders->$state);
			echoData($orders->$postal);
			echoData($orders->$country);
		}
	}
	
	/**
	 * accepts strings from the processData() function and echoes it followed by a tab.
	 * @return void
	 * @param $data String
	 */
	function echoData(string $data)
	{
		echo "$data\t";
	}
	
	/**
	 * Echoes the column headers for the order file
	 * @return void
	 */
	function echoHeader ()
	{
		echo "PONumber\tFirstname\tLastname\taddress1\taddress2\tcity\tstate\tpostal\tcountry\titemid\tquantiy\tmaxexpectedtotal\n";
	}
}

?>