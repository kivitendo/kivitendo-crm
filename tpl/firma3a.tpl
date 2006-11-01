<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>
	<script language="JavaScript">
	<!--
	function showP (id,nr) {
		if (id!='') {
			Frame=eval("parent.main_window");
			f1=open("rechng.php?id="+id+"&nr="+nr,"rechng","width=700,height=420,left=10,top=10,scrollbars=yes");
		}
	}
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<div style="position:absolute; top:44px; left:10px;  width:770px;">
	<ul id="maintab" class="shadetabs">
	<li><a href="{Link1}">Kundendaten</a><li>
	<li><a href="{Link2}">Ansprechpartner</a></li>
	<li class="selected"><a href="{Link3}" id="aktuell">Ums&auml;tze</a></li>
	<li><a href="{Link4}">Dokumente</a></li>
	</ul>
</div>

<span style="position:absolute; left:10px; top:67px; width:95%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<div style="position:absolute; left:0px; top:0px; width:450px; border:1px solid black" class="fett">
	{Name} &nbsp; {customernumber}<br />
	{Plz} {Ort}
</div>
<div style="position:absolute; left:1px; top:45px; width:450px;text-align:center;" class="normal">
Ums&auml;tze/Angebote von Monat {Monat}
	<table width="400px">
		<tr>
			<th class="smal" width="10%">Datum</th>
			<th class="smal">Art</th>
			<th class="smal">Nummer</th>
			<th class="smal">Netto</th>
			<th class="smal">Brutto</th>
			<th class="smal" width="10%"></th>
			<th class="smal">Staus</th>
		</tr>
<!-- BEGIN Liste -->
		<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showP('{Typ}{RNid}','{RNr}');">
			<td class="smal">{Datum}</td>
			<td class="smal">&nbsp;{Typ}</td>
			<td class="smal">&nbsp;{RNr}&nbsp;</td>
			<td class="smal re">{RSumme}&nbsp;&nbsp;</td>
			<td class="smal re">{RBrutto}&nbsp;</td>
			<td class="smal">{Curr}</td>
			<td class="smal">&nbsp;{offen}</td>
		</tr>
<!-- END Liste -->
		<tr><td class="smal" colspan="6"><b>R</b>echnung, <b>A</b>ngebot, <b>L</b>ieferung/Auftrag</td></tr>
		<tr><td class="smal" colspan="6"><b>o</b>ffen, <b>c</b>losed, <b>+</b>bezahlt, <b>-</b>unbezahlt</td></tr>
	</table>
</div>	
<!-- Hier endet die Karte ------------------------------------------->
</span>
</body>
</html>
