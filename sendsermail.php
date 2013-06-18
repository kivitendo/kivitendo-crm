<?php
//mb_internal_encoding("UTF-8");
require_once("inc/stdLib.php");
include_once("inc/UserLib.php");
require_once("inc/crmLib.php");
include_once("Mail.php");
include_once("Mail/mime.php");
mb_internal_encoding($_SESSION["charset"]);
$offset=($_GET["offset"])?$_GET["offset"]:1;
$mime = new Mail_Mime("\n");
$mail =& Mail::factory("mail");
$headers=$_SESSION["headers"];
$user=getUserStamm($_SESSION["loginCRM"]);
$mail->_params="-f ".$user["email"];
$subject=$headers["Subject"];
$betreff=$_SESSION["Subject"];
$bodytxt=$_SESSION["bodytxt"];
$limit=$_SESSION["limit"];
$abs=$headers["Return-Path"];
if ($_SESSION['logmail']) $f=fopen("log/maillog.txt","a");
$dateiname=$_SESSION["dateiname"];
if ($dateiname) {
	$ftmp=fopen("./dokumente/".$_SESSION["mansel"]."/".$_SESSION["loginCRM"]."/SerMail/".$dateiname,"rb");
	$filedata=fread($ftmp,filesize("./dokumente/".$_SESSION["mansel"]."/".$_SESSION["loginCRM"]."/SerMail/".$dateiname));
	fclose($ftmp);
	$mime->addAttachment($filedata, $_SESSION["type"],$_SESSION["dateiname"], false );
}

$sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' and id < 1";
$data=$db->getAll($sql);
$felder=explode(":",$data[0]["csvdaten"]);
$pemail=array_search("EMAIL",$felder);
$cid=array_search("ID",$felder);
$pkont=array_search("KONTAKT",$felder);
$sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' order by id offset ".$offset." limit ".$limit;
$data=$db->getAll($sql);
if ($data) {
	$bodytxt=strip_tags($bodytxt);
	foreach ($data as $row) {
		$to="";
		$tmp=explode(":",$row["csvdaten"]);
		$text=$bodytxt;
		if ($tmp[$pemail]=="") continue;
		if ($tmp[$pkont]<>"" and $tmp[$pemail]<>"") {
                        $Name = mb_encode_mimeheader($tmp[$pkont], $_SESSION["charset"], 'Q', '');
			$to=$Name." <".$tmp[$pemail].">";
		} else {
			$to=$tmp[$pemail];
		}
		if ($to<>"") {
			preg_match_all("/%([A-Z0-9_]+)%/U",$text,$ph, PREG_PATTERN_ORDER);
			if ($ph) {
				$ph=array_slice($ph,1);
				if ($ph[0]) { foreach ($ph as $x) {
					foreach ($x as $u) {
						$p=array_search($u,$felder);
						if ($p!==false) {
							$y=$tmp[$p];
							$text=str_replace("%".$u."%",$y,$text);
						} else {
							$text=str_replace("%".$u."%","",$text);
						}
					}
				}};
			};
			$mime->setTXTBody($text);
                        $body = $mime->get(array("text_encoding"=>"quoted-printable","text_charset"=>$_SESSION["charset"]));
			$hdr = $mime->headers($headers);
			$rc=$mail->send($to, $hdr, $body);
			if ($rc && $row['id']>0) {
				$data["CRMUSER"]=$_SESSION["loginCRM"];
				$data["cause"]="Sermail: ".$betreff;
				$data["c_cause"]=$bodytxt."\nAbs: ".$abs;
				//if ($dateiname) $data["c_cause"].="\nDatei: ".$_SESSION["loginCRM"].'/'.$dateiname;
				$data["CID"]=$tmp[$cid];
				$data["Kontakt"]="m";
				$data["Bezug"]=0;
				$data['Zeit']=date("H:i");
				$data['Datum']=date("d.m.Y");
				$data["DateiID"]=$_SESSION["dateiId"];
				$data["Status"]=1;
				$data["DCaption"]=$betreff;
				$stamm=false;
				// Einträge in den Kontaktverlauf
				insCall($data,false);
				if ($_GET["first"]==1) {
					$data["CID"]=$_SESSION["loginCRM"];		// Dann halt beim Absender in den Thread eintragen
					$data["cause"]=$data["cause"]."|Serienmail";
					$data["c_cause"]=$data["c_cause"]."\n$dateiname";
					$data["Kontakt"]="M";
					insCall($data,false);
					$_GET["first"]=0;
				}		
				if ($_SESSION['logmail']) fputs($f,date("Y-m-d H:i").";ok;$to;$abs;S:$betreff\n");
			} else {
				if ($_SESSION['logmail']) fputs($f,date("Y-m-d H:i").";error;$to;$abs;S:$betreff\n");
			} // if $rc
		} //if to
	} // foreach
	header("location: sendsermail.php?offset=".($offset+$limit));
} else {
    /* Was soll das??
	if ($dateiname) {
		$ok=chkdir($_SESSION["loginCRM"]);
       	copy("./dokumente/".$_SESSION["mansel"]."/".$_SESSION["loginCRM"]."/SerMail/$dateiname","./dokumente/".$_SESSION["mansel"]."/".$_SESSION["loginCRM"]."/".$dateiname);
	}; */
?>
	<center>
	Keine weiteren Mails.<br>
	<a href="javascript:self.close()">Sie k&ouml;nnen das Fenster 
	jetzt schie&szlig;en</a>
	</center>
	
<?php } ?>
