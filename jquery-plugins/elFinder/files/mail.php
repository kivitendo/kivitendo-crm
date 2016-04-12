<?php
// $Id$
    require_once("inc/stdLib.php");
    include_once("template.inc");
    include_once("crmLib.php");
    include_once("UserLib.php");
    include_once("Mail.php");
    include_once("Mail/mime.php");
    $USER = getUserEmployee(array('mailsign','mandsig'));
    if ( (isset($_GET['popup']) && $_GET['popup'] == 1) ) {
        $popup = true;
    } else if ( (isset($_POST['popup']) && $_POST['popup'] == 1) ) {
        $popup = true;
    } else {
        $popup = false;
    }
    if ($_POST["QUELLE"] != '') { 
        $referer=$_POST["QUELLE"]."?popup=".$_POST['popup']; 
        $TO=$_POST["TO"];
        $KontaktTO=$_POST["KontaktTO"];
    } else if ($_GET["TO"] != '') { 
        $referer=getenv("HTTP_REFERER");
        $TO=$_GET["TO"];
        $KontaktTO=$_GET["KontaktTO"];
        if (preg_match("/.+\.php/",$referer)) {
            if (preg_match("/firma/",$referer)) {
                $btn='<a href="'.$referer.'"><image src="image/firma.png" alt=".:back:." title=".:back:." border="0" ></a>';
                $hide="hidden";
            } else {
                $referer = substr($referer,0,strpos($referer,'?'));
                $btn='<a href="mail.php"><image src="image/new.png" alt=".:new:." title=".:new:." border="0" ></a>';
                $hide="visible";
            }
        } else { 
            $referer = $_SESSION['baseurl'].'/crm/mail.php';
            $btn='<a href="mail.php"><image src="image/new.png" alt=".:new:." title=".:new:." border="0" ></a>';
            $hide="visible";
        };
        $referer .= "&popup=".$_POST['popup'];
    } else { 
        $referer = ''; 
        $btn='<a href="mail.php"><image src="image/new.png" alt=".:new:." title=".:new:." border="0" ></a>';
        $hide="visible";
    };
    if ($_POST["aktion"]=="tplsave") {
        $rc=saveMailVorlage($_POST);
    } else if ($_POST["aktion"]=="sendmail") {
        $okT=true; $okC=true; $okA=true; $msg="";
        if ($_POST["TO"]) {
            $TO=preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)<>]/im", "", $_POST["TO"]);
            $rc=chkMailAdr($TO); if($rc<>"ok") { $okT=false; $msg="TO:".$rc; }; 
        };
        if ($_POST["CC"]) { 
            $CC=preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)<>]/im", "", $_POST["CC"]);
            $rc=chkMailAdr($CC); if($rc<>"ok") { $okC=false; $msg.=" CC:".$rc; }; 
        };
        if (!$_POST["TO"] && !$_POST["CC"]) {$okT=false; $msg="Kein Empf&auml;nger";};
        $user=getUserStamm($_SESSION["loginCRM"]);
        // geht hier nicht ums Konvertieren, sonder ums Quoten!
        mb_internal_encoding($_SESSION["charset"]);
        $Name = mb_encode_mimeheader($user["name"], $_SESSION["charset"], 'Q', '');
        $zeichen = "a-z0-9 ";
        if (preg_match("/[$zeichen]*[^$zeichen]+[$zeichen]*/i",$Name)) $Name = '"'.$Name.'"';
        if ( $user["email"] != '' ) {
            $abs = $Name.' <'.$user["email"].'>';
            $rc = chkMailAdr($user["email"]); if($rc<>"ok") { $okA=false; $msg.=" Abs:".$rc; };
        } else if ( $_SESSION['email'] != '' ) {
            $abs = $Name.' <'.$_SESSION["email"].'>';
            $rc = chkMailAdr($_SESSION["email"]); if($rc<>"ok") { $okA=false; $msg.=" Abs:".$rc; };
        } else {
            $okA=false; $msg.=" Kein Absender";
        }
        if ( $okT && $okC && $okA ) {
            $mime = new Mail_Mime("\n");
            $mail =& Mail::factory("mail");
            $Subject = preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["Subject"]);
            $SubjectMail = mb_encode_mimeheader($Subject, $_SESSION["charset"] , 'Q', '');
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
                        //move_uploaded_file($_FILES["Datei"]["tmp_name"][$o],$tmpdir.$_FILES["Datei"]["name"][$o]);
                        copy($_FILES["Datei"]["tmp_name"][$o],'tmp/'.$_FILES["Datei"]["name"][$o]);
                        $mime->addAttachment('tmp/'.$_FILES["Datei"]["name"][$o] , $_FILES["Datei"]["type"][$o],
                                                $_FILES["Datei"]["name"][$o]);
                        unlink ('tmp/'.$_FILES["Datei"]["name"][$o]);
                        $anh=true;
                    }
                }
            } else {
                $headers["Content-Type"] = "text/plain; charset=".$_SESSION["charset"];
            }
            
            $body = $mime->get(array("text_encoding"=>"quoted-printable","text_charset"=>$_SESSION["charset"]));
            $hdr = $mime->headers($headers);
            $mail->_params="-f ".$user["email"];
            $rc=$mail->send($to, $hdr, $body);
            if ($_SESSION['logmail']) {
                $f=fopen('log/maillog.txt','a');
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
                if (!$_POST["KontaktTO"]) {
                    //Aufruf erfolgte nicht aus Kundenmaske
                    //Hoffentlich ist die E-Mail nur einmal vergeben.
                    //Suche erfolgt zuerst in customer, dann vendort und control
                    //Der erste Treffer wird genommen.
                    if ($_POST["TO"]) {
                        $tmp = getSenderMail($_POST["TO"]);
                        $_POST["KontaktTO"] = $tmp["kontakttab"].$tmp["kontaktid"];
                    } else {
                        //Wenn kein TO, dann ist aber CC
                        $tmp = getSenderMail($_POST["CC"]);
                        $_POST["KontaktTO"] = $tmp["kontakttab"].$tmp["kontaktid"];
                    }
                }
                $data["Kontakt"]="M";
                $data["Bezug"]=0;
                $data['Zeit']=date("H:i");
                $data['Datum']=date("d.m.Y");
                $data["DateiID"]=0;
                $data["Status"]=1;
                $data["inout"]='o';
                $data["DCaption"]=$Subject;
                $stamm=false;
                if ($_POST["KontaktTO"]!="") {
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
                }
                if (!$stamm) {
                    $data["CID"]=$_SESSION["loginCRM"];        // Dann halt beim Absender in den Thread eintragen
                    $data["cause"]=$Subject."|".$_POST["TO"];
                    insCall($data,$_FILES);
                }
                $TO=""; $CC=""; $msg="Mail versendet";
                $Subject=""; $BodyText="";
                if ($_POST["QUELLE"]) header("Location: ".$referer);
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
        $BodyText="";// \n".$MailSign;
    }
    switch ($USER['mandsig']) {
        case '0' :  $MailSign  = $USER["mailsign"];
                    break;
        case '1' :  $MailSign  = $USER["msignature"];
                    break;
        case '2' :  $MailSign  = $USER["msignature"];
                    $MailSign .= "\n".$USER["mailsign"];
                    break;
        case '3' :  $MailSign  = $USER["mailsign"];
                    $MailSign .= "\n".$USER["msignature"];
                    break;
        default  :  $MailSign  = $USER["mailsign"];
    }
    $MailSign=str_replace("\n","<br>",$MailSign);
    $MailSign=str_replace("\r","",$MailSign);
    $t = new Template($base);
    $menu =  $_SESSION['menu'];
    $head = mkHeader();

    if ( $popup ) {
        $t->set_var(array(
            'STYLESHEETS'   => $menu['stylesheets'],
            'JAVASCRIPTS'   => $menu['javascripts'],
            'CRMCSS'        => $head['CRMCSS'],
            'THEME'         => $head['THEME'],
            'AUTOCOMPLETE'  => $head['AUTOCOMPLETE'],
        ));
        $hide = 'hidden';
    } else {
        $t->set_var(array(
            'STYLESHEETS'   => $menu['stylesheets'],
            'JAVASCRIPTS'   => $menu['javascripts'],
            'PRE_CONTENT'   => $menu['pre_content'],
            'START_CONTENT' => $menu['start_content'],
            'END_CONTENT'   => $menu['end_content'],
            'CRMCSS'        => $head['CRMCSS'],
            'THEME'         => $head['THEME'],
            'AUTOCOMPLETE'  => $head['AUTOCOMPLETE'],
        ));
    }
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
    $tmpdata = getUserEmployee(array('feature_ac_minlength','feature_ac_delay'));
    $t->set_var(array(
            HEADER   => $header,
            acminlen => ($tmpdata['feature_ac_minLength']!='')?$tmpdata['feature_ac_minLength']:2,
            acdelay  => ($tmpdata['feature_ac_delay']!='')?    $tmpdata['feature_ac_delay']:100,
            Msg      => $msg,
            btn      => $btn,
            Subject  => $Subject,
            BodyText => $BodyText,
            CC       => $CC,
            TO       => $TO,
            Sign     => $MailSign,
            KontaktCC => $_POST["KontaktCC"],
            KontaktTO => $KontaktTO,
            QUELLE   => $referer,
            JS       => "",
            hide     => $hide,
            popup    => $popup,
            closelink => ($popup)?'<a href="JavaScript:self.close()">.:close:.</a>':'',
            vorlage  => ($_GET["MID"])?$_GET["MID"]:$_POST["MID"]
            ));
    $t->Lpparse("out",array("mail"),$_SESSION['countrycode'],"work");
?>
