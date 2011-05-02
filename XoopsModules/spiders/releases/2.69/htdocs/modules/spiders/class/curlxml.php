<?php


foreach (get_loaded_extensions() as $ext){
	if ($ext=="curl")
		$nativecurl=true;
}

if ($nativecurl==true) {
	define('XOOPS_CURLXML_LIB', 'PHPCURLXML');
	define('XORTIFY_CURLXML_USERAGENT', 'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.9.0.2) XOOPS/20100101 XoopsAuth/1.xx (php)');
}

$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
$xoMod = $module_handler->getByDirname('spiders');
$xoConfig = $config_handler->getConfigList($xoMod->getVar('mid'));

define('XORTIFY_CURLXML_API', $xoConfig['spiders_urixml']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));

class CURLXMLSpidersExchange {

	var $curl_client;
	var $curl_xoops_username = '';
	var $curl_xoops_password = '';
	
	function CURLXMLSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		$xoModule = $module_handler->getByDirname('spiders');
		$configs = $config_handler->getConfigList($xoModule->mid());
		
		$this->curl_xoops_username = $configs['spiders_username'];
		$this->curl_xoops_password = md5($configs['spiders_password']);
		
		if (!$ch = curl_init(XORTIFY_CURLXML_API)) {
			trigger_error('Could not intialise CURL file: '.$url);
			return false;
		}
		$cookies = XOOPS_VAR_PATH.'/cache/xoops_cache/authcurl_'.md5(XORTIFY_CURLXML_API).'.cookie'; 

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, XORTIFY_CURLXML_USERAGENT); 
		$this->curl_client =& $ch;			
	}
	
	function sendSpider($spider) {
		@$this->CURLXMLSpidersExchange();
		switch (XOOPS_CURLXML_LIB){
		case "PHPCURLXML":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spider='.spiders_toXml(array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "spider"	=> 	$spider	), 'spider'));
			$data = curl_exec($this->curl_client);
			xoops_error($data, 'cURL Result');
			exit(0);
			curl_close($this->curl_client);
			$result = spiders_elekey2numeric(spiders_xml2array($data), 'spider');		
			break;
		}
			
		return $result['spider'];	
	}
	
	function sendStatistic($statistic) {
		@$this->CURLXMLSpidersExchange();
		switch (XOOPS_CURLXML_LIB){
		case "PHPCURLXML":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spiderstat='.spiders_toXml(array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "statistic"	=> 	$statistic	), 'spiderstat'));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);
			$result = spiders_elekey2numeric(spiders_xml2array($data), 'spiderstat');		
			break;
		}
			
		return $result['spiderstat'];	
	}
	
	function getSpiders() {
		@$this->CURLXMLSpidersExchange();		
		switch (XOOPS_CURLXML_LIB){
		case "PHPCURLXML":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'spiders='.spiders_toXml(array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password ), 'spiders'));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);
			$result = spiders_elekey2numeric(spiders_xml2array($data), 'spiders');		
			break;
		}
			
		return $result['spiders']['robots'];
	}
	
	function getSEOLinks() {
		@$this->CURLXMLSpidersExchange();		
		switch (XOOPS_CURLXML_LIB){
		case "PHPCURLXML":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'seolinks='.spiders_toXml(array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password ), 'seolinks'));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);	
			$result = spiders_elekey2numeric(spiders_xml2array($data), 'seolinks');		
			break;
		}			
		return $result['seolinks']['seolinks'];		
	}
	
}


?>