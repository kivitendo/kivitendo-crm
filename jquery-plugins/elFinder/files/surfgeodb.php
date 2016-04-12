<?php require("inc/stdLib.php"); ?>
<html>
<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	function getort(ort) {
		document.location.href="surfgeodb.php?loc_id="+ort;
	}
	</script>
</head>
<body>
<?php
class MyGeo {
	var $loc_id=0;
	var $Teilvon=false;
	var $Ebene=0;
	var $Type=false;
	var $Name='';
	var $Plz='';
	var $Vorwahl='';
	var $Kfz='';
	var $Verw='';
	var $GemSchl='';
	var $Einwohner=0;
	var $Flaeche=0;
	var $Lat=false;
	var $Lon=false;
	var $db=false;
	var $cntgemeinden=0;

	function clean() {
		$this->loc_id=0;     	
                $this->Teilvon=false;
		$this->Ebene=0;
                $this->Type=false;
		$this->Plz='';
		$this->Vorwahl='';
                $this->Name='';
                $this->Kfz='';
		$this->Verw='';
		$this->GemSchl='';
                $this->Einwohner=0;
                $this->Flaeche=0;
                $this->Lat=false;
	        $this->Lon=false;
		$this->cntgemeinden=0;
	}
	function getData($loc_id) {
		$sql = 'SELECT ta.loc_id,ta.text_type,tn.name,ta.text_val,ta.text_locale from  ';
		$sql.= 'geodb_textdata ta left join geodb_type_names tn on tn.type_id=ta.text_type ';
		$sql.= 'where loc_id ='.$loc_id;
		$sql.= ' union all ';
		$sql.= 'SELECT loc_id,float_type,tn.name,cast(float_val as text),\'\' from ';
		$sql.= 'geodb_floatdata left join geodb_type_names tn on tn.type_id=float_type ';
		$sql.= 'where loc_id ='.$loc_id;
		$sql.= ' union all ';
		$sql.= 'SELECT loc_id,int_type,tn.name,cast(int_val as text),\'\' from ';
		$sql.= 'geodb_intdata left join geodb_type_names tn on tn.type_id=int_type ';
		$sql.= 'where loc_id ='.$loc_id;
		$sql.= ' union all ';
		$sql.= 'SELECT loc_id,coord_type,textcat(tn.name,\' lat\'),cast(lat as text),\'\' from ';
		$sql.= 'geodb_coordinates left join geodb_type_names tn on tn.type_id=coord_type ';
		$sql.= 'where loc_id ='.$loc_id;
		$sql.= ' union all ';
		$sql.= 'SELECT loc_id,coord_type,textcat(tn.name,\' lon\'),cast(lon as text),\'\' from ';
		$sql.= 'geodb_coordinates left join geodb_type_names tn on tn.type_id=coord_type ';
		$sql.= 'where loc_id ='.$loc_id;
		$rsort=$this->db->getAll($sql);
		$this->clean();
		$this->loc_id=$loc_id;
		if ($rsort) foreach ($rsort as $row) {
			switch ($row['text_type']) {
				case 400100000 : $this->Teilvon=$row['text_val']; break;
				case 400200000 : $this->Ebene=$row['text_val'];   break;
				case 400300000 : $this->Type=$row['text_val'];    break;
				case 500100000 : if (empty($this->Name)) { $this->Name=$row['text_val']; }
						 else if ($row['text_locale']=='de') {
							$this->Name=$row['text_val'];   
						 }
						 break;
				case 500300000 : $this->Plz=(($this->plz)?", ":"").$row['text_val'];    break;
				case 500400000 : $this->Vorwahl=$row['text_val'];    break;
				case 500500000 : $this->Kfz=$row['text_val'];    break;
				case 500600000 : $this->GemSchl=$row['text_val'];    break;
				case 500700000 : $this->Verw=$row['text_val'];    break;
				case 600700000 : $this->Einwohner=$row['text_val'];    break;
				case 610000000 : $this->Flaeche=$row['text_val'];    break;
				case 200100000 : if (substr($row["name"],-3)=='lat') {
							$this->Lat=$row['text_val'];   
						 } else {
							$this->Lon=$row['text_val'];   
						 }
						 break;
			}
		}
		$sql='SELECT count(*) as cnt from geodb_textdata where text_val=\''.$this->loc_id.'\' and text_type = 400100000';
		$rsort=$this->db->getAll($sql);
		$this->cntgemeinden=$rsort[0]['cnt'];
	}
	function MyGeo($db) {
		$this->db=$db;
	}
	function suchOrt($ort,$plz,$tel,$fuzzy=0,$ao="or") {
		$sql= 'select loc_id,text_type,name,text_val from  geodb_textdata ';
		$sql.='ta left join geodb_type_names tn on tn.type_id=ta.text_type where loc_id in (';
		$subsel='SELECT loc_id from  geodb_textdata where ';
		$where='';
		$type=array();
		if ($fuzzy==1) { $f1='like'; $f2='%'; }
		else { $f1='='; $f2=''; }
		if ($ao == "and") {
			if ($ort<>"") {	$where=$subsel.' UPPER(text_val) '.$f1.' \''.strtoupper($ort).$f2.'\' and text_type = \'500100000\' '; }
			if ($plz<>"") {	$where.=((empty($where))?'':'INTERSECT ').$subsel.' text_val '.$f1.' \''.$plz.$f2.'\' and text_type = \'500300000\'  '; }
			if ($tel<>"") {	$where.=((empty($where))?'':'INTERSECT ').$subsel.' text_val '.$f1.' \''.$tel.$f2.'\' and text_type = \'500400000\'  ';	}
		} else {
			if ($ort<>"") {	$where=$subsel.'( UPPER(text_val) '.$f1.' \''.strtoupper($ort).$f2.'\' and text_type = \'500100000\' ) '; }
			if ($plz<>"") {	$where.=((empty($where))?$subsel:' OR ').'( text_val '.$f1.' \''.$plz.$f2.'\' and text_type = \'500300000\' ) '; }
			if ($tel<>"") {	$where.=((empty($where))?subsel:' OR ').'( text_val '.$f1.' \''.$tel.$f2.'\' and text_type = \'500400000\' ) ';	}
		}
		$sql.=$where.') order by loc_id,text_type';
		$rs=$this->db->getAll($sql);
		return $rs;
	}
	function suchGemeinden($id) {
		$sql= 'select loc_id,text_type,name,text_val from  geodb_textdata ';
		$sql.='ta left join geodb_type_names tn on tn.type_id=ta.text_type where ';
		$sql.='loc_id in (SELECT loc_id from  geodb_textdata where text_val = \''.$id.'\' ';
		$sql.='and text_type = 400100000 ) and text_type in (500100000,500300000)';
		$sql.='order by loc_id,text_type';
		$rs=$this->db->getAll($sql);
		return $rs;
	}
}
function ebene($nr) {
	for ($i=1; $i<$nr; $i++) {
		$str.='-';
	}
	return $str;
}
echo "<center>Surfen durch die Geodaten<br>\n";
echo "[n] = weiterf&uuml;hrende Links<br>\n";
echo "<table border=0>\n";
$geo=new MyGeo($db);
if ($_GET["loc_id"]) {
	$geo->getData($_GET['loc_id']);
	echo "<tr><td><b>".$geo->Plz.' '.$geo->Name."</b></td><td>".$geo->Type."</td></tr>";
	echo "<tr class='mini'><td>Bezeichnung</td><td>Type</td></tr>";
	echo '<tr><td>'.$geo->Vorwahl.' </td><td>'.$geo->Kfz.'</td></tr>';
	echo "<tr class='mini'><td>Vorwahl</td><td>KFZ</td></tr>";
	echo '<tr><td>'.$geo->Einwohner.' </td><td>'.$geo->Flaeche.'</td></tr>';
	echo "<tr class='mini'><td>Einwohner</td><td>Fl&auml;che qkm</td></tr>";
	if ($geo->Lat) {
  	    echo '<tr><td>'.(($geo->Lat>0)?'N':'S').$geo->Lat.' '.(($geo->Lon>0)?'E':'W').$geo->Lon.'</td><td>'.$geo->Verw.'</td></tr>';
	    echo "<tr class='mini'><td>GEO-Koordinaten</b></td><td>Verwaltung</td></tr>";
	}
	if ($geo->cntgemeinden>0) {
	    echo '<tr><td><a href="surfgeodb.php?gemid='.$geo->loc_id.'">['.$geo->cntgemeinden.']</a></td><td></td></tr>';
	    echo "<tr class='mini'><td>Zugeh&ouml;rige Gemeinden</td><td></td></tr>";};
	echo "<tr><td></td><td></td></tr>";
	echo "<tr class='mini'><td>&Uuml;bergeordnet:</td><td>Type:</td></tr>";
	while ($geo->Ebene>1)	{
		$geo->getData($geo->Teilvon);
		echo '<tr onclick="getort('.$geo->loc_id.')"><td>'.ebene($geo->Ebene).$geo->Name.'</td>';
		echo '<td>'.$geo->Type.' <a href="surfgeodb.php?gemid='.$geo->loc_id.'">['.$geo->cntgemeinden.']</a></td></tr>'."\n";
	}
} else {
	$lastid=0;$lasttype='';
	if ($_GET['gemid']>0) {
		$rs=$geo->suchGemeinden($_GET['gemid']);
	} else {
		$rs=$geo->suchOrt($_GET["ort"],$_GET["plz"],$_GET["tel"],$_GET["fuzzy"],$_GET["ao"]);
	}
	if ($rs) foreach($rs as $row) {
		if ($lastid<>$row["loc_id"]) {
			if ($lastid<>0); '</td></tr>'."\n";
			$lastid=$row["loc_id"];
			if ($lasttype<>$row["name"]) { 
				$type="<span class='lg'>".$row["name"].":</span>"; $lasttype=$row["name"];}
			else { $type=''; }
			echo '<tr class="ptr" onclick="getort('.$row['loc_id'].')"><td>'.$type.$row['text_val'].' ';
		} else {
			if ($lasttype<>$row["name"]) { 
				$type="<span class='lg'>".$row["name"].":</span>"; $lasttype=$row["name"];}
			else { $type=''; }
			echo $type.$row['text_val'].' ';
		};
	}
}
?>
</table>
[<a href="JavaScript:self.close()">Fenster schlie&szlig;en</a>]
</body>
</html>
