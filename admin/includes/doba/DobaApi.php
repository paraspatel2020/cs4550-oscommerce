<?php
require_once('DobaAuth.inc');

define('DOBA_PAGE', 		'/api/xml_retailer_api.php');
define('DOBA_URL_SANDBOX', 	'https://sandbox.doba.com' . DOBA_PAGE);
define('DOBA_URL_LIVE', 	'https://www.doba.com' . DOBA_PAGE);

define('DOBA_API_ACTION_GETSUPPLIERS', 			'getSuppliers');
define('DOBA_API_ACTION_GETBRANDS', 			'getBrands');
define('DOBA_API_ACTION_GETQTYOPTIONS', 		'getQtyOptions');
define('DOBA_API_ACTION_GETPRICERANGES', 		'getPriceRanges');
define('DOBA_API_ACTION_GETPRODUCTSEARCH', 		'getProductSearch');
define('DOBA_API_ACTION_GETPRODUCTDETAIL', 		'getProductDetail');
define('DOBA_API_ACTION_GETPRODUCTINVENTORY', 	'getProductInventory');
define('DOBA_API_ACTION_GETWATCHLISTS', 		'getWatchlists');
define('DOBA_API_ACTION_ORDERLOOKUP', 			'orderLookup');
define('DOBA_API_ACTION_CREATEORDER', 			'createOrder');
define('DOBA_API_ACTION_FUNDORDER', 			'fundOrder');
define('DOBA_API_ACTION_GETORDERDETAIL', 		'getOrderDetail');

class DobaApi {
	var $api_url;
	var $username;
	var $password;
	var $retailer_id;
	var $enabled;
	
	var $requestXml = '';
	var $responseXml = '';
	
	var $errors = array();
	
	function DobaApi() {
		$this->api_url = (DOBA_TEST_MODE_ENABLED === true) ? DOBA_URL_SANDBOX : DOBA_URL_LIVE;
		$this->username = DOBA_USERNAME;
		$this->password = DOBA_PASSWORD;
		$this->retailer_id = DOBA_RETAILER_ID;
		$this->enabled = DOBA_API_ENABLED;
	}
	
	function isEnabled() {
		return $this->enabled;
	}
	
	function addErrorMsg($msg) {
		$this->errors[] = trim($msg);
	}
	
	function hasErrors() {
		return (count($this->errors) > 0);
	}
	
	function getErrors() {
		return $this->errors;
	}
	
	function clearErrors() {
		$this->errors = array();
	}
	
	function sendRequest() {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return false;
		} else if (trim($this->requestXml) == '') {
			$this->addErrorMsg('You must compile the request XML before you can send it.');
			return false;
		}
		
