<?php
	/**************************************************************************\
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/


include_once ('inc/egwlxcontactmap.inc.php');
require_once ('inc/class.logger.inc.php');

class EgwContactWrapper
{
	public static $categories = array(
		'customer' => array( 'id' => '10', 'value' => 'Kunde'),
		'vendor' => array( 'id' => '11', 'value' => 'Lieferant'),
		'person' => array( 'id' => '12', 'value' => 'Einzelperson'),
		'employee' => array( 'id' => '13', 'value' => 'Mitarbeiter')
		);


	public static function add_egw_categorie($contacts, $cat) {
		$id = self::$categories[$cat];
		$num_rows = $contacts ? count($contacts) : 0;
		for($i = 0; $i < $num_rows; $i++) {
			$contacts[$i]['categorie'] = $id['id'];
		}
		return $contacts;
	}
   	public static function employee_lx2egw($userid, $contact)
   	{
		global $db_employee_map;

		$mapped_contact = array();
		if(!is_array($contact))
			return $mapped_contact;
		
		foreach($contact as $col_name => $col_value) {
			$mapped_key = array_search($col_name, $db_employee_map);
			if($mapped_key) {
				if($mapped_key == 'cat_id') {
					foreach(self::$categories as $cat_name => $cat_desc) {
						if($col_value == $cat_desc['id'])
							$mapped_contact[$mapped_key] = (object) array($col_value => $cat_desc['value']);

					}
				}
				else
					$mapped_contact[$mapped_key] =  $col_value;

			}			
		}

		$spname = split("[ ,;]",$contact['name']);
		$c = count($spname);
		//TODO: make conversion better
		if($c == 2) {
			$mapped_contact['n_given'] = $spname[0];
			$mapped_contact['n_family'] = $spname[1];	
		} elseif($c = 3) {
			$mapped_contact['n_given'] = $spname[0];
			$mapped_contact['n_middle'] = $spname[1];
			$mapped_contact['n_family'] = $spname[2];				
		}
			
				
		$mapped_contact['rights'] = 1;
		return $mapped_contact;
   	}
   	
   	public static function contact_lx2egw($userid, $contact)
   	{
		global $db_map;

		$mapped_contact = array();
		if(!is_array($contact))
			return $mapped_contact;
		
		$contact['country'] = self::country_lx2egw($contact['country']);
		$contact['cp_country'] = self::country_lx2egw($contact['cp_country']);

		foreach($contact as $col_name => $col_value) {
			$mapped_key = array_search($col_name, $db_map);
			if($mapped_key) {
				if($mapped_key == 'cat_id') {
					foreach(self::$categories as $cat_name => $cat_desc) {
						if($col_value == $cat_desc['id'])
							$mapped_contact[$mapped_key] = (object) array($col_value => $cat_desc['value']);

					}
				}
				else
					$mapped_contact[$mapped_key] =  $col_value;

			}
		}
		
//		XML_RPC_iso8601_decode(); to unix timestamp
//XML_RPC_iso8601_encode
		$bday = $contact['cp_birthday'];
		$mapped_contact['bday'] = $bday; // only test 
		$mapped_contact['adr_one_type'] = ''; // only test
		$mapped_contact['adr_two_type'] = ''; // only test
		$mapped_contact['fn'] = $contact['cp_givenname'] . ' ' . $contact['cp_name'];
		$mapped_contact['n_middle'] = '';

		$mapped_contact['access'] = ($contact['cp_owener'] == null ? 'public' : 'private');
		$mapped_contact['rights'] = -1; // always -1
		$mapped_contact['email_type'] ='INTERNET';
		$mapped_contact['email_home_type'] = 'INTERNET';
		$mapped_contact['freebusy_url'] = ''; // should direct to the freebusy url wchich is providing an iCal-file

		$mapped_contact['owner'] = $userid;


		return $mapped_contact;
   }

   public static function contact_egw2lx($contact)
   {
		global $db_map;
		$result = array();
//		$rpc_contact->structreset();
//		while (list($key, $keyval) = $rpc_contact->structeach ()) {
//
//			if(isset($db_map[$key])) {
//				$result[$db_map[$key]] = $keyval->scalarval();
//			}
//		}
		foreach($contact as $key => $val) {
			if(isset($db_map[$key])) {
				$result[$db_map[$key]] = $val;
			}
		}
		
        Logger::log(M_SERVICES, L_INFO, "access:  ".$contact['access'],__FILE__,__LINE__);
        
		$result['cp_owener'] = $contact['access'] == 'private' ? $_SESSION["loginCRM"] : null;
		$result['cp_country'] = self::country_egw2lx($result['cp_country']);

		return $result;
   }

   public static function country_egw2lx($country_fullname) {
		$return = 'D';

		switch($country_fullname) {
			case 'Deutschland':
			case 'Germany':
				$return = 'D';
				break;
		}
		return $return;
	}

   	public static function country_lx2egw($country_shortname) {
		$return = 'Deutschland';

		switch($country_shortname) {
			case 'D':
			case 'De':
			case 'Deu':
				$return = 'Deutschland';
				break;
		}
		return $return;
	}

	public static function employee_egw2lx($rpc_contact)	{
		global $db_employee_map;
		$result = array();
		$rpc_contact->structreset();
		while (list($key, $keyval) = $rpc_contact->structeach ()) {

			if(isset($db_employee_map[$key])) {
				$result[$db_employee_map[$key]] = $keyval->scalarval();
			}
		}
		
		//TODO: finish function ....

		return $result;
   	}
//
//
//		$mapped_contact['adr_one_type'] = new XML_RPC_Value(''); // only test
//		$mapped_contact['adr_two_type'] = new XML_RPC_Value(''); // only test
//		$mapped_contact['fn'] = new XML_RPC_Value($contact['name']);
//		$mapped_contact['n_middle'] = new XML_RPC_Value('');
//
//		$mapped_contact['access'] = new XML_RPC_Value('public');
//		$mapped_contact['rights'] = new XML_RPC_Value('1');// only test
//		$mapped_contact['email_type'] = new XML_RPC_Value('INTERNET');
//		$mapped_contact['email_home_type'] = new XML_RPC_Value('INTERNET');
//		$mapped_contact['freebusy_url'] = new XML_RPC_Value(''); // only test
//
//
//		$mapped_contact['cat_id'] = new XML_RPC_Value(array(
//						    elf::$cat_employee['id'] => new XML_RPC_Value(self::$cat_employee['value'])), "struct");
//
//		return $mapped_contact;
//   }

}

 ?>