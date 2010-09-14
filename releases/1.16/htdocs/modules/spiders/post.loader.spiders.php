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

	$spider_handler = &xoops_getmodulehandler( 'spiders', _MI_SPIDERS_DIRNAME );	
	$suser_handler = &xoops_getmodulehandler( 'spiders_user', _MI_SPIDERS_DIRNAME );	
	$member_handler = &xoops_gethandler( 'member' );
	
	$criteria = new CriteriaCompo();
	$part = $spider_handler->safeAgent($_SERVER['HTTP_USER_AGENT']);
	foreach(array(';','/',',','/','(',')',' ') as $split) {
		$ret= array();
		foreach(explode($split, $part) as $value) {
			$ret[] = $value;
		}
		$part = implode(' ',$ret);
	}
	foreach($ret as $value) 
		if (!is_numeric((substr($value,0,1)))&&(substr($value,0,1))!='x')
			if (!empty($value)) {
				$criteria->add(new Criteria('`robot-safeuseragent`', '%'.$value.'%', 'LIKE'), 'OR');
				$uagereg[] = strtolower($value);
			}

	
	$spiders = $spider_handler->getObjects($criteria, true);
	if (!is_object($GLOBALS['xoopsUser']))
	foreach($spiders as $spider) {
		$suser = $suser_handler->get($spider->getVar('id'));
		$robot = $member_handler->getUser( $suser->getVar('uid') );
	
		$part = strtolower($spider->getVar('robot-exclusion-useragent'));
		foreach(array(';','/',',','/','(',')',' ') as $split) {
			$usersafeagent = array();
			foreach(explode($split, $part) as $value) {
				$usersafeagent[] = $value;
			}
			$part = implode(' ',$usersafeagent);
		}
		$usersafeagent = explode(' ', $part);
		foreach($uagereg as $ireg) {		
			if((in_array($ireg, $usersafeagent)||strpos(strtolower(' '.$part), strtolower($ireg)))&&!is_object($GLOBALS['xoopsUser'])) {	

				$GLOBALS['xoopsUser'] = '';
				$xoopsUserIsAdmin = false;
				$member_handler = &xoops_gethandler( 'member' );
				$sess_handler = &xoops_gethandler( 'session' );
				@ini_set( 'session.gc_maxlifetime', $xoopsConfig['session_expire'] * 60 );
				session_set_save_handler( array( &$sess_handler, 'open' ), array( &$sess_handler, 'close' ), array( &$sess_handler, 'read' ), array( &$sess_handler, 'write' ), array( &$sess_handler, 'destroy' ), array( &$sess_handler, 'gc' ) );
				session_start();
				
				$_SESSION['xoopsUserId'] = $suser->getVar('uid');

				if ( !empty( $_SESSION['xoopsUserId'] ) ) {
					$GLOBALS['xoopsUser'] = &$member_handler->getUser( $_SESSION['xoopsUserId'] );
					if ( !is_object( $GLOBALS['xoopsUser'] ) || ( isset( $hash_login ) && md5( $GLOBALS['xoopsUser']->getVar( 'pass' ) . XOOPS_DB_NAME . XOOPS_DB_PASS . XOOPS_DB_PREFIX ) != $hash_login ) ) {
						$GLOBALS['xoopsUser'] = '';
						$_SESSION = array();
						session_destroy();
						setcookie($xoopsConfig['usercookie'], 0, - 1, '/');
					} else {
						$GLOBALS['sess_handler']->update_cookie();
						if ( isset( $_SESSION['xoopsUserGroups'] ) ) {
							$GLOBALS['xoopsUser']->setGroups( $_SESSION['xoopsUserGroups'] );
						} else {
							$_SESSION['xoopsUserGroups'] = $GLOBALS['xoopsUser']->getGroups();
						}
						$xoopsUserIsAdmin = $GLOBALS['xoopsUser']->isAdmin();
						if ( in_array( XOOPS_GROUP_BANNED, $GLOBALS['xoopsUser']->getGroups() ) ) {
							include_once $GLOBALS['xoops']->path( 'include/site-banned.php' );
							exit();
						}
					}
				}

			if (is_object($robot))
				if ($robot->isOnline())
					$dos_crsafe .= $spider->getVar('robot-exclusion-useragent').'|'.ucfirst($spider->getVar('robot-safeuseragen')).'|';
			}
		}		
	}
	
	if (strlen($dos_crsafe)>0) {
		$module =& $module_handler->getByDirname('protector');
		if (is_object($module)&&!empty($module)) {
			$config_handler =& xoops_gethandler('config');
			$criteria = CriteriaCompo(new Criteria('conf_name', 'dos_crsafe'), "AND");
			$criteria->add(new Criteria('conf_modid', $module->getVar('mid')));
			$ret = array();
			if ($config_handler->getConfigCount($criteria)>0) {
				$configs = $config_handler->getConfigs($criteria);
				if (is_object($configs[0])) {
					if (!strpos($configs[0]->getVar('conf_value'), $dos_crsafe)) {
						$buf = explode('|', $dos_crsafe);
						$bufb = explode('|', $configs[0]->getVar('conf_value'));
						if (count($bufb)>1) {
							foreach($bufb as $id => $pagent) {
								$ret[] = $pagent;
								if ($id==2) 
									foreach($buf as $id => $pagentb) 
										$ret[] = $pagentb;
							}
							$configs[0]->setVar('conf_value', implode('|', $ret));
							$config_handler->insertConfig($configs[0]);
						}
					}
				}
			}
		
		}
	}
	
?>