<?php

$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->getVar('mid'));

define('SPIDERS_API_LOCAL', $GLOBALS['spidersModuleConfig']['spiders_urisoap']);
define('SPIDERS_API_URI', $GLOBALS['spidersModuleConfig']['spiders_urisoap']);

foreach (get_loaded_extensions() as $ext){
	if ($ext=="soap")
		$nativesoap=true;
}

if ($nativesoap==true)
	define('XOOPS_SOAP_LIB', 'PHPSOAP');

class SOAPSpidersExchange {

	var $soap_client;
	var $soap_xoops_username = '';
	var $soap_xoops_password = '';
	
	function SOAPSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
		if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->mid());
		
		$this->soap_xoops_username = $GLOBALS['spidersModuleConfig']['spiders_username'];
		$this->soap_xoops_password = $GLOBALS['spidersModuleConfig']['spiders_password'];
				
		switch (XOOPS_SOAP_LIB){
		case "PHPSOAP":
			$this->soap_client = new soapclient(NULL, array('location' => SPIDERS_API_LOCAL, 'uri' => SPIDERS_API_URI));
			break;
		}
	}
	
	function sendSpider($spider) {

		switch (XOOPS_SOAP_LIB){
		case "PHPSOAP":
			try {
				$result = $this->soap_client->__soapCall('spider',
					array(      "username"	=> 	$this->soap_xoops_username, 
								"password"	=> 	$this->soap_xoops_password, 
								"spider"	=> 	$spider	) );
			}
			catch (Exception $err) { }
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {

		switch (XOOPS_SOAP_LIB){
		case "PHPSOAP":
			try {
				$result = $this->soap_client->__soapCall('spiderstat',
					array(      "username"	=> 	$this->soap_xoops_username, 
								"password"	=> 	$this->soap_xoops_password, 
								"statistic"	=> 	$statistic	) );
			}
			catch (Exception $err) { }
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
			
		switch (XOOPS_SOAP_LIB){
		case "PHPSOAP":
			try {
				$result = $this->soap_client->__soapCall('spiders',
							array(  "username"	=> 	$this->soap_xoops_username, 
									"password"	=> 	$this->soap_xoops_password));
			}
			catch (Exception $err) { }
			break;
		}
			
		return $result['robots'];		
	}
	
	function getSEOLinks() {
			
		switch (XOOPS_SOAP_LIB){
		case "PHPSOAP":
			try {
				$result = $this->soap_client->__soapCall('seolinks',
							array(  "username"	=> 	$this->soap_xoops_username, 
									"password"	=> 	$this->soap_xoops_password));
			}
			catch (Exception $err) { }
			break;
		}
			
		return $result['seolinks'];		
	}
}


?>