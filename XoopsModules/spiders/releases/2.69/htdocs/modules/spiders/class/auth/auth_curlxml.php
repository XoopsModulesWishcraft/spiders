<?php
/**
 * @package     spiders
 * @subpackage  module
 * @description	Sector Network Security Drone
 * @author	    Simon Roberts WISHCRAFT <simon@chronolabs.coop>
 * @copyright	copyright (c) 2010-2013 XOOPS.org
 * @licence		GPL 2.0 - see docs/LICENCE.txt
 */

 
define('XORTIFY_CURLXML_API', $GLOBALS['xoopsModuleConfig']['spiders_urixml']);
define('XORTIFY_USER_AGENT', 'Mozilla/5.0 (X11; U; Linux i686; pl-PL; rv:1.9.0.2) XOOPS/20100101 XoopsAuth/1.xx (php)');
	
include_once XOOPS_ROOT_PATH . '/modules/spiders/class/auth/auth_curlxml_provisionning.php';

class XortifyAuthCurlxml extends XortifyAuth {
	
	var $curl_client;
	var $curl_xoops_username = '';
	var $curl_xoops_password = '';
	var $_dao;
	var $_json = '';
	/**
	 * Authentication Service constructor
	 */
	function XortifyAuthCurlxml (&$dao) {
		if (!$ch = curl_init(XORTIFY_CURLXML_API)) {
			trigger_error('Could not intialise CURL file: '.$url);
			return false;
		}
		$cookies = XOOPS_VAR_PATH.'/cache/xoops_cache/authcurl_'.md5(XORTIFY_CURLXML_API).'.cookie'; 

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, XORTIFY_USER_AGENT); 
		$this->curl_client =& $ch;
	}


	/**
	 *  Authenticate  user again curl directory (Bind)
	 *
	 * @param string $uname Username
	 * @param string $pwd Password
	 *
	 * @return bool
	 */	
	function authenticate($uname, $pwd = null) {
		$authenticated = false;
		$this->XortifyAuthCurlxml($GLOBALS['xoopsDB']);
		
		if (!$this->curl_client) {
			$this->setErrors(0, _AUTH_CURL_EXTENSION_NOT_LOAD);
			return $authenticated;
		}

				
		$rnd = rand(-100000, 100000000);
		curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'xoops_authentication='. spiders_toXml(array("username"=> $this->curl_xoops_username, "password"=> $this->curl_xoops_password, "auth" => array('username' => $uname, "password" => $pwd, "time" => time(), "passhash" => sha1((time()-$rnd).$uname.$pwd), "rand"=>$rnd)), 'xoops_authentication'));
		$data = curl_exec($this->curl_client);
		curl_close($this->curl_client);
		$result = spiders_elekey2numeric(spiders_xml2array($data), 'xoops_authentication');
		return $result['xoops_authentication']["RESULT"];		
	}
	
				  
	/**
	 *  validate a user via curl
	 *
	 * @param string $uname
	 * @param string $email
	 * @param string $pass
	 * @param string $vpass
	 *
	 * @return string
	 */		
	function validate($uname, $email, $pass, $vpass){
		$this->XortifyAuthCurlxml($GLOBALS['xoopsDB']);
		$rnd = rand(-100000, 100000000);
		curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'xoops_user_validate=' . spiders_toXml(array("username"=> $this->curl_xoops_username, "password"=> $this->curl_xoops_password, "validate" => array('uname' => $uname, "pass" => $pass, "vpass" => $vpass, "email" => $email, "time" => time(), "passhash" => sha1((time()-$rnd).$uname.$pass), "rand"=>$rnd)), 'xoops_user_validate'));
		$data = curl_exec($this->curl_client);
		curl_close($this->curl_client);
		$result = spiders_elekey2numeric(spiders_xml2array($data), 'xoops_user_validate');
		if ($result['xoops_user_validate']['ERRNUM']==1){
			return $result['xoops_user_validate']["RESULT"];
		} else {
			return false;
		}
	
	}

	
	/**
	 *  get the xoops site disclaimer via curl
	 *
	 * @return string
	 */			
	function network_disclaimer(){
		$this->XortifyAuthCurlxml($GLOBALS['xoopsDB']);
		curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'xoops_network_disclaimer=' . spiders_toXml(array("username"=> $this->curl_xoops_username, "password"=> $this->curl_xoops_password), 'xoops_network_disclaimer'));
		$data = curl_exec($this->curl_client);
		curl_close($this->curl_client);
		$result = spiders_elekey2numeric(spiders_xml2array($data), 'xoops_network_disclaimer');
		if ($result['xoops_network_disclaimer']['ERRNUM']==1){
			return $result['xoops_network_disclaimer']["RESULT"];
		} else {
			return false;
		}

	}
	
	/**
	 *  create a user
	 *
	 * @param bool $user_viewemail
	 * @param string $uname
	 * @param string $email
	 * @param string $url
	 * @param string $actkey
	 * @param string $pass
	 * @param integer $timezone_offset
	 * @param bool $user_mailok		 
	 * @param array $siteinfo
	 *
	 * @return array
	 */	
	function create_user($user_viewemail, $uname, $email, $url, $actkey, 
						 $pass, $timezone_offset, $user_mailok, $siteinfo){
						 
		$siteinfo = $this->check_siteinfo($siteinfo);

		$rnd = rand(-100000, 100000000);
		$this->XortifyAuthCurlxml($GLOBALS['xoopsDB']);
		curl_setopt($this->curl_client, CURLOPT_POSTFIELDS, 'xoops_create_user='.spiders_toXml(array("username"=> $this->curl_xoops_username, "password"=> $this->curl_xoops_password, "user" => array('user_viewemail' =>$user_viewemail, 'uname' => $uname, 'email' => $email, 'url' => $url, 'actkey' => $actkey, 'pass' => $pass, 'timezone_offset' => $timezone_offset, 'user_mailok' => $user_mailok, "time" => time(), "passhash" => sha1((time()-$rnd).$uname.$pass), "rand"=>$rnd), "siteinfo" => $siteinfo), 'xoops_create_user'));
		$data = curl_exec($this->curl_client);
		curl_close($this->curl_client);	
		$result = spiders_elekey2numeric(spiders_xml2array($data), 'xoops_create_user');	
		if ($result['xoops_create_user']['ERRNUM']==1){
			return $result['xoops_create_user']["RESULT"];		
		} else {
			return false;
		}
	}
	
		
}
// end class


?>
