<!-- $Id: firma4.tpl,v 1.4 2005/11/02 10:38:58 hli Exp $ -->
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
<table class="reiter">
	<tr>
		<td class="reiter desel">
			<a href="{Link1}" >Kundendaten</a>
		</td>
		<td class="reiter desel">
			<a href="{Link2}" >Kontakte</a>
		</td>
		<td class="reiter desel">
			<a href="{Link3}">Ums&auml;tze</a>
		</td>
		<td class="reiter sel">
			<a href="{Link4}" class="reiterA">Dokumente</a>
		</td>
	</tr>
</table>

<table width="99%" class="karte"><tr><td class="karte">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="firma4" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="pid" value="{PID}">
<input type="hidden" name="fid" value="{FID}">
<table><tr>
	<td width="290">
		<table class="stamm">
			<tr title="Firmenanschrift"><td class="smal bold">{Name}</td><td class="smal bold">{KDNR}</td></tr>
			<tr title="Firmenanschrift"><td class="smal bold">{Plz} {Ort}</td><td></td></tr>
		</table>
	</td>
	<td>&nbsp;</td>
	<td class="smal"> 
		<input type="file" name="Datei" size="40"><br>
		Ein neues Dokument speichern<br>
		<input type="text" name="caption" size="40"> <input type="submit" name="sichern" value="sichern"><br>
		Beschreibung
	</td>
</tr></table>
<table><tr>
<td width="290">
	
	<table class="liste"  width="290">
	<tr><td class="norm" width="290">Neues Dokument aus Vorlage:</td><td>&nbsp;</td></tr>
<!-- BEGIN Liste -->
	<tr  class="smal" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showD({ID});">
		<td class="smal" width="290">{Bezeichnung}</td><td>{Appl}</td>
	</tr>
<!-- END Liste -->
	</table>
	<br><br>
	<table>
	<tr><td class="norm" width="290">Wartungsvertr&auml;ge:</td><td>&nbsp;</td></tr>
<!-- BEGIN Vertrag -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showV({cid});">
		<td class="smal" width="290">{vertrag}</td>
	</tr>
<!-- END Vertrag -->
	</table>
</td><td width="*" class="norm">
	&nbsp;&nbsp;gespeicherte Dokumente:<br>&nbsp;
	<select name="dateien" size="16" style="width:450px" onClick="openfile()">
<!-- BEGIN Liste2 -->
	<option value="{val}">{key}
<!-- END Liste2 -->
	</select>
</td>
</tr></table>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
