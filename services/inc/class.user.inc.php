<?php
	/**************************************************************************\	
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/


	class User
	{
		public function User() {
		}
		
		public function set_db($db) {
			$this->db = $db;
		}
		
			
		public function get_user($id) {
			
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
		
		private $db;
	}
		
?>