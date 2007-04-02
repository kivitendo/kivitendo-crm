<?
	/**************************************************************************\
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
define('L_FATAL',  0);
define('L_ERROR',  1);
define('L_WARNING',2);
define('L_INFO',   3);
define('L_DEBUG1', 4);
define('L_DEBUG2', 5);
define('L_DEBUG3', 6);

class Logger 
{
	private static $logpath;
	private static $logfiles = array();
	
	private static $loglevel;
	
	static public function setLevel($level) {
		self::$loglevel = $level;
	}
	static public function getLevel() {
		return self::$loglevel;
	}
	
	static public function setLogPath($path) {
		if(is_writable($path))
			self::$logpath = $path;
	}
	
	static public function initModule( $module , $openmode='w')
	{
		if (array_key_exists($module, self::$logfiles)) {
			self::syslogger( "ALERT", "Logger ".$module." already initialized" );
			return;
		}
		$logfile = self::$logpath.$module.".log";
		$writable = is_writable($logfile);
		if(!$writable) {
			self::syslogger( "ALERT", "Failed to open logfile ".$logfile );
			return false;
		}
		
		// open file, set pointer to end, if missing create.
		$handle = fopen($logfile , $openmode);
		if(!$handle) {
			self::syslogger( "ALERT", "Failed to open logfile ".$logfile );
			return false;
		}
		self::$logfiles[$module] = &$handle;
		self::log($module, L_INFO, strftime("%H:%M:%S")." *** log started ***\n");
		return true;
	}
	
	/**
	* fileLogger - logs a message into given file.
	* @access public
	*
	* @param string The log file name.
	* @param string Message to be logged.
	* @returns boolean True if message logged, otherwise false.
	*/
	static public function log($module, $level, $msg, $file='', $line='' )
	{
		if(! ($level <= self::$loglevel ))
			return;
		if (!array_key_exists($module, self::$logfiles)) {
			self::syslogger( "ALERT", "Logger ".$module." not initialized" );
			return;
		}
		
		$tmp = split('/',$file);
		$file = (is_array($tmp) ? $tmp[count($tmp) -1] : $file);
		
		$m = strftime("%H:%M:%S.%s")."   ".$level."  ".$msg;
		if($file != '')
			$m .= ' (in file: '.$file;
		if($line != '')
			$m .= ', in line: '.$line.')';
		$m .= "\n";

		if ( !( fwrite(self::$logfiles[$module], $m) ) )
		{
			// something went wrong with writing.
			self::syslogger( "ALERT", "Failed to log to file $filename" );
			return FALSE;
		}

		// logging successful!
		return TRUE;
	}


	/**
	* htmlMsg - reports to the user in html form. Provides a keyword/detailed
	* message and return button to go back.
	* @access public
	*
	* @param string Name of message keyword.
	* @param string Details of message.
	* @returns void
	*/
	public function htmlMsg( $keyword, $details )
	{
		print "<center>\n\n";
		print "<p>\n";
		print "<h1>Failure report: </h1>\n";
		print "<h1>'" . $keyword . "' " . "</h1>\n";
		print "<h2>'" . $details . "' " . "</h2>\n";
		print "<br>";
		print "Please go back and try again.\n";
		print "</p>\n";
		print "<hr>\n";
		print "<form name='buttonbar'>\n";
		print "<input type='button' STYLE='color: red;background: green' value='Back' onClick='history.back()'>\n";
		print "</input>\n";
		print "</form>\n";
		print "</center>\n\n";
		return;
	}


	/**
	* syslogger - this method will send the given message to the
	* system logging facility.
	* @access public
	*
	* @param string Logging severity level (LOW, INFO, HIGH, ALERT)
	* @param string The actual message to be logged.
	* @return bool True if message logged, otherwise false.
	**/
	public function syslogger($level, $message)
	{
		# picks up the syslog variables.
		define_syslog_variables();

		# open the logging.
		openlog( "PMS-Log", LOG_PERROR, LOG_USER );

		# send log messages, comments here from AMI TSD document.
		switch ( $level )
		{
			case "LOW" :

				$message = "LOW - " . $message;

				if ( ! syslog( LOG_NOTICE, $message ) )
				{
					print "DEBUG: Unable to reach log system...\n\n";
					return FALSE;
				}
				break;

			case "INFO" :

				$message = "INFO - " . $message;

				if ( ! syslog( LOG_INFO, $message ) )
				{
					print "DEBUG: Unable to reach log system...\n\n";
					return FALSE;
				}
				break;

			case "HIGH" :
				
				$message = "HIGH - " . $message;

				if ( ! syslog( LOG_CRIT, $message ) )
				{
					print "DEBUG: Unable to reach log system...\n\n";
					return FALSE;
				}
				break;

			case "ALERT" :

				$message = "ALERT - " . $message;

				if ( ! syslog( LOG_ALERT, $message ) )
				{
					print "DEBUG: Unable to reach log system...\n\n";
					return FALSE;
				}
				break;

			default :

				print "DEBUG: Unknown severity level: $level\n\n";
				return FALSE;
		}

		# close the logging.
		closelog();
		return TRUE;
	}

}

?>
