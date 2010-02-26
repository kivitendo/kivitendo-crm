<?php
    require_once("inc/stdLib.php");
    include("inc/crmLib.php");
    include("inc/UserLib.php");
    require_once( 'inc/iCalcreator.class.php' );
    $user=getUserStamm($_SESSION["loginCRM"]);
    $start=($_GET["start"]<>"")?$_GET["start"]:date('d.m.Y');
    $stop = ($_GET["stop"]<>"")?$_GET["stop"]:'';
    $termine = searchTermin('%',$start,$stop,$_SESSION["loginCRM"]);
    $v = new vcalendar(); // create a new calendar instance
    $v->setConfig( 'unique_id', strtr($user["Name"],' ','_')); // set Your unique id
    $v->setProperty( 'method', 'PUBLISH' ); // required of some calendar software
    if ($termine) {
        $ts="";
        foreach ($termine as $t) {
            $ts.=$t["id"].",";
        }
        $data=getTerminList($ts."0");
        $cnt=0;
        foreach ($data as $term) {
            $cnt++;
            $vevent = new vevent(); // create an event calendar component
            $vevent->setProperty( 'dtstart', array( 'year'=>substr($term["starttag"],0,4), 
                                                    'month'=>substr($term["starttag"],5,2), 
                                                    'day'=>substr($term["starttag"],8,2), 
                                                    'hour'=>substr($term["startzeit"],0,2), 
                                                    'min'=>substr($term["startzeit"],3,2),
                                                    'sec'=>0 ));
            $vevent->setProperty( 'dtend', array(   'year'=>substr($term["stoptag"],0,4), 
                                                    'month'=>substr($term["stoptag"],5,2), 
                                                    'day'=>substr($term["stoptag"],8,2), 
                                                    'hour'=>substr($term["stopzeit"],0,2), 
                                                    'min'=>substr($term["stopzeit"],3,2),
                                                    'sec'=>0 ));
            $vevent->setProperty( 'LOCATION', $term["location"]  ); // property name - case independent
            $vevent->setProperty( 'categories', $term["catname"] );
            //$vevent->setProperty( "Exrule" , array ("FREQ" => "", "INTERVAL" => "MONTHLY" , "UNTIL" => "20060831", "INTERVAL" => 2)
            $vevent->setProperty( 'summary', $term["cause"] );
            $vevent->setProperty( 'description', $term["c_cause"] );
            $vevent->setProperty( 'attendee', $user["eMail"] );
            $v->setComponent ( $vevent ); // add event to calendar
        }
    }
    $v->setConfig( 'directory', 'calendar' ); // identify directory
    $v->setConfig( 'filename', $user["Login"].'_calendar.'.$_GET["ext"] ); // set file name
    $v->saveCalendar(); // save calendar to file
    echo $cnt.' Termine exportiert';
?>
