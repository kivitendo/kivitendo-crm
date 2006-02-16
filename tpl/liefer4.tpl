<!-- $Id: liefer4.tpl,v 1.4 2005/12/01 08:14:26 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showD (id) {
		if (id>0) {
			Frame=eval("parent.main_window");
			uri="liefer4a.php?did=" + id + "&fid={FID}&pid={PID}";
			Frame.location.href=uri;
		}
	}
	function openfile() {
		sel=document.liefer4.dateien.selectedIndex;
		filename=document.liefer4.dateien.options[sel].value;
		open("dokumente/"+filename,"File");
	}
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<table class="reiter">
	<tr>
                <td class="reiter desel">
                        <a href="{Link1}" >Lieferantendaten</a>
                </td>
                <td class="reiter desel">
                        <a href="{Link2}">Kontakte</a>
                </td>
                <td class="reiter desel">
                        <a href="{Link3}" >Ums&auml;tze</a>
                </td>
                <td class="reiter sel">
                        <a href="{Link4}" class="reiterA">Dokumente</a>
                </td>
        </tr>

</table>

<table class="karte"><tr><td class="karte">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="liefer4" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="pid" value="{PID}">
<input type="hidden" name="fid" value="{FID}">
<table width="760"><tr>
<td width="290">
	<table class="stamm">
		<tr title="Lieferantenanschrift"><td class="smal bold">{Name}</td>
			<td class="smal re bold" title="Lieferantennummer">{LInr}</td></tr>
		<tr title="Lieferantenanschrift"><td class="smal bold">{Plz} {Ort}</td><td></td></tr>
		<tr><td class="smal bold">{Firma}</td><td></td></tr>
	</table>
</td><td class="smal"> 
	<input type="file" name="Datei" size="40"><br>
	Ein neues Dokument speichern<br>
	<input type="text" name="caption" size="40"> <input type="submit" name="sichern" value="sichern"><br>
	Beschreibung
</td></tr>
<tr><td class="smal">
Erzeugen aus Dokumentvorlage
	<table class="liste">
<!-- BEGIN Liste -->
		<tr  class="smal" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showD({ID});">
			<td>{Bezeichnung}</td><td>{Appl}</td>
		</tr>
<!-- END Liste -->
	</table>
</td><td class="smal">
	gespeicherte Dokumente<br>
	<select name="dateien" size="16" style="width:450px" onClick="openfile()">
<!-- BEGIN Liste2 -->
		<option value="{val}">{key}
<!-- END Liste2 -->
	</select>
<!--br><input type="button" value="&ouml;ffnen" onClick="openfile()"> <input type="button" value="l&ouml;schen"-->
</td></tr></table>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
