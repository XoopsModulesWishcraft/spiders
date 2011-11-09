<?php


defined('XOOPS_ROOT_PATH') or die('Restricted access');


class SpidersCorePreload extends XoopsPreloadItem
{
	function eventCoreIncludeCommonEnd($args)
	{
		global $spidersModule, $spidersModuleConfig;
		
		xoops_load('xoopscache');
		if (!class_exists('XoopsCache')) {
			// XOOPS 2.4 Compliance
			xoops_load('cache');
			if (!class_exists('XoopsCache')) {
				include_once XOOPS_ROOT_PATH.'/class/cache/xoopscache.php';		
			}
		}
	    $module_handler = xoops_gethandler('module');
	    $config_handler = xoops_gethandler('config');
	    if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
	    if (is_object($GLOBALS['spidersModule'])) {
	    	if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $config_handler->getConfigList($GLOBALS['spidersModule']->getVar('mid'));
			switch ($GLOBALS['spidersModuleConfig']['crontype']) {
				case 'preloader':
					if (!$read = XoopsCache::read('spiders_pause_preload')) {
						XoopsCache::write('spiders_pause_preload', true, $GLOBALS['spidersModuleConfig']['croninterval']);
						$GLOBALS['spiders_preloader']=true;
						ob_start();
						include(XOOPS_ROOT_PATH.'/modules/spiders/cron/serverup.php');
						ob_end_clean();
					}
					break;
			}
	    }
	    
		if (SpidersCorePreload::isActive()) {
			include_once XOOPS_ROOT_PATH . ( '/modules/spiders/post.loader.spiders.php' );
		}
	}
	
	function eventCoreFooterEnd($args)
	{
		if (SpidersCorePreload::isActive()) {
			include_once XOOPS_ROOT_PATH . ( '/modules/spiders/post.loader.footer.php' );
		}
	}
	
    function eventCoreHeaderCacheEnd($args)
    {
		if (SpidersCorePreload::isActive()) {
			include_once XOOPS_ROOT_PATH . ( '/modules/spiders/post.loader.footer.php' );
		}
    }
    
	function isActive()
	{
		$module_handler =& xoops_getHandler('module');
		$module = $module_handler->getByDirname('spiders');
		return ($module && $module->getVar('isactive')) ? true : false;
	}
}

?>