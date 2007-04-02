<?php
    /**************************************************************************\
    * This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
    * ------------------------------------------------------------------------ *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/
    

    
    function mk_dir($path, $rights = 0777) {
      $folder_path = array(
        strstr($path, '.') ? dirname($path) : $path);
    
      while(!@is_dir(dirname(end($folder_path)))
              && dirname(end($folder_path)) != '/'
              && dirname(end($folder_path)) != '.'
              && dirname(end($folder_path)) != '')
        array_push($folder_path, dirname(end($folder_path)));
    
      while($parent_folder_path = array_pop($folder_path))
        if(!@mkdir($parent_folder_path, $rights))
          user_error("Can't create folder \"$parent_folder_path\".");
    }
    
    function is_obj( &$object, $check=null, $strict=true )
    {
       if( $check == null && is_object($object) )
       {
           return true;
       }
       if( is_object($object) )
       {
           $object_name = get_class($object);
           if( $strict === true )
           {
               if( $object_name == $check )
               {
                   return true;
               }
           }
           else
           {
               if( strtolower($object_name) == strtolower($check) )
               {
                   return true;
               }
           }
       }
    }
// convert a date-array or timestamp into a datetime.iso8601 string
    function date2iso8601($date)
    {
        if (!is_array($date))
        {
            if($this->simpledate)
            {
                return date('Ymd\TH:i:s',$date);
            }
            return date('Y-m-d\TH:i:s',$date);
        }

        $formatstring = "%04d-%02d-%02dT%02d:%02d:%02d";
        if($this->simpledate)
        {
            $formatstring = "%04d%02d%02dT%02d:%02d:%02d";
        }
        return sprintf($formatstring,
            $date['year'],$date['month'],$date['mday'],
            $date['hour'],$date['min'],$date['sec']);
    }

    // convert a datetime.iso8601 string into a datearray or timestamp
    function iso86012date($isodate,$timestamp=False)
    {
        $arr = array();

        if (ereg('^([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})$',$isodate,$arr))
        {
            // $isodate is simple ISO8601, remove the difference between split and ereg
            array_shift($arr);
        }
        elseif (($arr = split('[-:T]',$isodate)) && count($arr) == 6)
        {
            // $isodate is extended ISO8601, do nothing
        }
        else
        {
            return False;
        }

        foreach(array('year','month','mday','hour','min','sec') as $n => $name)
        {
            $date[$name] = (int)$arr[$n];
        }
        return $timestamp ? mktime($date['hour'],$date['min'],$date['sec'],
            $date['month'],$date['mday'],$date['year']) : $date;
    }

    /**
     * Load a class and include the class file if not done so already.
     *
     * This function is used to create an instance of a class, and if the class file has not been included it will do so.
     * $GLOBALS['egw']->acl =& CreateObject('phpgwapi.acl');
     *
     * @author RalfBecker@outdoor-training.de
     * @param $classname name of class
     * @param $p1,$p2,... class parameters (all optional)
     * @return object reference to an object
     */
    function &CreateObject($class)
    {
        $classname = $class;

        include_once($file='inc/class.'.strtolower($classname).'.inc.php');

        if (class_exists($classname))
        {
            $args = func_get_args();
            if(count($args) == 1)
            {
                $obj =& new $classname;
            }
            else
            {
                $code = '$obj =& new ' . $classname . '(';
                foreach($args as $n => $arg)
                {
                    if ($n)
                    {
                        $code .= ($n > 1 ? ',' : '') . '$args[' . $n . ']';
                    }
                }
                $code .= ');';
                eval($code);
            }
        }
        if (!is_object($obj))
        {
            echo "<p>CreateObject('$class'): Cant instanciate class!!!<br />\n".function_backtrace(1)."</p>\n";
        }
        return $obj;
    }

    /**
     * backtrace of the calling functions for php4.3+ else menuaction/scriptname
     *
     * @author RalfBecker-AT-outdoor-training.de
     * @param int $remove=0 number of levels to remove
     * @return string function-names separated by slashes (beginning with the calling function not this one)
     */
    function function_backtrace($remove=0)
    {
        if (function_exists('debug_backtrace'))
        {
            $backtrace = debug_backtrace();
            //echo "function_backtrace($remove)<pre>".print_r($backtrace,True)."</pre>\n";
            foreach($backtrace as $level)
            {
                if ($remove-- < 0)
                {
                    $ret[] = (isset($level['class'])?$level['class'].'::':'').$level['function'].
                        (!$level['class'] ? '('.str_replace(EGW_SERVER_ROOT,'',$level['args'][0]).')' : '');
                }
            }
            if (is_array($ret))
            {
                return implode(' / ',$ret);
            }
        }
        return $_GET['menuaction'] ? $_GET['menuaction'] : str_replace(EGW_SERVER_ROOT,'',$_SERVER['SCRIPT_FILENAME']);
    }

?>