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

foreach (get_loaded_extensions() as $ext){
	if ($ext=="curl")
		$nativecurl=true;
}

$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->getVar('mid'));

if ($nativecurl==true) {
	define('SPIDERS_CURLJSON_LIB', 'PHPCURLJSON');
	define('SPIDERS_CURLJSON_USERAGENT', 'Mozilla/5.0 '.ucfirst($GLOBALS['spidersModule']->getVar('dirname')) . '/' . number_format($GLOBALS['spidersModule']->getVar('version')/100,2). ' ' . XOOPS_VERSION . ' - XoopsAuth/1 (php)');
}

define('SPIDERS_CURLJSON_API', $GLOBALS['spidersModuleConfig']['spiders_uricurl']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));

class CURLSpidersExchange {

	var $curl_client;
	var $curl_xoops_username = '';
	var $curl_xoops_password = '';
	
	function CURLSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
		if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->mid());
		
		$this->curl_xoops_username = $GLOBALS['spidersModuleConfig']['spiders_username'];
		$this->curl_xoops_password = md5($GLOBALS['spidersModuleConfig']['spiders_password']);
		
		if (!$ch = curl_init(SPIDERS_CURLJSON_API)) {
			trigger_error('Could not intialise CURL file: '.$url);
			return false;
		}
		$cookies = XOOPS_VAR_PATH.'/cache/xoops_cache/authcurl_'.md5(SPIDERS_CURLJSON_API).'.cookie'; 

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, SPIDERS_CURLJSON_USERAGENT); 
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1500);
		$this->curl_client =& $ch;			
	}
	
	function sendSpider($spider) {
		@$this->CURLSpidersExchange();
		switch (SPIDERS_CURLJSON_LIB){
		case "PHPCURLJSON":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spider='.json_encode(array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "spider"	=> 	$spider	)));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);
			
				if (!function_exists('json_decode')) {
					if (!class_exists('Services_JSON') ) { include_once $GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'); }
					$json = new Services_JSON();
					$result = spiders_obj2array($json->decode($data));
				} else {
					$result = spiders_obj2array(json_decode($data));	
			}
				}
			catch (Exception $err) { }		
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {
		@$this->CURLSpidersExchange();
		switch (SPIDERS_CURLJSON_LIB){
		case "PHPCURLJSON":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spiderstat='.json_encode(array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "statistic"	=> 	$statistic	)));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);
			
				if (!function_exists('json_decode')) {
					if (!class_exists('Services_JSON') ) { include_once $GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'); }
					$json = new Services_JSON();
					$result = spiders_obj2array($json->decode($data));
				} else {
					$result = spiders_obj2array(json_decode($data));	
			}
				}
			catch (Exception $err) { }		
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
		@$this->CURLSpidersExchange();		
		switch (SPIDERS_CURLJSON_LIB){
		case "PHPCURLJSON":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spiders='.json_encode(array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password )));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);
			
				if (!function_exists('json_decode')) {
					if (!class_exists('Services_JSON') ) { include_once $GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'); }
					$json = new Services_JSON();
					$result = spiders_obj2array($json->decode($data));
				} else {
					$result = spiders_obj2array(json_decode($data));	
			}
				}
			catch (Exception $err) { }		
			break;
		}
			
		return $result['robots'];
	}
	
	function getSEOLinks() {
		@$this->CURLSpidersExchange();		
		switch (SPIDERS_CURLJSON_LIB){
		case "PHPCURLJSON":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'seolinks='.json_encode(array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password )));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);	
				if (!function_exists('json_encode')) {
					if (!class_exists('Services_JSON') ) { include_once $GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'); }
					$json = new Services_JSON();
					$result = spiders_obj2array($json->encode($data));
				} else {
					$result = spiders_obj2array(json_encode($data));	
			}		
			}
			catch (Exception $err) { }		
			break;
		}			
		return $result['seolinks'];		
	}
	
}


?>