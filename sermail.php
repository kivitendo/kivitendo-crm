<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");

	$user=getUserStamm($_SESSION["loginCRM"]);
	$MailSign=str_replace("\n","<br>",$user["mailsign"]);
	$MailSign=str_replace("\r","",$MailSign);

	if ($_POST) {
		$Subject=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["Subject"]);
		$BodyText=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["BodyText"]);
		$okC=true;
		if ($_POST["CC"]<>"") { 
			$CC=preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)]/im", "", $_POST["CC"]);
			$CC=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $CC);
			$rc=chkMailAdr($CC); 
			if($rc<>"ok") { 
				$okC=false; $msg.=" CC:".$rc; 
			} else {
				insertCSVData(array("CC",$CC,"","","","","","",$CC,""),-1);
			}
		};
		if ($okC) {
			$anh="";
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
				//move_uploaded_file($_FILES["Datei"]["tmp_name"],"./dokumente/".$_SESSION["mansel"]."/".$_SESSION["loginCRM"]."/".$dateiname);
				//"tmp/".$_SESSION["loginCRM"].".file");
				//$type=$_FILES["Datei"]["type"];

			}
			$limit=50;
			$abs=sprintf("%s <%s>",$user["name"],$user["email"]);
            // geht hier nicht ums Konvertieren, sonder ums Quoten!
            mb_internal_encoding(ini_get("default_charset"));
            $Name = mb_encode_mimeheader($user["name"], ini_get("default_charset"), 'Q', '');
            $abs = $Name.' <'.$user["email"].'>';
            $SubjectMail = mb_encode_mimeheader($Subject, ini_get("default_charset"), 'Q', '');
			$headers=array(
                                        "Return-Path"   => $user["email"],
                                        "Reply-To"      => $abs,
                                        "From"          => $abs,
                                        "X-Mailer"      => "PHP/".phpversion(),
                                        "Subject"       => $SubjectMail);
			//$headers['Content-Type']='text/plain; charset=utf-8';
			//$headers['Content-Type']='text/plain; charset=iso-8859-1';
			$_SESSION["headers"]=$headers;
            $_SESSION["Subject"]=$Subject;
			$_SESSION["bodytxt"]=$BodyText;
			$_SESSION["dateiname"]=$dateiname;
			$_SESSION["type"]=$type;
			$_SESSION["dateiId"]=($dateiID)?$dateiID:0;
			$_SESSION["limit"]=$limit;

			$sendtxt="Es &ouml;ffnet sich nun ein extra Fenster.<br>";
			$sendtxt.="Bitte schlie&szlig;en sie es nur wenn sie dazu aufgefordert werden,<br>";
			$sendtxt.="da sonst der Mailversand beendet wird.<br><br>";
			$sendtxt.="Sie k&ouml;nnen aber ganz normal mit anderen Programmteilen arbeiten.";
			$sendtxt.="<script language='JavaScript'>fx=open('sendsermail.php?first=1$anh','sendmail','width=200,height=100');</script>";
			$sendtxt.="<pre>$BodyText</pre>";
		}
	}  else {
		$BodyText=" \n".str_replace("\r","",$user["mailsign"]);
	}
	
		$t = new Template($base);
		$t->set_file(array("mail" => "sermail.tpl"));
		$t->set_var(array(
                ERPCSS      => $_SESSION["stylesheet"],
				Msg	 => $msg,
				CC	 => $CC,
				Subject  => $Subject,
				BodyText => $BodyText,
				Sign 	 => $MailSign,
				SENDTXT  => $sendtxt
		));
		$t->pparse("out",array("mail"));
			
?>
