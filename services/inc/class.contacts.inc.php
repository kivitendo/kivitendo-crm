<?php
	/**************************************************************************\	
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	class Contacts
	{
		public function Contacts() {
		}
		
		public function set_db($db) {
			$this->db = $db;
		}
		
		function create_new_contact($employeeid) {	
			$newID=uniqid (rand());
			if (!$employeeid) {$uid='null';} else {$uid=$employeeid;};
			$sql="insert into contacts (cp_name,cp_employee) values ('$newID',$uid)";
			$rc = $this->db->query($sql);
			$id = false;
			if ($rc) {
				$sql="select cp_id from contacts where cp_name = '$newID'";
				$rs=$this->db->getAll($sql);
				if ($rs) {
					$id=$rs[0]["cp_id"];
				} else {
					$id=false;
				}
			} else {
				$id=false;
			}
			return $id;
		}
				
		function update_contact($employeeid, $contactid, $params) {	
			$entries = array(
					'cp_title' => "'".$params['cp_title']."'",
					'cp_givenname' => "'".$params['cp_givenname']."'",
					'cp_name' => "'".$params['cp_name']."'",
					'cp_street' => "'".$params['cp_street']."'",
					'cp_country' => "'".$params['cp_country']."'",
					'cp_zipcode' => "'".$params['cp_zipcode']."'",
					'cp_city' => "'".$params['cp_city']."'",
					'cp_fax' => "'".$params['cp_fax']."'",
					'cp_phone2' => "'".$params['cp_phone2']."'",
					'cp_phone1' => "'".$params['cp_phone1']."'",
					'cp_email' => "'".$params['cp_email']."'",
					'cp_homepage' => "'".$params['cp_homepage']."'",
					'cp_gebdatum' =>"'".$params['cp_gebdatum']."'",
					'cp_abteilung' => "'".$params['cp_abteilung']."'",
					'cp_position' => 'null',
					'cp_stichwort1' => 'null',
					'cp_beziehung' => 'null',
					'cp_notes' => "'".$params['cp_notes']."'",
					'cp_owener' => 'null',
					'cp_sonder' => 0    		
				);
						
			$sql_query = 'UPDATE contacts set ';
			foreach($entries as $key => $val) {
				$sql_query .= $key . '=' . $val . ", " ;    		
			}
			
			$sql_query .= "cp_employee='".$employeeid."' WHERE cp_id='".$contactid."'";
//			echo $customerid; 
			$result = $this->db->getAll($sql_query);
			
			return ($result ? true : false);
		}
		
		public function get_employee_contacts() {
			
			$sql_query = 'SELECT * FROM contacts INNER JOIN customer ON (contacts.cp_cv_id = customer.id)';	
			$res=$this->db->getAll($sql_query);
			if(!$res)
				return false;
			return $res; 
		}
		
		public function get_customer_contacts() {
			
			$sql_query = 'SELECT * FROM contacts INNER JOIN customer ON (contacts.cp_cv_id = customer.id)';	
			$res=$this->db->getAll($sql_query);
			if(!$res)
				return false;
			return $res; 
		}
		
		public function get_vendor_contacts() {
					
			$sql_query = 'SELECT * FROM contacts INNER JOIN vendor ON (contacts.cp_cv_id = customer.id)';
			$res=$this->db->getAll($sql_query);
			if(!$res)
				return false;
			return $res; 
		}
		
		public function get_contacts() {
						
			$sql_query = 'SELECT * FROM contacts WHERE cp_cv_id IS NULL';
				$res=$this->db->getAll($sql_query);
			if(!$res)
				return false;
			return $res; 
		}
		
		
		public function delete_contact($id) {
						
			$sql_query = "DELETE FROM contacts WHERE cp_id='".$id."'";
				$res=$this->db->query($sql_query);
			if(!$res)
				return false;
			return true; 
		}
		
		private $db;
	}
		
?>