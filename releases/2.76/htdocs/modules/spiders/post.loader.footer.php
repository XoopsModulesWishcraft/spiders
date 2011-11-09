<?php

// $Author: wishcraft $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Simon Roberts (AKA wishcraft)                                     //
// URL: http://www.chronolabs.org.au                                         //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
	
	
	$results = NULL;
	
	if (is_object($GLOBALS['xoopsUser'])) {
		$modulehandler =& xoops_gethandler('module');
		$confighandler =& xoops_gethandler('config');
		if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $modulehandler->getByDirname('spiders');
		if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $confighandler->getConfigList($GLOBALS['spidersModule']->getVar('mid'),false);
		if (in_array($GLOBALS['spidersModuleConfig']['bot_group'], $GLOBALS['xoopsUser']->getGroups())) 
			if ($GLOBALS['spidersModuleConfig']['spiders_shareme']==true&&$GLOBALS['spidersModuleConfig']['spiders_api']==true) {
				xoops_load('cache');
				if (!$result = XoopsCache::read('spider_uid%%'.$GLOBALS['xoopsUser']->getVar('uid').'%%'.$GLOBALS['spidersModuleConfig']['bot_group'])) {
					// Connect to API
					include_once($GLOBALS['xoops']->path('/modules/spiders/class/'.$GLOBALS['spidersModuleConfig']['protocol'].'.php'));
					$func = strtoupper($GLOBALS['spidersModuleConfig']['protocol']).'SpidersExchange';
					$exchange = new $func;
					
					//Recieve From API
					$result = $exchange->getSEOLinks();
					XoopsCache::write('spider_uid%%'.$GLOBALS['xoopsUser']->getVar('uid').'%%'.$GLOBALS['spidersModuleConfig']['bot_group'], $result, 1200);
				}
				$GLOBALS['spiderTpl'] = new XoopsTpl();
				$GLOBALS['spiderTpl']->assign('spiderseo', $result);
				$GLOBALS['spiderTpl']->display('db:spiders_footer_seo.html');
			}
	}		
	
?>