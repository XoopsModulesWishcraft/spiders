<?php
$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
$xoMod = $module_handler->getByDirname('spiders');
$xoConfig = $config_handler->getConfigList($xoMod->getVar('mid'));

define('XORTIFY_JSON_API', $xoConfig['xortify_urijson']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));
include_once($GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'));
$GLOBALS['sJSON'] = new Services_JSON();	

define('XOOPS_JSON_LIB', 'PHPJSON');

class JSONSpidersExchange {

	var $json_client;
	var $json_xoops_username = '';
	var $json_xoops_password = '';
	var $refresh = 600;
	
	function JSONSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		$xoModule = $module_handler->getByDirname('spiders');
		$configs = $config_handler->getConfigList($xoModule->mid());
		
		$this->json_xoops_username = $configs['xortify_username'];
		$this->json_xoops_password = $configs['xortify_password'];
		$this->refresh = $configs['xortify_records'];
			
	}
	
	function sendSpider($spider) {
		@$this->JSONSpidersExchange();
		$GLOBALS['sJSON'] = new Services_JSON();
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?spider='.urlencode($GLOBALS['sJSON']->encode(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "spider" => $spider ))));
			$result = spiders_obj2array($GLOBALS['sJSON']->decode($data));
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {
		@$this->JSONSpidersExchange();
		$GLOBALS['sJSON'] = new Services_JSON();
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?spiderstat='.urlencode($GLOBALS['sJSON']->encode(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "statistic" => $statistic ))));
			$result = spiders_obj2array($GLOBALS['sJSON']->decode($data));
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
		@$this->JSONSpidersExchange();
		$GLOBALS['sJSON'] = new Services_JSON();		
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?spiders='.urlencode($GLOBALS['sJSON']->encode(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
			$result = spiders_obj2array($GLOBALS['sJSON']->decode($data));
			break;
		}
		return $result['robots'];
	}
	
	function getSEOLinks() {
		@$this->JSONSpidersExchange();	
		$GLOBALS['sJSON'] = new Services_JSON();	
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?seolinks='.urlencode($GLOBALS['sJSON']->encode(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
			$result = spiders_obj2array($GLOBALS['sJSON']->decode($data));;
			break;
		}
		return $result['seolinks'];	
	}
	
}


?>