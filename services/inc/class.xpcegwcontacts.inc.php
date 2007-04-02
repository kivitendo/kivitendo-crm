<?php
	/**************************************************************************\
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/


	include_once ('inc/class.egwcontactwrapper.inc.php');
	include_once ('inc/class.xmlrpchandler.inc.php');
	include_once ('inc/class.datetime.inc.php');

	class XPCEGWContacts extends XMLRPCHandler
	{
		function __construct($db, $employeeid) {
       		parent::__construct($db);

			$this->service_list = 	    array(
		        'addressbook.boaddressbook.search' => array(
		            'function' => array($this, 'search')
		        ),
		        'addressbook.boaddressbook.write' => array(
		            'function' => array($this, 'write')
		        ),
		        'addressbook.boaddressbook.delete' => array(
		            'function' => array($this, 'delete')
		        ),
		        'addressbook.boaddressbook.categories' => array(
		            'function' => array($this, 'categories')
		        ),
		        'addressbook.boaddressbook.customfields' => array(
		            'function' => array($this, 'customfields')
		        ));

//				$this->db() = $db;
				 Logger::log(M_SERVICES, L_DEBUG3, "XPCEGWContacts():\nemployeeid = ".$employeeid."\ndb = ".print_array($db),__FILE__,__LINE__);
				$this->contacts = CreateObject('EGWContacts',$db, $employeeid);
//				$this->contacts->set_db($this->db());
		}

		function get_rpc_list() {
			return $this->service_list;
		}


		public function search($params) {
			$param = $params->getParam(0);

		    if (!XML_RPC_Value::isValue($param)) {
		        return $param;
		    }
			Logger::log(M_SERVICES, L_DEBUG3, $param->serialize(), __FILE__,__LINE__);

			$cs = $this->contacts->get_contacts();
			
			$rsp = new XML_RPC_Response($this->encode($cs));
			Logger::log(M_SERVICES, L_DEBUG3, $rsp->serialize(), __FILE__,__LINE__);

		    return $rsp;

		}

		public function write($params) {

		   	$param = $params->getParam(0);
		    if (!XML_RPC_Value::isValue($param)) {
		        return new XML_RPC_Response(new XML_RPC_Value(1, 'string'));
		    }
		   	$param = $this->decode($params->getParam(0));

		    $contactid = ($param['id'] ? $param['id'] : null);
		    if(!isset($contactid) || $contactid == 0){

		    	$contactid = $this->contacts->create_new_contact();
		    	if(!$contactid)
		    		return new XML_RPC_Response(new XML_RPC_Value(3, 'string'));
		    }
			//TODO: Firmendaten abgleichen !!!

			$res = $this->contacts->update_contact($contactid, $param);
			if(!$res)
				return new XML_RPC_Response(new XML_RPC_Value(5, 'string'));


		    return new XML_RPC_Response($this->encode(true));

		}

		public function delete($params) {

			$this->db_map;

		   	$param = $params->getParam(0);

		    if (!XML_RPC_Value::isValue($param)) {
		        return new XML_RPC_Response(new XML_RPC_Value(1, 'string'));
		    }

		    $customerid = ($param->scalarval() ? $param->scalarval() : null);
		    if(isset($customerid)){

		    	$this->contacts->delete_contact($customerid);
		    }

		    return $this->check_return(new XML_RPC_Response(new XML_RPC_Value(1, 'boolean')));
		}

		public function categories($params) {

		   	$param = $params->getParam(0);


		    if (!XML_RPC_Value::isValue($param)) {
		        return $param;
		    }

		    $cats=array();
		    foreach(EgwContactWrapper::$categories as $cat_name => $cat_desc) {
		    	$cats[$cat_desc['id']] = new XML_RPC_Value($cat_desc['value']);
		    }

			$resp_val = new XML_RPC_Value($cats, "struct");

		    return $this->check_return(new XML_RPC_Response($resp_val));

		}

		public function customfields($params) {
			   $param = $params->getParam(0);


		    if (!XML_RPC_Value::isValue($param)) {
		        return $param;
		    }

			$resp_val = new XML_RPC_Value(array(
			    "freebusy_url" => new XML_RPC_Value('freebusy URL')), "struct");

//			$resp_val = new XML_RPC_Value(array(), "struct");

		    return $this->check_return(new XML_RPC_Response($resp_val));

		}

//		private $db;
		private $contacts;
		private $employees;
		private $service_list;
	}

?>