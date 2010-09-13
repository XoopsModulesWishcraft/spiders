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
	
	
	if (is_object($GLOBALS['xoopsUser'])) {
		
		$modulehandler =& xoops_gethandler('module');
		$confighandler =& xoops_gethandler('config');
		$xoModule = $modulehandler->getByDirname('spiders');
		$xoConfig = $confighandler->getConfigList($xoModule->getVar('mid'),false);
	
		if (in_array($xoConfig['bot_group'], $GLOBALS['xoopsUser']->getGroups())) 
			if ($xoConfig['xortify_shareme']==true) {
				
				xoops_load('cache');
				$result = XoopsCache::read('spider_uid%%'.$GLOBALS['xoopsUser']->getVar('uid').'%%'.$xoConfig['bot_group']);
			
				if (!is_array($result)) {
					// Connect to API
					$api = spiders_apimethod();
					include_once($GLOBALS['xoops']->path('/modules/spiders/class/'.$api.'.php'));
					$func = strtoupper($api).'SpidersExchange';
					$exchange = new $func;
					
					//Recieve From API
					$result = $exchange->getSEOLinks();
					XoopsCache::write('spider_uid%%'.$GLOBALS['xoopsUser']->getVar('uid').'%%'.$xoConfig['bot_group'], $result, 3600);
				}
				$GLOBALS['xoopsTpl']->assign('spiderseo', $result);
				$GLOBALS['xoopsTpl']->display('db:spiders_footer_seo.html');
			}
	}		
	
	if (!function_exists('spiders_apimethod')) {
		function spiders_apimethod() {
			foreach (get_loaded_extensions() as $ext){
				if ($ext=="soap")
					return $ext;
			}
			foreach (get_loaded_extensions() as $ext){
				if ($ext=="curl")
					return $ext;
			}
			return 'json';
		}
	}
?>