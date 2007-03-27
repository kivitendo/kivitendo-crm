<?php
	/**************************************************************************\	
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	
	
	include_once (LX_CRM_SERVICE_ROOT.'inc/class.egwwrapper.inc.php');

	class XMLRPCContacts
	{
		function XMLRPCContacts($db) {
//			global $db_map;
//			$this->db_map = $db_map;
	
			$this->service_list = 	    array(
		        'addressbook.boaddressbook.search' => array(
		            'function' => array($this, 'addressbook_boaddressbook_search')
		        ),
		        'addressbook.boaddressbook.write' => array(
		            'function' => array($this, 'addressbook_boaddressbook_write')
		        ),  
		        'addressbook.boaddressbook.delete' => array(
		            'function' => array($this, 'addressbook_boaddressbook_delete')
		        ),  
		        'addressbook.boaddressbook.categories' => array(
		            'function' => array($this, 'addressbook_boaddressbook_categories')
		        ),   
		        'addressbook.boaddressbook.customfields' => array(
		            'function' => array($this, 'addressbook_boaddressbook_customfields')
		        ));
				
				$this->db = $db;
				$this->contacts = CreateObject('Contacts');
				$this->contacts->set_db($this->db);
				$this->employees = CreateObject('Employees');
				$this->employees->set_db($this->db);
		}
		
		function get_rpc_list() {
			return $this->service_list;
		}
		
		
		public function addressbook_boaddressbook_search($params) {
			$param = $params->getParam(0);
		
		    // This error checking syntax was added in Release 1.3.0
		    if (!XML_RPC_Value::isValue($param)) {
		        return $param;
		    }    
					
			$customers = $this->contacts->get_customer_contacts();			
			$customers = EgwWrapper::add_egw_categorie($customers, 'customer');
			
			$vendors = $this->contacts->get_vendor_contacts();
			$vendors = EgwWrapper::add_egw_categorie($vendors, 'vendor');
			
			$persons = $this->contacts->get_contacts();
			$persons = EgwWrapper::add_egw_categorie($persons, 'person');		
				
			$employees = $this->employees->get_employees();
			$employees = EgwWrapper::add_egw_categorie($employees, 'employee');
				
			$data = array_merge(($customers ? $customers : array()) ,
								($vendors ? $vendors : array()), 
								($persons ? $persons : array()),
								($employees ? $employees : array())								
								);
					
		    $userid= $GLOBALS['lxemployee_id']; 
			$key=array();
			$tmp_result_data = array();
			$final_result = array();
			foreach ($data as $row) {
				if (!in_array($row["cp_id"],$key)) {
					$key[]=$row["cp_id"];
					$tmp_result_data[]=$row;
//					$mapped_address = $this->prepare_addressbook_entry($row);
					$mapped_address = EgwWrapper::contact_lx2egw($userid, $row);
					
					$final_result[] = new XML_RPC_Value($mapped_address, 'struct');
				}
			}
			
			$rsp_value = new XML_RPC_Value($final_result, 'array');
			
			    
		    return new XML_RPC_Response($rsp_value);
			
		}
		
		public function addressbook_boaddressbook_write($params) {
			$this->db_map;
    
		   	$param = $params->getParam(0);
		
		    // This error checking syntax was added in Release 1.3.0
		    if (!XML_RPC_Value::isValue($param)) {
		        return new XML_RPC_Response(new XML_RPC_Value(1, 'string'));
		    }
		    
		    $userid= $GLOBALS['lxemployee_id']; 
		    
		    $contactid = ($param->structmem('id') ? $param->structmem('id')->scalarval() : null);
		    if(!isset($contactid) || $contactid == 0){
		    	    	
		    	$contactid = $this->contacts->create_new_contact($userid);
		    	if(!$contactid)
		    		return new XML_RPC_Response(new XML_RPC_Value(3, 'string'));
		    }
		    
			$mapped_params = EgwWrapper::contact_egw2lx($param);
			
			//TODO: Firmendaten abgleichen !!!
			
			$res = $this->contacts->update_contact($userid, $contactid, $mapped_params);
			if(!$res)
				return new XML_RPC_Response(new XML_RPC_Value(5, 'string'));
			
		
				    
		    return new XML_RPC_Response(new XML_RPC_Value(1, 'boolean'));
			
		}
		
		public function addressbook_boaddressbook_delete($params) {
			
			$this->db_map;
    
		   	$param = $params->getParam(0);
		
		    // This error checking syntax was added in Release 1.3.0
		    if (!XML_RPC_Value::isValue($param)) {
		        return new XML_RPC_Response(new XML_RPC_Value(1, 'string'));
		    }
		    		    
		    $customerid = ($param->scalarval() ? $param->scalarval() : null);
		    if(isset($customerid)){
		    	    	
		    	$this->contacts->delete_contact($customerid);
		    }		
				    
		    return new XML_RPC_Response(new XML_RPC_Value(1, 'boolean'));
		}
		
		public function addressbook_boaddressbook_categories($params) {

		   	$param = $params->getParam(0);
		
		    // This error checking syntax was added in Release 1.3.0
		    if (!XML_RPC_Value::isValue($param)) {
		        return $param;
		    }
		    
		    $cats=array();
		    foreach(EgwWrapper::$categories as $cat_name => $cat_desc) {
		    	$cats[$cat_desc['id']] = new XML_RPC_Value($cat_desc['value']);
		    }
		
			$resp_val = new XML_RPC_Value($cats, "struct");
		    
		    return new XML_RPC_Response($resp_val);
			
		}
		
		public function addressbook_boaddressbook_customfields($params) {
			   $param = $params->getParam(0);

		    // This error checking syntax was added in Release 1.3.0
		    if (!XML_RPC_Value::isValue($param)) {
		        return $param;
		    }
		    
			$resp_val = new XML_RPC_Value(array(
			    "freebusy_url" => new XML_RPC_Value('freebusy URL')), "struct");
			
//			$resp_val = new XML_RPC_Value(array(), "struct");
		    
		    return new XML_RPC_Response($resp_val);
			
		}
		
		private $db;
		private $contacts;
		private $employees;
		private $service_list;
	}
		
?>