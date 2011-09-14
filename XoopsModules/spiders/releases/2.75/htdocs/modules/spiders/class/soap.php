<?php

$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
$xoMod = $module_handler->getByDirname('spiders');
$xoConfig = $config_handler->getConfigList($xoMod->getVar('mid'));

define('XORTIFY_API_LOCAL', $xoConfig['spiders_urisoap']);
define('XORTIFY_API_URI', $xoConfig['spiders_urisoap']);

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
		$xoModule = $module_handler->getByDirname('spiders');
		$configs = $config_handler->getConfigList($xoModule->mid());
		
		$this->soap_xoops_username = $configs['spiders_username'];
		$this->soap_xoops_password = $configs['spiders_password'];
				
		switch (XOOPS_SOAP_LIB){
		case "PHPSOAP":
			$this->soap_client = new soapclient(NULL, array('location' => XORTIFY_API_LOCAL, 'uri' => XORTIFY_API_URI));
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
			catch (Errors $err) { }
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
			catch (Errors $err) { }
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
			catch (Errors $err) { }
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
			catch (Errors $err) { }
			break;
		}
			
		return $result['seolinks'];		
	}
}


?>