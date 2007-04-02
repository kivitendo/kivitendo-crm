<?php

    /**************************************************************************\    
    * This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
    * ------------------------------------------------------------------------ *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/
 
 
 if (function_exists ("DebugBreak")) {
  DebugBreak ();
}
 
 require_once 'XML/RPC.php';
 require_once 'XML/RPC/Dump.php';

//$cli = new XML_RPC_Client('/egw/xmlrpc.php', 'localhost');
//$cli = new XML_RPC_Client('/egw/xmlrpc.php?DBGSESSID=1@localhost:10001', 'localhost');
//$cli = new XML_RPC_Client('/egroupware/xmlrpc.php', 'ldapserver');
$cli = new XML_RPC_Client('/lx/crm/services/xmlrpc.php?DBGSESSID=1@localhost:10001', 'localhost');
//$cli = new XML_RPC_Client('/lx/crm/services/xmlrpc.php', 'localhost');
//$cli = new XML_RPC_Client('/lx/crm/services/xmlrpc.php', 'ldapserver');


function rpc_call($function_name, $params) {
    global $cli;
    $msg = new XML_RPC_Message($function_name);
    $msg->addParam($params);
    
    $cli->setDebug(1);
    $resp = $cli->send($msg);
    
    echo  $msg->serialize ()."\n";
    
    if (!$resp) {
        echo 'Communication error: ' . $cli->errstr;
        return false;
    }
    
    if (!$resp->faultCode()) {
        return $resp->value();
//      $val = $resp->value();
    //    echo var_dump($val)."<br>";
//      $sessionid = $val->structmem('sessionid')->scalarval();
//      $kp3 = $val->structmem('kp3')->scalarval();
//      echo "sessionid: ".$sessionid."<br>";
//      echo "kpr: ".$kp3."<br>";
        
    } else {
        /*
         * Display problems that have been gracefully cought and
         * reported by the xmlrpc.php script.
         */
        echo 'Fault Code: ' . $resp->faultCode()."\n";
        echo 'Fault Reason: ' . $resp->faultString()."\n";
        return false;
    }   
}

function doLogin() {

    $params = new XML_RPC_Value(array(
        "domain" => new XML_RPC_Value("default"),
        "username" => new XML_RPC_Value("jd"),
        "password" => new XML_RPC_Value("joshi75")), "struct");
    
    $resp = rpc_call('system.login', $params);
     
    if (!$resp) {    
        return false;
    }
    
    $val = $resp;
    //    echo var_dump($val)."<br>";
    $sessionid = $val->structmem('sessionid')->scalarval();
    $kp3 = $val->structmem('kp3')->scalarval();
    
    return array('sessionid' => $sessionid, 'kp3' => $kp3);
}

function doLogout() {

    $params = new XML_RPC_Value(array(
        "sessionid" => new XML_RPC_Value("default"),
        "kp3" => new XML_RPC_Value("jd")), "struct");
    
    $resp = rpc_call('system.logout', $params);
     
    if (!$resp) {    
        return false;
    }
    
    $val = $resp->value();
    //    echo var_dump($val)."<br>";
    echo $val->structmem('GOODBYE')->scalarval()."\n";
    
}

function addressbook_boaddressbook_search() {   
    global $cli;
        
    $params = new XML_RPC_Value(array(
        "start" => new XML_RPC_Value("0"),
        "query" => new XML_RPC_Value(""),
        "filter" => new XML_RPC_Value(""),
        "sort" => new XML_RPC_Value(""),
        "order" => new XML_RPC_Value(""),
        "include_users" => new XML_RPC_Value("calendar")), "struct");   
        
    $rsp = rpc_call('addressbook.boaddressbook.search', $params);   
    if(!$rsp)
        return;
    
//  echo $rsp->serialize();
//  echo $rsp->serialize();
//  XML_RPC_Dump($rsp);
    for($i=0; $i < $rsp->arraysize(); $i++) {
        $rpc_val = $rsp->arraymem($i);
        $rpc_val->structreset();
        while (list($key, $keyval) = $rpc_val->structeach ()) {
            echo $key." => ".$keyval->scalarval().", ";
        }
        echo "\n\r";
    }

}

function addressbook_boaddressbook_read() {
    global $cli;
    
//  $params = new XML_RPC_Value(array(
//      "id" => new XML_RPC_Value("1")), "struct"); 
    $params = new XML_RPC_Value(array(
        "id" => new XML_RPC_Value("833")), "struct");
    $rsp = rpc_call('addressbook.boaddressbook.read', $params); 
    if(!$rsp)
        return;
//  echo $rsp->serialize();
//  echo $rsp->serialize();
//  XML_RPC_Dump($rsp);
    for($i=0; $i < $rsp->arraysize(); $i++) {
        $rpc_val = $rsp->arraymem($i);
        $rpc_val->structreset();
        while (list($key, $keyval) = $rpc_val->structeach ()) {
            echo $key." => ".$keyval->scalarval().", ";
        }
        echo "\n\r";
    }

}


