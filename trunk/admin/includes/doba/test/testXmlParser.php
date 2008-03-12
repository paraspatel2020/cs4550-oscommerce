<?php
ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaProductAPI.php');
/*
 class XMLParser  {
   
    // raw xml
    private $rawXML;
    // xml parser
    private $parser = null;
    // array returned by the xml parser
    private $valueArray = array();
    private $keyArray = array();
   
    // arrays for dealing with duplicate keys
    private $duplicateKeys = array();
   
    // return data
    private $output = array();
    private $status;

    public function XMLParser($xml){
        $this->rawXML = $xml;
        $this->parser = xml_parser_create();
        return $this->parse();
    }

    private function parse(){
       
        $parser = $this->parser;
       
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); // Dont mess with my cAsE sEtTings
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);     // Dont bother with empty info
        if(!xml_parse_into_struct($parser, $this->rawXML, $this->valueArray, $this->keyArray)){
            $this->status = 'error: '.xml_error_string(xml_get_error_code($parser)).' at line '.xml_get_current_line_number($parser);
            return false;
        }
        xml_parser_free($parser);

        $this->findDuplicateKeys();

        // tmp array used for stacking
        $stack = array();        
        $increment = 0;
       
        foreach($this->valueArray as $val) {
            if($val['type'] == "open") {
                //if array key is duplicate then send in increment
                if(array_key_exists($val['tag'], $this->duplicateKeys)){
                    array_push($stack, $this->duplicateKeys[$val['tag']]);
                    $this->duplicateKeys[$val['tag']]++;
                }
                else{
                    // else send in tag
                    array_push($stack, $val['tag']);
                }
            } elseif($val['type'] == "close") {
                array_pop($stack);
                // reset the increment if they tag does not exists in the stack
                if(array_key_exists($val['tag'], $stack)){
                    $this->duplicateKeys[$val['tag']] = 0;
                }
            } elseif($val['type'] == "complete") {
                //if array key is duplicate then send in increment
                if(array_key_exists($val['tag'], $this->duplicateKeys)){
                    array_push($stack, $this->duplicateKeys[$val['tag']]);
                    $this->duplicateKeys[$val['tag']]++;
                }
                else{               
                    // else send in tag
                    array_push($stack,  $val['tag']);
                }
                $this->setArrayValue($this->output, $stack, $val['value']);
                array_pop($stack);
            }
            $increment++;
        }

        $this->status = 'success: xml was parsed';
        return true;

    }
   
    private function findDuplicateKeys(){
       
        for($i=0;$i < count($this->valueArray); $i++) {
            // duplicate keys are when two complete tags are side by side
            if($this->valueArray[$i]['type'] == "complete"){
                if( $i+1 < count($this->valueArray) ){
                    if($this->valueArray[$i+1]['tag'] == $this->valueArray[$i]['tag'] && $this->valueArray[$i+1]['type'] == "complete"){
                        $this->duplicateKeys[$this->valueArray[$i]['tag']] = 0;
                    }
                }
            }
            // also when a close tag is before an open tag and the tags are the same
            if($this->valueArray[$i]['type'] == "close"){
                if( $i+1 < count($this->valueArray) ){
                    if(    $this->valueArray[$i+1]['type'] == "open" && $this->valueArray[$i+1]['tag'] == $this->valueArray[$i]['tag'])
                        $this->duplicateKeys[$this->valueArray[$i]['tag']] = 0;
                }
            }
           
        }
       
    }
   
    private function setArrayValue(&$array, $stack, $value){
        if ($stack) {
            $key = array_shift($stack);
            $this->setArrayValue($array[$key], $stack, $value);
            return $array;
        } else {
            $array = $value;
        }
    }
   
    public function getOutput(){
        return $this->output;
    }
   
    public function getStatus(){
        return $this->status;   
    }
      
}
*/

