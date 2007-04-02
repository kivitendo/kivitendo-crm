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
require_once 'XML/RPC.php';

class XMLRPCServer
{	

   private static $instance;
   private $simpledate;
  
   private function __construct()
   {
		global $XML_RPC_Server_dmap;
		
		$XML_RPC_Server_dmap['system.login']['function'] = array($this,'login');
		$XML_RPC_Server_dmap['system.logout']['function'] = array($this,'logout');
		
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
   
   function setSimpleDate($simple) {
   	$this->simpledate = $simple;
   }
   
   function getSimpleDate() {
   	return $this->simpledate;
   }

	public function addService($xmlrpc_service) {
			
		if(!is_array($xmlrpc_service))
			return false;
		$this->dispatch_map = array_merge($this->dispatch_map, $xmlrpc_service);
	}   
	
	public function doSystemService() {
				
    	Logger::log(M_SERVICES, L_INFO, "Start system services",__FILE__,__LINE__);       
		
		$xmlrpc_server = new XML_RPC_Server(array(), 1);					
	}	
	
	public function doService() {
		
		$mp = (!$this->authed ? array() :  $this->dispatch_map);
		
    	Logger::log(M_SERVICES, L_INFO, "start service witth methods:\n".print_array($mp),__FILE__,__LINE__);       
		
		$xmlrpc_server = new XML_RPC_Server($mp, 1);					
	}	

	// 
	public function __clone()
	{
	    trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	
	public function login($xml_rpc_msg, $params) {	
		
	   $param = $params->getParam(0);
	//	dump_rpc($param);
	
	    if (!XML_RPC_Value::isValue($param) || !$param->kindof('struct')) {
	        return $param;
	    }
	    
//	    $param->structreset();
//		while (list($key, $keyval) = $param->structeach ()) {
//		   		echo $key." => ".$keyval->scalarval().", ";
//		}
//		while( list($key,$val) = each($_SESSION) ) {
//			unset($_SESSION[$key]);
//		};
		$username = $param->structmem('username');
		$password = $param->structmem('password');
		
		$crypt_passwd = crypt($password->scalarval(), substr($username->scalarval(),0,2));
		
		$_SESSION['employee'] = $username->scalarval();
		$_SESSION['password'] = $crypt_passwd;
		
				
		$_SESSION['sessionid'] = $username->scalarval(); // should be depraceted
		$_SESSION['kp3'] = $crypt_passwd;// should be depraceted
		
		
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
//		unset($_SESSION['employee']);
//		unset($_SESSION['password']);
				
		$resp_val = new XML_RPC_Value(array(
		    "GOODBYE" => new XML_RPC_Value('XOXO')), "struct");
	    
	    return new XML_RPC_Response($resp_val);
	}
	
//		
//	/**
//	 * Converts an XML_RPC_Value object into native PHP types
//	 *
//	 * @param object $XML_RPC_val  the XML_RPC_Value object to decode
//	 *
//	 * @return mixed  the PHP values
//	 */
//	function XML_RPC_decode($XML_RPC_val)
//	{
//	    $kind = $XML_RPC_val->kindOf();
//	
//	    if ($kind == 'scalar') {
//	        return $XML_RPC_val->scalarval();
//	
//	    } elseif ($kind == 'array') {
//	        $size = $XML_RPC_val->arraysize();
//	        $arr = array();
//	        for ($i = 0; $i < $size; $i++) {
//	            $arr[] = XML_RPC_decode($XML_RPC_val->arraymem($i));
//	        }
//	        return $arr;
//	
//	    } elseif ($kind == 'struct') {
//	        $XML_RPC_val->structreset();
//	        $arr = array();
//	        while (list($key, $value) = $XML_RPC_val->structeach()) {
//	            $arr[$key] = XML_RPC_decode($value);
//	        }
//	        return $arr;
//	    }
//	}
//	
//	/**
//	 * Converts native PHP types into an XML_RPC_Value object
//	 *
//	 * @param mixed $php_val  the PHP value or variable you want encoded
//	 *
//	 * @return object  the XML_RPC_Value object
//	 */
//	function XML_RPC_encode($php_val)
//	{
//	    $type = gettype($php_val);
//	    $XML_RPC_val = new XML_RPC_Value;
//	
//	    switch ($type) {
//	    case 'array':
//	        if (empty($php_val)) {
//	            $XML_RPC_val->addArray($php_val);
//	            break;
//	        }
//	        $tmp = array_diff(array_keys($php_val), range(0, count($php_val)-1));
//	        if (empty($tmp)) {
//	           $arr = array();
//	           foreach ($php_val as $k => $v) {
//	               $arr[$k] = XML_RPC_encode($v);
//	           }
//	           $XML_RPC_val->addArray($arr);
//	           break;
//	        }
//	        // fall though if it's not an enumerated array
//	
//	    case 'object':
//	        $arr = array();
//	        foreach ($php_val as $k => $v) {
//	            $arr[$k] = XML_RPC_encode($v);
//	        }
//	        $XML_RPC_val->addStruct($arr);
//	        break;
//	
//	    case 'integer':
//	        $XML_RPC_val->addScalar($php_val, $GLOBALS['XML_RPC_Int']);
//	        break;
//	
//	    case 'double':
//	        $XML_RPC_val->addScalar($php_val, $GLOBALS['XML_RPC_Double']);
//	        break;
//	
//	    case 'string':
//	    case 'NULL':
//			if (($arr = split('[-:T]',$php_val)) && count($arr) == 6)
//			{
//				// $php_val is extended ISO8601
//				$XML_RPC_val->addScalar($php_val, $GLOBALS['XML_RPC_DateTime']);
//			}
//	        elseif ($GLOBALS['XML_RPC_func_ereg']('^[0-9]{8}\T{1}[0-9]{2}\:[0-9]{2}\:[0-9]{2}$', $php_val)) {
//	        	// $php_val is simple ISO8601
//	            $XML_RPC_val->addScalar($php_val, $GLOBALS['XML_RPC_DateTime']);
//	        } elseif ($GLOBALS['XML_RPC_auto_base64']
//	                  && $GLOBALS['XML_RPC_func_ereg']("[^ -~\t\r\n]", $php_val))
//	        {
//	            // Characters other than alpha-numeric, punctuation, SP, TAB,
//	            // LF and CR break the XML parser, encode value via Base 64.
//	            $XML_RPC_val->addScalar($php_val, $GLOBALS['XML_RPC_Base64']);
//	        } else {
//	            $XML_RPC_val->addScalar($php_val, $GLOBALS['XML_RPC_String']);
//	        }
//	        break;
//	
//	    case 'boolean':
//	        // Add support for encoding/decoding of booleans, since they
//	        // are supported in PHP
//	        // by <G_Giunta_2001-02-29>
//	        $XML_RPC_val->addScalar($php_val, $GLOBALS['XML_RPC_Boolean']);
//	        break;
//	
//	    case 'unknown type':
//	    default:
//	        $XML_RPC_val = false;
//	    }
//	    return $XML_RPC_val;
//	}
   
   	public $authed;
	private $dispatch_map;
}
?>