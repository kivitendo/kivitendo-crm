<?php
// $Id$
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/crmLib.php");
    include("inc/UserLib.php");
    include_once("Mail.php");
    include_once("Mail/mime.php");
    require("mailcommon".XajaxVer.".php");
    $referer=getenv("HTTP_REFERER");
    if (preg_match("/mail.php/",$referer)) {
        $referer=$_POST["QUELLE"];
        $TO=$_POST["TO"];
        $KontaktTO=$_POST["KontaktTO"];
        if (preg_match("/mail.php/",$referer)) {
            $btn="<a href='mail.php'><input type=\"button\" name=\"return\" value=\".:new:.\"></a>";
        } else {
            $btn="<a href=\"$referer\"><input type=\"button\" name=\"return\" value=\".:back:.\"></a>";
        }
    } else if (preg_match("/.+\.pl/",$referer)) { //Kommt vom Menue
        $TO=$_GET["TO"];
        $KontaktTO=$_GET["KontaktTO"];
        if ($_GET["vc"]=="customer") { $KontaktTO="C".$KontaktTO;} 
        elseif ($_GET["vc"]=="vendor") { $KontaktTO="V".$KontaktTO;} ;
        $referer="mail.php";
        $btn="<a href='mail.php'><input type=\"button\" name=\"return\" value=\".:new:.\"></a>";
        $hide="visible";
    } else { // Rückkehr zur Ausgangsseite
        $TO=$_GET["TO"];
        $KontaktTO=$_GET["KontaktTO"];
        if (substr($KontaktTO,0,1)=="P") $referer.="&id=".substr($KontaktTO,1);
        $btn="<a href=\"$referer\"><input type=\"button\" name=\"return\" value=\".:back:.\"></a>";
        $hide="hidden";
    }
    if ($_POST["aktion"]=="tplsave") {
        $rc=saveMailVorlage($_POST);
    } else     if ($_POST["MID"]) {
        $KontaktTO=$_POST["KontaktTO"];
        $data=getOneMailVorlage($_POST["MID"]);
        $Subject=$data["cause"];
        $BodyText=$data["c_long"];
        if (substr($KontaktTO,0,1)=="K") {
            include("inc/persLib.php");
            $empf=getKontaktStamm(substr($KontaktTO,1));
            $TO=$empf["cp_email"];
        } else if ($KontaktTO) {
            include("inc/FirmenLib.php");
            $empf=getFirmenStamm(substr($KontaktTO,1),true,substr($KontaktTO,0,1));
            $TO=$empf["email"];
        }
        if ($KontaktTO) {
            preg_match_all("/%([A-Z0-9_]+)%/iU",$BodyText,$ph, PREG_PATTERN_ORDER);
            $ph=array_slice($ph,1);
            if ($ph[0]) {
                $anrede=false;
                foreach ($ph[0] as $x) {
                    $y=$empf[strtolower($x)];
                    if ($x=="cp_greeting") $anrede=$y;
                    $BodyText=preg_replace("/%".$x."%/i",$y,$BodyText);
                }
                if ($anrede=="Herr") { $BodyText=preg_replace("/%cp_anrede%/","r",$BodyText); }
                else if ($anrede) { $BodyText=preg_replace("/%cp_anrede%/","",$BodyText); }
            }
        }
    } else if ($_POST["aktion"]=="sendmail") {
        $okT=true; $okC=true; $msg="";
        if ($_POST["TO"]) {
            $TO=preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)<>]/im", "", $_POST["TO"]);
            $rc=chkMailAdr($TO); if($rc<>"ok") { $okT=false; $msg="TO:".$rc; }; 
        };
        if ($_POST["CC"]) { 
            $CC=preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)<>]/im", "", $_POST["CC"]);
            $rc=chkMailAdr($CC); if($rc<>"ok") { $okC=false; $msg.=" CC:".$rc; }; 
        };
        if (!$_POST["TO"] && !$_POST["CC"]) {$okT=false; $msg="Kein (g&uuml;ltiger) Empf&auml;nger";};
        if ($okT&&$okC) {
            $mime = new Mail_Mime("\n");
            $mail =& Mail::factory("mail");
            $user=getUserStamm($_SESSION["loginCRM"]);
            // geht hier nicht ums Konvertieren, sonder ums Quoten!
            mb_internal_encoding(ini_get("default_charset"));
            $Name = mb_encode_mimeheader($user["name"], ini_get("default_charset"), 'Q', '');
            $abs = $Name.' <'.$user["email"].'>';
            $Subject = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["Subject"]);
            $SubjectMail = mb_encode_mimeheader($Subject, ini_get("default_charset") , 'Q', '');
            $headers=array( 
                    "Return-Path"    => $abs,
                    "Reply-To"    => $abs,
                    "From"        => $abs,
                    "X-Mailer"    => "PHP/".phpversion(),
                    "Subject"    => $SubjectMail);
            $to=($TO)?$TO:$CC;
            if ((strpos($to,",")>0)) {
                $tmp=explode(",",$to);
                $to=array();
                foreach ($tmp as $row) { $to[]=trim($row); }
            }
            if ($TO && $CC) {
                if ($_POST["bcc"]) {
                    $headers["Bcc"] = $CC;
                } else {
                    $headers["Cc"]=$CC;
                };
            };
            $BodyText=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["BodyText"]);
            $mime->setTXTBody(strip_tags($BodyText));
            $anz=($_FILES["Datei"]["name"][0]<>"")?count($_FILES["Datei"]["name"]):0;
            $anh=false;
            if ($anz>0) {
                for ($o=0; $o<$anz; $o++) {
                    if ($_FILES["Datei"]["name"][$o]<>"") {
                        //move_uploaded_file($_FILES["Datei"]["tmp_name"][$o],"tmp/".$_FILES["Datei"]["name"][$o]);
                        copy($_FILES["Datei"]["tmp_name"][$o],"tmp/".$_FILES["Datei"]["name"][$o]);
                        $mime->addAttachment("tmp/".$_FILES["Datei"]["name"][$o] , $_FILES["Datei"]["type"][$o],
                                                $_FILES["Datei"]["name"][$o]);
                        unlink ("tmp/".$_FILES["Datei"]["name"][$o]);
                        $anh=true;
                    }
                }
            } else {
                $headers["Content-Type"] = "text/plain; charset=".ini_get("default_charset");
            }
            
            $body = $mime->get(array("text_encoding"=>"quoted-printable","text_charset"=>ini_get("default_charset")));
            $hdr = $mime->headers($headers);
            $mail->_params="-f ".$user["email"];
            $rc=$mail->send($to, $hdr, $body);
            if ($logmail) {
                $f=fopen("tmp/maillog.txt","a");
                if ($rc) {
                    fputs($f,date("Y-m-d H:i").';ok;'.$TO.';'.$CC.';'.$user["name"].' <'.$user["email"].'>;'.$Subject.";\n");
                } else {
                    fputs($f,date("Y-m-d H:i").';error;'.$_POST["TO"].';'.$_POST["CC"].';'.
                                    $user["name"].' <'.$user["email"].'>;'.$_POST["Subject"].
                                    ';'.PEAR_Error::getMessage()."\n");
                }
            }
            if ($rc) {
                if (!$anh) { $_FILES=false; };
                $data["CRMUSER"]=$_SESSION["loginCRM"];
                $data["cause"]=$Subject;
                $data["c_cause"]=$BodyText."\nAbs: ".$user["name"].' <'.$user["email"].'>';
                $data["Q"]=$_POST["KontaktTO"][0];
                if ($data["Q"]=="C" || $data["Q"]=="V") {
                    include("inc/FirmenLib.php");
                    $empf=getFirmenStamm(substr($KontaktTO,1),true,substr($KontaktTO,0,1));
                    $data["fid"]=$empf["id"];
                    $data["CID"]=$empf["id"];
                    $data["nummer"]=$empf["nummer"];
                } else {
                    include("inc/persLib.php");
                    $empf=getKontaktStamm(substr($KontaktTO,1));
                    $data["fid"]=$empf["cp_cv_id"];
                    $data["CID"]==$empf["cp_id"];
                    $data["nummer"]=$empf["nummer"];
                };        
                $data["Kontakt"]="M";
                $data["Bezug"]=0;
                $data['Zeit']=date("H:i");
                $data['Datum']=date("d.m.Y");
                $data["DateiID"]=0;
                $data["Status"]=1;
                $data["DCaption"]=$Subject;
                $stamm=false;
                // Einträge in den Kontaktverlauf
                if ($_POST["KontaktTO"] && substr($_POST["KontaktTO"],0,1)<>"E"){
                    $data["CID"]=substr($_POST["KontaktTO"],1);
                    insCall($data,$_FILES);
                    $stamm=true;
                }
                if ($_POST["KontaktCC"] && !substr($_POST["KontaktCC"],0,1)<>"E"){
                    $data["CID"]=substr($_POST["KontaktCC"],1);
                    insCall($data,$_FILES);
                    $stamm=true;
                }
                if (!$stamm) {
                    $data["CID"]=$_SESSION["loginCRM"];        // Dann halt beim Absender in den Thread eintragen
                    $data["cause"]=$Subject."|".$_POST["TO"];
                    insCall($data,$_FILES);
                }
                $TO=""; $CC=""; $msg="Mail versendet";
                $Subject=""; $BodyText="";
                if ($_POST["QUELLE"]) header("Location: ".$_POST["QUELLE"]);
            } else {
                $msg="Fehler beim Versenden ".PEAR_Error::getMessage ();
                //$TO=$_POST["TO"]; $CC=$_POST["CC"]; $msg="Fehler beim Versenden ".PEAR_Error::getMessage ();
                //$Subject=$_POST["Subject"]; $BodyText=$_POST["BodyText"];
            }
        } else {
            $Subject=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["Subject"]);
            $BodyText=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["BodyText"]);    
        }
    } else {    
        $user=getUserStamm($_SESSION["loginCRM"]);
        $MailSign=ereg_replace("\r","",$user["mailsign"]);
        $BodyText=" \n".$MailSign;
        $MailSign=ereg_replace("\n","<br>",$user["mailsign"]);
        $MailSign=ereg_replace("\r","",$MailSign);
    }

    $t = new Template($base);
    $t->set_file(array("mail" => "mail.tpl"));
    $t->set_block("mail","Betreff","Block");
    $mailvorlagen=getMailVorlage();
    if ($mailvorlagen) foreach ($mailvorlagen as $vorlage) {
        $t->set_var(array(
            MID => $vorlage["id"],
            CAUSE => $vorlage["cause"],
            C_LONG => $vorlage["c_long"]
        ));
                $t->parse("Block","Betreff",true);
    }
    $t->set_var(array(
            ERPCSS      => $_SESSION["stylesheet"],
            AJAXJS    => $xajax->printJavascript(XajaxPath),
            Msg    => $msg,
            btn    => $btn,
            Subject => $Subject,
            BodyText => $BodyText,
            CC     => $CC,
            TO     => $TO,
            Sign     => $MailSign,
            KontaktCC => $_POST["KontaktCC"],
            KontaktTO => $KontaktTO,
            QUELLE     => $referer,
            JS     => "",
            hide    => $hide,
            vorlage => ($_GET["MID"])?$_GET["MID"]:$_POST["MID"]
            ));
    $t->Lpparse("out",array("mail"),$_SESSION["lang"],"work");
?>
