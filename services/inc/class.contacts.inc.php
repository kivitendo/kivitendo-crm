<?php
    /**************************************************************************\
    * This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
    * ------------------------------------------------------------------------ *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

//    require_once('inc/stdLib.php');

    class Contacts
    {
        private $userid;

        public function Contacts($userid, $db) {
            $this->userid = $userid;
            $this->db = $db;
        }

        public function set_db($db) {
            $this->db = $db;
        }

        function create_new_contact() {
            $newID=uniqid (rand());
            if (!$this->userid) {$uid='null';} else {$uid=$this->userid;};
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

        function update_contact($contactid, $params) {
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
                    'cp_birthday' =>"'".$params['cp_birthday']."'",
                    'cp_abteilung' => "'".$params['cp_abteilung']."'",
                    'cp_position' => 'null',
                    'cp_stichwort1' => 'null',
                    'cp_beziehung' => 'null',
                    'cp_notes' => "'".$params['cp_notes']."'",
                    'cp_owener' => $params['cp_owener'] ? $params['cp_owener'] : 'null',
                    'cp_sonder' => 0
                );
                
            
            $entries['cp_birthday'] = "'".Database::time2db($params['cp_birthday'], true)."'";
                
            $sql_query = 'UPDATE contacts set ';
            foreach($entries as $key => $val) {
                $sql_query .= $key . '=' . $val . ", " ;
            }
            $rights = $this->rights("cp_");

            $sql_query .= "cp_employee='".$this->userid."' WHERE cp_id='".$contactid."'  AND  $rights";
//          echo $customerid;
            $result = $this->db->getAll($sql_query);

            return ($result ? true : false);
        }

        public function get_employee_contacts() {

            $sql_query = 'SELECT * FROM contacts INNER JOIN customer ON (contacts.cp_cv_id = customer.id)';
            $res=$this->db->getAll($sql_query);
            if(!$res)
                return false;
            //@TODO: Add column birthday to table employee
            return $res;
        }

        public function get_customer_contacts() {

            $rights = $this->rights("cp_");
            $sql_query = "SELECT * FROM contacts INNER JOIN customer ON (contacts.cp_cv_id = customer.id) where $rights";
            $res=$this->db->getAll($sql_query);
            if(!$res)
                return false;
//              
//          if($res['cp_birthday'])
//              $res['cp_birthday'] = $res['cp_birthday'];
            return $res;
        }

        public function get_vendor_contacts() {

            $rights = $this->rights("cp_");
            $sql_query = "SELECT * FROM contacts INNER JOIN vendor ON (contacts.cp_cv_id = customer.id) where $rights";
            $res=$this->db->getAll($sql_query);
            if(!$res)
                return false;
            return $res;
        }

        public function get_contacts() {
            $rights = $this->rights("cp_");
            $sql_query = "SELECT * FROM contacts WHERE cp_cv_id IS NULL AND $rights";
                $res=$this->db->getAll($sql_query);
            if(!$res)
                return false;

            return $res;
        }


        public function delete_contact($id) {
            $rights = $this->rights("cp_");

            $sql_query = "DELETE FROM contacts WHERE cp_id='".$id."' AND ".$rights;
                $res=$this->db->query($sql_query);
            if(!$res)
                return false;
            return true;
        }
        
        public function rights($prefix="") {
            $grp=$this->group($_SESSION["loginCRM"]);
            $rechte="( ".$prefix."owener=".$_SESSION["loginCRM"]." or ".$prefix."owener is null";
            if ($grp) $rechte.=" or ".$prefix."owener in $grp";
            return $rechte.")";
        }
        
        public function group($usrid,$inkluid=false){
        
            $sql="select distinct(grpid) from grpusr where usrid=$usrid";
            $rs=$this->db->getAll($sql);
            if(!$rs) {
                if ($inkluid) { return "($usrid)"; }
                else { $data=false; };
            } else {
                if ($rs) {
                   $data="(";
                    foreach($rs as $row) {
                        $data.=$row["grpid"].",";
                    };
                    if ($inkluid) { $data.="$usrid)"; }
                    else {$data=substr($data,0,-1).")";};
                } else {
                    if ($inkluid) { $data.="($usrid)"; }
                    else { $data=false; };
                }
                return $data;
            }
        }

        private $db;
    }


?>
