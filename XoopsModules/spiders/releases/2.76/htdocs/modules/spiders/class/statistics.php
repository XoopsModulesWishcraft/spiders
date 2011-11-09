<?php

if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
/**
 * Class for Spiders
 * @author Simon Roberts (simon@xoops.org)
 * @copyright copyright (c) 2000-2009 XOOPS.org
 * @package kernel
 */
class SpidersStatistics extends XoopsObject
{

    function SpidersStatistics($fid = null)
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('uri', XOBJ_DTYPE_UNICODE_TXTBOX, null, false, 65535);
        $this->initVar('useragent', XOBJ_DTYPE_UNICODE_TXTBOX, null, false, 255);
        $this->initVar('netaddy', XOBJ_DTYPE_UNICODE_TXTBOX, null, false, 255);
        $this->initVar('ip', XOBJ_DTYPE_UNICODE_TXTBOX, null, false, 65535);
        $this->initVar('server-ip', XOBJ_DTYPE_UNICODE_TXTBOX, null, false, 65535);
        $this->initVar('when', XOBJ_DTYPE_INT, time(), false);
        $this->initVar('sitename', XOBJ_DTYPE_UNICODE_TXTBOX, null, false, 255);		
    }


}


/**
* XOOPS Spider handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS user class objects.
*
* @author  Simon Roberts <simon@xoops.org>
* @package kernel
*/
class SpidersStatisticsHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
        parent::__construct($db, "spiders_statistics", 'SpidersStatistics', "id", "uri");
    }
	
	function apimethod() {
		$config_handler =& xoops_gethandler('config');
		$module_handler =& xoops_gethandler('module');
		if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
		if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->getVar('mid'));
		return $GLOBALS['spidersModuleConfig']['protocol'];
	}

	function insert($obj, $force=true) {
		xoops_load('xoopscache');
		if (!class_exists('XoopsCache'))
			xoops_load('cache');
		$read = XoopsCache::read('spider_id%%'.$obj->getVar('id'));
		if (!is_array($read)) {
			$value = '0A';
		} else {
			$value = $read['value'];
		}
		$value++;
		$read = XoopsCache::delete('spider_id%%'.$obj->getVar('id'));
		$read = XoopsCache::write('spider_id%%'.$obj->getVar('id'), array('value' => $value));	
		
		$modulehandler =& xoops_gethandler('module');
		$confighandler =& xoops_gethandler('config');
		if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $modulehandler->getByDirname('spiders');
		if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $confighandler->getConfigList($GLOBALS['spidersModule']->getVar('mid'),false);

		if ($GLOBALS['spidersModuleConfig']['spiders_shareme']==true&&$GLOBALS['spidersModuleConfig']['spiders_api']==true) {
			
			// Connect to API
			include_once($GLOBALS['xoops']->path('/modules/spiders/class/'.$GLOBALS['spidersModuleConfig']['protocol'].'.php'));
			$func = strtoupper($GLOBALS['spidersModuleConfig']['protocol']).'SpidersExchange';
			$exchange = new $func;
			
			//Form Associated Array
			$spiders_handler =& xoops_getmodulehandler('spiders', 'spiders');
			$spider = $spiders_handler->get($obj->getVar('id'));
			if (!$notfound = XoopsCache::read('spiders_exists_in_api%%'.$obj->getVar('id'))) 
			{
				$notfound=0;
				if (!$spiders = XoopsCache::read('spiders_off_api')) {
					$spiders = $exchange->getSpiders();
					XoopsCache::write('spiders_off_api', $spiders, 60*60*24*5);
				}
				foreach($spiders as $id => $apispider) {
					$criteria = new CriteriaCompo();
					$part = $spider_handler->safeAgent($apispider['robot-useragent']);
					foreach(array(';','/',',','/','(',')',' ') as $split) {
						$ret= array();
						foreach(explode($split, $part) as $value) {
							$ret[] = $value;
						}
						$part = implode(' ',$ret);
					}
	
					foreach($ret as $value) { 
						if (!is_numeric((substr($value,0,1)))&&(substr($value,0,1))!='x')
							if (!empty($value)) {
								$uagereg[] = strtolower($value);
								$uageregb[] = $value;
							}
					}
		
					$part = $spider_handler->safeAgent($spider->getVar('robot-useragent'));
					foreach(array(';','/',',','\\','(',')',' ') as $split) {
						$usersafeagent = array();
						foreach(explode($split, $part) as $value) {
							$usersafeagent[] = $value;
						}
						$part = implode(' ',$usersafeagent);
					}
					$usersafeagent = explode(' ', $part);
					$match=0;
					foreach($uagereg as $uaid => $ireg) {		
						if((in_array($ireg, $usersafeagent)||strpos(strtolower(' '.$part), strtolower($ireg)))&&is_object($GLOBALS['xoopsUser'])) {
							$match++;			
						}
					}		
			
					if (intval($match/count($uagereg)*100)<intval($GLOBALS['spidersModuleConfig']['match_percentile'])) {
						$notfound++;
					}
				}
				if ($notfound>=count($spiders)) {
					$exchange->sendSpider($spider->toArray());
					XoopsCache::write('spiders_exists_in_api%%'.$obj->getVar('id'), true, 60*60*24*7*12);
				}
			}
							
			$ret = array();
			$ret['useragent'] = $obj->getVar('useragent');
			$ret['netaddy'] = $obj->getVar('netaddy');
			$ret['ip'] = $obj->getVar('ip');
			$ret['server-ip'] = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
			$obj->setVar('server-ip', isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']);
			$ret['when'] = $obj->getVar('when');
			$ret['uri'] = $obj->getVar('uri');
			$ret['sitename'] = $GLOBALS['xoopsConfig']['sitename'];
			$ret['robot-name'] = $spider->getVar('robot-name');
			$ret['robot-id'] = $spider->getVar('robot-id');			
			
			//Send to API
			$exchange->sendStatistic($ret);
		}
		
		// Clear Statistics - Save on database size
		$modulehandler = xoops_gethandler('module');
		$confighandler = xoops_gethandler('config');
		if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $modulehandler->getByDirname('spiders');
		if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $confighandler->getConfigList($GLOBALS['spidersModule']->getVar('mid'));	
		$criteria = new Criteria('when', time() - ($GLOBALS['spidersModuleConfig']['weeks_stats'] *(60*60*24*7)), '<');
		$this->deleteAll($criteria, true);
								
		return parent::insert($obj, $force);
	}
}
?>