<?php
	/**************************************************************************\	
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

require_once 'XML/RPC/Server.php';


class XMLRPCServer
{	

   private static $instance;
  
   private function __construct()
   {
		$this->dispatch_map = array();
   }

   public static function instance()
   {
       if (!isset(self::$instance)) {
           $c = __CLASS__;
           self::$instance = new $c;
       }

       return self::$instance;
   }
   
   public function &getDatabase() {
   		return $GLOBALS['lxdb'];
   }

	public function addService($xmlrpc_service) {
			
		if(!is_array($xmlrpc_service))
			return false;
		$this->dispatch_map = array_merge($this->dispatch_map, $xmlrpc_service);
	}   
	
	public function doService() {
		global $XML_RPC_Server_dmap;
       
		$XML_RPC_Server_dmap['system.login']['function'] = array($this,'login');
		$XML_RPC_Server_dmap['system.logout']['function'] = array($this,'logout');	
		if(!$this->authed) {
			
			$xmlrpc_server = new XML_RPC_Server(array(), 1);
		}
		else{
			$xmlrpc_server = new XML_RPC_Server($this->dispatch_map, 1);
		}
		
	}
	
	
	// convert a date-array or timestamp into a datetime.iso8601 string
	function date2iso8601($date)
	{
		if (!is_array($date))
		{
			if($this->simpledate)
			{
				return date('Ymd\TH:i:s',$date);
			}
			return date('Y-m-d\TH:i:s',$date);
		}

		$formatstring = "%04d-%02d-%02dT%02d:%02d:%02d";
		if($this->simpledate)
		{
			$formatstring = "%04d%02d%02dT%02d:%02d:%02d";
		}
		return sprintf($formatstring,
			$date['year'],$date['month'],$date['mday'],
			$date['hour'],$date['min'],$date['sec']);
	}

	// convert a datetime.iso8601 string into a datearray or timestamp
	function iso86012date($isodate,$timestamp=False)
	{
		$arr = array();

		if (ereg('^([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})$',$isodate,$arr))
		{
			// $isodate is simple ISO8601, remove the difference between split and ereg
			array_shift($arr);
		}
		elseif (($arr = split('[-:T]',$isodate)) && count($arr) == 6)
		{
			// $isodate is extended ISO8601, do nothing
		}
		else
		{
			return False;
		}

		foreach(array('year','month','mday','hour','min','sec') as $n => $name)
		{
			$date[$name] = (int)$arr[$n];
		}
		return $timestamp ? mktime($date['hour'],$date['min'],$date['sec'],
			$date['month'],$date['mday'],$date['year']) : $date;
	}


	// 
	public function __clone()
	{
	    trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	
	public function login($xml_rpc_msg, $params) {	
		
	   $param = $params->getParam(0);
	//	dump_rpc($param);
	
	    // This error checking syntax was added in Release 1.3.0
	    if (!XML_RPC_Value::isValue($param) || !$param->kindof('struct')) {
	        return $param;
	    }
	    
	//    $param->structreset();
	//	while (list($key, $keyval) = $param->structeach ()) {
	//	   		echo $key." => ".$keyval->scalarval().", ";
	//	}
		while( list($key,$val) = each($_SESSION) ) {
			unset($_SESSION[$key]);
		};
		$username = $param->structmem('username');
		$password = $param->structmem('password');
		
		$crypt_passwd = crypt($password->scalarval(), substr($username->scalarval(),0,2));
		
		$_SESSION['login'] = $username->scalarval();
		$_SESSION['password'] = $crypt_passwd;
		
		
		$resp_val = new XML_RPC_Value(array(
		    "sessionid" => new XML_RPC_Value($username->scalarval()),
		    "kp3" => new XML_RPC_Value($crypt_passwd)), "struct");
	    
	    return new XML_RPC_Response($resp_val);
	}
	
	public function logout($xml_rpc_msg, $params) {
	   $param = $params->getParam(0);
	
	    // This error checking syntax was added in Release 1.3.0
	    if (!XML_RPC_Value::isValue($param)) {
	        return $param;
	    }
	
		
		$resp_val = new XML_RPC_Value(array(
		    "GOODBYE" => new XML_RPC_Value('XOXO')), "struct");
	    
	    return new XML_RPC_Response($resp_val);
	}
   
   	public $authed;
	private $dispatch_map;
}
?>