<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showD (id) {
		if (id>0) {
			Frame=eval("parent.main_window");
			uri="firma4a.php?did=" + id + "&fid={FID}&pid={PID}";
			Frame.location.href=uri;
		}
	}
	function showV (nr) {
		if (nr>0) {
			Frame=eval("parent.main_window");
			uri="vertrag3.php?vid=" + nr;
			Frame.location.href=uri;
		}
	}	
	function openfile() {
		sel=document.firma4.dateien.selectedIndex;
		filename=document.firma4.dateien.options[sel].value;
		open("dokumente/"+filename,"File");
	}
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<div style="position:absolute; top:33px; left:8px;  width:770px;">
	<ul id="tabmenue">
	<li><a href="{Link1}">Kundendaten</a><li>
	<li><a href="{Link2}">Kontakte</a></li>
	<li><a href="{Link3}">Ums&auml;tze</a></li>
	<li><a href="{Link4}" id="aktuell">Dokumente</a></li>
	</ul>
</div>

<span style="position:absolute; left:10px; top:67px; width:99%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="firma4" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="pid" value="{PID}">
<input type="hidden" name="fid" value="{FID}">
<span style="float:left; width:43%; height:410px; text-align:center; padding:2px; border: 1px solid black;">
	<div style="float:left; width:100%; height:50px; text-align:left; border-bottom: 1px solid black;" class="fett">
		{Name} &nbsp; &nbsp; {customernumber}<br />
		{Plz} {Ort}<br />
	</div>
	<div style="float:left; width:100%; height:50px; text-align:left; border-bottom: 0px solid black;" class="normal">
	<table class="liste" width="300">
	<tr><td class="norm" width="260">Neues Dokument aus Vorlage:</td><td>&nbsp;</td></tr>
<!-- BEGIN Liste -->
	<tr  class="smal" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showD({ID});">
		<td class="smal">{Bezeichnung}</td><td>{Appl}</td>
	</tr>
<!-- END Liste -->
	</table>
	<br><br>
	<table>
	<tr><td class="norm" width="320">Wartungsvertr&auml;ge:</td></tr>
<!-- BEGIN Vertrag -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showV({cid});">
		<td class="smal">{vertrag}</td>
	</tr>
<!-- END Vertrag -->
	</table>
	</div>
</span>
<span style="float:left;  height:410px; text-align:left; border: 1px solid black; padding:2px; border-left:0px;">
	<input type="file" name="Datei" size="30"><br />
	Ein neues Dokument speichern<br />
	<input type="text" name="caption" size="30"> <input type="submit" name="sichern" value="sichern"><br />
	Beschreibung<br /><br />
	gespeicherte Dokumente:<br>
	<select name="dateien" size="16" style="width:445px" onClick="openfile()">
<!-- BEGIN Liste2 -->
	<option value="{val}">{key}
<!-- END Liste2 -->
	</select>
</span>
	
<!-- Hier endet die Karte ------------------------------------------->
</span>
</body>
</html>
