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
	define('XOOPS_CURLXML_LIB', 'PHPCURLXML');
	define('SPIDERS_CURLXML_USERAGENT', 'Mozilla/5.0 '.ucfirst($GLOBALS['spidersModule']->getVar('dirname')) . '/' . number_format($GLOBALS['spidersModule']->getVar('version')/100, 2). ' ' . XOOPS_VERSION . ' - XoopsAuth/1 (php)');
}

define('SPIDERS_CURLXML_API', $GLOBALS['spidersModuleConfig']['spiders_urixml']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));

class CURLXMLSpidersExchange {

	var $curl_client;
	var $curl_xoops_username = '';
	var $curl_xoops_password = '';
	
	function CURLXMLSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		
		if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
		if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->mid());
		
		$this->curl_xoops_username = $GLOBALS['spidersModuleConfig']['spiders_username'];
		$this->curl_xoops_password = md5($GLOBALS['spidersModuleConfig']['spiders_password']);
		
		if (!$ch = curl_init(SPIDERS_CURLXML_API)) {
			trigger_error('Could not intialise CURL file: '.$url);
			return false;
		}
		$cookies = XOOPS_VAR_PATH.'/cache/xoops_cache/authcurl_'.md5(SPIDERS_CURLXML_API).'.cookie'; 

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, SPIDERS_CURLXML_USERAGENT); 
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1500);
		$this->curl_client =& $ch;			
	}
	
	function sendSpider($spider) {
		@$this->CURLXMLSpidersExchange();
		switch (XOOPS_CURLXML_LIB){
		case "PHPCURLXML":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spider='.spiders_toXml(array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "spider"	=> 	$spider	), 'spider'));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);
				$result = spiders_elekey2numeric(spiders_xml2array($data), 'spider');
			}
			catch (Exception $err) { }		
			break;
		}
			
		return $result['spider'];	
	}
	
	function sendStatistic($statistic) {
		@$this->CURLXMLSpidersExchange();
		switch (XOOPS_CURLXML_LIB){
		case "PHPCURLXML":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spiderstat='.spiders_toXml(array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "statistic"	=> 	$statistic	), 'spiderstat'));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);
				$result = spiders_elekey2numeric(spiders_xml2array($data), 'spiderstat');
			}
			catch (Exception $err) { }		
			break;
		}
			
		return $result['spiderstat'];	
	}
	
	function getSpiders() {
		@$this->CURLXMLSpidersExchange();		
		switch (XOOPS_CURLXML_LIB){
		case "PHPCURLXML":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spiders='.spiders_toXml(array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password ), 'spiders'));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);
				$result = spiders_elekey2numeric(spiders_xml2array($data), 'spiders');
			}
			catch (Exception $err) { }		
			break;
		}
			
		return $result['spiders']['robots'];
	}
	
	function getSEOLinks() {
		@$this->CURLXMLSpidersExchange();		
		switch (XOOPS_CURLXML_LIB){
		case "PHPCURLXML":
			try {
				curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'seolinks='.spiders_toXml(array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password ), 'seolinks'));
				$data = curl_exec($this->curl_client);
				curl_close($this->curl_client);	
				$result = spiders_elekey2numeric(spiders_xml2array($data), 'seolinks');
			}
			catch (Exception $err) { }		
			break;
		}			
		return $result['seolinks']['seolinks'];		
	}
	
}


?>