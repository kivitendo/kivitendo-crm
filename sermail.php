<?php
// $Id$
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/crmLib.php");
    include_once("inc/UserLib.php");
    $menu =  $_SESSION['menu'];
    $t = new Template($base);
    if ( $_GET['src'] == 'F' ) {
        $t->set_var(array(
            JAVASCRIPTS   => '',
            STYLESHEETS   => $menu['stylesheets'],
            PRE_CONTENT   => '',
            START_CONTENT => '',
            END_CONTENT   => '',
            'JQUERY'        => $_SESSION['basepath'].'crm/',
            'THEME'         => $_SESSION['theme'],
        ));
    } else {
        $t->set_var(array(
            JAVASCRIPTS   => $menu['javascripts'],
            STYLESHEETS   => $menu['stylesheets'],
            PRE_CONTENT   => $menu['pre_content'],
            START_CONTENT => $menu['start_content'],
            END_CONTENT   => $menu['end_content'],
            'JQUERY'        => $_SESSION['basepath'].'crm/',
            'THEME'         => $_SESSION['theme'],
        ));
    }
    $user=getUserStamm($_SESSION["loginCRM"]);
    $MailSign=str_replace("\n","<br>",$user["mailsign"]);
    $MailSign=str_replace("\r","",$MailSign);

    if ($_POST) {
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
                $ok=chkdir($_SESSION["loginCRM"].'/SerMail');
                $pfad=$_SESSION["loginCRM"].'/SerMail';
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
        }
    }  else {
        $BodyText=" \n".str_replace("\r","",$user["mailsign"]);
    }
    
        $t->set_file(array("mail" => "sermail.tpl"));
        $t->set_var(array(
                ERPCSS      => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
                Msg     => $msg,
                CC     => $CC,
                Subject  => $Subject,
                BodyText => $BodyText,
                Sign      => $MailSign,
                SENDTXT  => $sendtxt
        ));
        $t->pparse("out",array("mail"));
            
?>
