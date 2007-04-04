<?php
    /**************************************************************************\
    * This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
    * ------------------------------------------------------------------------ *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

    $inclpath=ini_get('include_path');
    ini_set('include_path',$inclpath.":./services:../");

    session_start();

    require ('../inc/conf.php');
    require ('inc/globalfunctions.inc.php');
    require ('inc/class.logger.inc.php');

    define('M_SERVICES', 'services');   
    
    //TODO: sollte an einer zentralen Stele stehen; kann ueber PEAR::DB nicht abgefragt werden
    $GLOBALS['db_charset'] = 'ISO-8859-1';
    
    //--- some initialization stuff
    // initialize logging service
    Logger::setLevel(L_DEBUG3);
    Logger::setLogPath("./");
    Logger::initModule(M_SERVICES, "a");
    
    Logger::log(M_SERVICES, L_DEBUG3, "Session:\n".print_array($_SESSION),__FILE__,__LINE__);   
    
    $headers = getallheaders();
    
    //---

    // helper
    function print_array($array) {
//      $ret = '';
//      if(is_array($array)) foreach ($array as $key => $val) {
//          if(is_string($val))
//              $ret .= "$key => $val\n";
//          else if(is_array($val) || is_object($val)) {            
//              $ret .= "$key => ".print_array($val, &$ret)."\n";
//          }       
//      }   
        return print_r($array, TRUE);
    }
    
    // do login stuff
    function login($user, $passwd) {
        global $ERPNAME, $db;
        ini_set("gc_maxlifetime","3600");
        
        Logger::log(M_SERVICES, L_INFO, "User ${user}(${passwd}) try to login",__FILE__,__LINE__);

        $userfile = "../../".$ERPNAME."/users/".$user.".conf";

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
        if (!$dbhost) $dbhost="localhost";


        preg_match("/password => '(.+)'/",$tmp,$hits);
        $pwd=$hits[1];
        if($pwd != $passwd && $pwd != crypt($passwd, substr($user,0,2)))
            return false;                    

        $_SESSION["employee"]= $user;
        $_SESSION["password"]= $pwd;
        $_SESSION["mansel"]=$dbname;
        $_SESSION["dbname"]=$dbname;
        $_SESSION["dbhost"]=$dbhost;
        $_SESSION["dbport"]=(empty($dbport))?5432:$dbport;
        $_SESSION["dbuser"]=$dbuser;
        $_SESSION["dbpasswd"]=$dbpasswd;        
        Logger::log(M_SERVICES, L_INFO, "Open db connection: ".$_SESSION["dbhost"]."@".$_SESSION["dbpasswd"].":".$_SESSION["dbuser"]."/".$_SESSION["dbname"].":".$_SESSION["dbport"],__FILE__,__LINE__);
        $_SESSION["db"]= &CreateObject("Database",$_SESSION["dbhost"],$_SESSION["dbuser"],$_SESSION["dbpasswd"],$_SESSION["dbname"],$_SESSION["dbport"],false);     
        $db = $_SESSION["db"];
        
        $sql="select * from employee where login='$user'";
        $rs=$_SESSION["db"]->getAll($sql);
        if(!$rs) {
            return false;
        } else {
            if ($rs) {
                $tmp=$rs[0];
                $_SESSION["termbegin"]=(($tmp["termbegin"]>=0)?$tmp["termbegin"]:8);
                $_SESSION["termend"]=($tmp["termend"])?$tmp["termend"]:19;
                $_SESSION["Pre"]=$tmp["pre"];
                $_SESSION["interv"]=($tmp["interv"]>0)?$tmp["interv"]:60;
                $_SESSION["loginCRM"]=$tmp["id"];
                $_SESSION["lang"]=$tmp["countrycode"]; //"de";
                $_SESSION["kdview"]=$tmp["kdview"];
                $sql="select * from defaults";
                $rs=$_SESSION["db"]->getAll($sql);
                $_SESSION["ERPver"]=$rs[0]["version"];
                
                return true;
            } else {
                return false;
            }
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
    
    // check user verfication
    function verifyuser() {
        global $headers;
//      $fp = fopen('xml_rpc_out.log','a+');
//      fwrite($fp,"==== GOT ============================\n" . $GLOBALS['HTTP_RAW_POST_DATA']
//          . "\n==== RETURNED =======================\n");
//      fclose($fp);
        Logger::log(M_SERVICES, L_DEBUG3, "==== GOT ============================\n" . $GLOBALS['HTTP_RAW_POST_DATA']
                                          . "\n==== RETURNED =======================\n",__FILE__,__LINE__);


        $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : $headers['authorization'];

        Logger::log(M_SERVICES, L_DEBUG3, "HEADERS: \n".print_array($headers),__FILE__,__LINE__);
        $auth = false;
        if(eregi('Basic *([^ ]*)',$auth_header,$auth))
        {
            list($login, $passwd) = explode(':',base64_decode($auth[1]));
            if(login($login, $passwd))
                $auth = true;
            else
                $auth = false;

        }

        if(!$auth) {

            $auth = login($_SESSION['employee'], $_SESSION['password']);
        }

        return $auth;
    }

?>