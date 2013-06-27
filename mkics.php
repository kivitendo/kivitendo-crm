<?php
    require_once("inc/stdLib.php");
    include("inc/crmLib.php");
    include_once("inc/UserLib.php");
    require_once( 'inc/iCalcreator.class.php' );
    $user=getUserStamm($_SESSION["loginCRM"]);
    $start=($_POST["start"]<>"")?$_POST["start"]:date('d.m.Y');
    $stop = ($_POST["stop"]<>"")?$_POST["stop"]:'';
    $termine = searchTermin('%',0,$start,$stop,$_SESSION["loginCRM"]);
    $v = new vcalendar(); // create a new calendar instance
    $v->setConfig( 'unique_id', strtr($user["name"],' ','_')); // set Your unique id
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
            $vevent->setProperty( 'attendee', $user["email"] );
            $v->setComponent ( $vevent ); // add event to calendar
        }
    }
    $v->setConfig( 'filename', date('Ymd').'_calendar.'.$_POST["icalext"] ); // set file name
    if ($_POST["icalart"]=="client") {
        $v->returnCalendar();
    } else if ($_POST["icalart"]=="mail") {
        $user=getUserStamm($_SESSION["loginCRM"]);
        $abs=sprintf("%s <%s>",$user["name"],$user["email"]);        
        $Subject="LxO-Kalender";
        $v->setConfig( 'directory', "/tmp/" ); // identify directory
        $v->saveCalendar(); // save calendar to file
        include_once("Mail.php");
        include_once("Mail/mime.php");
        $headers=array(
                "Return-Path"   => $abs,
                "Reply-To"  => $abs,
                "From"      => $abs,
                "X-Mailer"  => "PHP/".phpversion(),
                "Subject"   => $Subject);
        $mime = new Mail_Mime("\n");
        $mime->setTXTBody("");
        echo "!".$v->getConfig('directory')."/".$v->getConfig('filename')."!".$v->getConfig('filename')."!";
        $mime->addAttachment($v->getConfig('directory')."/".$v->getConfig('filename'),"text/plain",$v->getConfig('filename'));
        $body = $mime->get(array("text_encoding"=>"quoted-printable","text_charset"=>$_SESSION["charset"]));
        $hdr = $mime->headers($headers);
        $mail =& Mail::factory("mail");
        $mail->_params="-f ".$user["email"];
        $rc=$mail->send($_POST["icaldest"], $hdr, $body);                
    } else {
        if (strtoupper($_POST["icaldest"]) == "HOME")  $_POST["icaldest"] = "dokumente/".$_SESSION["dbname"]."/".$_SESSION["loginCRM"]."/";
        $v->setConfig( 'directory', $_POST["icaldest"] ); // identify directory
        $v->saveCalendar(); // save calendar to file
    }
    if ($cnt>0) {
        echo $cnt.' Termine exportiert';
    } else {
        echo "Keine Termine";
    }
?>
