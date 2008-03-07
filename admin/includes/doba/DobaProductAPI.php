<?php
//ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaProductData.php');
include_once('doba/DobaProducts.php');

class DobaProductAPI {
	var $currentTag ='';
	
	/**
	 * Default constructor
	 * @return 
	 */
	function DobaProductAPI () {
				
	}

	function startElement($parser, $name, $attrs) 
	{
		switch($name)
		{	
		case 'outcome':
			$currentTag = $name;
			break;
		case 'products':
			$currentTag = $name;
			break;	
		case 'product':
			$currentTag = $name;
			break;			
		default:
		
		}
	}
	
	function endElement($parser, $name) 
	{

	}
	
	function characterData($parser, $data) 
	{
	   switch ($currentTag)
	   {
	   	case 'product':
			parseProductDetails($data);
			break;
	   	case 'watchlist':
			parseWatchlistDetails($data);
			break;			
		default:
			
	   }
	   	
	}
	
	function parseXML($filename)
	{
		
		$xml_parser = xml_parser_create();
		// use case-folding so we are sure to find the tag in $map_array (To upper)
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($xml_parser, "characterData");
		
		if (!($fp = fopen($filename, "r"))) {
		    die("could not open XML input");
		}
		
		while ($data = fread($fp, 4096)) {
		    if (!xml_parse($xml_parser, $data, feof($fp))) {
		        die(sprintf("XML error: %s at line %d",
		                    xml_error_string(xml_get_error_code($xml_parser)),
		                    xml_get_current_line_number($xml_parser)));
		    }
		}
		xml_parser_free($xml_parser);		
	}
	
	function parseProductDetails($data)
	{
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
                
                <images>
                    <image>
                        </url><!-- large image -->
                        </thumb_url><!-- thumbnail image -->
                        </image_height>
                        </image_width>
                        </default><!-- 1 if this image is the default -->
                    </image>
                <images>
                
                <supplier_id>
                <supplier_name>     
			</product>           
		 */
	}
	
	function parseWatchlistDetails($data)
	{
		
	}
}
?>