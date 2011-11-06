<?php
// $Author$
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

$modversion['name'] = _MI_SPIDERS_NAME;
$modversion['version'] = 1.19;
$modversion['releasedate'] = "Wednesday: May 20, 2010";
$modversion['description'] = _MI_SPIDERS_DESCRIPTION;
$modversion['author'] = "Wishcraft";
$modversion['credits'] = "Chronolabs";
$modversion['help'] = "spiders.html";
$modversion['license'] = "GPL";
$modversion['official'] = 1;
$modversion['status']  = "Stable";
$modversion['image'] = "images/spiders_slogo.png";
$modversion['dirname'] = _MI_SPIDERS_DIRNAME;

$modversion['author_realname'] = "Simon Roberts";
$modversion['author_website_url'] = "http://www.chronolabs.org.au";
$modversion['author_website_name'] = "Chronolabs Australia";
$modversion['author_email'] = "simon@chronolabs.org.au";
$modversion['demo_site_url'] = "Chronolabs Australia";
$modversion['demo_site_name'] = "";
$modversion['support_site_url'] = "http://www.chronolabs.org.au/forums/viewforum.php?forum=30";
$modversion['support_site_name'] = "Chronolabs";
$modversion['submit_bug'] = "http://www.chronolabs.org.au/forums/viewforum.php?forum=30";
$modversion['submit_feature'] = "http://www.chronolabs.org.au/forums/viewforum.php?forum=30";
$modversion['usenet_group'] = "sci.chronolabs";
$modversion['maillist_announcements'] = "";
$modversion['maillist_bugs'] = "";
$modversion['maillist_features'] = "";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

$modversion['onUpdate'] = "include/update.php";
$modversion['onInstall'] = "include/install.php";
$modversion['onUninstall'] = "include/uninstall.php";

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/spiders.sql";

// $modversion['sqlfile']['postgresql'] = "sql/pgsql.sql";
// Tables created by sql file (without prefix!)
$modversion['tables'][0] = "spiders";
$modversion['tables'][1] = "spiders_user";

// Templates
$modversion['templates'][1]['file'] = 'spiders_index.html';
$modversion['templates'][1]['description'] = 'Main Spiders Index Page';

// Menu
$modversion['sub'][1]['name'] = _MI_SPIDERS_MM1;
$modversion['sub'][1]['url'] = "index.php?op=statistics";
$modversion['sub'][2]['name'] = _MI_SPIDERS_MM2;
$modversion['sub'][2]['url'] = "index.php?op=lastin";

$modversion['hasMain'] = 0;

$i++;
$options=array();
$modversion['config'][$i]['name'] = 'reserved_prases';
$modversion['config'][$i]['title'] = "_MI_SPIDER_RESERVEDPHRASES";
$modversion['config'][$i]['description'] = "_MI_SPIDER_RESERVEDPHRASES_DESC";
$modversion['config'][$i]['formtype'] = 'textarea';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = 'Mozilla/5.0|Mozilla/4.0|Mozilla/3.0|Mozilla/3.01|Mozilla/2.0|Mozilla|mozilla';

$i++;
$options=array();
for($i=1;$i<101;$i++)
	$options[$i.'%'] = $i;
$modversion['config'][$i]['name'] = 'match_percentile';
$modversion['config'][$i]['title'] = "_MI_SPIDER_MATCHPERCENTILE";
$modversion['config'][$i]['description'] = "_MI_SPIDER_MATCHPERCENTILE_DESC";
$modversion['config'][$i]['formtype'] = 'select';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 60;
$modversion['config'][$i]['options'] = $options;

?>
