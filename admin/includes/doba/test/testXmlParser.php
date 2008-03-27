<?php
ini_set('include_path', ini_get('include_path').':'.$_SERVER['DOCUMENT_ROOT'].'/admin/includes/');
include_once('doba/DobaProductAPI.php');


$simple = "
<dce>
  <response>
    <outcome>success</outcome>
    <products>
      <product>
        <product_id>51696</product_id>
        <title>a</title>
        <description>&lt;li&gt;Allows accessories to be in neat application&lt;li&gt;Automatic switching to the active device without connecting and reconnecting&lt;li&gt;Matches with Playstation 2&#x99; unit</description>
        <product_sku>GEPS2CS</product_sku>
        <brand>GAME ELEMENTS</brand>
        <condition>new</condition>
        <freight>a</freight>
        <ship_width>8</ship_width>
        <ship_length>11</ship_length>
        <ship_height>4</ship_height>
        <ship_weight>2.16</ship_weight>
        <ship_cost>10.49</ship_cost>
        <items>
          <item>
            <item_id>64670</item_id>
            <supplier_id>a</supplier_id>
            <name>PS2&reg; Command Stand 2-in-1</name>
            <item_sku>GEPS2CS</item_sku>
            <map>0.00</map>
            <price>25.08</price>
            <prepay_price>24.00</prepay_price>
            <msrp>49.95</msrp>
            <qty_avail>3</qty_avail>
            <stock>in-stock</stock>
            <last_update>2008-02-20 17:06:10</last_update>
          </item>
        </items>
        <images>
          <image>
            <url>http://images.doba.com/products/1/geps2cs.jpg</url>
            <thumb_url>http://images.doba.com/products/1/_thumb/geps2cs.jpg</thumb_url>
            <image_height>200</image_height>
            <image_width>240</image_width>
            <default>1</default>
          </image>
        </images>
        <supplier_id>1</supplier_id>
        <supplier_name>Alpha</supplier_name>
        <supplier_processing>1-4 business days to ship out and receive tracking number.</supplier_processing>
        <supplier_alerts>a</supplier_alerts>
        <upc>026616065332</upc>
        <supplier_notes>This supplier does not ship to Hawaii or Alaska. 

Plasma TVs are not eligible for return to Alpha. Most TV/Monitors 30&#x94;/projections products and Samsung TV&#x92;s 14&#x94; or larger to not qualify under normal Alpha Return Policy. Contact Samsung @ 1-800-SAMSUNG for service. Most TV&#x92;s/Monitors 30&#x94; or larger and projection products require in-field service repairs under manufacturer&#x92;s warranty. These products are not eligible for return. Please contact the appropriate manufacturer for troubleshooting repair service.</supplier_notes>
        <supplier_drop_fee>2.50</supplier_drop_fee>
        <product_group>basic</product_group>
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