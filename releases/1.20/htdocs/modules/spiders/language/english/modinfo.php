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

	define('_MI_SPIDERS_DIRNAME','spiders');
	
	define('_MI_SPIDERS_NAME','Robot Manager');
	define('_MI_SPIDERS_DESCRIPTION','This module is for robot management');	
	define('_MI_SPIDERS_LOGON', 'Logon Session');
	define('_MI_SPIDERS_LOGONDESC', 'When a robots agent is detected log the bot in under its username.');	
	
	define('_MI_SPIDERS_MM1', 'Statistics');
	define('_MI_SPIDERS_MM2', 'Last Here Report');	
	define('_MI_SPIDERS_ADMINMENU1', 'Manage Robots');
	define('_MI_SPIDERS_ADMINMENU2', 'Add Robot');
	define('_MI_SPIDERS_ADMINMENU3', 'Import Robot Definitions');		
	
	define('_MI_SPIDERS_GROUP_TYPE', 'Spider');
	define('_MI_SPIDERS_GROUP_NAME', 'Robots, Crawlers & Spiders');
	define('_MI_SPIDERS_GROUP_DESCRIPTION', 'Robots, Crawlers & Spiders that scan your website and have authority.');	
	
	define('_MI_SPIDER_RESERVEDPHRASES', 'Protected Keywords from Useragents');
	define('_MI_SPIDER_RESERVEDPHRASES_DESC', 'These phrases cannot be a component used in authentication with the site (Seperate with a <strong>|</strong>)');

	define('_MI_SPIDER_MATCHPERCENTILE', 'User Agent Match Percentile');
	define('_MI_SPIDER_MATCHPERCENTILE_DESC', 'For increased security you and specify the percentile needed for match of a User agent of a spider.');
	
	
?>