function addressbook_boaddressbook_write() {
    global $cli;

    
    $params = new XML_RPC_Value(array(
        "id" => new XML_RPC_Value("892"),
        "n_given" => new XML_RPC_Value("Christina"),
        "n_family" => new XML_RPC_Value("NaseNAse"),
        "fn" => new XML_RPC_Value("Christina NaseNase"),
        "adr_two_countryname" => new XML_RPC_Value("Deutschland"),
        "adr_two_locality" => new XML_RPC_Value("Imääääääääääään"),
        "adr_two_postalcode" => new XML_RPC_Value("78655"),
        "adr_two_street" => new XML_RPC_Value("Testweg 333"),
        "tel_home" => new XML_RPC_Value("093409234"),
        "url" => new XML_RPC_Value("www.test.de"),
        "bday" => new XML_RPC_Value("1955-12-30T00:00:00",$GLOBALS['XML_RPC_DateTime']),
        "org_name" => new XML_RPC_Value("Firma")), "struct");   
        
    $rsp = rpc_call('addressbook.boaddressbook.write', $params);    
    if(!$rsp)
        return;
    

//  XML_RPC_Dump($rsp);
    echo "id: ".$rsp->scalarval();
}


function addressbook_boaddressbook_delete() {
   

    $params = new XML_RPC_Value(860, "int");    
        
    $rsp = rpc_call('addressbook.boaddressbook.delete', $params);   
    if(!$rsp)
        return;
}

function addressbook_boaddressbook_categories() {

    $params = new XML_RPC_Value(false, 'boolean');
    $rsp = rpc_call('addressbook.boaddressbook.categories', $params);   
    if(!$rsp)
        return;

    $rsp->structreset();
    while (list($key, $keyval) = $rsp->structeach ()) {
        echo $key." => ".$keyval->scalarval().", ";
    }
    echo "\n\r";
    
    $params = new XML_RPC_Value(true, 'boolean');
    $rsp = rpc_call('addressbook.boaddressbook.categories', $params);   
    if(!$rsp)
        return;

    $rsp->structreset();
    while (list($key1, $keyval1) = $rsp->structeach ()) {
//          echo $key." => ".$keyval->scalarval().", ";
        while (list($key2, $keyval2) = $keyval1->structeach ()) {
            echo $key2." => ".$keyval2->scalarval().", ";
        }
    }
    echo "\n\r";

}

function addressbook_boaddressbook_customfields() {
    $params = new XML_RPC_Value(array(),'array');
    $rsp = rpc_call('addressbook.boaddressbook.customfields', $params); 
    if(!$rsp)
        return;

    $rsp->structreset();
    while (list($key, $keyval) = $rsp->structeach ()) {
        echo $key." => ".$keyval->scalarval().", ";
    }
    echo "\n\r";

}

function calender_bocalender_search() { 
    global $cli;
        
    $params = new XML_RPC_Value(array(
        "start" => new XML_RPC_Value("2007-03-09T00:00:00",'dateTime.iso8601'),
        "end" => new XML_RPC_Value("2012-09-10T00:00:00",'dateTime.iso8601')), "struct");   
        
    $rsp = rpc_call('calender.bocalender.search', $params); 
    if(!$rsp)
        return;
    
//  echo $rsp->serialize();
//  echo $rsp->serialize();
//  XML_RPC_Dump($rsp);
    for($i=0; $i < $rsp->arraysize(); $i++) {
        $rpc_val = $rsp->arraymem($i);
        $rpc_val->structreset();
        while (list($key, $keyval) = $rpc_val->structeach ()) {
            echo $key." => ".$keyval->scalarval().", ";
        }
        echo "\n\r";
    }

}

$cli->setCredentials ('jd','joshi75');
//$cli->setCredentials ('md','mane');
//$auth = doLogin();
//if(!$auth)
//  return;
//$cli->setCredentials ($auth['sessionid'], $auth['kp3']);


addressbook_boaddressbook_search();
//addressbook_boaddressbook_read();
//addressbook_boaddressbook_write();
//addressbook_boaddressbook_delete();
//addressbook_boaddressbook_categories();
//addressbook_boaddressbook_customfields();

//calender_bocalender_search();
?>
