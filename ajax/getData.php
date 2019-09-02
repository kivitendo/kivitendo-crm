<?php

require_once __DIR__.'/../inc/ajax2function.php';

//ToDo: Add autocompletion, etc

function getHistory(){
    $rs = $GLOBALS['dbh']->getOne( "select val from crmemployee where uid = '" . $_SESSION["loginCRM"]."' AND manid = ".$_SESSION['manid']." AND key = 'search_history'" );
    echo $rs['val'] ? $rs['val'] : '0';
}



/**
 *  Send the Serienmail and store the text in the contacts table for each recepient.
 *  This method uses the $_POST variable rather than the $data parameter provided by ajax2function.php
 *  because the request contains form data instead of a JSON object to enable file upload.
 *  The return type is also not JSON but HTML. Ajax request datatype on client must be set to 'html'.
 */
function sendSerienmail() {
    // Imports must be inside the function instead of at the top of the file because the way the functions are called
    // the imports are ignored otherwise.
    require_once __DIR__.'/../inc/crmLib.php';
    require_once __DIR__.'/../inc/UserLib.php';

    $user=getUserStamm($_SESSION["loginCRM"]);

    $Subject=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["Subject"]);
    $BodyText=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["BodyText"]);
    $okC=true; $okA = true;
    if ($_POST["CC"]<>"") {
        $CC=preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)]/im", "", $_POST["CC"]);
        $CC=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $CC);
        $rc=chkMailAdr($CC);
        if($rc<>"ok") {
            $okC=false; $msg.=" CC:".$rc;
        } else {
            insertCSVData(array('CC',$CC,'','','','','','','',$CC,'',-1,'','','','','','','','',''),-1);
        }
    };
    // geht hier nicht ums Konvertieren, sonder ums Quoten!
    mb_internal_encoding($_SESSION["charset"]);
    $Name = mb_encode_mimeheader($user["name"], $_SESSION["charset"], 'Q', '');
    if ( $user["email"] != '' ) {
        $abs = $Name.' <'.$user["email"].'>';
        $rc = chkMailAdr($user["email"]); if($rc<>"ok") { $okA=false; $msg.=" Abs:".$rc; };
    } else if ( $_SESSION['email'] != '' ) {
        $abs = $Name.' <'.$_SESSION["email"].'>';
        $rc = chkMailAdr($_SESSION["email"]); if($rc<>"ok") { $okA=false; $msg.=" Abs:".$rc; };
    } else {
        $okA=false; $msg.=" Kein Absender";
    }
    if ( $okC && $okA ) {
        $dateiname = "";
        if ($_FILES["Datei"]["name"]<>"") {
            $dat["Datei"]["name"]=$_FILES["Datei"]["name"];
            $dat["Datei"]["tmp_name"]=$_FILES["Datei"]["tmp_name"];
            $dat["Datei"]["type"]=$_FILES["Datei"]["type"];
            $dat["Datei"]["size"]=$_FILES["Datei"]["size"];
            $dbfile=new document();
            $dbfile->setDocData("descript",$Subject);
            $ok=chkdir($_SESSION["login"].'/SerMail');
            $pfad=$_SESSION["login"].'/SerMail';
            $rc=$dbfile->uploadDocument($_FILES,$pfad);
            $dateiID=$dbfile->id;
            $dateiname=$_FILES["Datei"]["name"];
        }
        $limit=50;
        $SubjectMail = mb_encode_mimeheader($Subject, $_SESSION["charset"], 'Q', '');
        $headers=array(
                       "Return-Path"   => $user["email"],
                       "Reply-To"      => $abs,
                       "From"          => $abs,
                       "X-Mailer"      => "PHP/".phpversion(),
                       "Subject"       => $SubjectMail);
        if ($dateiname=="")   $headers["Content-Type"] = "text/plain; charset=".$_SESSION["charset"];
        $_SESSION["headers"]=$headers;
        $_SESSION["Subject"]=$Subject;
        $_SESSION["bodytxt"]=$BodyText;
        $_SESSION["dateiname"]=$dateiname;
        $_SESSION["dateiId"]=($dateiID)?$dateiID:0;
        $_SESSION["limit"]=$limit;

        $sendtxt="Es &ouml;ffnet sich nun ein extra Fenster.<br>";
        $sendtxt.="Bitte schlie&szlig;en sie es nur wenn sie dazu aufgefordert werden,<br>";
        $sendtxt.="da sonst der Mailversand beendet wird.<br><br>";
        $sendtxt.="Sie k&ouml;nnen aber ganz normal mit anderen Programmteilen arbeiten.";
        $sendtxt.="<script language='JavaScript'>fx=open('sendsermail.php?first=1','sendmail','width=200,height=100');</script>";
        $sendtxt.="<pre>$BodyText</pre>";
        echo $sendtxt;
    }
}


/**
 * Get the user's mail signature.
 */
function getMailSign(){
    require_once __DIR__.'/../inc/UserLib.php';

    $user=getUserStamm($_SESSION["loginCRM"]);
    $MailSign=str_replace("\n","<br>",$user["mailsign"]);
    $MailSign=str_replace("\r","",$MailSign);
    echo json_encode($MailSign);
}
?>
