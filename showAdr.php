<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/FirmenLib.php");
	include("inc/persLib.php");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	$ALabels=getLableNames();
	$freitext=$_POST["freitext"];
    if ($_GET["vc"]=="customer") { $Q="C";}
    elseif ($_GET["vc"]=="vendor") { $Q="V";} 
    else { $Q=($_GET["Q"])?$_GET["Q"]:$_POST["Q"];}
    $complete=($_POST["complete"])?"checked":"";
	if (!$_POST["format"] || empty($_POST["format"])) {
		$usr=getUserStamm($_SESSION["loginCRM"]);
		$etikett=($usr["etikett"])?$usr["etikett"]:$ALabels[0]["id"];
	} else {
		$tmp=explode("=",$_POST["src"]);
		$_GET[$tmp[0]]=$tmp[1];
		if ($tmp[2]=="ep") $_GET["ep"]=$tmp[3];  
		$etikett=$_POST["format"];
	}
	if ($_GET["pid"]) {
        $anredenFrau = getCpAnredenGeneric('female');
        $anredenHerr = getCpAnredenGeneric('male');
		$dest="pid=".$_GET["pid"];
		$data=getKontaktStamm($_GET["pid"]);
		$id=$_GET["pid"];
		if ($_GET["ep"]==1) {	// Einzelperson
			$dest.="=ep=1";
			$firma="";
		} else {
			$data2=getFirmenStamm($data["cp_cv_id"],true,"C");
			if (!$data2) $data2=getFirmenStamm($data["cp_cv_id"],true,"V");
			$firma=$data2["name"];
		}
        if ($data["language_id"]) {
            if ($data["cp_gender"]=="m") {
	            $anrede = $anredenHerr[$data["language_id"]]; 
            } else { 
	            $anrede = $anredenFrau[$data["language_id"]]; 
            }
        } else {
            $anrede = ($data["cp_gender"]=="m")?"Herr":"Frau";
        }
		$titel=$data["cp_title"];
		$name=$data["cp_givenname"]." ".$data["cp_name"];
		$name1=$data["cp_name"];
		$name2=$data["cp_givenname"];
		$strasse=(!$data["cp_street"] && $complete)?$data2["street"]:$data["cp_street"];
		$land=(!$data["cp_country"] && $complete)?$data2["country"]:$data["cp_country"];
		$plz=(!$data["cp_zipcode"] && $complete)?$data2["zipcode"]:$data["cp_zipcode"];
		$ort=(!$data["cp_city"] && $complete)?$data2["city"]:$data["cp_city"];
		$telefon=(!$data["cp_phone1"] && $complete)?$data2["phone1"]:$data["cp_phone1"];
		$fax=$data["cp_fax"];
		$email=$data["cp_email"];
		$kontakt="";
	} else if ($_GET["sid"]) {
		$id=$_GET["sid"];
		$dest="sid=".$_GET["sid"];
		$data=getShipStamm($_GET["sid"],$Q,$_POST["complete"]);
		//$anrede="Firma";
		if ($data) {
			$anrede=$data ["shiptogreeting"];
			$name=$data ["shiptoname"];
			$name2=$data["shiptodepartment_1"];
			$kontakt=$data ["shiptocontact"];
			$strasse=$data ["shiptostreet"];
			$land=$data ["shiptocountry"];
			$plz=$data["shiptozipcode"];
			$ort=$data["shiptocity"];
			$telefon=$data["shiptophone"];
			$fax=$data["shiptofax"];
			$email=$data["shiptoemail"];
		} 
		$name1=$name;
	} else {
		$id=$_GET["fid"];
		$dest="fid=".$_GET["fid"];
		$data=getFirmenStamm($_GET["fid"],true,$Q);
		$anrede=$data["greeting"];
		$name=$data ["name"];
		$name1=$name;
		$name2=$data["department_1"];
		$kontakt=$data["contact"];
		$strasse=$data["street"];
		$land=$data["country"];
		$plz=$data["zipcode"];
		$ort=$data["city"];
		$telefon=$data["phone"];
		$fax=$data["fax"];
		$email=$data["email"];
		$kdnr=$data["customernumber"];
	}
	$label=getOneLable($etikett);
	if ($_POST["print"]) {
		$platzhalter=array("ANREDE"=>"anrede","TITEL"=>"titel","TEXT"=>"freitext",
				   "NAME"=>"name","NAME1"=>"name1","NAME2"=>"name2",
				   "STRASSE"=>"strasse","PLZ"=>"plz","ORT"=>"ort","LAND"=>"land",
				   "KONTAKT"=>"kontakt","FIRMA"=>"firma","ID"=>"id","KDNR"=>"kdnr",
				   "EMAIL"=>"email","TEL"=>"telefon","FAX"=>"fax");
		$lableformat=array("paper-size"=>$label["papersize"],'name'=>$label["name"], 'metric'=>$label["metric"], 
							'marginLeft'=>$label["marginleft"], 'marginTop'=>$label["margintop"], 
							'NX'=>$label["nx"], 'NY'=>$label["ny"], 'SpaceX'=>$label["spacex"], 'SpaceY'=>$label["spacey"],
							'width'=>$label["width"], 'height'=>$label["height"], 'font-size'=>6);
		require_once('inc/PDF_Label.php');
		$tmp=explode(":",$_POST["xy"]);
		$SX=substr($tmp[0],1);
		$SY=substr($tmp[1],1);
		$pdf = new PDF_Label($lableformat, $label["metric"], $SX, $SY);
		$pdf->Open(); 
		unset($tmp);
		if ($SX<>1 or $SY<>1)	$pdf->AddPage();
		foreach ($label["Text"] as $row) {
			preg_match_all("/%([A-Z0-9_]+)%/U",$row["zeile"],$ph, PREG_PATTERN_ORDER);
			if ($ph) {
				$first=true;
				$oder=strpos($row["zeile"],"|");
				$ph=array_slice($ph,1);
				if ($ph[0]) { foreach ($ph as $x) {
					foreach ($x as $u) {
						$y=$platzhalter[$u];
						if (${$y} <>"" and $first) {
							$y=utf8_decode($y);
							$row["zeile"]=str_replace("%".$u."%",${$y},$row["zeile"]);	
							if ($oder>0) $first=false;
						} else {
							$row["zeile"]=str_replace("%".$u."%","",$row["zeile"]);
						}
					}
				}
			};
			if ($oder>0) $row["zeile"]=str_replace("|","",$row["zeile"]);
			if ($row["zeile"]<>"!") {
				if ($row["zeile"][0]=="!") {
							$text=substr($row["zeile"],1);
						} else {
							$text=$row["zeile"];
						}
						$tmp[]=array("text"=>utf8_decode($text),"font"=>$row["font"]);
						//$tmp[]=array("text"=>$text,"font"=>$row["font"]);
					}
			}
		};
		$pdf->Add_PDF_Label2($tmp);
		$pdf->Output();
		exit;
	}
