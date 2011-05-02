<?php
/**
 * @package     spiders
 * @subpackage  module
 * @description	Sector Network Security Drone
 * @author	    Simon Roberts WISHCRAFT <simon@chronolabs.coop>
 * @copyright	copyright (c) 2010-2013 XOOPS.org
 * @licence		GPL 2.0 - see docs/LICENCE.txt
 */


class XortifyAuthFactory
{

	/**
	 * Get a reference to the only instance of authentication class
	 * 
	 * if the class has not been instantiated yet, this will also take 
	 * care of that
	 * 
	 * @static
	 * @return      object  Reference to the only instance of authentication class
	 */
	function &getAuthConnection($uname, $spiders_auth_method = 'soap')
	{
		
		$conn = mysql_connect(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS);
		@mysql_select_db(XOOPS_DB_NAME);
		$sql[0] = "SELECT mid FROM `".XOOPS_DB_PREFIX."_modules` WHERE dirname = 'spiders'";		
		list($modid) = @mysql_fetch_row(mysql_query($sql[0]));
		$sql[1] = "SELECT conf_value FROM `".XOOPS_DB_PREFIX."_config` WHERE conf_name = 'spiders_providers' and conf_modid = '".$modid."'";
		list($cvalue) = @mysql_fetch_row(mysql_query($sql[1]));
		$sql[2] = "SELECT conf_value FROM `".XOOPS_DB_PREFIX."_config` WHERE conf_name = 'protocol' and conf_modid = '".$modid."'";
		list($capi) = @mysql_fetch_row(mysql_query($sql[2]));
		$GLOBALS['spiders_api'] = $capi;
		$sql[3] = "SELECT conf_value FROM `".XOOPS_DB_PREFIX."_config` WHERE conf_name = 'spiders_urisoap' and conf_modid = '".$modid."'";
		list($spiders_urisoap) = @mysql_fetch_row(mysql_query($sql[3]));
		$GLOBALS['spiders_urisoap'] = $spiders_urisoap;
		$sql[4] = "SELECT conf_value FROM `".XOOPS_DB_PREFIX."_config` WHERE conf_name = 'spiders_uricurl' and conf_modid = '".$modid."'";
		list($spiders_urisoap) = @mysql_fetch_row(mysql_query($sql[4]));
		$GLOBALS['spiders_uricurl'] = $spiders_urisoap;
		$sql[5] = "SELECT conf_value FROM `".XOOPS_DB_PREFIX."_config` WHERE conf_name = 'spiders_urijson' and conf_modid = '".$modid."'";
		list($spiders_urisoap) = @mysql_fetch_row(mysql_query($sql[5]));
		$GLOBALS['spiders_urijson'] = $spiders_urisoap;		
		
		static $auth_instance;		
		if (!isset($auth_instance)) {
			require_once XOOPS_ROOT_PATH.'/modules/spiders/class/auth/auth.php';
			// Verify if uname allow to bypass LDAP auth 
			$file = XOOPS_ROOT_PATH . '/modules/spiders/class/auth/auth_' . $spiders_auth_method . '.php';			
			require_once $file;
			$class = 'XortifyAuth' . ucfirst($spiders_auth_method);
			switch ($spiders_auth_method) {
				case 'soap';
					$dao = null;
					break;

			}
			$auth_instance = new $class($dao);
		}
		return $auth_instance;
	}

}

?>
