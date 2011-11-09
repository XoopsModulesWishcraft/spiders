<?php
$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->getVar('mid'));

define('SPIDERS_WGETXML_API', $GLOBALS['spidersModuleConfig']['spiders_urixml']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));

define('XOOPS_XML_LIB', 'PHPXML');

class WGETXMLSpidersExchange {

	var $json_client;
	var $json_xoops_username = '';
	var $json_xoops_password = '';
	
	function WGETXMLSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
		if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->mid());
		
		$this->json_xoops_username = $GLOBALS['spidersModuleConfig']['spiders_username'];
		$this->json_xoops_password = md5($GLOBALS['spidersModuleConfig']['spiders_password']);
			
	}
	
	function sendSpider($spider) {
		@$this->WGETXMLSpidersExchange();
		switch (XOOPS_XML_LIB){
		case "PHPXML":
			try {
				$data = file_get_contents(SPIDERS_WGETXML_API.'?spider='.urlencode(spiders_toXml(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "spider" => $spider ), 'spider')));
				$result = spiders_elekey2numeric(spiders_xml2array($data), 'spider');
			}
			catch (Exception $err) { }
			break;
		}
			
		return $result['spider'];	
	}
	
	function sendStatistic($statistic) {
		@$this->WGETXMLSpidersExchange();
		switch (XOOPS_XML_LIB){
		case "PHPXML":
			try {
				$data = file_get_contents(SPIDERS_WGETXML_API.'?spiderstat='.urlencode(spiders_toXml(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "statistic" => $statistic ), 'spiderstat')));
				$result = spiders_elekey2numeric(spiders_xml2array($data), 'spiderstat');
			}
			catch (Exception $err) { }
			break;
		}
			
		return $result['spiderstat'];	
	}
	
	function getSpiders() {
		@$this->WGETXMLSpidersExchange();
		switch (XOOPS_XML_LIB){
		case "PHPXML":
			try {
				$data = file_get_contents(SPIDERS_WGETXML_API.'?spiders='.urlencode(spiders_toXml(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ), 'spiders')));
				$result = spiders_elekey2numeric(spiders_xml2array($data), 'spiders');
			}
			catch (Exception $err) { }
			break;
		}
		return $result['spiders']['robots'];
	}
	
	function getSEOLinks() {
		@$this->WGETXMLSpidersExchange();		
		switch (XOOPS_XML_LIB){
		case "PHPXML":
			try {
				$data = file_get_contents(SPIDERS_WGETXML_API.'?seolinks='.urlencode(spiders_toXml(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ), 'seolinks')));
				$result = spiders_elekey2numeric(spiders_xml2array($data), 'seolinks');;
			}
			catch (Exception $err) { }
			break;
		}
		return $result['seolinks']['seolinks'];	
	}
	
}


?>