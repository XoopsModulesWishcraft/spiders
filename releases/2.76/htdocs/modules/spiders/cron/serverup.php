<?php
/**
 * @package     Spider
 * @subpackage  module
 * @description	Sector Network Security Drone for Robots
 * @author	    Simon Roberts WISHCRAFT <simon@chronolabs.coop>
 * @copyright	copyright (c) 2010-2013 XOOPS.org
 * @licence		GPL 2.0 - see docs/LICENCE.txt
 * @cron		Run at Least Once an Hour to five minutes!
 */

	set_time_limit(1800);
	
	function spiders_getURLData($URI, $curl=false) {
		$ret = '';
		try {
			switch ($curl) {
				case true:
					if (!$ch = curl_init($URI)) {
						trigger_error('Could not intialise CURL file: '.$url);
						return false;
					}
					$cookies = XOOPS_VAR_PATH.'/cache/xoops_cache/croncurl_'.md5($URI).'.cookie'; 
			
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies); 
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
					curl_setopt($ch, CURLOPT_TIMEOUT_MS, 900);
					curl_setopt($ch, CURLOPT_USERAGENT, XORTIFY_USER_AGENT);
					$ret = curl_exec($ch);
					curl_close($ch);
					break;
				case false:
					$ret = file_get_contents($uri);
					break;
				
			}
		}
		catch(Exception $e) {
			echo 'Exception: "'.$e."\n";
		}	
		return $ret;
	}
	
	define('XORTIFY_USER_AGENT', 'Mozilla/5.0 (PHP) Xortify 2.5.x XOOPS/20100101');
	define("SERVER1", 'http://xortify.chronolabs.coop/unban/');
	define("SERVER2", 'http://xortify.com/unban/');
	define("SERVER3", 'http://xortify.xoops.org/unban/');
	define("REPLACE", 'unban');
	define("SOAP", 'soap');
	define("CURL", 'curl');
	define("JSON", 'json');
	define("SERIAL", 'serial');
	define("XML", 'xml');
	define("SEARCHFOR", 'Solve Puzzel:');
	
	foreach (get_loaded_extensions() as $ext){
		if ($ext=="curl")
			$nativecurl=true;
	}

	if (!isset($GLOBALS['spiders_preloader'])) {
		$xoopsOption["nocommon"]=true;
		require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/mainfile.php');
		
		defined('DS') or define('DS', DIRECTORY_SEPARATOR);
		defined('NWLINE')or define('NWLINE', "\n");
		
		global $xoops, $xoopsPreload, $xoopsLogger, $xoopsErrorHandler, $xoopsSecurity, $sess_handler;
	
		include_once XOOPS_ROOT_PATH . DS . 'include' . DS . 'defines.php';
		include_once XOOPS_ROOT_PATH . DS . 'include' . DS . 'version.php';
		include_once XOOPS_ROOT_PATH . DS . 'include' . DS . 'license.php';
		
		require_once XOOPS_ROOT_PATH . DS . 'class' . DS . 'xoopsload.php';
		
		XoopsLoad::load('preload');
		$xoopsPreload =& XoopsPreload::getInstance();
		
		XoopsLoad::load('xoopskernel');
		$xoops = new xos_kernel_Xoops2();
		$xoops->pathTranslation();
		$xoopsRequestUri =& $_SERVER['REQUEST_URI'];// Deprecated (use the corrected $_SERVER variable now)
		
		XoopsLoad::load('xoopssecurity');
		$xoopsSecurity = new XoopsSecurity();
		$xoopsSecurity->checkSuperglobals();
		
		XoopsLoad::load('xoopslogger');
		$xoopsLogger =& XoopsLogger::getInstance();
		$xoopsErrorHandler =& XoopsLogger::getInstance();
		$xoopsLogger->startTime();
		$xoopsLogger->startTime('XOOPS Boot');
		
		include_once $xoops->path('kernel/object.php');
		include_once $xoops->path('class/criteria.php');
		include_once $xoops->path('class/module.textsanitizer.php');
		include_once $xoops->path('include/functions.php');
		
		include_once $xoops->path('class/database/databasefactory.php');
		$GLOBALS['xoopsDB'] =& XoopsDatabaseFactory::getDatabaseConnection();
	}
		
	$module_handler =& xoops_gethandler('module');
	$configitem_handler =& xoops_gethandler('configitem');
	$GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
	if (is_object($GLOBALS['spidersModule'])) {
		$source = spiders_getURLData(SERVER1, $nativecurl);
		if (strpos(strtolower($source), strtolower(SEARCHFOR))>0) {
			
			echo 'Server 1 is UP - check @ '.SERVER1;
			
			$GLOBALS['spidersModule']->setVar('isactive', true);
			
			$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
			$criteria->add(new Criteria('conf_name', 'spiders_urisoap'));
			$xoConfig = $configitem_handler->getObjects($criteria);
			if (is_object($xoConfig[0])) {
				$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, SOAP, SERVER1));
				$configitem_handler->insert($xoConfig[0], true);
			}
			
			$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
			$criteria->add(new Criteria('conf_name', 'spiders_uricurl'));
			$xoConfig = $configitem_handler->getObjects($criteria);
			if (is_object($xoConfig[0])) {
				$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, CURL, SERVER1));
				$configitem_handler->insert($xoConfig[0], true);
			}
			
			$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
			$criteria->add(new Criteria('conf_name', 'spiders_urijson'));
			$xoConfig = $configitem_handler->getObjects($criteria);
			if (is_object($xoConfig[0])) {
				$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, JSON, SERVER1));
				$configitem_handler->insert($xoConfig[0], true);
			}
			
			$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
			$criteria->add(new Criteria('conf_name', 'spiders_uriserial'));
			$xoConfig = $configitem_handler->getObjects($criteria);
			if (is_object($xoConfig[0])) {
				$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, SERIAL, SERVER1));
				$configitem_handler->insert($xoConfig[0], true);
			}
			
			$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
			$criteria->add(new Criteria('conf_name', 'spiders_urixml'));
			$xoConfig = $configitem_handler->getObjects($criteria);
			if (is_object($xoConfig[0])) {
				$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, XML, SERVER1));
				$configitem_handler->insert($xoConfig[0], true);
			}
			
			$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
			$criteria->add(new Criteria('conf_name', 'spiders_api'));
			$xoConfig = $configitem_handler->getObjects($criteria);
			if (is_object($xoConfig[0])) {
				$xoConfig[0]->setVar('conf_value', true);
				$configitem_handler->insert($xoConfig[0], true);
			}
			
		} else {
			$source = spiders_getURLData(SERVER2, $nativecurl);;
			if (strpos(strtolower($source), strtolower(SEARCHFOR))>0) {
				
				echo 'Server 2 is UP - check @ '.SERVER2;
				
				$GLOBALS['spidersModule']->setVar('isactive', true);

				$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
				$criteria->add(new Criteria('conf_name', 'spiders_urisoap'));
				$xoConfig = $configitem_handler->getObjects($criteria);
				if (is_object($xoConfig[0])) {
					$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, SOAP, SERVER2));
					$configitem_handler->insert($xoConfig[0], true);
				}
				
				$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
				$criteria->add(new Criteria('conf_name', 'spiders_uricurl'));
				$xoConfig = $configitem_handler->getObjects($criteria);
				if (is_object($xoConfig[0])) {
					$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, CURL, SERVER2));
					$configitem_handler->insert($xoConfig[0], true);
				}
				
				$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
				$criteria->add(new Criteria('conf_name', 'spiders_urijson'));
				$xoConfig = $configitem_handler->getObjects($criteria);
				if (is_object($xoConfig[0])) {
					$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, JSON, SERVER2));
					$configitem_handler->insert($xoConfig[0], true);
				}
				
				$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
				$criteria->add(new Criteria('conf_name', 'spiders_uriserial'));
				$xoConfig = $configitem_handler->getObjects($criteria);
				if (is_object($xoConfig[0])) {
					$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, SERIAL, SERVER2));
					$configitem_handler->insert($xoConfig[0], true);
				}
				
				$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
				$criteria->add(new Criteria('conf_name', 'spiders_urixml'));
				$xoConfig = $configitem_handler->getObjects($criteria);
				if (is_object($xoConfig[0])) {
					$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, XML, SERVER2));
					$configitem_handler->insert($xoConfig[0], true);
				}
				
				$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
				$criteria->add(new Criteria('conf_name', 'spiders_api'));
				$xoConfig = $configitem_handler->getObjects($criteria);
				if (is_object($xoConfig[0])) {
					$xoConfig[0]->setVar('conf_value', true);
					$configitem_handler->insert($xoConfig[0], true);
				}
				
			} else {
				$source = spiders_getURLData(SERVER3, $nativecurl);;
				if (strpos(strtolower($source), strtolower(SEARCHFOR))>0) {
					
					echo 'Server 3 is UP - check @ '.SERVER3;
					
					$GLOBALS['spidersModule']->setVar('isactive', true);
	
					$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
					$criteria->add(new Criteria('conf_name', 'spiders_urisoap'));
					$xoConfig = $configitem_handler->getObjects($criteria);
					if (is_object($xoConfig[0])) {
						$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, SOAP, SERVER3));
						$configitem_handler->insert($xoConfig[0], true);
					}
					
					$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
					$criteria->add(new Criteria('conf_name', 'spiders_uricurl'));
					$xoConfig = $configitem_handler->getObjects($criteria);
					if (is_object($xoConfig[0])) {
						$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, CURL, SERVER3));
						$configitem_handler->insert($xoConfig[0], true);
					}
					
					$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
					$criteria->add(new Criteria('conf_name', 'spiders_urijson'));
					$xoConfig = $configitem_handler->getObjects($criteria);
					if (is_object($xoConfig[0])) {
						$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, JSON, SERVER3));
						$configitem_handler->insert($xoConfig[0], true);
					}
					
					$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
					$criteria->add(new Criteria('conf_name', 'spiders_uriserial'));
					$xoConfig = $configitem_handler->getObjects($criteria);
					if (is_object($xoConfig[0])) {
						$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, SERIAL, SERVER3));
						$configitem_handler->insert($xoConfig[0], true);
					}
					
					$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
					$criteria->add(new Criteria('conf_name', 'spiders_urixml'));
					$xoConfig = $configitem_handler->getObjects($criteria);
					if (is_object($xoConfig[0])) {
						$xoConfig[0]->setVar('conf_value', str_replace(REPLACE, XML, SERVER3));
						$configitem_handler->insert($xoConfig[0], true);
					}

					$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
					$criteria->add(new Criteria('conf_name', 'spiders_api'));
					$xoConfig = $configitem_handler->getObjects($criteria);
					if (is_object($xoConfig[0])) {
						$xoConfig[0]->setVar('conf_value', true);
						$configitem_handler->insert($xoConfig[0], true);
					}
					
				} else {
					$criteria = new CriteriaCompo(new Criteria('conf_modid', $GLOBALS['spidersModule']->getVar('mid')));
					$criteria->add(new Criteria('conf_name', 'spiders_api'));
					$xoConfig = $configitem_handler->getObjects($criteria);
					if (is_object($xoConfig[0])) {
						$xoConfig[0]->setVar('conf_value', false);
						$configitem_handler->insert($xoConfig[0], true);
					}
				}
			}
		}	
		$module_handler->insert($GLOBALS['spidersModule'], true);
	}
?>