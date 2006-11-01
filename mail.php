<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	include_once("Mail.php");
	include_once("Mail/mime.php");
	// $data=$_POST;
	$referer=getenv("HTTP_REFERER");
	if (preg_match("/.+\.pl/",$referer) || preg_match("/mail.php/",$referer)) {
		$referer="";
		$btn="<a href='mail.php'><input type=\"button\" name=\"return\" value=\"neu\"></a>";
	} else { // Rückkehr zur Ausgangsseite
		$btn="<a href=\"$referer\"><input type=\"button\" name=\"return\" value=\"zur&uuml;ck\"></a>";
		$TO=$_GET["TO"];
		$KontaktTO=$_GET["KontaktTO"];
	}
	if (!$_POST["ok"]) {	
		$user=getUserStamm($_SESSION["loginCRM"]);
		$MailSign=ereg_replace("\r","",$user["MailSign"]);
		$BodyText=" \n".$MailSign;
		$MailSign=ereg_replace("\n","<br>",$user["MailSign"]);
		$MailSign=ereg_replace("\r","",$MailSign);
	}
	if ($_POST) {
		$okT=true; $okC=true; $msg="";
		if ($_POST["TO"]) { 
			$TO=preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)]/im", "", $_POST["TO"]);
			$rc=chkMailAdr($TO); if($rc<>"ok") { $okT=false; $msg="TO:".$rc; }; 
		};
		if ($_POST["CC"]) { 
			$CC=preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)]/im", "", $_POST["CC"]);
			$rc=chkMailAdr($CC); if($rc<>"ok") { $okC=false; $msg.=" CC:".$rc; }; 
		};
		if (!$_POST["TO"] && !$_POST["CC"]) {$okT=false; $msg="Kein (g&uuml;ltiger) Empf&auml;nger";};
		if ($okT&&$okC) {
			$mime = new Mail_Mime("\n");
			$mail =& Mail::factory("mail");
			$user=getUserStamm($_SESSION["loginCRM"]);
			$abs=$user["Name"]." <".$user["eMail"].">";
			$Subject=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["Subject"]);
			$headers=array( 
					"Return-Path"	=> $abs,
					"Reply-To"	=> $abs,
					"From"		=> $abs,
					"X-Mailer"	=> "PHP/".phpversion(),
					"Subject"	=> $Subject);

      			$to=($TO)?$TO:$CC;
      			if (substr(",",$to)) {
      				$tmp=split(",",$to);
      				$to=array();
      				foreach ($tmp as $row) { $to[]=trim($row); }
      			}
			if ($TO && $CC) $headers["Cc"]=$CC;
			$BodyText=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["BodyText"]);
			$mime->setTXTBody(strip_tags($BodyText));
			$anz=($_FILES["Datei"]["name"][0]<>"")?count($_FILES["Datei"]["name"]):0;
			$anh=false;
			if ($anz>0) {
				for ($o=0; $o<$anz; $o++) {
					if ($_FILES["Datei"]["name"][$o]<>"") {
						//move_uploaded_file($_FILES["Datei"]["tmp_name"][$o],"tmp/".$_FILES["Datei"]["name"][$o]);
						copy($_FILES["Datei"]["tmp_name"][$o],"tmp/".$_FILES["Datei"]["name"][$o]);
						$mime->addAttachment("tmp/".$_FILES["Datei"]["name"][$o] , $_FILES["Datei"]["type"][$o]);
						unlink ("tmp/".$_FILES["Datei"]["name"][$o]);
						$anh=true;
					}
				}
			}
			
			$body = $mime->get(array("text_encoding"=>"quoted-printable"));
			$hdr = $mime->headers($headers);
			$rc=$mail->send($to, $hdr, $body);
			if ($logmail) {
				$f=fopen("tmp/maillog.txt","a");
				if ($rc) {
					fputs($f,date("Y-m-d H:i").";ok;".$TO.";".$CC.";$abs;".$Subject.";\n");
				} else {
					fputs($f,date("Y-m-d H:i").";error;".$_POST["TO"].";".$_POST["CC"].";$abs;".$_POST["Subject"].";".PEAR_Error::getMessage()."\n");
				}
			}
			if ($rc) {
				if (!$anh) { $_FILES=false; };
				$data["CRMUSER"]=$_SESSION["loginCRM"];
				$data["cause"]=$Subject;
				$data["c_cause"]=$BodyText."\nAbs: ".$abs;
				$data["CID"]="";
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
					$data["CID"]=$_SESSION["loginCRM"];		// Dann halt beim Absender in den Thread eintragen
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
			$Subject=preg_replace( "/(content-type:|bcc:|cc:|to:|from:|\n|\r|%0D)/im", "", $_POST["Subject"]);
			$BodyText=preg_replace( "/(content-type:|bcc:|cc:|to:|from:|\n|\r|%0D)/im", "", $_POST["BodyText"]);	
		}
	}
	$t = new Template($base);
	$t->set_file(array("mail" => "mail.tpl"));
	$t->set_var(array(
			Msg		=> $msg,
			btn		=> $btn,
			Subject => $Subject,
			BodyText => $BodyText,
			CC => $CC,
			TO => $TO,
			Sign => $MailSign,
			KontaktCC => $KontaktCC,
			KontaktTO => $KontaktTO,
			QUELLE => $referer,
			JS => ""
			));
	$t->pparse("out",array("mail"));
?>
