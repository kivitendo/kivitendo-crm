<?php
	/**************************************************************************\
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	require_once('./header.inc.php');

	require_once('inc/class.xmlrpcserver.inc.php');
	require_once('inc/class.xpcegwcontacts.inc.php');
	
    

	$server = XMLRPCServer::instance();
	// check date format
	$isodate = isset($headers['isoDate']) ? $headers['isoDate'] : $headers['isodate'];
    $isodate = ($isodate == 'simple') ? True : False;
	// set date format
	$server->setSimpleDate($isodate);
	
	// verify incoming user
	$server->authed = verifyuser();
	if(!$server->authed) {
		Logger::log(M_SERVICES, L_DEBUG3, "Request without, userdata -> allow only system services",__FILE__,__LINE__);
		$server->doSystemService();
    	Logger::log(M_SERVICES, L_INFO, "*** service finished ***",__FILE__,__LINE__);
		return;		
	}

    Logger::log(M_SERVICES, L_DEBUG3, 'Login: '. $_SESSION['employee']. ', password: '. $_SESSION['password']. ', auth='. ($server->authed ? 'true' : 'false'),__FILE__,__LINE__);
    

	// create contact service handler based on kaddressbook <=> egroupware interface
	$xmlrpccontacts = new XPCEGWContacts($_SESSION['db'], $_SESSION['loginCRM']);

	// add service to the server
	$server->addService($xmlrpccontacts->get_rpc_list());
	// handle service request
   	$server->doService();
    Logger::log(M_SERVICES, L_INFO, "*** service finished ***",__FILE__,__LINE__);



?>