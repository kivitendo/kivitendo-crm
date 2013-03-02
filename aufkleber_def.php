<?php
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
	
	if ($_POST["hole"]) {
		unset($_POST["Text"]);
		$label=getOneLable($_POST["format"]);
		$id=$label["id"];
		$format=$id;
		$name=$label["name"];
		$cust=$label["cust"];
		$papersize=$label["papersize"]; 
		$metric=$label["metric"]; 
		$margintop=$label["margintop"]; 
		$marginleft=$label["marginleft"]; 
		$spacex=$label["spacex"];;
		$spacey=$label["spacey"]; 
		$nx=$label["nx"]; 
		$ny=$label["ny"]; 
		$width=$label["width"]; 
		$height=$label["height"]; 
		$Ssel="S".$nx;	$Zsel="Z".$ny; $Psel="P".$papersize; $tmp=$metric;
		${$Ssel}=" selected";	${$Zsel}=" selected"; ${$Psel}=" selected"; ${$tmp}=" selected";
		$Textzeilen=count($label["Text"]);
		if ($Textzeilen>0) { 
			$i=0; unset($Text);
			foreach($label["Text"] as $row) {
				$Text[] = $row["zeile"];
				$tmp="SG".$row["font"];
				${$tmp}[$i]=" selected";
				$i++;
			}
		}
	} else if ($_POST["ok"] || $_POST["csave"]) {
		if ($_POST["ok"]) { 
			updLable($_POST); $id=$_POST["id"]; 
			$format=$_POST["format"]; 
		} else { 
			$id=insLable($_POST); 
			$format=$id; 
		};
		$margintop=$_POST["margintop"]; 
		$marginleft=$_POST["marginleft"]; 
		$spacex=$_POST["spacex"];;
		$spacey=$_POST["spacey"]; 
		$nx=$_POST["nx"]; 
		$cust=$_POST["cust"]; 
		$name=$_POST["name"]; 
		$ny=$_POST["ny"]; 
		$width=$_POST["width"]; 
		$height=$_POST["height"]; 
		$Ssel="S".$nx;	$Zsel="Z".$ny;$Psel="P".$papersize; $tmp=$metric;
		${$Ssel}=" selected";	${$Zsel}=" selected"; ${$Psel}=" selected"; ${$tmp}=" selected";
		if ($_POST["Text"]) {
			$Text = $_POST["Text"];
			$Schrift = $_POST["Schrift"];
			$i=0;
			for($i=0; $i<count($Schrift); $i++){
				$tmp="SG".$Schrift[$i];
				${$tmp}[$i]=" selected";
			}
		}
		$Textzeilen=count($_POST["Text"]);
	} else if ($_POST["test"]) {
		$lableformat=array("paper-size"=>$_POST["papersize"],'name'=>$_POST["name"], 'metric'=>$_POST["metric"], 
							'marginLeft'=>$_POST["marginleft"], 'marginTop'=>$_POST["margintop"], 
							'NX'=>$_POST["nx"], 'NY'=>$_POST["ny"], 'SpaceX'=>$_POST["spacex"], 'SpaceY'=>$_POST["spacey"],
							'width'=>$_POST["width"], 'height'=>$_POST["height"], 'font-size'=>6);
		require_once('inc/PDF_Label.php');
		$SX=1; $SY=1; unset($tmp);
		$pdf = new PDF_Label($lableformat, $metric, $SX, $SY);
		$pdf->Open();
		if ($SX<>1 or $SY<>1)	$pdf->AddPage();
		for ($i=0; $i<count($_POST["Text"]); $i++) {
			$tmp[]=array("text"=>$_POST["Text"][$i],"font"=>$_POST["Schrift"][$i]);
		};
		for ($i=0; $i<($_POST["nx"]*$_POST["ny"]); $i++) {
			$pdf->Add_PDF_Label2($tmp);
		};
		$pdf->Output();
		$Textzeilen=count($_POST["Text"]);
	}
	$ALabels=getLableNames();
	
	if (!$Textzeilen || $Textzeilen===0) $Textzeilen=floor($_POST["height"]/(($_POST["metric"]=="mm")?5:0.197));
	if ($_POST["more"]) {
		$Textzeilen=count($_POST["Text"]);
		$Textzeilen++;
		for($i=0; $i<$Textzeilen; $i++){
			$tmp="SG".$_POST["Schrift"][$i];
			${$tmp}[$i]=" selected";
		}
	}
	if ($_POST["less"]) {
		$Textzeilen=count($_POST["Text"]);
		if ($Textzeilen>1) $Textzeilen--;
		for($i=0; $i<$Textzeilen; $i++){
			$tmp="SG".$_POST["Schrift"][$i];
			${$tmp}[$i]=" selected";
		}
	}
	if ($_POST["less"] || $_POST["more"]) {
		$Text = $_POST["Text"];
		$format=$_POST["format"]; 
		$margintop=$_POST["margintop"]; 
		$marginleft=$_POST["marginleft"]; 
		$spacex=$_POST["spacex"];;
		$spacey=$_POST["spacey"]; 
		$nx=$_POST["nx"]; 
		$ny=$_POST["ny"]; 
		$id=$_POST["id"]; 
		$name=$_POST["name"]; 
		$cust=$_POST["cust"]; 
		$width=$_POST["width"]; 
		$height=$_POST["height"]; 
		$Ssel="S".$nx;	$Zsel="Z".$ny;$Psel="P".$papersize; $tmp=$metric;
		${$Ssel}=" selected";	${$Zsel}=" selected"; ${$Psel}=" selected"; ${$tmp}=" selected";
	}
	$menu =  $_SESSION['menu'];
