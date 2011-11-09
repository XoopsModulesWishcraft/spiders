<?php


foreach (get_loaded_extensions() as $ext){
	if ($ext=="curl")
		$nativecurl=true;
}

$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->getVar('mid'));

if ($nativecurl==true) {
	define('XOOPS_CURLSERIALISED_LIB', 'PHPCURLSERIALISED');
	define('SPIDERS_CURLSERIAL_USERAGENT', 'Mozilla/5.0 '.ucfirst($GLOBALS['spidersModule']->getVar('dirname')) . '/' . number_format($GLOBALS['spidersModule']->getVar('version')/100, 2). ' ' . XOOPS_VERSION . ' - XoopsAuth/1 (php)');
}

define('SPIDERS_CURLSERIALISED_API', $GLOBALS['spidersModuleConfig']['spiders_uriserial']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));

class CURLSERIALISEDSpidersExchange {

	var $curl_client;
	var $curl_xoops_username = '';
	var $curl_xoops_password = '';
		
	function CURLSERIALISEDSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
		if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->mid());
		
		$this->curl_xoops_username = $GLOBALS['spidersModuleConfig']['spiders_username'];
		$this->curl_xoops_password = md5($GLOBALS['spidersModuleConfig']['spiders_password']);

		if (!$ch = curl_init(SPIDERS_CURLSERIALISED_API)) {
			trigger_error('Could not intialise CURLSERIALISED file: '.$url);
			return false;
		}
		$cookies = XOOPS_VAR_PATH.'/cache/xoops_cache/authcurl_'.md5(SPIDERS_CURLSERIALISED_API).'.cookie'; 

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, SPIDERS_CURLSERIAL_USERAGENT);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1500); 
		$this->curl_client =& $ch;			
	}
	
	function sendSpider($spider) {
		@$this->CURLSERIALISEDSpidersExchange();
		switch (XOOPS_CURLSERIALISED_LIB){
		case "PHPCURLSERIALISED":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spider='.serialize(array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "spider"	=> 	$spider	)));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);
				$result = unserialize($data);
			}
			catch (Exception $err) { }		
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {
		@$this->CURLSERIALISEDSpidersExchange();
		switch (XOOPS_CURLSERIALISED_LIB){
		case "PHPCURLSERIALISED":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spiderstat='.serialize(array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "statistic"	=> 	$statistic	)));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);
				$result = unserialize($data);
			}
			catch (Exception $err) { }
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
		@$this->CURLSERIALISEDSpidersExchange();		
		switch (XOOPS_CURLSERIALISED_LIB){
		case "PHPCURLSERIALISED":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spiders='.serialize(array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password )));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);
				$result = unserialize($data);
			}
			catch (Exception $err) { }		
			break;
		}
			
		return $result['robots'];
	}
	
	function getSEOLinks() {
		@$this->CURLSERIALISEDSpidersExchange();		
		switch (XOOPS_CURLSERIALISED_LIB){
		case "PHPCURLSERIALISED":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'seolinks='.serialize(array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password )));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);	
				$result = unserialize($data);
			}
			catch (Exception $err) { }		
			break;
		}			
		return $result['seolinks'];		
	}
	
}


?>