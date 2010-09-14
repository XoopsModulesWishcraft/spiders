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

	if(!defined('_MI_SPIDERS_DIRNAME'))
		define('_MI_SPIDERS_DIRNAME','spiders');
		
	global $xoopsConfig;
	$module_handler =& xoops_gethandler('module');
	$critera = new CriteriaCompo(new Criteria('dirname', _MI_SPIDERS_DIRNAME));
	$installed = $module_handler->getCount($critera);

	if ($installed!=0)
	{
		$module =& $module_handler->getByDirname(_MI_SPIDERS_DIRNAME);
		if ($module->getVar('isactive')==true)
		{
			$spider_handler = &xoops_getmodulehandler( 'spiders', _MI_SPIDERS_DIRNAME );	
			$suser_handler = &xoops_getmodulehandler( 'spiders_user', _MI_SPIDERS_DIRNAME );	
			
			$spiders = $spider_handler->getObjects(NULL);
			foreach($spiders as $spider) {
				if(strpos(' '.$_SERVER['HTTP_USER_AGENT'], $spider->getVar('robot-exclusion-useragent'))||strpos(' '.$_SERVER['HTTP_USER_AGENT'], $spider->getVar('robot-useragent'))) {
					$suser = $suser_handler->get($spider->getVar('id'));
					
					/**
					 * User Sessions
					 */
					$xoopsUser = '';
					$xoopsUserIsAdmin = false;
					$member_handler = &xoops_gethandler( 'member' );
					$sess_handler = &xoops_gethandler( 'session' );
					@ini_set( 'session.gc_maxlifetime', $xoopsConfig['session_expire'] * 60 );
					session_set_save_handler( array( &$sess_handler, 'open' ), array( &$sess_handler, 'close' ), array( &$sess_handler, 'read' ), array( &$sess_handler, 'write' ), array( &$sess_handler, 'destroy' ), array( &$sess_handler, 'gc' ) );
					session_start();
					
					$_SESSION['xoopsUserId'] = $suser->getVar('uid');
										
					/**
					 * Log user is and deal with Sessions and Cookies
					 */
					if ( !empty( $_SESSION['xoopsUserId'] ) ) {
						$xoopsUser = &$member_handler->getUser( $_SESSION['xoopsUserId'] );
						if ( !is_object( $xoopsUser ) || ( isset( $hash_login ) && md5( $xoopsUser->getVar( 'pass' ) . XOOPS_DB_NAME . XOOPS_DB_PASS . XOOPS_DB_PREFIX ) != $hash_login ) ) {
							$xoopsUser = '';
							$_SESSION = array();
							session_destroy();
						} else {
							$GLOBALS['sess_handler']->update_cookie();
							if ( isset( $_SESSION['xoopsUserGroups'] ) ) {
								$xoopsUser->setGroups( $_SESSION['xoopsUserGroups'] );
							} else {
								$_SESSION['xoopsUserGroups'] = $xoopsUser->getGroups();
							}
							$xoopsUserIsAdmin = $xoopsUser->isAdmin();
							if ( in_array( XOOPS_GROUP_BANNED, $xoopsUser->getGroups() ) ) {
								include_once $GLOBALS['xoops']->path( 'include/site-banned.php' );
								exit();
							}
						}
					}
	
				}
			}		
		}
		
	}
?>