?>
<html>
	<head>
		<title></title>
		   <?php echo $menu['stylesheets'];?>
           <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"]; ?>main.css">
           <script type="text/javascript" src="<?php echo $_SESSION['basepath']; ?>crm/jquery-ui/jquery.js"></script> 
           <?php echo $menu['javascripts'];?>

	</head>
<body>
<?php echo $menu['pre_content'];?>
<?php echo $menu['start_content'];?>
<p class=listtop>Etiketten-Editor</p>
<table><tr><td class="norm" style="width:280px">
<form name="defaufkleber" action="aufkleber_def.php" method="post">
<input type="hidden" name="id" value="<?php echo  $id ?>">
<input type="hidden" name="name" value="<?php echo  $name ?>">
<input type="hidden" name="cust" value="<?php echo  $cust ?>">
<table style="width:100%">
	<tr>
		<th colspan="4" class="listelement norm">Seitendefinition</th>
	</tr>
	<tr>
		<th colspan="4" class="listtop">Seitengr&ouml;&szlig;e</th>
	</tr>
	<tr>
		<td>Format</td>
		<td>
			<select name="papersize">
				<option value="A4"<?php echo  $PA4 ?>>A4
				<option value="A3"<?php echo  $PA3 ?>>A3
				<option value="A5"<?php echo  $PA5 ?>>A5
				<option value="letter"<?php echo  $Pletter ?>>Letter
				<option value="legal"<?php echo  $Plegal ?>>Legal
			</select>
		</td>
		<td>Metric</td>
		<td>
			<select name="metric">
				<option value="mm"<?php echo  $mm ?>>mm
				<option value="in"<?php echo  $in ?>>in
			</select>
		</td>
	</tr>
	<tr>
		<th colspan="4" class="listtop">Seitenr&auml;nder</th>
	</tr>
	<tr>
		<td>oben</td><td><input type="text" name="margintop" size="6" value="<?php echo  $margintop ?>"></td>
		<td>links</td><td><input type="text" name="marginleft" size="6" value="<?php echo   $marginleft ?>"></td>
	</tr>	
	<tr>
		<th colspan="4" class="listtop">Abst&auml;nde</th>
	</tr>
	<tr>
		<td>Spalten</td><td><input type="text" name="spacex" size="6" value="<?php echo  $spacex ?>"></td>
		<td>Zeilen</td><td><input type="text" name="spacey" size="6" value="<?php echo  $spacey ?>"></td>
	</tr>
	<tr>
		<th colspan="4" class="listtop">Gr&ouml;&szlig;e der Aufkleber</th>
	</tr>
	<tr>
		<td>Breite</td><td><input type="text" name="width" size="6" value="<?php echo  $width ?>"></td>
		<td>H&ouml;he</td><td><input type="text" name="height" size="6" value="<?php echo  $height ?>"></td>
	</tr>
	<tr>
		<th colspan="4" class="listtop">Anzahl der Aufkleber</th>
	</tr>
	<tr>
		<td>Spalten</td>
		<td><select name="nx">
			<option value="1"<?php echo  $S1 ?>>1
			<option value="2"<?php echo  $S2 ?>>2
			<option value="3"<?php echo  $S3 ?>>3
			<option value="4"<?php echo  $S4 ?>>4
			<option value="5"<?php echo  $S5 ?>>5
			<option value="6"<?php echo  $S6 ?>>6
			</select>
		</td>
		<td>Zeilen</td>
		<td><select name="ny">
			<option value="1"<?php echo  $Z1 ?>>1
			<option value="2"<?php echo  $Z2 ?>>2
			<option value="3"<?php echo  $Z3 ?>>3
			<option value="4"<?php echo  $Z4 ?>>4
			<option value="5"<?php echo  $Z5 ?>>5
			<option value="6"<?php echo  $Z6 ?>>6
			<option value="7"<?php echo  $Z7 ?>>7
			<option value="8"<?php echo  $Z8 ?>>8
			<option value="9"<?php echo  $Z9 ?>>9
			<option value="10"<?php echo  $Z10 ?>>10
			<option value="11"<?php echo  $Z11 ?>>11
			<option value="12"<?php echo  $Z12 ?>>12
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="3"></td><td valign="right"></td>
	</tr>
