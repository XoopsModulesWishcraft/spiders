<?php
$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
$xoMod = $module_handler->getByDirname('spiders');
$xoConfig = $config_handler->getConfigList($xoMod->getVar('mid'));

define('XORTIFY_SERIALISED_API', $xoConfig['spiders_uriserial']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));

define('XOOPS_SERIAL_LIB', 'PHPSERIALISE');

class WGETSERIALISEDSpidersExchange {

	var $json_client;
	var $json_xoops_username = '';
	var $json_xoops_password = '';
		
	function WGETSERIALISEDSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		$xoModule = $module_handler->getByDirname('spiders');
		$configs = $config_handler->getConfigList($xoModule->mid());
		
		$this->json_xoops_username = $configs['spiders_username'];
		$this->json_xoops_password = md5($configs['spiders_password']);
			
	}
	
	function sendSpider($spider) {
		@$this->WGETSERIALISEDSpidersExchange();
		switch (XOOPS_SERIAL_LIB){
		case "PHPSERIALISE":
			$data = file_get_contents(XORTIFY_SERIALISED_API.'?spider='.urlencode(serialize(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "spider" => $spider ))));
			$result = unserialize($data);
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {
		@$this->WGETSERIALISEDSpidersExchange();
		switch (XOOPS_SERIAL_LIB){
		case "PHPSERIALISE":
			$data = file_get_contents(XORTIFY_SERIALISED_API.'?spiderstat='.urlencode(serialize(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "statistic" => $statistic ))));
			$result = unserialize($data);
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
		@$this->WGETSERIALISEDSpidersExchange();		
		switch (XOOPS_SERIAL_LIB){
		case "PHPSERIALISE":
			$data = file_get_contents(XORTIFY_SERIALISED_API.'?spiders='.urlencode(serialize(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
			$result = unserialize($data);
			break;
		}
		return $result['robots'];
	}
	
	function getSEOLinks() {
		@$this->WGETSERIALISEDSpidersExchange();
		switch (XOOPS_SERIAL_LIB){
		case "PHPSERIALISE":
			$data = file_get_contents(XORTIFY_SERIALISED_API.'?seolinks='.urlencode(serialize(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
			$result = unserialize($data);
			break;
		}
		return $result['seolinks'];	
	}
	
}


?>