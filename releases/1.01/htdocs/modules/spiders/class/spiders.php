<?php
if (!defined('XOOPS_ROOT_PATH')) {
	exit();
}
/**
 * Class for Spiders
 * @author Simon Roberts (simon@xoops.org)
 * @copyright copyright (c) 2000-2009 XOOPS.org
 * @package kernel
 */
class SpidersSpiders extends XoopsObject
{

    function SpidersSpiders($fid = null)
    {
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('robot-id', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('robot-name', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('robot-cover-url', XOBJ_DTYPE_OTHER, null, false, 255);
        $this->initVar('robot-details-url', XOBJ_DTYPE_OTHER, null, false, 255);
        $this->initVar('robot-owner-name', XOBJ_DTYPE_TXTBOX, null, false, 128);
        $this->initVar('robot-owner-url', XOBJ_DTYPE_OTHER, null, false, 255);
        $this->initVar('robot-owner-email', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('robot-status', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('robot-purpose', XOBJ_DTYPE_TXTBOX, null, false, 128);
		$this->initVar('robot-type', XOBJ_DTYPE_TXTBOX, null, false, 64);
		$this->initVar('robot-platform', XOBJ_DTYPE_TXTBOX, null, false, 64);
		$this->initVar('robot-type', XOBJ_DTYPE_TXTBOX, null, false, 64);
		$this->initVar('robot-platform', XOBJ_DTYPE_TXTBOX, null, false, 64);
		$this->initVar('robot-availability', XOBJ_DTYPE_TXTBOX, null, false, 128);
		$this->initVar('robot-exclusion', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('robot-exclusion-useragent', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('robot-noindex', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('robot-host', XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('robot-from', XOBJ_DTYPE_TXTBOX, null, false, 32);
		$this->initVar('robot-useragent', XOBJ_DTYPE_TXTBOX, null, false, 255);
		$this->initVar('robot-language', XOBJ_DTYPE_TXTBOX, null, false, 64);
		$this->initVar('robot-description', XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('robot-history', XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('robot-environment', XOBJ_DTYPE_TXTBOX, null, false, 128);
		$this->initVar('modified-date', XOBJ_DTYPE_TXTBOX, null, false, 64);										
		$this->initVar('modified-by', XOBJ_DTYPE_TXTBOX, null, false, 64);
    }


}


/**
* XOOPS Spider handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS user class objects.
*
* @author  Simon Roberts <simon@xoops.org>
* @package kernel
*/
class SpidersSpidersHandler extends XoopsPersistableObjectHandler
{
    function __construct(&$db) 
    {
        parent::__construct($db, "spiders", 'SpidersSpiders', "id", "robot-name");
    }
	
	function import_insert($spider)
	{
		if (!is_a($spider, 'SpidersSpiders'))
			return false;
		
		$group_handler = &xoops_gethandler( 'group' );
		$suser_handler = &xoops_getmodulehandler( 'spiders_user', _MI_SPIDERS_DIRNAME );
		$criteria = new Criteria('group_type', _MI_SPIDERS_GROUP_TYPE);
		$groups = $group_handler->getObjects($criteria);
		if (is_array($groups))
		{
			$groupid = $groups[0]->getVar('groupid');
		}
		
		$sql = "SELECT count(*) as rc FROM ".$GLOBALS['xoopsDB']->prefix('spiders')." WHERE `robot-id` = '".$spider->getVar('robot-id')."'";
		list($rc) = $GLOBALS['xoopsDB']->fetchRow($GLOBALS['xoopsDB']->query($sql));
		if ($rc==0&&strlen($spider->getVar('robot-id'))>0)
		{
		
			$sid = $this->insert($spider);
			if ($sid>0) {
				$member_handler =& xoops_gethandler('member');
				$criteria = new CriteriaCompo(new Criteria('uname', ucfirst($spider->getVar('robot-id'))));
				if (!$member_handler->getUserCount($criteria)){
					$user = $member_handler->createUser();
					$user->setVar('name', $spider->getVar('robot-name'));
					$user->setVar('uname', ucfirst($spider->getVar('robot-id')));
					$user->setVar('email', str_replace('+', '@', $spider->getVar('robot-owner-email')));
					if (strlen($spider->getVar('robot-details-url')))
						$user->setVar('url', $spider->getVar('robot-details-url'));
					else
						$user->setVar('url', $spider->getVar('robot-cover-url'));
					$user->setVar('pass', md5(XOOPS_URL.XOOPS_ROOT_PATH.$spider->getVar('robot-id').rand(1,30000)));
					$user->setVar('level', 1);
					$user->setVar('user_mailok', 0);
					$user->setVar('user_occ', _MI_SPIDERS_GROUP_NAME);
					$user->setVar('bio', $spider->getVar('robot-description'));
					$uid = $member_handler->insertUser($user);
					@$member_handler->addUserToGroup($groupid, $uid);
				} else {
					$user = $member_handler->createUser();
					$user->setVar('name', $spider->getVar('robot-name'));
					$user->setVar('uname', ucfirst($spider->getVar('robot-id').'-'.rand(1,9)));
					$user->setVar('email', str_replace('+', '@', $spider->getVar('robot-owner-email')));
					if (strlen($spider->getVar('robot-details-url')))
						$user->setVar('url', $spider->getVar('robot-details-url'));
					else
						$user->setVar('url', $spider->getVar('robot-cover-url'));
					$user->setVar('pass', md5(XOOPS_URL.XOOPS_ROOT_PATH.$spider->getVar('robot-id').rand(1,30000)));
					$user->setVar('level', 1);
					$user->setVar('user_mailok', 0);
					$user->setVar('user_occ', _MI_SPIDERS_GROUP_NAME);
					$user->setVar('bio', $spider->getVar('robot-description'));
					$uid = $member_handler->insertUser($user);
					@$member_handler->addUserToGroup($groupid, $uid);
				}
				$suser = $suser_handler->create();
				$suser->setVar('spider_id', $spider->getVar('id'));
				$suser->setVar('uid', $uid);
				$suser_handler->insert($suser);
			}				
		} else {
			return false;
		}
	}
}
?>