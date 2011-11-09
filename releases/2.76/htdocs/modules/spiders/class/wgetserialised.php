<?php
$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->getVar('mid'));

define('SPIDERS_SERIALISED_API', $GLOBALS['spidersModuleConfig']['spiders_uriserial']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));

define('XOOPS_SERIAL_LIB', 'PHPSERIALISE');

class WGETSERIALISEDSpidersExchange {

	var $json_client;
	var $json_xoops_username = '';
	var $json_xoops_password = '';
		
	function WGETSERIALISEDSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
		$configs = $config_handler->getConfigList($GLOBALS['spidersModule']->mid());
		
		$this->json_xoops_username = $configs['spiders_username'];
		$this->json_xoops_password = md5($configs['spiders_password']);
			
	}
	
	function sendSpider($spider) {
		@$this->WGETSERIALISEDSpidersExchange();
		switch (XOOPS_SERIAL_LIB){
		case "PHPSERIALISE":
			try {
				$data = file_get_contents(SPIDERS_SERIALISED_API.'?spider='.urlencode(serialize(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "spider" => $spider ))));
				$result = unserialize($data);
			}
			catch (Exception $err) { }
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {
		@$this->WGETSERIALISEDSpidersExchange();
		switch (XOOPS_SERIAL_LIB){
		case "PHPSERIALISE":
			try {
				$data = file_get_contents(SPIDERS_SERIALISED_API.'?spiderstat='.urlencode(serialize(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "statistic" => $statistic ))));
				$result = unserialize($data);
			}
			catch (Exception $err) { }
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
		@$this->WGETSERIALISEDSpidersExchange();		
		switch (XOOPS_SERIAL_LIB){
		case "PHPSERIALISE":
			try {
				$data = file_get_contents(SPIDERS_SERIALISED_API.'?spiders='.urlencode(serialize(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
				$result = unserialize($data);
			}
			catch (Exception $err) { }
			break;
		}
		return $result['robots'];
	}
	
	function getSEOLinks() {
		@$this->WGETSERIALISEDSpidersExchange();
		switch (XOOPS_SERIAL_LIB){
		case "PHPSERIALISE":
			try {
				$data = file_get_contents(SPIDERS_SERIALISED_API.'?seolinks='.urlencode(serialize(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
				$result = unserialize($data);
			}
			catch (Exception $err) { }
			break;
		}
		return $result['seolinks'];	
	}
	
}


?>