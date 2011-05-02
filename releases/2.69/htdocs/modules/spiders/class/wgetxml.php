<?php
$config_handler =& xoops_gethandler('config');
$module_handler =& xoops_gethandler('module');
$xoMod = $module_handler->getByDirname('spiders');
$xoConfig = $config_handler->getConfigList($xoMod->getVar('mid'));

define('XORTIFY_WGETXML_API', $xoConfig['spiders_urixml']);

include_once($GLOBALS['xoops']->path('/modules/spiders/include/functions.php'));

define('XOOPS_XML_LIB', 'PHPXML');

class WGETXMLSpidersExchange {

	var $json_client;
	var $json_xoops_username = '';
	var $json_xoops_password = '';
	
	function WGETXMLSpidersExchange () {
	
		$module_handler =& xoops_gethandler('module');
		$config_handler =& xoops_gethandler('config');
		$xoModule = $module_handler->getByDirname('spiders');
		$configs = $config_handler->getConfigList($xoModule->mid());
		
		$this->json_xoops_username = $configs['spiders_username'];
		$this->json_xoops_password = md5($configs['spiders_password']);
			
	}
	
	function sendSpider($spider) {
		@$this->WGETXMLSpidersExchange();
		switch (XOOPS_XML_LIB){
		case "PHPXML":
			$data = file_get_contents(XORTIFY_WGETXML_API.'?spider='.urlencode(spiders_toXml(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "spider" => $spider ), 'spider')));
			$result = spiders_elekey2numeric(spiders_xml2array($data), 'spider');
			break;
		}
			
		return $result['spider'];	
	}
	
	function sendStatistic($statistic) {
		@$this->WGETXMLSpidersExchange();
		switch (XOOPS_XML_LIB){
		case "PHPXML":
			$data = file_get_contents(XORTIFY_WGETXML_API.'?spiderstat='.urlencode(spiders_toXml(array("username" => $this->json_xoops_username, "password"	=> $this->json_xoops_password, "statistic" => $statistic ), 'spiderstat')));
			$result = spiders_elekey2numeric(spiders_xml2array($data), 'spiderstat');
			break;
		}
			
		return $result['spiderstat'];	
	}
	
	function getSpiders() {
		@$this->WGETXMLSpidersExchange();
		switch (XOOPS_XML_LIB){
		case "PHPXML":
			$data = file_get_contents(XORTIFY_WGETXML_API.'?spiders='.urlencode(spiders_toXml(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ), 'spiders')));
			$result = spiders_elekey2numeric(spiders_xml2array($data), 'spiders');
			break;
		}
		return $result['spiders']['robots'];
	}
	
	function getSEOLinks() {
		@$this->WGETXMLSpidersExchange();		
		switch (XOOPS_XML_LIB){
		case "PHPXML":
			$data = file_get_contents(XORTIFY_WGETXML_API.'?seolinks='.urlencode(spiders_toXml(array( "username" => $this->json_xoops_username, "password"	=> 	$this->json_xoops_password ), 'seolinks')));
			$result = spiders_elekey2numeric(spiders_xml2array($data), 'seolinks');;
			break;
		}
		return $result['seolinks']['seolinks'];	
	}
	
}


?>