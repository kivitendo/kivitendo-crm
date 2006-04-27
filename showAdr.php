<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/FirmaLib.php");
	include("inc/LieferLib.php");
	include("inc/persLib.php");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	$ALabels=getLableNames();
	if (!$_POST["format"] || empty($_POST["format"])) {
		$usr=getUserStamm($_SESSION["loginCRM"]);
		$etikett=($usr["etikett"])?$usr["etikett"]:$ALabels[0]["id"];
	} else {
		$tmp=split("=",$_POST["src"]);
		$_GET[$tmp[0]]=$tmp[1];
		if ($tmp[2]=="ep") $_GET["ep"]=$tmp[3];  
		$etikett=$_POST["format"];
	}
	if ($_GET["pid"]) {
		$dest="pid=".$_GET["pid"];
		$data=getKontaktStamm($_GET["pid"]);
		if ($_GET["ep"]==1) {	// Einzelpersona
			$dest.="=ep=1";
			$anredeF=$data["cp_greeting"]." ".$data["cp_titel"];
			$anredeP=$data["cp_greeting"]." ";
			if ($data["cp_titel"]) $anredeP.=$data["cp_titel"]." ";
			$name1F=$data["cp_givenname"]." ".$data["cp_name"];
			$name1P=$name1F;
			$strasseF=$data["cp_street"];
			$strasseP=$strasseF;
			$landF=$data["cp_country"];
			$landP=$landF;
			$plzF=$data["cp_zipcode"];
			$ortF=$data["cp_city"];
			$ortP=$ortF;
			$plzP=$plzP;
			$title=$data["cp_title"];
		} else {
			$data2=getFirmaStamm($data["cp_cv_id"]);
			if (!$data2) $data2=getLieferStamm($data["cp_cv_id"]);
			$anredeF="Firma";
			$anredeP=$data["cp_greeting"]." ";
			if ($data["cp_titel"]) $anredeP.=$data["cp_titel"]." ";
			$name1P=$data["cp_givenname"]." ".$data["cp_name"];
			$name1F=$data2["name"];
			$name2F=$anredeP.$name1P;
			$strasseF=$data2["street"];
			$strasseP=$data["cp_street"];
			$landF=$data2["country"];
			$landP=$data["cp_country"];
			$plzF=$data2["zipcode"];
			$ortF=$data2["city"];
			$plzP=$data["cp_zipcode"];
			$ortP=$data["cp_city"];
			$title=$data["cp_title"];
		}
	}
	if ($_GET["PID"]) {
		$dest="PID=".$_GET["PID"];
		$data=getKontaktStamm($_GET["PID"]);
		$data2=getLieferStamm($data["cp_cv_id"]);
		$anredeF="Firma";
		$anredeP=$data["cp_greeting"]." ";
		if ($data["cp_titel"]) $anredeP.=$data["cp_titel"]." ";
		$name1P=$data["cp_givenname"]." ".$data["cp_name"];
		$name1F=$data2["name"];
		$name2F=$anredeP.$name1P;
		$strasseF=$data2["street"];
		$strasseP=$data["cp_street"];
		$landF=$data2["country"];
		$landP=$data["cp_country"];
		$plzF=$data2["zipcode"];
		$ortF=$data2["city"];
		$plzP=$data["cp_zipcode"];
		$ortP=$data["cp_city"];
		$title=$data["cp_title"];
	}

	if ($_GET["fid"]) {
		$dest="fid=".$_GET["fid"];
		$data=getFirmaStamm($_GET["fid"]);
		$anredeF="Firma";
		$name1F=$data ["name"];
		$name2F=$data ["name2"];
		$department_1F=$data["department_1"];
		$name1P=$data ["contact"];
		$strasseF=$data ["street"];
		$landF=$data ["country"];
		$plzF=$data["zipcode"];
		$ortF=$data["city"];
	}
	if ($_GET["lid"]) {
		$dest="lid=".$_GET["lid"];
		$data=getLieferStamm($_GET["lid"]);
		$anredeF="Firma";
		$name1F=$data ["name"];
		$name2F=$data ["name2"];
		$department_1F=$data["department_1"];
		$name1P=$data ["contact"];
		$strasseF=$data ["street"];
		$landF=$data ["country"];
		$plzF=$data["zipcode"];
		$ortF=$data["city"];
	}
	if ($_GET["sid"]) {
		$dest="sid=".$_GET["sid"];
		$data=getShipStamm($_GET["sid"]);
		if ($data) {
			$anredeF="Firma";
			$name1F=$data ["shiptoname"];
			$name2F=$data ["shiptoname2"];
			$department_1F=$data["shiptodepartment_1"];
			$name1P=$data ["shiptocontact"];
			$strasseF=$data ["shiptostreet"];
			$landF=$data ["shiptocountry"];
			$plzF=$data["shiptozipcode"];
			$ortF=$data["shiptocity"];
		} else {
			$data=getFirmaStamm($_GET["sid"]);
			if ($data["name"]=="") $data=getLieferStamm($_GET["sid"]);
			$anredeF="Firma";
			$name1F=$data ["name"];
			$department_1F=$data["department_1"];
			$name1P=$data ["contact"];
			$name2F=$data ["name2"];
			$strasseF=$data ["street"];
			$landF=$data ["country"];
			$plzF=$data["zipcode"];
			$ortF=$data["city"];
		}
	}
	$label=getOneLable($etikett);
	if ($_POST["print"]) {
		$platzhalter=array("ANREDE"=>"anredeF","NAME1"=>"name1F","DEPARTMENT"=>"department_1F",
				   "NAME2"=>"name2F","STRASSE"=>"strasseF","PLZ"=>"plzF","ORT"=>"ortF",
				   "KONTAKT"=>"name1P",
				   "ANREDEPERS"=>"anredeP","TITLE"=>"title","NAMEPERS"=>"name1P",
				   "STRASSEPERS"=>"strasseP","PLZPERS"=>"plzP","ORTPERS"=>"ortP","LAND"=>"landF");
		$lableformat=array("paper-size"=>$label["papersize"],'name'=>$label["name"], 'metric'=>$label["metric"], 
							'marginLeft'=>$label["marginleft"], 'marginTop'=>$label["margintop"], 
							'NX'=>$label["nx"], 'NY'=>$label["ny"], 'SpaceX'=>$label["spacex"], 'SpaceY'=>$label["spacey"],
							'width'=>$label["width"], 'height'=>$label["height"], 'font-size'=>6);
		require_once('inc/PDF_Label.php');
		$tmp=split(":",$_POST["xy"]);
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
						$tmp[]=array("text"=>$text,"font"=>$row["font"]);
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
<p class="norm">
Anschrift<br><hr>
	<?= $anredeF ?><br>
	<?= $name1F ?><br>
	<?=  ($name2F)?$name2F."<br>":"" ?>
	<?=  ($name1P)?$name1P."<br>":"" ?>
	<?=  $strasseF ?><br><br>
	<?= ($landF<>"")?$landF." - ":"" ?>
	<?= $plzF ?> <?= $ortF ?><br>
<?	if ($_GET["pid"] || $_GET["PID"]) { ?>
		<hr><br>
		<?= $anredeP ?> <?= $title ?><br>
		<?= $name1P ?><br>
		<?= $strasseP ?><br><br>
		<?= ($landP<>"")?$landP." - ":"" ?>
		<?= $plzP ?> <?= $ortP ?><br>
<?	} ?>
</p>
	<hr>
	<table>
		<tr><td>Etikett&nbsp;</td>
			<td>
				<form name='form' method='post' action='showAdr.php'>
				<input type="hidden" name="src" value="<?= $dest ?>">
				<select name='format' >
<?	foreach ($ALabels as $data) { ?>
					<option value='<?= $data["id"]?>'<?= ($data["id"]==$etikett)?" selected":"" ?>><?= $data["name"] ?>

<?	} ?>
				</select>&nbsp;<input type='submit' name='chfrm' value='wechseln'><br><br> 
			</td>
		</tr>
		<tr>
			<td>
<?	$sel="checked";
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
				<input type='submit' name='print' value='erzeugen'></form><br><br>
				<a href="javascript:self.close()">schlie&szlig;en</a>
				<script language='JavaScript'>self.focus();</script>
			</td>
		</tr>
	</table>
</body>
</html>
