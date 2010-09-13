<?php

foreach (get_loaded_extensions() as $ext){
	if ($ext=="curl")
		$nativecurl=true;
}

if ($nativecurl==true) {
	define('XOOPS_CURL_LIB', 'PHPCURL');
	define('XORTIFY_USER_AGENT', 'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.9.0.2) XOOPS/20100101 XoopsAuth/1.xx (php)');
}

class CURLSpidersExchange {

	var $curl_client;
	var $curl_xoops_username = '';
	var $curl_xoops_password = '';
	var $refresh = 600;
	
	function CURLSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		$xoModule = $module_handler->getByDirname('spiders');
		$configs = $config_handler->getConfigList($xoModule->mid());
		
		$this->curl_xoops_username = $configs['xortify_username'];
		$this->curl_xoops_password = $configs['xortify_password'];
		$this->refresh = $configs['xortify_records'];

		if (!$ch = curl_init(XORTIFY_CURL_API)) {
			trigger_error('Could not intialise CURL file: '.$url);
			return false;
		}
		$cookies = XOOPS_VAR_PATH.'/cache/xoops_cache/authcurl_'.md5($url).'.cookie'; 

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, XORTIFY_USER_AGENT); 
		$this->curl_client =& $ch;			
	}
	
	function sendSpider($spider) {
		@$this->CURLSpidersExchange();
		switch (XOOPS_CURL_LIB){
		case "PHPCURL":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, array('spider' => array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "spider"	=> 	$spider	)));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);
			include_once($GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'));	
			$result = Services_JSON::decode($data);		
			break;
		}
			
		return $result;	
	}
	
	function sendStatistic($statistic) {
		@$this->CURLSpidersExchange();
		switch (XOOPS_CURL_LIB){
		case "PHPCURL":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, array('spiderstat' => array(      "username"	=> 	$this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password, "statistic"	=> 	$statistic	)));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);
			include_once($GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'));	
			$result = Services_JSON::decode($data);		
			break;
		}
			
		return $result;	
	}
	
	function getSpiders() {
		@$this->CURLSpidersExchange();		
		switch (XOOPS_CURL_LIB){
		case "PHPCURL":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS,array('spiders' => array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password )));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);
			include_once($GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'));	
			$result = Services_JSON::decode($data);
			break;
		}
			
		return $result['robots'];
	}
	
	function getSEOLinks() {
		@$this->CURLSpidersExchange();		
		switch (XOOPS_CURL_LIB){
		case "PHPCURL":
			curl_setopt($this->curl_client, CURLOPT_POSTFIELDS,array('seolinks' => array( "username" => $this->curl_xoops_username, "password"	=> 	$this->curl_xoops_password )));
			$data = curl_exec($this->curl_client);
			curl_close($this->curl_client);
			include_once($GLOBALS['xoops']->path('/modules/spiders/include/JSON.php'));	
			$result = Services_JSON::decode($data);
			break;
		}
			
		return $result['seolinks'];		
	}
}

?>