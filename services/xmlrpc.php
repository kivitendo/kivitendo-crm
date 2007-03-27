<?php
	/**************************************************************************\	
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	require_once('header.inc.php');
	
	require_once(LX_CRM_SERVICE_ROOT.'inc/class.xmlrpcserver.inc.php');
	require_once(LX_CRM_SERVICE_ROOT.'inc/class.xmlrpccontacts.inc.php');
	

	XMLRPCServer::instance()->authed = verifyuser();
	
//	echo 'Login: '. $_SESSION['login']. ', password: '. $_SESSION['password']. 'auth='. (XMLRPCServer::instance()->authed ? 'true' : 'false');
	
	$xmlrpccontacts = new XMLRPCContacts($GLOBALS['lxdb']);
			
	XMLRPCServer::instance()->addService($xmlrpccontacts->get_rpc_list());
	
   	XMLRPCServer::instance()->doService();	
			

?>