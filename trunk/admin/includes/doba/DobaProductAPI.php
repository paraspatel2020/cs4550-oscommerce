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
		//$productList = new DobaProducts();
		$p = new XMLParser($data);	
				
		$WatchDetails = $p->getOutput();
		
		$retWatchlstInfo = array();
		
		if ($WatchDetails['dce']['response']['outcome'] == 'Success')
		{			
			if (array_key_exists('watchlist',$WatchDetails['dce']['response']['watchlists']))
			{
				//Only 1 watchlist;
				
				$retWatchlstInfo[0] = $WatchDetails['dce']['response']['watchlists']['watchlist'];
			}
			else
			{
				$cnt =0;
				foreach($WatchDetails['dce']['response']['watchlists'] as $wtch)
				{
					$retWatchlstInfo[cnt] = $wtch;
					$cnt++;
				}				
			}
			return $retWatchlstInfo;
		}
		else
		{
			return $WatchDetails['dce']['response']['outcome'];
		}		
	}	
	
	function parseProductDetails($data)
	{		
		$productList = new DobaProducts();
		$p = new XMLParser($data);	
			
		$ProdDetails = $p->getOutput();
//echo '<pre>';
//echo print_r($ProdDetails);
//echo '</pre>';
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
					
					//$currProd->ship_cost($prod['ship_cost']);
					//$currProd->upc($prod['upc']);
					//$currProd->brand($prod['brand']);
				
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
						
						//$currProd->ship_cost($prod['ship_cost']);
						//$currProd->upc($prod['upc']);
						//$currProd->brand($prod['brand']);
					
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
		
		/*
		  	<product>
				</product_id>
                </title>
                
                </supplier_id>
                </supplier_name>
                </supplier_pro_name>
                </product_sku>
                </upc>
                </brand>
                
                </description>
                </ship_width>
                </ship_length>
                </ship_height>
                </ship_weight>
                </ship_cost>
                
                <items>            
					<!-----other funcion-------->
                </items>
                
                <images>
					<!-----other funcion-------->
                <images>
                
                <supplier_id>
                <supplier_name>     
			</product>           
		 */
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
			
			//TODO: Use the 'setPrice' function in DobaProductFile instead of just setting the price
			//$wholesale = $item['price'];
			//$map = $item['map'];
			//$msrp = $item['msrp'];
			//$tempDPD->price(DobaProductFile::setPrice($headers, $values, $wholesale, $map, $msrp));
			$tempDPD->price($ItemDetails['items']['item']['price']);
			
			//TODO: Use the 'setQuantity' function in DobaProductFile instead of just setting the quantity
			//$tempDPD->quantity(DobaProductFile::setQuantity($headers, $values, $supplied_qty));
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
				
				//TODO: Use the 'setPrice' function in DobaProductFile instead of just setting the price
				//$wholesale = $item['price'];
				//$map = $item['map'];
				//$msrp = $item['msrp'];
				//$tempDPD->price(DobaProductFile::setPrice($headers, $values, $wholesale, $map, $msrp));
				$tempDPD->price($item['price']);
				
				//TODO: Use the 'setQuantity' function in DobaProductFile instead of just setting the quantity
				//$tempDPD->quantity(DobaProductFile::setQuantity($headers, $values, $supplied_qty));
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