		$headers = array(
            "POST " . DOBA_PAGE . " HTTP/1.0",
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: ".strlen($this->requestXml)
        );
		
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'osCommerce Doba API Plugin');
       
        // Apply the XML to our curl call
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestXml);

        $this->responseXml = curl_exec($ch);

        if (curl_errno($ch)) {
        	$this->addErrorMsg('API Error: ' . curl_error($ch));
			curl_close($ch);
			return false;
        }

		curl_close($ch);
		return true;
	}
	
	function getRequestXml() {
		return $this->requestXml;
	}
	
	function getResponseXml() {
		return $this->responseXml;
	}
	
	function compileRequestXml($action, $data=array()) {
		$action = trim($action);
		
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return false;
		} else if ($action == '') {
			$this->addErrorMsg('An "action" is required to compile the xml.');
			return false;
		} else if (!defined('DOBA_API_ACTION_' . strtoupper($action))) {
			$this->addErrorMsg('"' . $action . '" does not exist or is not available for use.');
			return false;
		} //else if (count($data) == 0) {
			//$this->addErrorMsg('No data available to submit to the Doba API.');
			//return false;
		//}

		$data_xml = trim($this->dataToXml($action, $data));
		
		//if ($data_xml == '') {
		//	$this->addErrorMsg('No XML was compiled from the data submitted.');
		//	return false;
		//}
	
		$this->requestXml = '
			<dce>
				<request>
					<authentication>
						<username>' . $this->username . '</username>
						<password>' . $this->password . '</password>
					</authentication>
					<retailer_id>' . $this->retailer_id . '</retailer_id>
					<action>' . $action . '</action>				
					' . $data_xml . '
				</request>
			</dce>';
			echo '<pre>';
			echo $this->requestXml;
			echo '</pre>';
		return true;
	}
	
	function dataToXml($action, $data=array()) {
		$action = trim($action);
		
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if ($action == '') {
			$this->addErrorMsg('An "action" is required to compile the xml.');
			return '';
		} else if (!defined('DOBA_API_ACTION_' . strtoupper($action))) {
			$this->addErrorMsg('"' . $action . '" does not exist or is not available for use.');
			return '';
		}// else if (count($data) == 0) {
		//	$this->addErrorMsg('No data available to submit to the Doba API.');
		//	return '';
		//}
		
		$method = $action . 'Xml';
		
		if (!method_exists($this, $method)) {
			$this->addErrorMsg('"' . $method . '" is not available for use.');
			return '';
		}
		
		return $this->$method($data);
	}
	
	function getSuppliersXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}
		
		// compile the getSuppliers XML code
		$xml = '';
		
		return $xml;
	}
	
	function getBrandsXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}
		
		// compile the getBrands XML code
		$xml = '';
		
		return $xml;
	}

	function getQtyOptionsXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}
		
		// compile the getQtyOptions XML code
		$xml = '';
		
		return $xml;
	}

	function getPriceRangesXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}
		
		// compile the getPriceRanges XML code
		$xml = '';
		
		return $xml;
	}

	function getProductSearchXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}
		
		// compile the getProductSearch XML code
		$xml = '';
		
		return $xml;
	}

	function getProductDetailXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}
		
		// compile the getProductDetail XML code
		$xml = '<watchlists>
					<watchlist>'.$data['watchlist_id'].'</watchlist>
				</watchlists>';
		
		return $xml;
	}

	/**
	 * A function to compile the XML request for a product inventory.
	 * 		param should have the watchlist ID that is beeing requested.
	 * @return an XML string
	 * @param $data Object[optional]
	 */
	function getProductInventoryXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}

		// compile the getProductInventory XML code
		$xml = '';
		
		return $xml;
	}

	function getWatchlistsXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} //else if (count($data) == 0) {
			//$this->addErrorMsg('No data available to submit to the Doba API.');
		//	return '';
		//}
		
		// compile the getWatchLists XML code
		$xml = ''; // According to API doc this should be enough....
		
		return $xml;
	}

	function orderLookupXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}
		
		// compile the orderLookup XML code
		$xml = '
			<shipping_state>' . $data['state'] . '</shipping_state>
			<shipping_postal>' . $data['postal'] . '</shipping_postal>
			<shipping_country>' . $data['country'] . '</shipping_country>
		';
		
		if (isset($data['items']) && is_array($data['items']) && count($data['items']) > 0) {
			$xml .= '<items>';
			foreach ($data['items'] as $item) {
				$xml .= '		
					<item>
	        			<item_id>' . $item['item_id'] . '</item_id>
						<quantity>' . $item['quantity'] . '</quantity>
					</item>
				';	
			}
			$xml .= '</items>';
		}	

		return $xml;
	}

	function createOrderXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}
		
		// compile the createOrder XML code
		$xml = '
			<po_number>' .  $data['po_number'] . '</po_number>
			<shipping_firstname>' . $data['first_name'] . '</shipping_firstname>
			<shipping_lastname>' . $data['last_name'] . '</shipping_lastname>
			<shipping_street>' . $data['address1'] . ' ' . $data['address2'] . '</shipping_street>
			<shipping_city>' . $data['city'] . '</shipping_city>
			<shipping_state>' . $data['state'] . '</shipping_state>
			<shipping_postal>' . $data['postal'] . '</shipping_postal>
			<shipping_country>' . $data['country'] . '</shipping_country>
			';

		if (isset($data['items']) && is_array($data['items']) && count($data['items']) > 0) {
			$xml .= '<items>';
			foreach ($data['items'] as $item) {
				$xml .= '		
					<item>
	        			<item_id>' . $item['item_id'] . '</item_id>
						<quantity>' . $item['quantity'] . '</quantity>
					</item>
				';	
			}
			$xml .= '</items>';
		}	
		
		return $xml;
	}

	function fundOrderXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}

		// compile the fundOrder XML code
		$xml = '
        	<order_ids>
		    	<order_id>' . $data['order_id'] .'</order_id>
			</order_ids>
			<fund_method>default_payment_account</fund_method>
		';
		
		return $xml;
	}

	function getOrderDetailXml($data=array()) {
		if (!$this->isEnabled()) {
			$this->addErrorMsg('Doba API is not enabled.');
			return '';
		} else if (count($data) == 0) {
			$this->addErrorMsg('No data available to submit to the Doba API.');
			return '';
		}
		
		// compile the getOrderDetail XML code
		$xml = '
        	<order_ids>
		    	<order_id>' . $data['order_id'] .'</order_id>
			</order_ids>
		';
		
		return $xml;
	}
}
?>
