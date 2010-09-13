<?php

define('XORTIFY_JSON_API', 'http://www.xortify.com/json/');

include_once($GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'));	

ini_set('allow_furl_open', true);

if (ini_get('allow_furl_open')==true)
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
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?spider='.urlencode(Services_JSON::encode(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "spider" => $spider ))));
			$result = Services_JSON::decode($data);
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {
		@$this->JSONSpidersExchange();
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?spiderstat='.urlencode(Services_JSON::encode(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "statistic" => $statistic ))));
			$result = Services_JSON::decode($data);
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
		@$this->JSONSpidersExchange();		
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?spiders='.urlencode(Services_JSON::encode(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
			$result = Services_JSON::decode($data);
			break;
		}
		return $result['robots'];
	}
	
	function getSEOLinks() {
		@$this->JSONSpidersExchange();		
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			$data = file_get_contents(XORTIFY_JSON_API.'?seolinks='.urlencode(Services_JSON::encode(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
			$result = Services_JSON::decode($data);
			break;
		}
		return $result['seolinks'];	
	}
}

?>