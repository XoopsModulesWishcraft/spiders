<?php


defined('XOOPS_ROOT_PATH') or die('Restricted access');


class SpidersCorePreload extends XoopsPreloadItem
{
	function eventCoreIncludeCommonEnd($args)
	{
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
	    $xoMod = $module_handler->getByDirname('spiders');
	    if (is_object($xoMod)) {
	    	$xoConfig = $config_handler->getConfigList($xoMod->getVar('mid'));
			switch ($xoConfig['crontype']) {
				case 'preloader':
					if (!$read = XoopsCache::read('spiders_pause_preload')) {
						XoopsCache::write('spiders_pause_preload', true, $xoConfig['croninterval']);
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
	
	
	function isActive()
	{
		$module_handler =& xoops_getHandler('module');
		$module = $module_handler->getByDirname('spiders');
		return ($module && $module->getVar('isactive')) ? true : false;
	}
}

?>