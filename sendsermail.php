<?
//mb_internal_encoding("UTF-8");
require_once("inc/stdLib.php");
require_once("inc/crmLib.php");
include_once("Mail.php");
include_once("Mail/mime.php");

$offset=($_GET["offset"])?$_GET["offset"]:1;
$mime = new Mail_Mime("\n");
$mail =& Mail::factory("mail");
			

$headers=$_SESSION["headers"];
$subject=$_SESSION["subject"];
$bodytxt=$_SESSION["bodytxt"];
$limit=$_SESSION["limit"];

if ($logmail) $f=fopen("tmp/maillog.txt","a");

if ($dateiname) {
	$ftmp=fopen("tmp/".$_SESSION["loginCRM"].".file","rb");
	$filedata=fread($ftmp,filesize("tmp/".$_SESSION["loginCRM"].".file"));
	fclose($ftmp);
	$mime->addAttachment($filedata, $_SESSION["type"],$_SESSION["dateiname"], false );
}

$sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' limit 1";
$data=$db->getAll($sql);
$felder=split(";",$data[0]["csvdaten"]);
$pemail=array_search("EMAIL",$felder);
$pkont=array_search("KONTAKT",$felder);
$sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' offset ".$offset." limit ".$limit;
$data=$db->getAll($sql);
if ($data) {
	$bodytxt=strip_tags($bodytxt);
	foreach ($data as $row) {
		$to="";
		$tmp=split(";",$row["csvdaten"]);
		$text=$bodytxt;
		if ($tmp[$pemail]=="") continue;
		if ($tmp[$pkont]<>"" and $tmp[$pemail]<>"") {
			$to=$tmp[$pkont]." <".$tmp[$pemail].">";
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
			//$body = $mime->get(array("text_encoding"=>"quoted-printable"));
			//$body = $mime->get(array("text_charset"=>"utf-8"));
			$body = $mime->get();
			$hdr = $mime->headers($headers);
			$rc=$mail->send($to, $hdr, $body);
			if ($rc) {
				$data["CRMUSER"]=$_SESSION["loginCRM"];
				$data["cause"]=$subject;
				$data["c_cause"]=$bodytxt."\nAbs: ".$abs;
				$data["CID"]="";
				$data["Kontakt"]="m";
				$data["Bezug"]=0;
				$data['Zeit']=date("H:i");
				$data['Datum']=date("d.m.Y");
				$data["DateiID"]=0;
				$data["Status"]=1;
				$data["DCaption"]=$subject;
				$stamm=false;
				// Einträge in den Kontaktverlauf
				$data["CID"]=$tmp[10];
				insCall($data,false);
				if ($_GET["first"]==1) {
					$data["CID"]=$_SESSION["loginCRM"];		// Dann halt beim Absender in den Thread eintragen
					$data["cause"]=$data["cause"]."|Serienmail";
					$data["c_cause"]=$data["c_cause"]."\n$dateiname";
					$data["Kontakt"]="M";
					insCall($data,false);
					$_GET["first"]=0;
				}		
				if ($logmail) fputs($f,date("Y-m-d H:i").";ok;$to;$abs;$subject\n");
			} else {
				if ($logmail) fputs($f,date("Y-m-d H:i").";error;$to;$abs;$subject\n");
			} // if $rc
		} //if to
	} // foreach
	header("location: sendsermail.php?offset=".($offset+$limit));
} else {
?>
	<center>
	Keine weiteren Mails.<br>
	<a href="javascript:self.close()">Sie k&ouml;nnen das Fenster 
	jetzt schie&szlig;en</a>
	</center>
	
<? } ?>
