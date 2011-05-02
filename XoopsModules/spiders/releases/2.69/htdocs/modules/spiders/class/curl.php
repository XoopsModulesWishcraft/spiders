<?php


foreach (get_loaded_extensions() as $ext){
	if ($ext=="curl")
		$nativecurl=true;
}

if ($nativecurl==true) {
	define('XORTIFY_CURLJSON_LIB', 'PHPCURLJSON');
	define('XORTIFY_CURLJSON_USERAGENT', 'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.9.0.2) XOOPS/20100101 XoopsAuth/1.xx (php)');
}

$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
$xoMod = $module_handler->getByDirname('spiders');
$xoConfig = $config_handler->getConfigList($xoMod->getVar('mid'));

define('XORTIFY_CURLJSON_API', $xoConfig['spiders_uricurl']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));

class CURLSpidersExchange {

	var $curl_client;
	var $curl_xoops_username = '';
	var $curl_xoops_password = '';
	
	function CURLSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		$xoModule = $module_handler->getByDirname('spiders');
		$configs = $config_handler->getConfigList($xoModule->mid());
		
		$this->curl_xoops_username = $configs['spiders_username'];
		$this->curl_xoops_password = md5($configs['spiders_password']);
		
		if (!$ch = curl_init(XORTIFY_CURLJSON_API)) {
			trigger_error('Could not intialise CURL file: '.$url);
			return false;
		}
		$cookies = XOOPS_VAR_PATH.'/cache/xoops_cache/authcurl_'.md5(XORTIFY_CURLJSON_API).'.cookie'; 

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, XORTIFY_CURLJSON_USERAGENT); 
		$this->curl_client =& $ch;			
	}
	
	function sendSpider($spider) {
		@$this->CURLSpidersExchange();
		switch (XORTIFY_CURLJSON_LIB){
		case "PHPCURLJSON":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spider='.json_encode(array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "spider"	=> 	$spider	)));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);
			$result = spiders_obj2array(json_decode($data));		
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {
		@$this->CURLSpidersExchange();
		switch (XORTIFY_CURLJSON_LIB){
		case "PHPCURLJSON":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spiderstat='.json_encode(array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "statistic"	=> 	$statistic	)));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);
			$result = spiders_obj2array(json_decode($data));		
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
		@$this->CURLSpidersExchange();		
		switch (XORTIFY_CURLJSON_LIB){
		case "PHPCURLJSON":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spiders='.json_encode(array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password )));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);
			$result = spiders_obj2array(json_decode($data));		
			break;
		}
			
		return $result['robots'];
	}
	
	function getSEOLinks() {
		@$this->CURLSpidersExchange();		
		switch (XORTIFY_CURLJSON_LIB){
		case "PHPCURLJSON":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'seolinks='.json_encode(array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password )));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);	
			$result = spiders_obj2array(json_decode($data));		
		
			break;
		}			
		return $result['seolinks'];		
	}
	
}


?>