</table>
</td><td width="*">
<table style="width:100%">
	<tr>
		<th class="listelement norm">Aktionen</th>
	</tr>
	<tr><td class="listtop">gespeicherte Labels</td></tr>
	<tr><td class="ce">
			<select name="format">
				<option></option>
<?php
	foreach ($ALabels as $data) {
		echo "\t\t\t\t<option value='".$data["id"]."'";
		if ($data["id"]==$format) echo " selected";
		echo ">";
		if ($data["Cust"]) echo "C ";
		echo $data["name"]."\n";
	}
?>
			</select><input type="submit" name="hole" value="lade">
	</td></tr>
	<tr><td class="ce"><input type="text" name="custname" size="12"></td></tr>
	<tr><td class="ce"><input type="submit" name="csave" value="sichern als Neu"></td>	</tr>
	<tr><td class="ce"><br><input type="submit" name="test" value="testen"></td></tr>
	<tr><td class="ce"><br><input type="submit" name="more" value="mehr Textzeilen"></td></tr>
	<tr><td class="ce"><input type="submit" name="less" value="weniger Textzeilen"></td></tr>
	<tr><td class="ce"><br><input type="submit" name="ok" value="sichern"> </td></tr>
</table>
</td><td>
	<form name="adrtxt" method="post">
<table style="width:290px">
	<tr>
		<th colspan="3" class="listelement norm">Texte f&uuml;r  Aufkleber</th>
	</tr>
	<tr>
		<th colspan="2" class="listtop">Font</th>
		<th class="listtop">Text</th>
	</tr>
<?php	for ($i=0; $i<$Textzeilen;$i++) { ?>
		<tr><td width="50px"><select name="Schrift[]">
				<option value="6"<?php echo  $SG6[$i] ?>>6
				<option value="7"<?php echo  $SG7[$i] ?>>7
				<option value="8"<?php echo  $SG8[$i] ?>>8
				<option value="9"<?php echo  $SG9[$i] ?>>9
				<option value="10"<?php echo  $SG10[$i] ?>>10
				<option value="11"<?php echo  $SG11[$i] ?>>11
				<option value="12"<?php echo  $SG12[$i] ?>>12
				<option value="13"<?php echo  $SG13[$i] ?>>13
				<option value="14"<?php echo  $SG14[$i] ?>>14
				<option value="15"<?php echo  $SG15[$i] ?>>15
				<option value="16"<?php echo  $SG16[$i] ?>>16
			</select>
			</td>
			<td width="*" class="norm"><?php echo  $i ?></td><td><input type="text" name="Text[]" size="30" value="<?php echo  $Text[$i] ?>"></td></tr>
<?php } ?>
</table>
</td></tr>

</table>

</form>
<?=$menu['end_content'];?>
</body>
</html>