?>
<html>
<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<body>
<form name='form' method='post' action='showAdr.php'>
<input type="hidden" name="src" value="<?php echo  $dest ?>">
<input type="hidden" name="Q" value="<?php echo  $Q ?>">
<p class="norm">
Anschrift<br><hr>
	<?php echo  ($firma)?"Firma ".$firma."<br><br>":"" ?>
	<?php echo  $anrede ?> <?php echo  $title ?><br>
    <?php echo  $name ?><br>
	<?php echo  (!$_GET["pid"] && $name2)?"$name2<br>":"" ?>
	<?php echo  ($kontakt)?$kontakt."<br>":"" ?>
	<?php echo  $strasse ?><br><br>
	<?php echo  ($land<>"")?$land." - ":"" ?>
	<?php echo  $plz ?> <?php echo  $ort ?><br>
</p>
	<hr>
	<input type="text" name="freitext" size="25" value="<?php echo  $freitext ?>">
	<hr>
	<table>
<?php if ($_GET["pid"] or $_GET["sid"]) { ?>
        <tr><td colspan="2">Daten aus Hauptanschrift erg&auml;nzen <input type="checkbox" name="complete" value="1" <?php echo  $complete ?>></TD></tr>
<?php } ?>
		<tr><td>Etikett&nbsp;</td>
			<td>
				<select name='format' >
<?php foreach ($ALabels as $data) { ?>
					<option value='<?php echo  $data["id"]?>'<?php echo  ($data["id"]==$etikett)?" selected":"" ?>><?php echo  $data["name"] ?>

<?php } ?>
				</select>&nbsp;<input type='submit' name='chfrm' value='Ansicht erneuern'>
			</td>
		</tr>
		<tr>
			<td><br> 
<?php $sel="checked";
	for ($y=1; $y<=$label["ny"];$y++) {
		echo "\t\t\t\t";
		for ($x=1; $x<=$label["nx"];$x++) {
			echo "<input type='radio' name='xy' value='x$x:y$y' $sel>";
			$sel="";
		}
		echo "<br>\n";
	}
?>
			</td>
			<td>
                <br />
				<input type='submit' name='print' value='erzeugen'></form><br><br>
				<a href="javascript:self.close()">schlie&szlig;en</a>
				<script language='JavaScript'>self.focus();</script>
			</td>
		</tr>
	</table>
</body>
</html>
