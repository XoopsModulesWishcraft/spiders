<?php
$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
$xoMod = $module_handler->getByDirname('spiders');
$xoConfig = $config_handler->getConfigList($xoMod->getVar('mid'));

define('XORTIFY_JSON_API', $xoConfig['spiders_urijson']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));

define('XOOPS_JSON_LIB', 'PHPJSON');

class JSONSpidersExchange {

	var $json_client;
	var $json_xoops_username = '';
	var $json_xoops_password = '';
		
	function JSONSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		$xoModule = $module_handler->getByDirname('spiders');
		$configs = $config_handler->getConfigList($xoModule->mid());
		
		$this->json_xoops_username = $configs['spiders_username'];
		$this->json_xoops_password = md5($configs['spiders_password']);
				
	}
	
	function sendSpider($spider) {
		@$this->JSONSpidersExchange();
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?spider='.urlencode(json_encode(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "spider" => $spider ))));
			$result = spiders_obj2array(json_decode($data));
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {
		@$this->JSONSpidersExchange();
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?spiderstat='.urlencode(json_encode(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "statistic" => $statistic ))));
			$result = spiders_obj2array(json_decode($data));
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
		@$this->JSONSpidersExchange();
		$GLOBALS['sJSON'] = new Services_JSON();		
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?spiders='.urlencode(json_encode(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
			$result = spiders_obj2array(json_decode($data));
			break;
		}
		return $result['robots'];
	}
	
	function getSEOLinks() {
		@$this->JSONSpidersExchange();	
		$GLOBALS['sJSON'] = new Services_JSON();	
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?seolinks='.urlencode(json_encode(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
			$result = spiders_obj2array(json_decode($data));;
			break;
		}
		return $result['seolinks'];	
	}
	
}


?>