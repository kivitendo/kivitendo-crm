<?php

	/**************************************************************************\
	* This file was originaly written by Jan Dierolf (jadi75@gmx.de)           *
	* ------------------------------------------------------------------------ *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

      class DateTimeConv
      {
          /**
           * Compares two dates.
           *
           * Returns:
           *
           *     < 0 if date1 is less than date2;
           *     > 0 if date1 is greater than date2;
           *     0 if they are equal.
           *
           * @return int
           * @param  string|timestamp $date1
           * @param  string|timestamp $date2
           */
          static function compareDates($date1, $date2)
          {
              if (!is_numeric($date1)) {
                  $date1 = DateTimeConv::timeStringToStamp($date1);
              }
              if (!is_numeric($date2)) {
                  $date2 = DateTimeConv::timeStringToStamp($date2);
              }
              if ($date1 < $date2) {
                  return -1;
              } else if ($date1 > $date2) {
                  return 1;
              } else {
                  return 0;
              }
          }

          /**
           * Converts a date/time string to Unix timestamp
           *
           * @return timestamp
           * @param  string $string
           */
          static function timeStringToStamp($string)
          {
              return strtotime($string);
          }

          /**
           * Converts Unix timestamp to a date/time string using format given
           *
           * Special options can be passed for the format parameter.  These are
           * set format types.  The options currently include:
           *
           *     o mysql
           *
           * If the time parameter isn't supplied, then the current local time
           * will be used.
           *
           * @return string
           * @param  integer $time
           * @param  string  $format
           */
          static function timeStampToString($time = 0, $format = 'Y-m-d H:i:s', $short)
          {
              if ($format == 'sql') {
                  $format = ($short ? 'Y-m-d' : 'Y-m-d H:i:s');
              }
              
              if ($time == 0) {
                  $time = time();
              }
              
              return date($format, $time);
          }

          /**
           * Converts a Unix timestamp or date/time string to a specific format.
           *
           * Special options can be passed for the format parameter.  These are
           * set format types.  The options currently include:
           *
           *     o mysql
           *
           * If the time parameter isn't supplied, then the current local time
           * will be used.
           *
           * @return string
           * @param  integer|string $time
           * @param  string         $format
           * @see    timeStringToStamp()
           * @see    timeStampToString()
           */
          static function timeFormat($time = 0, $format = 'Y-m-d H:i:s')
          {
              if (!is_numeric($time)) {
                  $time = DateTimeConv::timeStringToStamp($time);
              }
              
              if ($time == 0) {
                  $time = time();
              }

              return DateTimeConv::timeStampToString($time, $format);
        }
          		// convert a date-array or timestamp into a datetime.iso8601 string
			
		static function date2iso8601($datetime, $simpleDate=TRUE)
		{
			if (!is_array($datetime))
			{
				if($simpleDate)
				{
					return date('Ymd\TH:i:s',$datetime);
				}
				return date('Y-m-d\TH:i:s',$datetime);
			}

			$formatstring = "%04d-%02d-%02dT%02d:%02d:%02d";
			if($simpleDate)
			{
				$formatstring = "%04d%02d%02dT%02d:%02d:%02d";
			}
			return sprintf($formatstring,
				$datetime['year'],$datetime['month'],$datetime['mday'],
				$datetime['hour'],$datetime['min'],$datetime['sec']);
		}

		// convert a datetime.iso8601 string into a datearray or timestamp
		static function iso86012date($isodate,$timestamp=False)
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
           * Converts a Unix timestamp or date/time string to a human-readable
           * format, such as '1 day, 2 hours, 42 mins, and 52 secs'
           *
           * Based on the word_time() function from PG+ (http://pgplus.ewtoo.org)
           *
           * @return string
           * @param  integer|string $time
           * @see    timeStringToStamp()
           */
          static function timeToHumanReadable($time = 0)
          {
              if (!is_numeric($time)) {
                  $time = DateTime::timeStringToStamp($time);
              }

              if ($time == 0) {
                  return 'no time at all';
              } else {
                  if ($time < 0) {
                      $neg = 1;
                      $time = 0 - $time;
                  } else {
                      $neg = 0;
                  }
          
                  $days = $time / 86400;
                  $days = floor($days);
                  $hrs  = ($time / 3600) % 24;
                  $mins = ($time / 60) % 60;
                  $secs = $time % 60;
          
                  $timestring = '';
                  if ($neg) {
                      $timestring .= 'negative ';
                  }
                  if ($days) {
                      $timestring .= "$days day" . ($days == 1 ? '' : 's');
                      if ($hrs || $mins || $secs) {
                          $timestring .= ', ';
                      }
                  }
                  if ($hrs) {
                      $timestring .= "$hrs hour" . ($hrs == 1 ? '' : 's');
                      if ($mins && $secs) {
                          $timestring .= ', ';
                      }
                      if (($mins && !$secs) || (!$mins && $secs)) {
                          $timestring .= ' and ';
                      }
                  }
                  if ($mins) {
                      $timestring .= "$mins min" . ($mins == 1 ? '' : 's');
                      if ($mins && $secs) {
                          $timestring .= ', ';
                      }
                      if ($secs) {
                          $timestring .= ' and ';
                      }
                  }
                  if ($secs) {
                      $timestring .= "$secs sec" . ($secs == 1 ? '' : 's');
                  }
                  return $timestring;
              }
          }

          /**
           * Give a slightly more fuzzy time string. such as: yesterday at 3:51pm
           *     
           *
           * @return string
           * @param  integer|string $time
           * @see    timeStringToStamp()
           */
          static function fuzzyTimeString($time = 0)
          {
              if (!is_numeric($time)) {
                  $time = DateTime::timeStringToStamp($time);
              }

              $now = time();
              $sodTime = mktime(0, 0, 0, date('m', $time), date('d', $time), date('Y', $time));
              $sodNow  = mktime(0, 0, 0, date('m', $now), date('d', $now), date('Y', $now));
              
              if ($sodNow == $sodTime) {
                  return 'today at ' . date('g:ia', $time); // check 'today'
              } else if (($sodNow - $sodTime) <= 86400) {
                  return 'yesterday at ' . date('g:ia', $time); // check 'yesterday'
              } else if (($sodNow - $sodTime) <= 432000) {
                  return date('l \a\\t g:ia', $time); // give a day name if within the last 5 days
              } else if (date('Y', $now) == date('Y', $time)) {
                  return date('M j \a\\t g:ia', $time); // miss off the year if it's this year
              } else {
                  return date('M j, Y \a\\t g:ia', $time); // return the date as normal
              }
          }
          
      }
?>
