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
$module_handler = xoops_gethandler('module');
$GLOBALS['spidersModule'] = $module_handler->getByDirname('spiders');
$adminmenu = array();
$adminmenu[0]['title'] = _MI_SPIDERS_ADMINMENU0;
$adminmenu[0]['link'] = "admin/index.php?op=dashboard";
$adminmenu[0]['icon'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.dashboard.png';
$adminmenu[0]['image'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.dashboard.png';
$adminmenu[1]['title'] = _MI_SPIDERS_ADMINMENU1;
$adminmenu[1]['link'] = "admin/index.php?op=list";
$adminmenu[1]['icon'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.list.png';
$adminmenu[1]['image'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.list.png';
$adminmenu[2]['title'] = _MI_SPIDERS_ADMINMENU2;
$adminmenu[2]['link'] = "admin/index.php?op=add";
$adminmenu[2]['icon'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.add.png';
$adminmenu[2]['image'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.add.png';
$adminmenu[3]['title'] = _MI_SPIDERS_ADMINMENU3;
$adminmenu[3]['link'] = "admin/index.php?op=listmods";
$adminmenu[3]['icon'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.modifications.png';
$adminmenu[3]['image'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.modifications.png';
$adminmenu[4]['title'] = _MI_SPIDERS_ADMINMENU4;
$adminmenu[4]['link'] = "admin/index.php?op=import";
$adminmenu[4]['icon'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.import.png';
$adminmenu[4]['image'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.import.png';
$adminmenu[5]['title'] = _MI_SPIDERS_ADMINMENU5;
$adminmenu[5]['link'] = "admin/index.php?op=signup&fct=signup";
$adminmenu[5]['icon'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.signup.png';
$adminmenu[5]['image'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.signup.png';
$adminmenu[6]['title'] = _MI_SPIDERS_ADMINMENU6;
$adminmenu[6]['link'] = '../system/admin.php?fct=preferences&op=showmod&mod='.$GLOBALS['spidersModule']->getVar('mid');
$adminmenu[6]['icon'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.preferences.png';
$adminmenu[6]['image'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/spiders.preferences.png';
$adminmenu[7]['title'] = _MI_SPIDERS_ADMINMENU7;
$adminmenu[7]['link'] = "admin/index.php?op=about";
$adminmenu[7]['icon'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/about.png';
$adminmenu[7]['image'] = '../../'.$GLOBALS['spidersModule']->getInfo('icons32').'/about.png';

?>