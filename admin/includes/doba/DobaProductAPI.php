<?php
//ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaProductData.php');
include_once('doba/DobaProducts.php');
include_once('doba/XmlParser.php');

class DobaProductAPI {
	
	/**
	 * Default constructor
	 * @return 
	 */
	function DobaProductAPI () {
		
	}
	
	function parseWatchlistResponse($data)
	{
		/*
        <watchlists>
            <watchlist>
                </watchlist_id>
                </retailer_id>
                </name>
                </default>
                </send_callback>
            </watchlist>
        </watchlists> 
		 */
		$p = new XMLParser($data);	
				
		$WatchDetails = $p->getOutput();
				
		return array(
			'response' => $WatchDetails['dce']['response']['outcome'],
			'data' => (isset($WatchDetails['dce']['response']['watchlists'])) ? $WatchDetails['dce']['response']['watchlists'] : array()
		);
	}	
	
	function parseProductDetails($data)
	{		
		$productList = new DobaProducts();
		$p = new XMLParser($data);	
			
		$ProdDetails = $p->getOutput();

		if ($ProdDetails['dce']['response']['outcome'] == 'success')
		{		
			if (array_key_exists('product',$ProdDetails['dce']['response']['products']))
			{
				//Only 1 product;
				$prod = $ProdDetails['dce']['response']['products']['product'];
				$tempDPDArray = DobaProductAPI::findItems($prod);
				
				foreach($tempDPDArray as $currProd)
				{
					$currProd->product_id($prod['product_id']);
					$currProd->title($prod['title']);
					$currProd->description($prod['description']);
					DobaProductAPI::findImage($prod, $currProd);
					$currProd->product_sku($prod['product_sku']);
					$currProd->ship_weight($prod['ship_weight']);
					$currProd->ship_cost($prod['ship_cost']);
					$currProd->upc($prod['upc']);
					$currProd->brand($prod['brand']);
					if (isset($prod['OSC_BRAND'])) {
						$currProd->brand($prod['OSC_BRAND']);
					}
					$currProd->category_name('');
					if (isset($prod['OSC_CATEGORY'])) {
						$currProd->category_name($prod['OSC_CATEGORY']);
					}
				
					//Set Price
					if (isset($prod['OSC_WHOLESALE_MARKUP_PERCENT'])) {
						$currProd->price(DobaInteraction::setPrice('osc_wholesale_markup_percent', 
						$prod['OSC_WHOLESALE_MARKUP_PERCENT'], $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));					
					}
					elseif (isset($prod['OSC_WHOLESALE_MARKUP_DOLLAR'])) {
						$currProd->price(DobaInteraction::setPrice('osc_wholesale_markup_dollar', 
						$prod['OSC_WHOLESALE_MARKUP_DOLLAR'], $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));				
					}	
					elseif (isset($prod['OSC_MARKUP_EXACT'])) {
						$currProd->price(DobaInteraction::setPrice('osc_markup_exact', 
						$prod['OSC_MARKUP_EXACT'], $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));				
					}
					elseif (isset($prod['OSC_MSRP_MARKUP_PERCENT'])) {
						$currProd->price(DobaInteraction::setPrice('osc_msrp_markup_percent', 
						$prod['OSC_MSRP_MARKUP_PERCENT'], $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));				
					}
					elseif (isset($prod['OSC_MSRP_MARKUP_DOLLAR'])) {
						$currProd->price(DobaInteraction::setPrice('osc_msrp_markup_dollar', 
						$prod['OSC_MSRP_MARKUP_DOLLAR'], $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));				
					}
					else {
						$currProd->price(DobaInteraction::setPrice('none', 
						$currProd->wholesale_price(), $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));
					}							

					//Set Quantity
					if (isset($prod['OSC_QUANTITY_AUTOADJUST'])) {
						$currProd->quantity(DobaInteraction::setQuantity('osc_quantity_autoadjust', 
						$prod['OSC_QUANTITY_AUTOADJUST'], $currProd->quantity));	
					}			
					elseif (isset($prod['OSC_QUANTITY_EXACT'])) {
						$currProd->quantity(DobaInteraction::setQuantity('osc_quantity_exact', 
						$prod['OSC_QUANTITY_EXACT'], $currProd->quantity));	
					}	
					else {
						$currProd->quantity(DobaInteraction::setQuantity('none', $currProd->quantity, $currProd->quantity));
					}					
					
					$productList->addProduct($currProd);							
				}				
			}
			else
			{
				foreach($ProdDetails['dce']['response']['products'] as $prod)
				{
					$tempDPDArray = DobaProductAPI::findItems($prod);
					
					foreach($tempDPDArray as $currProd)
					{
						$currProd->product_id($prod['product_id']);
						$currProd->title($prod['title']);
						$currProd->description($prod['description']);
						DobaProductAPI::findImage($prod, $currProd);
						$currProd->product_sku($prod['product_sku']);
						$currProd->ship_weight($prod['ship_weight']);
						$currProd->ship_cost($prod['ship_cost']);
						$currProd->upc($prod['upc']);
						$currProd->brand($prod['brand']);
						if (isset($prod['OSC_BRAND'])) {
							$currProd->brand($prod['OSC_BRAND']);
						} else if (isset($prod['osc_brand'])) {
							$currProd->brand($prod['osc_brand']);
						}
						$currProd->category_name('');
						if (isset($prod['OSC_CATEGORY'])) {
							$currProd->category_name($prod['OSC_CATEGORY']);
						} else if (isset($prod['osc_category'])) {
							$currProd->category_name($prod['osc_category']);
						}
						
						//Set Price
						if (isset($prod['OSC_WHOLESALE_MARKUP_PERCENT'])) {
							$currProd->price(DobaInteraction::setPrice('osc_wholesale_markup_percent', 
							$prod['OSC_WHOLESALE_MARKUP_PERCENT'], $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));					
						}
						elseif (isset($prod['OSC_WHOLESALE_MARKUP_DOLLAR'])) {
							$currProd->price(DobaInteraction::setPrice('osc_wholesale_markup_dollar', 
							$prod['OSC_WHOLESALE_MARKUP_DOLLAR'], $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));				
						}	
						elseif (isset($prod['OSC_MARKUP_EXACT'])) {
							$currProd->price(DobaInteraction::setPrice('osc_markup_exact', 
							$prod['OSC_MARKUP_EXACT'], $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));				
						}
						elseif (isset($prod['OSC_MSRP_MARKUP_PERCENT'])) {
							$currProd->price(DobaInteraction::setPrice('osc_msrp_markup_percent', 
							$prod['OSC_MSRP_MARKUP_PERCENT'], $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));				
						}
						elseif (isset($prod['OSC_MSRP_MARKUP_DOLLAR'])) {
							$currProd->price(DobaInteraction::setPrice('osc_msrp_markup_dollar', 
							$prod['OSC_MSRP_MARKUP_DOLLAR'], $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));				
						}
						else {
							$currProd->price(DobaInteraction::setPrice('none', 
							$currProd->wholesale_price(), $currProd->wholesale_price(), $currProd->map(), $currProd->msrp()));
						}							

						//Set Quantity
						if (isset($prod['OSC_QUANTITY_AUTOADJUST'])) {
							$currProd->quantity(DobaInteraction::setQuantity('osc_quantity_autoadjust', 
							$prod['OSC_QUANTITY_AUTOADJUST'], $currProd->quantity));	
						}			
						elseif (isset($prod['OSC_QUANTITY_EXACT'])) {
							$currProd->quantity(DobaInteraction::setQuantity('osc_quantity_exact', 
							$prod['OSC_QUANTITY_EXACT'], $currProd->quantity));	
						}	
						else {
							$currProd->quantity(DobaInteraction::setQuantity('none', $currProd->quantity, $currProd->quantity));
						}					
						
						
						$productList->addProduct($currProd);							
					}				
				}				
			}
			return $productList;
		}
		else
		{
			return $ProdDetails['dce']['response']['outcome'];
		}
	}

