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

	include('admin_header.php');

	$op = isset($_REQUEST['op'])?$_REQUEST['op']:'dashboard';
	$fct = isset($_REQUEST['fct'])?$_REQUEST['fct']:'';
	
	switch ($op)
	{
	case "send":
	
		set_time_limit(120);
		$id = intval($_GET['id']);
		include_once($GLOBALS['xoops']->path('/modules/spiders/class/'.$GLOBALS['spidersModuleConfig']['protocol'].'.php'));
		$func = strtoupper($GLOBALS['spidersModuleConfig']['protocol']).'SpidersExchange';
		$exchange = new $func;
		
		$spiders_handler =& xoops_getmodulehandler('spiders', 'spiders');
		$spider = $spiders_handler->get($id);
		
		//Recieve From API
		$exchange->sendSpider($spider->toArray());

		redirect_header('index.php?op=list', 4, sprintf(_AM_SPIDERS_SENDDONE, $spider->getVar('robot-name')));
		exit(0);
		break;
	case "compair-api":
	
		set_time_limit(480);
									
		$spider_handler = &xoops_getmodulehandler( 'spiders', _MI_SPIDERS_DIRNAME );	
		$spidermods_handler = &xoops_getmodulehandler( 'modifications', _MI_SPIDERS_DIRNAME );			
		$suser_handler = &xoops_getmodulehandler( 'spiders_user', _MI_SPIDERS_DIRNAME );	
		$member_handler = &xoops_gethandler( 'member' );
		
		$modulehandler =& xoops_gethandler('module');
		$confighandler =& xoops_gethandler('config');
		$GLOBALS['spidersModule'] = $modulehandler->getByDirname('spiders');
		$GLOBALS['spidersModuleConfig'] = $confighandler->getConfigList($GLOBALS['spidersModule']->getVar('mid'),false);

		$api = !isset($_POST['api'])?$GLOBALS['spidersModuleConfig']['protocol']:$_POST['api'];
		include_once($GLOBALS['xoops']->path('/modules/spiders/class/'.$api.'.php'));
		$func = strtoupper($api).'SpidersExchange';
		$exchange = new $func;
		
		//Recieve From API
		$apispiders = $exchange->getSpiders();
		
		foreach($apispiders as $id => $apispider) {
							
			$criteria = new CriteriaCompo();
			$part = $spider_handler->safeAgent($apispider['robot-useragent']);
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
						$uageregb[] = $value;
					}
		
			$id = 0;
			$spiders = $spider_handler->getObjects($criteria, true);

			foreach($spiders as $spider) {
				
				$suser = $suser_handler->get($spider->getVar('id'));
				$robot = $member_handler->getUser( $suser->getVar('uid') );
			
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
				$dos_crsafe = array();
					
				foreach($uagereg as $uaid => $ireg) {		
					if((in_array($ireg, $usersafeagent)||strpos(strtolower(' '.$part), strtolower($ireg)))&&!is_object($GLOBALS['xoopsUser'])) {
						$match++;			
						$dos_crsafe[] = $uageregb[$uaid];
					}
				}		
		
				if (intval($match/count($uagereg)*100)>intval($GLOBALS['spidersModuleConfig']['match_percentile'])) {
					$id = $spider->getVar('id');
					$thespider = $spider;
				}
			}
			
			$newmod = $spidermods_handler->create();
			
			foreach($apispider as $key => $value){
				if ($id<>0) {
					if (md5($value)!=md5($thespider->getVar($key))&&strlen($value)<>strlen($thespider->getVar($key))) {
						$change++;
						$newmod->setVar($key, $value);							
					} else {
						$newmod->setVar($key, $thespider->getVar($key));							
					}
				} else {
					$change++;
					$newmod->setVar($key, $value);							
				}
			}
			
			$newmod->setVar('id', $id);
			
			if (($change/count($apispider)*100)>intval($GLOBALS['spidersModuleConfig']['compair_percent'])) {
				$spidermods_handler->insert($newmod, true);
			}
			
		}
		
		redirect_header('index.php?op=listmods', 4, _AM_SPIDERS_COMPARISONFINISHED);
		exit(0);		
		break;
	case "signup":	
	
		switch ($fct)
		{
		case "save":	

			$spidersAuth =& XortifyAuthFactory::getAuthConnection(false, $GLOBALS['spidersModuleConfig']['protocol']);
			$myts =& MyTextSanitizer::getInstance();
			$uname = isset($_POST['uname']) ? $myts->stripSlashesGPC(trim($_POST['uname'])) : '';
			$email = isset($_POST['email']) ? $myts->stripSlashesGPC(trim($_POST['email'])) : '';
			$url = isset($_POST['url']) ? $myts->stripSlashesGPC(trim($_POST['url'])) : '';
			$pass = isset($_POST['pass']) ? $myts->stripSlashesGPC(trim($_POST['pass'])) : '';
			$vpass = isset($_POST['vpass']) ? $myts->stripSlashesGPC(trim($_POST['vpass'])) : '';
			$agree = (isset($_POST['agree']) && intval($_POST['agree'])) ? 1 : 0;
			
			if ($agree != 1) {
				$stop .= _US_UNEEDAGREE . '<br />';
			}
			
			$validate = $spidersAuth->validate($uname, $email, $pass, $vpass);
			
			if ($validate!=false)
				$stop .= "User details didn't validate with Xortify.com<br/>$validate";
			
			xoops_load("captcha");
			$xoopsCaptcha = XoopsCaptcha::getInstance();
			if (! $xoopsCaptcha->verify() ) {
				$stop .= $xoopsCaptcha->getMessage();
			}
			
			if ($stop!='') {
				xoops_cp_header();			
				spiders_adminMenu(5);
				echo "<p align='center' style='font-size: 15px; color: #FF0000;'>$stop</p>";
				echo XortifySignupForm();
				xoops_cp_footer();
				exit(0);
			} else {
				@$spidersAuth->create_user(	$_REQUEST['viewemail'], $uname, $email, $url, $actkey, 
											$pass, $_REQUEST['timezone'], $_REQUEST['mailok'], $spidersAuth->check_siteinfo(array()));
				
				$moduleHandler =& xoops_gethandler('module');
				$configHandler =& xoops_gethandler('config');
				if (!is_object($GLOBALS['spidersModule'])) $GLOBALS['spidersModule'] = $modulehandler->getByDirname('spiders');
				if (!is_array($GLOBALS['spidersModuleConfig'])) $GLOBALS['spidersModuleConfig'] = $confighandler->getConfigList($GLOBALS['spidersModule']->getVar('mid'),false);
				foreach($GLOBALS['spidersModuleConfig'] as $id => $config)
					switch($config->getVar('conf_name')) {
					case 'spiders_username':
						$config->setVar('conf_value', $uname);
						@$configHandler->insertConfig($config);
						break;
					case 'spiders_password':
						$config->setVar('conf_value', $pass);
						@$configHandler->insertConfig($config);
						break;
					}
				redirect_header("index.php", 4, _AM_SPIDERS_USERCREATED_PLEASEACTIVATE);
				exit(0);
			}
			break;	
		default:
		case "signup":
			xoops_cp_header();			
			spiders_adminMenu("index.php?op=signup&fct=signup", 5);
			echo XortifySignupForm();
			footer_spiders_adminMenu();
			echo chronolabs_inline(false);
			xoops_cp_footer();
			exit(0);
			break;
		}
		break;
		
	case "import-file":
		set_time_limit(240);
		import_robotstxt_org($_REQUEST['file']);
		redirect_header('index.php', 3, _AM_SPIDERS_IMPORTCOMPLETE);
		break;
		
	case "save":

		$spiders_handler =& xoops_getmodulehandler('spiders', 'spiders');		
		if (intval($_POST['id'])<>0)
			$spider = $spiders_handler->get(intval($_POST['id']));
		else
			$spider = $spiders_handler->create();
			
		$spider->setVar('robot-id', $_POST['robot-id'][intval($_POST['id'])]);
		$spider->setVar('robot-name', $_POST['robot-name'][intval($_POST['id'])]);
		$spider->setVar('robot-cover-url', $_POST['robot-cover-url'][intval($_POST['id'])]);
		$spider->setVar('robot-details-url', $_POST['robot-details-url'][intval($_POST['id'])]);
		$spider->setVar('robot-owner-name', $_POST['robot-owner-name'][intval($_POST['id'])]);
		$spider->setVar('robot-owner-url', $_POST['robot-owner-url'][intval($_POST['id'])]);
		$spider->setVar('robot-owner-email', $_POST['robot-owner-email'][intval($_POST['id'])]);
		$spider->setVar('robot-status', $_POST['robot-status'][intval($_POST['id'])]);
		$spider->setVar('robot-purpose', $_POST['robot-purpose'][intval($_POST['id'])]);
		$spider->setVar('robot-type', $_POST['robot-type'][intval($_POST['id'])]);
		$spider->setVar('robot-platform', $_POST['robot-platform'][intval($_POST['id'])]);
		$spider->setVar('robot-availability', $_POST['robot-availability'][intval($_POST['id'])]);
		$spider->setVar('robot-exclusion', $_POST['robot-exclusion'][intval($_POST['id'])]);
		$spider->setVar('robot-exclusion-useragent', $_POST['robot-exclusion-useragent'][intval($_POST['id'])]);
		$spider->setVar('robot-noindex', $_POST['robot-noindex'][intval($_POST['id'])]);
		$spider->setVar('robot-host', $_POST['robot-host'][intval($_POST['id'])]);
		$spider->setVar('robot-from', $_POST['robot-from'][intval($_POST['id'])]);
		$spider->setVar('robot-useragent', $_POST['robot-useragent'][intval($_POST['id'])]);
		$spider->setVar('robot-language', $_POST['robot-language'][intval($_POST['id'])]);
		$spider->setVar('robot-description', $_POST['robot-description'][intval($_POST['id'])]);
		$spider->setVar('robot-history', $_POST['robot-history'][intval($_POST['id'])]);
		$spider->setVar('robot-environment', $_POST['robot-environment'][intval($_POST['id'])]);
		$spider->setVar('modified-by', $_POST['modified-by'][intval($_POST['id'])]);
		$spider->setVar('modified-date', $_POST['modified-date'][intval($_POST['id'])]);
		$spider->setVar('robot-safeuseragent', $_POST['robot-safeuseragent'][intval($_POST['id'])]);		
		
		if ($spider->isNew()) {
			$spiders_handler->import_insert($spider);
		} 
		
		if ($spiders_handler->insert($spider)) 
			redirect_header( $_SERVER['PHP_SELF'].'?op=edit&id='.$_REQUEST['id'] , 6 , _AM_SPIDERS_DATASAVEDSUCCESSFULLY) ;					
		else 
			redirect_header( $_SERVER['PHP_SELF'].'?op=list' , 6 , _AM_SPIDERS_DATASAVEDUNSUCCESSFULLY) ;					
		
		
		break;

	case "merge":

		$spiders_handler =& xoops_getmodulehandler('spiders', 'spiders');		
		$spidersmods_handler =& xoops_getmodulehandler('modifications', 'spiders');		

		if (intval($_REQUEST['modid'])<>0)
			$mod = $spidersmods_handler->get(intval($_REQUEST['modid']));
		else
			$mod = $spiders_handler->create();

		if (intval($mod->getVar('id'))<>0)
			$spider = $spiders_handler->get(intval($mod->getVar('id')));
		else
			$spider = $spiders_handler->create();
			
		$spider->setVar('robot-cover-url', $_POST['robot-cover-url'][intval($_POST['id'])]);
		$spider->setVar('robot-details-url', $_POST['robot-details-url'][intval($_POST['id'])]);
		$spider->setVar('robot-owner-name', $_POST['robot-owner-name'][intval($_POST['id'])]);
		$spider->setVar('robot-owner-url', $_POST['robot-owner-url'][intval($_POST['id'])]);
		$spider->setVar('robot-owner-email', $_POST['robot-owner-email'][intval($_POST['id'])]);
		$spider->setVar('robot-status', $_POST['robot-status'][intval($_POST['id'])]);
		$spider->setVar('robot-purpose', $_POST['robot-purpose'][intval($_POST['id'])]);
		$spider->setVar('robot-type', $_POST['robot-type'][intval($_POST['id'])]);
		$spider->setVar('robot-platform', $_POST['robot-platform'][intval($_POST['id'])]);
		$spider->setVar('robot-availability', $_POST['robot-availability'][intval($_POST['id'])]);
		$spider->setVar('robot-exclusion', $_POST['robot-exclusion'][intval($_POST['id'])]);
		$spider->setVar('robot-exclusion-useragent', $_POST['robot-exclusion-useragent'][intval($_POST['id'])]);
		$spider->setVar('robot-noindex', $_POST['robot-noindex'][intval($_POST['id'])]);
		$spider->setVar('robot-host', $_POST['robot-host'][intval($_POST['id'])]);
		$spider->setVar('robot-from', $_POST['robot-from'][intval($_POST['id'])]);
		$spider->setVar('robot-useragent', $_POST['robot-useragent'][intval($_POST['id'])]);
		$spider->setVar('robot-language', $_POST['robot-language'][intval($_POST['id'])]);
		$spider->setVar('robot-description', $_POST['robot-description'][intval($_POST['id'])]);
		$spider->setVar('robot-history', $_POST['robot-history'][intval($_POST['id'])]);
		$spider->setVar('robot-environment', $_POST['robot-environment'][intval($_POST['id'])]);
		$spider->setVar('modified-by', $_POST['modified-by'][intval($_POST['id'])]);
		$spider->setVar('modified-date', $_POST['modified-date'][intval($_POST['id'])]);
		$spider->setVar('robot-safeuseragent', $_POST['robot-safeuseragent'][intval($_POST['id'])]);		
		
		if ($spider->isNew()) {
			$spider->setVar('robot-id', $_POST['robot-id'][intval($_POST['id'])]);
			$spider->setVar('robot-name', $_POST['robot-name'][intval($_POST['id'])]);
			$spiders_handler->import_insert($spider);
		} else {
			$spidersuser_handler =& xoops_getmodulehandler('spiders_user', 'spiders');
			$suser = $spidersuser_handler->get($spider->getVar('id'));
			$member_handler =& xoops_gethandler('member');
			$xuser = $member_handler->getUser($suser->getVar('uid'));
			if ($spider->getVar('robot-id')!=$_POST['robot-id'][intval($_POST['id'])]) {
				$spider->setVar('robot-id', $_POST['robot-id'][intval($_POST['id'])]);
				$xuser->setVar('uname', ucfirst($spider->getVar('robot-id')));	
				$member_handler->insertUser($xuser);
			}
			if ($spider->getVar('robot-name')!=$_POST['robot-name'][intval($_POST['id'])]) {
				$spider->setVar('robot-name', $_POST['robot-name'][intval($_POST['id'])]);
				$xuser->setVar('name', ucfirst($spider->getVar('robot-name')));	
				$member_handler->insertUser($xuser);
			}
		}
		
		if ($spiders_handler->insert($spider)) {
			$sql[0] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('spiders_modifications').' WHERE `modid` = "'.intval($_REQUEST['modid']).'"';		
			if ($GLOBALS['xoopsDB']->queryF($sql[0])) {
				redirect_header( $_SERVER['PHP_SELF'].'?op=editmods' , 6 , _AM_SPIDERS_DATASAVEDSUCCESSFULLY) ;					
			} else {
				redirect_header( $_SERVER['PHP_SELF'].'?op=list' , 6 , _AM_SPIDERS_DATADELETEDUNSUCCESSFULLY) ;				
			}	
		} else 
			redirect_header( $_SERVER['PHP_SELF'].'?op=list' , 6 , _AM_SPIDERS_DATASAVEDUNSUCCESSFULLY) ;					
		
		
		break;

	case "savelist":

		$spiders_handler =& xoops_getmodulehandler('spiders', 'spiders');		

		foreach($_POST['id'] as $oid => $id) {
			$spider = $spiders_handler->get($id);
			$spider->setVar('robot-exclusion-useragent', $_POST['robot-exclusion-useragent'][$id]);
			$spider->setVar('robot-useragent', $_POST['robot-useragent'][$id]);
			@$spiders_handler->insert($spider);
		}

		redirect_header( $_SERVER['PHP_SELF'].'?op=list' , 6 , _AM_SPIDERS_DATASAVEDSUCCESSFULLY) ;					
		
		break;

	case "delete":

		$spiders_handler =& xoops_getmodulehandler('spiders', 'spiders');
		$spidersusers_handler =& xoops_getmodulehandler('spiders_user', 'spiders');

		$suser = $spidersusers_handler->get($_REQUEST['id']);
		$spider = $spiders_handler->get($_REQUEST['id']);
		
		if (empty($_POST['confirmed'])) {
			xoops_cp_header();			
			spiders_adminMenu("index.php?op=list", 1);
			xoops_confirm(array('confirmed' => true, 'id' => $_GET['id'], 'op' => $_GET['op']), $_SERVER['REQUEST_URI'], sprintf(_AM_SPIDERS_CONFIRM_DELETE, $spider->getVar('robot-name')));
			footer_spiders_adminMenu();
			echo chronolabs_inline(false);
			xoops_cp_footer();
			exit(0);
		}
		
		$sql[0] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('users').' WHERE `uid` = "'.$suser->getVar('uid').'"';
		$sql[1] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('spiders').' WHERE `id` = "'.$spider->getVar('id').'"';
		$sql[2] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('spiders_user').' WHERE `spider_id` = "'.$spider->getVar('id').'"';
		$sql[3] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('spiders_statistics').' WHERE `id` = "'.$spider->getVar('id').'"';
		$sql[4] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('spiders_modifications').' WHERE `id` = "'.$spider->getVar('id').'"';		
					
		if ($GLOBALS['xoopsDB']->queryF($sql[0])&&$GLOBALS['xoopsDB']->queryF($sql[1])&&$GLOBALS['xoopsDB']->queryF($sql[2])&&$GLOBALS['xoopsDB']->queryF($sql[3])
			&&$GLOBALS['xoopsDB']->queryF($sql[4])) {
			redirect_header( $_SERVER['PHP_SELF'].'?op=list' , 6 , _AM_SPIDERS_DATADELETEDSUCCESSFULLY) ;				
		} else {
			redirect_header( $_SERVER['PHP_SELF'].'?op=list' , 6 , _AM_SPIDERS_DATADELETEDUNSUCCESSFULLY) ;				
		}
		break;		

	case "deletemod":

		$spidersmods_handler =& xoops_getmodulehandler('modifications', 'spiders');
		$smod = $spidersmods_handler->get($_REQUEST['modid']);

		if (empty($_POST['confirmed'])) {
			xoops_cp_header();			
			spiders_adminMenu(1);
			xoops_confirm(array('confirmed' => true, 'modid' => $_GET['modid'], 'op' => $_GET['op']), $_SERVER['REQUEST_URI'], sprintf(_AM_SPIDERS_CONFIRM_DELETE, $smod->getVar('robot-name')));
			footer_spiders_adminMenu();
			echo chronolabs_inline(false);
			xoops_cp_footer();
			exit(0);
		}
		
		$sql[0] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('spiders_modifications').' WHERE `modid` = "'.intval($_POST['modid']).'"';		
					
		if ($GLOBALS['xoopsDB']->queryF($sql[0])) {
			redirect_header( $_SERVER['PHP_SELF'].'?op=list' , 6 , _AM_SPIDERS_DATADELETEDSUCCESSFULLY) ;				
		} else {
			redirect_header( $_SERVER['PHP_SELF'].'?op=list' , 6 , _AM_SPIDERS_DATADELETEDUNSUCCESSFULLY) ;				
		}
		break;		

	case "mergemod":
		xoops_cp_header();
		spiders_adminMenu("index.php?op=listmods", 3);
		import_spidersmods_edit(intval($_GET['id']));
		footer_spiders_adminMenu();
		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
		break;
	case "edit":
		xoops_cp_header();
		spiders_adminMenu("index.php?op=add", 2);
		import_spiders_edit(intval($_GET['id']));
		footer_spiders_adminMenu();
		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
		break;
	case "add":
		xoops_cp_header();
		spiders_adminMenu("index.php?op=add", 2);
		import_spiders_edit(0);
		footer_spiders_adminMenu();
		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
		break;
	case "listmods":
		xoops_cp_header();
		spiders_adminMenu("index.php?op=listmods", 3);
		import_spidersmods_list();
		footer_spiders_adminMenu();
		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
		break;
	case "list":
		xoops_cp_header();
		spiders_adminMenu("index.php?op=list", 1);
		import_spiders_list();
		footer_spiders_adminMenu();
		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
		break;
	case "import":
		xoops_cp_header();
		spiders_adminMenu("index.php?op=import", 4);
		compair_spiders_form();
		import_spiders_form();
		footer_spiders_adminMenu();
		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
	default:
	case "dashboard":
		xoops_cp_header();
		spiders_adminMenu("index.php?op=dashboard", 0);
		$spiders_handler = xoops_getmodulehandler('spiders', 'spiders');
	    $statistics_handler = xoops_getmodulehandler('statistics', 'spiders');
	    $user_handler = xoops_gethandler('user');
	    $spiders_user_handler = xoops_getmodulehandler('spiders_user', 'spiders');
	    
	    foreach($spiders_user_handler->getObjects(NULL) as $spiderid => $spideruser) {
	    	$uids[$spideruser->getVar('uid')] = $spideruser->getVar('uid');
	    	$spiderids[$spideruser->getVar('uid')] = $spideruser->getVar('spider_id');
	    }
	    
	 	$indexAdmin = new ModuleAdmin();	
	    $indexAdmin->addInfoBox(_AM_SPIDERS_ADMIN_COUNTS);
	    $criteria = new CriteriaCompo(new Criteria('uid','('.implode(',', $uids).')', 'IN'));
	    $criteria->add(new Criteria('`last_login`', '0', '>'));
	    $indexAdmin->addInfoBoxLine(_AM_SPIDERS_ADMIN_COUNTS, "<label>"._AM_SPIDERS_ADMIN_THEREARE_BOTSACTIVE."</label>", $user_handler->getCount($criteria), 'Green');
	    $criteria = new CriteriaCompo(new Criteria('uid','('.implode(',', $uids).')', 'IN'));
	    $criteria->add(new Criteria('`last_login`', '0', '='));
	    $indexAdmin->addInfoBoxLine(_AM_SPIDERS_ADMIN_COUNTS, "<label>"._AM_SPIDERS_ADMIN_THEREARE_BOTSINACTIVE."</label>", $user_handler->getCount($criteria), 'Green');
	   	$criteria = new CriteriaCompo(new Criteria('uid','('.implode(',', $uids).')', 'IN'));
	    $criteria->setSort('`last_login`');
	    $criteria->setOrder('DESC');
	    $criteria->setLimit(1);
	    $users = $user_handler->getObjects($criteria, false);
	    if (is_object($users[0]))
	   		$indexAdmin->addInfoBoxLine(_AM_SPIDERS_ADMIN_COUNTS, "<label>"._AM_SPIDERS_ADMIN_THEREARE_BOTLASTLOGGEDON."</label>", date(_DATESTRING, $users[0]->getVar('last_login')) . ' - ' . $users[0]->getVar('uname'), 'Green');
	   	$criteria = new CriteriaCompo(new Criteria('uid','('.implode(',', $uids).')', 'IN'));	   		
		$criteria = new CriteriaCompo(new Criteria('last_login',time()-(60*60*7*4), '>'));
		$criteria->add(new Criteria('`last_login`', time(), '<'));
	   	$indexAdmin->addInfoBoxLine(_AM_SPIDERS_ADMIN_COUNTS, "<label>"._AM_SPIDERS_ADMIN_THEREARE_BOTINMONTH."</label>", $user_handler->getCount($criteria), 'Orange');
	   	$criteria = new CriteriaCompo(new Criteria('uid','('.implode(',', $uids).')', 'IN'));	   		
		$criteria = new CriteriaCompo(new Criteria('last_login',time()-(60*60*7*4), '<'));
		$criteria->add(new Criteria('`last_login`', time()-(60*60*7*4*6), '>'));
	   	$indexAdmin->addInfoBoxLine(_AM_SPIDERS_ADMIN_COUNTS, "<label>"._AM_SPIDERS_ADMIN_THEREARE_BOTIN6MONTH."</label>", $user_handler->getCount($criteria), 'Orange');
		$totalHits = $statistics_handler->getCount(NULL);
	    foreach($spiderids as $uid => $spiderid) {		    
	    	$hits = $statistics_handler->getCount(new Criteria('id', $spiderid));
	    	if ($hits>0) {
	    		$spider = $spiders_handler->get($spiderid);
	    		$indexAdmin->addInfoBox(sprintf(_AM_SPIDERS_ROBOTS_COUNTS, $spider->getVar('robot-name')));
	    		$indexAdmin->addInfoBoxLine(sprintf(_AM_SPIDERS_ROBOTS_COUNTS, $spider->getVar('robot-name')), "<label>"._AM_SPIDERS_ROBOTS_THEREARE_PERCENTAGEOFHITS."</label>", number_format($hits/$totalHits*100,2).'%', 'Red');
	    		$indexAdmin->addInfoBoxLine(sprintf(_AM_SPIDERS_ROBOTS_COUNTS, $spider->getVar('robot-name')), "<label>"._AM_SPIDERS_ROBOTS_THEREARE_NUMBEROFHITS."</label>", number_format($hits,0), 'Blue');
	    		$criteria = new Criteria('id', $spiderid);
	    		$criteria->setSort('`when`');
	    		$criteria->setOrder('DESC');
	    		$criteria->setLimit(1);
	    		$statistics = $statistics_handler->getCount($criteria, false);
	    		if (is_object($statistics[0])) {
		    		$indexAdmin->addInfoBoxLine(sprintf(_AM_SPIDERS_ROBOTS_COUNTS, $spider->getVar('robot-name')), "<label>"._AM_SPIDERS_ROBOTS_THEREARE_LASTHIT."</label>", date(_DATESTRING,$statistics[0]->getVar('when')), 'Purple');
		    		$indexAdmin->addInfoBoxLine(sprintf(_AM_SPIDERS_ROBOTS_COUNTS, $spider->getVar('robot-name')), "<label>"._AM_SPIDERS_ROBOTS_THEREARE_LASTURI."</label>", "<a href='".$statistics[0]->getVar('uri').'" target="_blank">'.str_replace(XOOPS_URL, '', $statistics[0]->getVar('uri')).'</a>', 'Purple');
	    		}
	    		$criteria = new Criteria('id', $spiderid);
	    		$criteria->setSort('`when`');
	    		$criteria->setOrder('ASC');
	    		$criteria->setLimit(1);
	    		$statistics = $statistics_handler->getCount($criteria, false);
	    		if (is_object($statistics[0])) {
		    		$indexAdmin->addInfoBoxLine(sprintf(_AM_SPIDERS_ROBOTS_COUNTS, $spider->getVar('robot-name')), "<label>"._AM_SPIDERS_ROBOTS_THEREARE_FIRSTHIT."</label>", date(_DATESTRING,$statistics[0]->getVar('when')), 'Orange');
		    		$indexAdmin->addInfoBoxLine(sprintf(_AM_SPIDERS_ROBOTS_COUNTS, $spider->getVar('robot-name')), "<label>"._AM_SPIDERS_ROBOTS_THEREARE_FIRSTURI."</label>", "<a href='".$statistics[0]->getVar('uri').'" target="_blank">'.str_replace(XOOPS_URL, '', $statistics[0]->getVar('uri')).'</a>', 'Orange');
	    		}
	    	}
	    }
	    
	    echo $indexAdmin->renderIndex();
			
		footer_spiders_adminMenu();
		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
		break;
	case "about":
		xoops_cp_header();
		spiders_adminMenu("index.php?op=about", 6);
		$paypalitemno='SPIDERS272';
		$aboutAdmin = new ModuleAdmin();
		$about = $aboutAdmin->renderabout($paypalitemno, false);
		$donationform = array(	0 => '<form name="donation" id="donation" action="http://www.chronolabs.coop/modules/xpayment/" method="post" onsubmit="return xoopsFormValidate_donation();">',
								1 => '<table class="outer" cellspacing="1" width="100%"><tbody><tr><th colspan="2">'.constant('_AM_SPIDERS_ABOUT_MAKEDONATE').'</th></tr><tr align="left" valign="top"><td class="head"><div class="xoops-form-element-caption-required"><span class="caption-text">Donation Amount</span><span class="caption-marker">*</span></div></td><td class="even"><select size="1" name="item[A][amount]" id="item[A][amount]" title="Donation Amount"><option value="5">5.00 AUD</option><option value="10">10.00 AUD</option><option value="20">20.00 AUD</option><option value="40">40.00 AUD</option><option value="60">60.00 AUD</option><option value="80">80.00 AUD</option><option value="90">90.00 AUD</option><option value="100">100.00 AUD</option><option value="200">200.00 AUD</option></select></td></tr><tr align="left" valign="top"><td class="head"></td><td class="even"><input class="formButton" name="submit" id="submit" value="'._SUBMIT.'" title="'._SUBMIT.'" type="submit"></td></tr></tbody></table>',
								2 => '<input name="op" id="op" value="createinvoice" type="hidden"><input name="plugin" id="plugin" value="donations" type="hidden"><input name="donation" id="donation" value="1" type="hidden"><input name="drawfor" id="drawfor" value="Chronolabs Co-Operative" type="hidden"><input name="drawto" id="drawto" value="%s" type="hidden"><input name="drawto_email" id="drawto_email" value="%s" type="hidden"><input name="key" id="key" value="%s" type="hidden"><input name="currency" id="currency" value="AUD" type="hidden"><input name="weight_unit" id="weight_unit" value="kgs" type="hidden"><input name="item[A][cat]" id="item[A][cat]" value="XDN%s" type="hidden"><input name="item[A][name]" id="item[A][name]" value="Donation for %s" type="hidden"><input name="item[A][quantity]" id="item[A][quantity]" value="1" type="hidden"><input name="item[A][shipping]" id="item[A][shipping]" value="0" type="hidden"><input name="item[A][handling]" id="item[A][handling]" value="0" type="hidden"><input name="item[A][weight]" id="item[A][weight]" value="0" type="hidden"><input name="item[A][tax]" id="item[A][tax]" value="0" type="hidden"><input name="return" id="return" value="http://www.chronolabs.coop/modules/donations/success.php" type="hidden"><input name="cancel" id="cancel" value="http://www.chronolabs.coop/modules/donations/success.php" type="hidden"></form>',																'D'=>'',
								3 => '',
								4 => '<!-- Start Form Validation JavaScript //-->
<script type="text/javascript">
<!--//
function xoopsFormValidate_donation() { var myform = window.document.donation; 
var hasSelected = false; var selectBox = myform.item[A][amount];for (i = 0; i < selectBox.options.length; i++ ) { if (selectBox.options[i].selected == true && selectBox.options[i].value != \'\') { hasSelected = true; break; } }if (!hasSelected) { window.alert("Please enter Donation Amount"); selectBox.focus(); return false; }return true;
}
//--></script>
<!-- End Form Validation JavaScript //-->');
		$paypalform = array(	0 => '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">',
								1 => '<input name="cmd" value="_s-xclick" type="hidden">',
								2 => '<input name="hosted_button_id" value="%s" type="hidden">',
								3 => '<img alt="" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" height="1" border="0" width="1">',
								4 => '<input src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" border="0" type="image">',
								5 => '</form>');
		for($key=0;$key<=4;$key++) {
			switch ($key) {
				case 2:
					$donationform[$key] =  sprintf($donationform[$key], $GLOBALS['xoopsConfig']['sitename'] . ' - ' . (strlen($GLOBALS['xoopsUser']->getVar('name'))>0?$GLOBALS['xoopsUser']->getVar('name'). ' ['.$GLOBALS['xoopsUser']->getVar('uname').']':$GLOBALS['xoopsUser']->getVar('uname')), $GLOBALS['xoopsUser']->getVar('email'), XOOPS_LICENSE_KEY, strtoupper($GLOBALS['spidersModule']->getVar('dirname')), strtoupper($GLOBALS['spidersModule']->getVar('dirname')). ' '.$GLOBALS['spidersModule']->getVar('name'));
					break;
			}
		}
		
		$istart = strpos($about, ($paypalform[0]), 1);
		$iend = strpos($about, ($paypalform[5]), $istart+1)+strlen($paypalform[5])-1;
		echo (substr($about, 0, $istart-1));
		echo implode("\n", $donationform);
		echo (substr($about, $iend+1, strlen($about)-$iend-1));
		footer_spiders_adminMenu();
		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
		break;
	}		
?>