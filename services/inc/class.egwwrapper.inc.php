<?php
    /**************************************************************************\    
    * This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
    * ------------------------------------------------------------------------ *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/


include_once (LX_CRM_SERVICE_ROOT.'inc/egwlxmap.inc.php');

class EgwWrapper
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
    
    public static function contact_lx2egw($userid, $contact)
    {
        global $db_map;
         
        $mapped_contact = array();
        if(!is_array($contact))
            return $mapped_contact;
            
        foreach($contact as $col_name => $col_value) {
            $mapped_key = array_search($col_name, $db_map);
            if($mapped_key) {
                if($mapped_key == 'cat_id') {
                    foreach(self::$categories as $cat_name => $cat_desc) {
                        if($col_value == $cat_desc['id'])
                            $mapped_contact[$mapped_key] = new XML_RPC_Value(array(
                                $col_value => new XML_RPC_Value($cat_desc['value'])), "struct");
                        
                    }                   
                }
                else
                    $mapped_contact[$mapped_key] = new XML_RPC_Value($col_value);
                    
            }
        }   
    
        $mapped_contact['adr_one_type'] = new XML_RPC_Value(''); // only test
        $mapped_contact['adr_two_type'] = new XML_RPC_Value(''); // only test
        $mapped_contact['fn'] = new XML_RPC_Value($contact['cp_givenname'] . ' ' . $contact['cp_name']);
        $mapped_contact['n_middle'] = new XML_RPC_Value('');
        
        $mapped_contact['access'] = new XML_RPC_Value('public');    
        $mapped_contact['rights'] = new XML_RPC_Value(-1, 'int');// only test
        $mapped_contact['email_type'] = new XML_RPC_Value('INTERNET');
        $mapped_contact['email_home_type'] = new XML_RPC_Value('INTERNET');
        $mapped_contact['freebusy_url'] = new XML_RPC_Value(''); // only test
        
        $mapped_contact['owner'] = new XML_RPC_Value($userid); 
         
        
        return $mapped_contact;
   }
   
   public static function contact_egw2lx($rpc_contact)
   {    
        global $db_map;
        $result = array();
        $rpc_contact->structreset();
        while (list($key, $keyval) = $rpc_contact->structeach ()) {
            
            if(isset($db_map[$key])) {          
                $result[$db_map[$key]] = $keyval->scalarval();
            }
        }
        
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
    
    public static function country_lx2egw($country_fullname) {
        $return = 'Deutschland';
        
        switch($country_fullname) {
            case 'D':
            case 'De':
            case 'Deu':
                $return = 'Deutschland';
                break;
        }
        return $return;
    }
    
    public static function employee_egw2lx($rpc_contact)    {   
        global $db_employee_map;
        $result = array();
        $rpc_contact->structreset();
        while (list($key, $keyval) = $rpc_contact->structeach ()) {
            
            if(isset($db_map[$key])) {          
                $result[$db_map[$key]] = $keyval->scalarval();
            }
        }
                
        return $result;
    }   
    
    public static function employee_lx2egw($contact)    {   
        global $db_employee_map;
        
        $mapped_contact = array();
        if(!is_array($contact))
            return $mapped_contact;
            
        foreach($contact as $col_name => $col_value) {
            $mapped_key = array_search($col_name, $db_employee_map);
            if($mapped_key) {
                $mapped_contact[$mapped_key] = new XML_RPC_Value($col_value);                   
            }
        }   
        
        
        
        $mapped_contact['adr_one_type'] = new XML_RPC_Value(''); // only test
        $mapped_contact['adr_two_type'] = new XML_RPC_Value(''); // only test
        $mapped_contact['fn'] = new XML_RPC_Value($contact['name']);
        $mapped_contact['n_middle'] = new XML_RPC_Value('');
        
        $mapped_contact['access'] = new XML_RPC_Value('public');    
        $mapped_contact['rights'] = new XML_RPC_Value('1');// only test
        $mapped_contact['email_type'] = new XML_RPC_Value('INTERNET');
        $mapped_contact['email_home_type'] = new XML_RPC_Value('INTERNET');
        $mapped_contact['freebusy_url'] = new XML_RPC_Value(''); // only test

        
        $mapped_contact['cat_id'] = new XML_RPC_Value(array(
                            elf::$cat_employee['id'] => new XML_RPC_Value(self::$cat_employee['value'])), "struct");
        
        return $mapped_contact;
   }      
   
}

 ?>