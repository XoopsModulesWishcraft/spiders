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
	include('../include/forms.php');

	switch ($_REQUEST['op'])
	{
	case "import-file":
		import_robotstxt_org($_REQUEST['file']);
		redirect_header('index.php', 3, _AM_SPIDERS_IMPORTCOMPLETE);

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
			adminMenu(1);
			xoops_confirm(array('confirmed' => true, 'id' => $_GET['id'], 'op' => $_GET['op']), $_SERVER['REQUEST_URI'], sprintf(_AM_SPIDERS_CONFIRM_DELETE, $spider->getVar('robot-name')));
			xoops_cp_footer();
			exit(0);
		}
		
		$sql[0] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('users').' WHERE `uid` = "'.$suser->getVar('uid').'"';
		$sql[1] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('spiders').' WHERE `id` = "'.$spider->getVar('id').'"';
		$sql[2] = "DELETE FROM ".$GLOBALS['xoopsDB']->prefix('spiders_user').' WHERE `spider_id` = "'.$spider->getVar('id').'"';
					
		if ($GLOBALS['xoopsDB']->queryF($sql[0])&&$GLOBALS['xoopsDB']->queryF($sql[1])&&$GLOBALS['xoopsDB']->queryF($sql[2])) {
			redirect_header( $_SERVER['PHP_SELF'].'?op=list' , 6 , _AM_SPIDERS_DATADELETEDSUCCESSFULLY) ;				
		} else {
			redirect_header( $_SERVER['PHP_SELF'].'?op=list' , 6 , _AM_SPIDERS_DATADELETEDUNSUCCESSFULLY) ;				
		}
		break;		

	case "edit":
		xoops_cp_header();
		adminMenu(1);
		
		import_spiders_edit(intval($_GET['id']));
				
		footer_adminMenu();

		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
	
		break;

	case "add":
		xoops_cp_header();
		adminMenu(1);
		
		import_spiders_edit(0);
				
		footer_adminMenu();

		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
	
		break;


	default:
	case "list":
		xoops_cp_header();
		adminMenu(1);
		
		import_spiders_list();
				
		footer_adminMenu();

		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
	
		break;
	case "import":
		xoops_cp_header();
		adminMenu(2);
		
		import_spiders_form();
				
		footer_adminMenu();

		echo chronolabs_inline(false);
		xoops_cp_footer();
		exit;
	}		
?>