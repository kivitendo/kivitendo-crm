<?php
	/**************************************************************************\	
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

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
		
		include_once($file=LX_CRM_SERVICE_ROOT.'inc/class.'.strtolower($classname).'.inc.php');

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