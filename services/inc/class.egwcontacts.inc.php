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

	class EGWContacts
	{
	    private $userid;
		private $contacts;
		private $employees;
		private $db;
		private $id_collection;

		public function EGWContacts($db, $userid) {
		    $this->userid = $userid;
			$this->contacts = &CreateObject('Contacts', $userid, $db);

			$this->employees = CreateObject('Employees', $db);
			
			$this->db = $db;
			$this->id_collection = array('contacts' => array(),
									'employees' => array() );
									
			$sql = "SELECT id from contacts";
			$rs = $this->db->getAll($sql);
			if($rs) {
				$this->id_collection['contacts'] = $rs;													 				
			}
			$sql = "SELECT id from employee";
			$rs = $this->db->getAll($sql);
			if($rs) {
				$this->id_collection['employees'] = $rs;													 				
			}
		}

		public function set_db($db) {
			$this->contacts->set_db($db);
			$this->employees->set_db($db);
		}

		function create_new_contact() {
			return $this->contacts->create_new_contact();
		}

		function update_contact($contactid, $params) {
			$mapped_params = array();
			$ret = false;
		    if($params['bday'])
				$params['bday'] = DateTimeConv::iso86012date($params['bday'],true);
			
			if(in_array( $params['id'], $this->id_collection['contacts'])) {
				$mapped_params = EgwContactWrapper::contact_egw2lx($params);
				$ret = $this->contacts->update_contact($contactid, $mapped_params);
			}
			elseif (in_array($params['id'], $this->id_collection['employees'])) {				
				$mapped_params = EgwContactWrapper::employee_egw2lx($params);
				$ret = $this->employees->update_contact($contactid, $mapped_params);
			}
			
			return $ret;		

		}

		public function get_contacts() {
			$customers = $this->contacts->get_customer_contacts();
			$customers = EgwContactWrapper::add_egw_categorie($customers, 'customer');

			$vendors = $this->contacts->get_vendor_contacts();
			$vendors = EgwContactWrapper::add_egw_categorie($vendors, 'vendor');

			$persons = $this->contacts->get_contacts();
			$persons = EgwContactWrapper::add_egw_categorie($persons, 'person');


			$data = array_merge(($customers ? $customers : array()) ,
								($vendors ? $vendors : array()),
								($persons ? $persons : array())
								);
					    $userid= $GLOBALS['loginCRM'];
			$key=array();
			$tmp_result_data = array();
			$final_result = array();
			foreach ($data as $row) {
				if (!in_array($row["cp_id"],$key)) {
					$key[]=$row["cp_id"];
					$tmp_result_data[]=$row;
//					$mapped_address = $this->prepare_addressbook_entry($row);
					$mapped_address = EgwContactWrapper::contact_lx2egw($userid, $row);
					if($mapped_address['bday']) {
						$ts = DateTimeConv::timeStringToStamp($mapped_address['bday']);
						$mapped_address['bday'] = DateTimeConv::date2iso8601($ts, false);
					}
						
					$final_result[] = (object) $mapped_address; //convert to oject, so it should be converted automatically in a xml/rpc struct type
				}
			}
			
			
			$employees = $this->employees->get_employees();
			$employees = EgwContactWrapper::add_egw_categorie($employees, 'employee');
			foreach ($employees as $row) {
					$key[]=$row["cp_id"];
					$tmp_result_data[]=$row;
//					$mapped_address = $this->prepare_addressbook_entry($row);
					$mapped_address = EgwContactWrapper::employee_lx2egw($userid, $row);
						
					$final_result[] = (object) $mapped_address; //convert to oject, so it should be converted automatically in a xml/rpc struct type				
			}
			return $final_result;
		}
	}


?>