$simple = "
<dce>
    <response>
        <outcome>Success</outcome>
        <products>
            <product>
                <product_id>1234</product_id>
                <title>blah title</title>
                
                <product_sku>5</product_sku>
                <upc>88821</upc>
                <brand>Bang</brand>
                
                <description>blah blah</description>
                <ship_width>2</ship_width>
                <ship_length>2</ship_length>
                <ship_height>2.1</ship_height>
                <ship_weight>50</ship_weight>
                <ship_cost>5.48</ship_cost>

                <items>            
                    <item>
                        <item_id>321</item_id>
                        <item_sku>888555</item_sku>
                        <name>Blarg</name>
                        <price>5.22</price>
                        <prepay_price>10.2</prepay_price>                
                        <msrp>7.0</msrp>
                        <map>2.1</map>
                        <qty_avail>10</qty_avail>
                        <stock>1</stock>
                        <last_update>10/5/2007</last_update>
                    </item>
                    <item>
                        <item_id>221</item_id>
                        <item_sku>8</item_sku>
                        <name>Blarg2</name>
                        <price>5.22</price>
                        <prepay_price>10.2</prepay_price>                
                        <msrp>7.0</msrp>
                        <map>2.1</map>
                        <qty_avail>11</qty_avail>
                        <stock>1</stock>
                        <last_update>10/5/2007</last_update>
                    </item>					
                </items>
                
                <images>
                    <image>					
                        <url>www.boo.org/big</url>
                        <thumb_url>www.boo.org/sml_pic</thumb_url>
                        <image_height>20</image_height>
                        <image_width>22</image_width>
                        <default>1</default>
                    </image>
                    <image>					
                        <url>www.boo.org/big_pic</url>
                        <thumb_url>www.boo.org/sml_pic</thumb_url>
                        <image_height>20</image_height>
                        <image_width>22</image_width>
                        <default>1</default>
                    </image>					
                </images>                
				
                <supplier_id>54</supplier_id>
                <supplier_name>Hi</supplier_name>
                
            </product>
            <product>
                <product_id>134</product_id>
                <title>blah title</title>
                
                <product_sku>4</product_sku>
                <upc>8821</upc>
                <brand>Bana</brand>
                
                <description>blah blah</description>
                <ship_width>2</ship_width>
                <ship_length>2</ship_length>
                <ship_height>2.1</ship_height>
                <ship_weight>40</ship_weight>
                <ship_cost>5.40</ship_cost>

                <items>            
                    <item>
                        <item_id>31</item_id>
                        <item_sku>888555</item_sku>
                        <name>Blarg</name>
                        <price>5.02</price>
                        <prepay_price>10.2</prepay_price>                
                        <msrp>7.0</msrp>
                        <map>2.1</map>
                        <qty_avail>10</qty_avail>
                        <stock>1</stock>
                        <last_update>10/5/2007</last_update>
                    </item>
                    <item>
                        <item_id>21</item_id>
                        <item_sku>8</item_sku>
                        <name>Blarg2</name>
                        <price>5.12</price>
                        <prepay_price>10.2</prepay_price>                
                        <msrp>7.0</msrp>
                        <map>2.1</map>
                        <qty_avail>11</qty_avail>
                        <stock>1</stock>
                        <last_update>10/5/2007</last_update>
                    </item>					
                </items>
                
                <images>
                    <image>					
                        <url>www.boon.org/big</url>
                        <thumb_url>www.boon.org/sml_pic</thumb_url>
                        <image_height>20</image_height>
                        <image_width>22</image_width>
                        <default>0</default>
                    </image>
                    <image>					
                        <url>www.foo.org/big_pic</url>
                        <thumb_url>www.foo.org/sml_pic</thumb_url>
                        <image_height>20</image_height>
                        <image_width>22</image_width>
                        <default>1</default>
                    </image>					
                </images>                
				
                <supplier_id>54</supplier_id>
                <supplier_name>Hi</supplier_name>
                
            </product>				
        </products>

    </response>
</dce>
";

echo "<pre>";
$p = DobaProductAPI::parseProductDetails($simple);

print_r($p);
echo "</pre>";

?>