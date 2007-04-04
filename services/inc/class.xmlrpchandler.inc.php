<?php
    /**************************************************************************\    
    * This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
    * ------------------------------------------------------------------------ *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/
    
    
    class XMLRPCHandler
    {
        function XMLRPCHandler() {
            $args = func_get_args();
            call_user_func_array(array(&$this, "__construct"), $args);
        }
        function __construct($db) {
            $this->db = $db;            
        }   
        
        protected function decode($xml_rpc_value) {
            $decoded_val = XML_RPC_decode($xml_rpc_value);
            
            if(is_array($decoded_val)) {
                $this->decode_charset($decoded_val, null, array($GLOBALS['db_charset'], 'UTF-8'));
            }           
            
            return $decoded_val;
        }       
        
        protected function encode($value) {         
            if(is_array($value)) {
                $this->decode_charset($value, null, array('UTF-8', $GLOBALS['db_charset']));
            }                       
            $encoded_val = XML_RPC_encode($value);
            return $encoded_val;
        }
        
        protected function check_return(&$rpc_response) {
            
            if(!is_obj($rpc_response, 'XML_RPC_Response'))
                return; //TODO: error handling
                
//          XML_RPC_Message::setSendEncoding('UTF-8');
//          XML_RPC_Message::setConvertPayloadEncoding(1);
            return $rpc_response;           
        }
                
        protected function db() {
            return $this->db;
        }
        
        private function decode_charset(&$value, $key, $decode_instruction) {
            if(is_string($value)) {
                Logger::log(M_SERVICES, L_DEBUG3,'Before: '.$value);
//              $enc = mb_check_encoding($value, $decode_instruction[0]);
//              if(!mb_check_encoding($value, $decode_instruction[0]))
                $value = mb_convert_encoding($value, $decode_instruction[0], $decode_instruction[1]);
                Logger::log(M_SERVICES, L_DEBUG3,'After: '.$value,__FILE__, __LINE__);
            }
            else if(is_array($value) || is_object($value)) {
                foreach($value as $k => &$v)
                    $this->decode_charset(&$v, $k, $decode_instruction);
            }
        }
        
        private $db;
    }
        
?>