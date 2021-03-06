<?php
if (!function_exists('json_encode')){
	function json_encode($data) {
		static $json = NULL;
		if (!class_exists('Services_JSON') ) { include_once $GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'); }
		$json = new Services_JSON();
		return $json->encode($data);
	}
}

if (!function_exists('json_decode')){
	function json_decode($data) {
		static $json = NULL;
		if (!class_exists('Services_JSON') ) { include_once $GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'); }
		$json = new Services_JSON();
		return $json->decode($data);
	}
}

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
			try {
				$data = file_get_contents(XORTIFY_JSON_API.'?spider='.urlencode(json_encode(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "spider" => $spider ))));
			
				if (!function_exists('json_decode')) {
					if (!class_exists('Services_JSON') ) { include_once $GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'); }
					$json = new Services_JSON();
					$result = spiders_obj2array($json->decode($data));
				} else {
					$result = spiders_obj2array(json_decode($data));	
			}
				}
			catch (Errors $err) { }
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {
		@$this->JSONSpidersExchange();
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			try {
				$data = file_get_contents(XORTIFY_JSON_API.'?spiderstat='.urlencode(json_encode(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "statistic" => $statistic ))));
			
				if (!function_exists('json_decode')) {
					if (!class_exists('Services_JSON') ) { include_once $GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'); }
					$json = new Services_JSON();
					$result = spiders_obj2array($json->decode($data));
				} else {
					$result = spiders_obj2array(json_decode($data));	
			}
				}
			catch (Errors $err) { }
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
		@$this->JSONSpidersExchange();
		$GLOBALS['sJSON'] = new Services_JSON();		
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			try {
				$data = file_get_contents(XORTIFY_JSON_API.'?spiders='.urlencode(json_encode(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
			
				if (!function_exists('json_decode')) {
					if (!class_exists('Services_JSON') ) { include_once $GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'); }
					$json = new Services_JSON();
					$result = spiders_obj2array($json->decode($data));
				} else {
					$result = spiders_obj2array(json_decode($data));	
			}
				}
			catch (Errors $err) { }
			break;
		}
		return $result['robots'];
	}
	
	function getSEOLinks() {
		@$this->JSONSpidersExchange();	
		$GLOBALS['sJSON'] = new Services_JSON();	
		switch (XOOPS_JSON_LIB){
		case "PHPJSON":
			try {
				$data = file_get_contents(XORTIFY_JSON_API.'?seolinks='.urlencode(json_encode(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ))));
				if (!function_exists('json_decode')) {
					if (!class_exists('Services_JSON') ) { include_once $GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'); }
					$json = new Services_JSON();
					$result = spiders_obj2array($json->decode($data));
				} else {
					$result = spiders_obj2array(json_decode($data));	
			};
			}
			catch (Errors $err) { }
			break;
		}
		return $result['seolinks'];	
	}
	
}


?>