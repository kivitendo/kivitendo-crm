<?php
	/**************************************************************************\	
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/


	// Insert your lx paths (erp and crm)
	define('LX_ERP_ROOT','/var/www/unstable/');
	define('LX_CRM_ROOT',LX_ERP_ROOT. 'crm/'); // not in use at the moment
	
	// Should not be changed
	define('LX_CRM_SERVICE_ROOT', LX_CRM_ROOT. 'services/');	
	
	
	session_start();
	

//	require_once (LX_CRM_SERVICE_ROOT . 'inc/class.database.inc.php');
	require_once (LX_CRM_ROOT . 'inc/db.php');
	require_once (LX_CRM_SERVICE_ROOT . 'inc/global_functions.inc.php');
	
	

	$GLOBALS['lxlogin'] = '';									
	$GLOBALS['lxpasswd'] = '';
	$GLOBALS['lxemployee_id'] = '';	
   	$GLOBALS['lxlogin'] = '';	
	$GLOBALS['lxdb'] = null;	
	
	
	function login($user, $passwd) {
			
		ini_set("gc_maxlifetime","3600");
		
		$userfile = LX_ERP_ROOT."users/".$user.".conf";
		
		if(!is_readable($userfile))
			return false;
		$tmp = @file_get_contents($userfile);
		preg_match("/dbname => '(.+)'/",$tmp,$hits);
		$dbname=$hits[1];
		preg_match("/dbpasswd => '(.+)'/",$tmp,$hits);
		$dbpasswd=$hits[1];
		preg_match("/dbuser => '(.+)'/",$tmp,$hits);
		$dbuser=$hits[1];
		preg_match("/dbhost => '(.+)'/",$tmp,$hits);
		$dbhost=$hits[1];
		preg_match("/dbport => '(.+)'/",$tmp,$hits);
		$dbport=$hits[1];
		preg_match("/password => '(.+)'/",$tmp,$hits);
		$pwd=$hits[1];
		$rr = crypt($passwd, substr($user,0,2));
		if($pwd != $passwd && $pwd != crypt($passwd, substr($user,0,2)))
			return false;
		
		$GLOBALS['lxlogin'] = $user;									
		$GLOBALS['lxpasswd'] = $pwd;
		
		if (!$dbhost) $dbhost="localhost";
//		chkdir($dbname);
	   	$GLOBALS['lxlogin']=$user;	
		$GLOBALS['lxdb']= CreateObject('Database', $dbhost, $dbuser, $dbpasswd,$dbname,$dbport);	

		$sql="select * from employee where login='$user'";
		$rs=$GLOBALS['lxdb']->getAll($sql);
		if(!$rs) {
			return false;
		} 
		else {
			$tmp=$rs[0];
			$GLOBALS['lxemployee_id'] = $tmp['id'];
			$_SESSION['login'] = $user;
			$_SESSION['password'] = $pwd;
			return true;
		}
		
	}
	
	
	/* Note: this command only available natively in Apache (Netscape/iPlanet/SunONE in php >= 4.3.3) */
	if(!function_exists('getallheaders'))
	{
		function getallheaders()
		{
			settype($headers,'array');
			foreach($_SERVER as $h => $v)
			{
				if(ereg('HTTP_(.+)',$h,$hp))
				{
					$headers[$hp[1]] = $v;
				}
			}
			return $headers;
		}
	}
	
	function verifyuser() {
	
		$headers = getallheaders();
	
//	    $fp = fopen('xml_rpc_out.log','a+');	
//		fwrite($fp,"==== GOT ============================\n" . $GLOBALS['HTTP_RAW_POST_DATA']
//			. "\n==== RETURNED =======================\n");	
//		fclose($fp);
		
		$isodate = isset($headers['isoDate']) ? $headers['isoDate'] : $headers['isodate'];
		$isodate = ($isodate == 'simple') ? True : False;
	//	$server->setSimpleDate($isodate);
		$auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : $headers['authorization'];
	
		$auth = false;
		if(eregi('Basic *([^ ]*)',$auth_header,$auth))
		{
			list($login, $passwd) = explode(':',base64_decode($auth[1]));
			if(login($login, $passwd))
				$auth = true;		
			else			
				$auth = false;	
			
		}	
		
	//	echo 'Login: '. $_SESSION['login']. ', password: '. $_SESSION['password'];
		if(!$auth) {
			
			$auth = login($_SESSION['login'], $_SESSION['password']);
		}
		
		return $auth;
	}
	
?>