	function findItems($ItemDetails)	
	{
		/* 
        <items>       
            <item>
		        </item_id>
		        </item_sku>
		        </name><!-- used to differentiate between sizes or colors in multiple item products -->
		        </price>
		        </prepay_price>                
		        </msrp>
		        </map><!-- Minimum Advertised Price - retailers cannot advertise this product below this amount -->
		        </qty_avail>
		        </stock><!-- in-stock, out-of-stock or discontinued -->
		        </last_update><!-- date the item was last updated --> 
            </item>
        </items>		
		*/		
		$itemList = array();
		
		if (array_key_exists('item',$ItemDetails['items']))
		{
			//Only 1 item
			
			$tempDPD = new DobaProductData();
			
			$tempDPD->item_id($ItemDetails['items']['item']['item_id']); 				
			
			$tempDPD->msrp($ItemDetails['items']['item']['msrp']);
			$tempDPD->map($ItemDetails['items']['item']['map']);
			$tempDPD->wholesale_price($ItemDetails['items']['item']['price']);
			
			$tempDPD->quantity($ItemDetails['items']['item']['qty_avail']);

			
			$itemList[0] = $tempDPD;				
		}
		else
		{
			$cnt =0;
			foreach($ItemDetails['items'] as $item)
			{
				$tempDPD = new DobaProductData();
				
				$tempDPD->item_id($item['item_id']); 				
								
				$tempDPD->msrp($item['msrp']);
				$tempDPD->map($item['map']);
				$tempDPD->wholesale_price($item['price']);
				
				$tempDPD->quantity($item['qty_avail']);

				
				$itemList[$cnt++] = $tempDPD;
			}
		}
		return $itemList;
	}
	
	function findImage($XmlImgData, $DobaProd)
	{
		/*
        <images>
            <image>
                </url><!-- large image -->
                </thumb_url><!-- thumbnail image -->
                </image_height>
                </image_width>
                </default><!-- 1 if this image is the default -->
            </image>
        <images>
		*/		
		
		if (array_key_exists('image',$XmlImgData['images']))
		{		
			$DobaProd->image_url($XmlImgData['images']['image']['url']);
			
			//$temp = array_keys($headers, 'IMAGE_URL');
			//$tempDPD->image_url(DobaProductFile::pruneQuotes($values[$temp[0]]));				
		}
		else
		{
			foreach($XmlImgData['images'] as $tempImg)			
			{
				if ($tempImg['default'] == '1')
				{
					$DobaProd->image_url($tempImg['url']);
					break;
				}
			}
		}
		return;
	}
}
?>