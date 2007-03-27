<?php
	/**************************************************************************\	
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	class Employees
	{
		public function Employees() {
		}
		
		public function set_db($db) {
			$this->db = $db;
		}
						
		function update_employee($params) {	

			$entries = array(
					'id' => "'".$params['id']."'",
					'name' => "'".$params['name']."'",
					'addr1' => "'".$params['addr1']."'",
					'addr2' => "'".$params['addr2']."'",
					'workphone' => "'".$params['workphone']."'",
					'homephone' => "'".$params['homephone']."'",
					'notes' => "'".$params['notes']."'",
					'mtime' => "'".$params['mtime']."'",
					'abteilung' => "'".$params['abteilung']."'",
					'position' => "'".$params['position']."'",
					'email' => "'".$params['email']."'"
				);
						
			$sql_query = 'UPDATE Employees set ';
			foreach($entries as $key => $val) {
				$sql_query .= $key . '=' . $val . ", " ;    		
			}
			
			$sql_query .= " WHERE id='".$params['id']."'";
//			echo $customerid; 
			$result = $this->db->getAll($sql_query);
			
			return ($result ? true : false);
		}
		
		public function get_employees() {
						
			$sql_query = 'SELECT * FROM employee';
				$res=$this->db->getAll($sql_query);
			if(!$res)
				return false;
			return $res; 
		}
		
//		
//		public function delete_employee($id) {
//						
//			$sql_query = "DELETE FROM employee WHERE id='".$id."'";
//				$res=$this->db->query($sql_query);
//			if(!$res)
//				return false;
//			return true; 
//		}
		
		private $db;
	}
		
?>