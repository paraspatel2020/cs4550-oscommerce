<?php
//ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaOrderInfo.php');
include_once('doba/DobaOrders.php');
include_once('doba/XmlParser.php');

class DobaOrdersAPI {
	
	/**
	 * Default constructor
	 * @return
	 */
	function DobaOrdersAPI() {
		
	}
	
	function parseOrderLookupResponse($data)
	{
		$orderList = new DobaOrders();
		
		$p = new XMLParser($data);	
				
		$orderDetails = $p->getOutput();
		
		if ($orderDetails['dce']['response']['outcome'] == 'Success')
		{			
			$tempDobaOrderArray = new DobaOrders();
			
			if (array_key_exists('supplier_order',$orderDetails['dce']['response']['supplier_orders']))
			{
				//Only 1 supplier_order;
				
				$ord = $orderDetails['dce']['response']['supplier_orders']['supplier_order'];
				
				$tempDobaOrderArray = DobaOrdersAPI::findItems($ord);
				
				foreach($tempDobaOrderArray as $currOrder)
				{
					$orderList->addOrder($currOrder);						
				}				
			}
			else
			{
				//What does multiple supplier_order mean???			
			}
			return $orderList;
		}
		else
		{
			return $orderDetails['dce']['response']['outcome'];
		}
	}
	
	function parseCreateOrderResponse($data)
	{
		$orderList = new DobaOrders();
		
		$p = new XMLParser($data);	
				
		$orderDetails = $p->getOutput();
		
		if ($orderDetails['dce']['response']['outcome'] == 'Success')
		{			
			$tempDobaOrderArray = new DobaOrders();
			
			$tempPO = $orderDetails['dce']['response']['order_id'];
			
			if (array_key_exists('supplier_order',$orderDetails['dce']['response']['supplier_orders']))
			{
				//Only 1 supplier_order;
				
				$ord = $orderDetails['dce']['response']['supplier_orders']['supplier_order'];
				
				$tempDobaOrderArray = DobaOrdersAPI::findItems($ord);
				
				foreach($tempDobaOrderArray as $currOrder)
				{
					$currOrder->po_number($ord['order_id']);
				
					$orderList->addOrder($currOrder);						
				}				
			}
			else
			{
				//What does multiple supplier_order mean???			
			}
			return $orderList;
		}
		else
		{
			return $orderDetails['dce']['response']['outcome'];
		}
	}
	
	function parseGetOrderDetailResponse($data)
	{
		$orderList = new DobaOrders();
		
		$p = new XMLParser($data);	
				
		$orderDetails = $p->getOutput();
		
		if ($orderDetails['dce']['response']['outcome'] == 'Success')
		{			
			$tempDobaOrderArray = new DobaOrders();
			
			if (array_key_exists('order',$orderDetails['dce']['response']['orders']))
			{
				//only one order;
				
				$tempPO = $orderDetails['dce']['response']['orders']['order']['order_id'];
				
				$tmpName = DobaOrdersAPI::name_to_parts($orderD['dce']['response']['orders']['order']['ship_name']);
				
				if (array_key_exists('supplier_order',$orderDetails['dce']['response']['orders']['order']['supplier_orders']))
				{
					//Only 1 supplier_order;
					
					$ord = $orderDetails['dce']['response']['orders']['order']['supplier_orders']['supplier_order'];
					
					$tempDobaOrderArray = DobaOrdersAPI::findItems($ord);
					
					foreach($tempDobaOrderArray as $currOrder)
					{
						$currOrder->po_number($ord['order_id']);
						$currOrder->max_expected_total($ord['order_total']);
						$currOrder->first_name($tmpName['FirstName']);
						$currOrder->last_name($tmpName['LastName']);
						$currOrder->street($ord['ship_street']);
						$currOrder->city($ord['ship_city']);
						$currOrder->postal($ord['ship_postal']);
						$currOrder->country($ord['ship_country']);
					
						$orderList->addOrder($currOrder);						
					}				
				}
				else
				{
					//What does multiple supplier_order mean???			
				}
				return $orderList;
			}
			else
			{
				foreach( $orderDetails['dce']['response']['orders'] as $ord)
				{
				
					$tempPO = $orderDetails['dce']['response']['orders']['order']['order_id'];
					
					$tmpName = DobaOrdersAPI::name_to_parts($orderD['dce']['response']['orders']['order']['ship_name']);
					
					if (array_key_exists('supplier_order',$orderDetails['dce']['response']['orders']['order']['supplier_orders']))
					{
						//Only 1 supplier_order;
						
						$ord = $orderDetails['dce']['response']['orders']['order']['supplier_orders']['supplier_order'];
						
						$tempDobaOrderArray = DobaOrdersAPI::findItems($ord);
						
						foreach($tempDobaOrderArray as $currOrder)
						{
							$currOrder->po_number($ord['order_id']);
							$currOrder->max_expected_total($ord['order_total']);
							$currOrder->first_name($tmpName['FirstName']);
							$currOrder->last_name($tmpName['LastName']);
							$currOrder->street($ord['ship_street']);
							$currOrder->city($ord['ship_city']);
							$currOrder->postal($ord['ship_postal']);
							$currOrder->country($ord['ship_country']);
						
							$orderList->addOrder($currOrder);						
						}				
					}
					else
					{
						//What does multiple supplier_order mean???			
					}
				}
				return $orderList;
			}
		}
		else
		{
			return $orderDetails['dce']['response']['outcome'];
		}
	}
	
	
	function findItems($ItemDetails)	
	{
		/* 
        <items>       
            <item>
		        <item_id>
		        <qty>
		        <price>
            </item>
        </items>		
		*/		
		$itemList = array();
		
		if (array_key_exists('item',$ItemDetails['items']))
		{
			//Only 1 item
			
			$tempDobaOrder = new DobaOrderInfo();
			
			$tempDobaOrder->item_id($ItemDetails['items']['item']['item_id']); 

			$tempDobaOrder->quantity($ItemDetails['items']['item']['qty']); 
			
			//No where to put this...
			//$tempDobaOrder->$max_expected_total($ItemDetails['items']['item']['price']); 
			
			$itemList[0] = $tempDobaOrder;				
		}
		else
		{
			$cnt =0;
			foreach($ItemDetails['items'] as $item)
			{
				$tempDPD = new DobaProductData();
				
				$tempDobaOrder = new DobaOrderInfo();
				
				$tempDobaOrder->item_id($item['item_id']); 
	
				$tempDobaOrder->quantity($item['qty']); 
				
				//No where to put this...
				//$tempDobaOrder->$max_expected_total($item['price']); 

				$itemList[$cnt++] = $tempDobaOrder;
			}
		}
		return $itemList;
	}
	
}//end class DobaOrdersAPI
?>