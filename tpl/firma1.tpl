<!-- $Id$ -->
<html xmlns="http://www.w3.org/1999/xhtml">
	<head><title>Firma Stamm</title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>
	{AJAXJS}
	<script language="JavaScript" type="text/javascript">
	<!--
		var start = 0;
		var max = 0;
		function showCall(dir) {
			if (dir<0) {
				if(start>19) { start-=19; }
				else { start=0; }; }
			else if (dir>0) {
				if ((start+19)<max) { start+=19; } 
				else if (max<19) { start=0; }
				else { start=max-19; }; 
			}
			xajax_showCalls({FID},start,1);
			setTimeout('showCall(0)',{interv});
		}
		function showItem(id) {
			F1=open("getCall.php?Q=C&fid={FID}&Bezug="+id,"Caller","width=610, height=600, left=100, top=50, scrollbars=yes");
		}
		function anschr(A) {
			if (A==1) {
				F1=open("showAdr.php?fid={FID}","Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
			} else {
				F1=open("showAdr.php?sid={FID}","Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
			}
		}
		function notes() {
                                F1=open("showNote.php?fid={FID}","Notes","width=400, height=400, left=100, top=50, scrollbars=yes");
                }
		function vcard(){
			document.location.href="vcardexp.php?fid={FID}";
		}
		function ks() {
			sw=document.ksearch.suchwort.value;
			if (sw != "") 
				F1=open("suchKontakt.php?suchwort="+sw+"&Q=C&id={FID}","Suche","width=400, height=400, left=100, top=50, scrollbars=yes");
			return false;
		}
		var last = 'lie';
		function submenu(id) {
			document.getElementById(last).style.visibility='hidden';
			document.getElementById(id).style.visibility='visible';
			men='sub' + id; 
			document.getElementById('sub'+id).className="selected";
			document.getElementById('sub'+last).className="subshadetabs";
			last=id;
		}
		function KdHelp() {
			id=document.kdhelp.kdhelp.options[document.kdhelp.kdhelp.selectedIndex].value;
			f1=open("wissen.php?kdhelp=1&m="+id,"Wissen","width=750, height=600, left=50, top=50, scrollbars=yes");
			document.kdhelp.kdhelp.selectedIndex=0;
		}
		var shiptoids = new Array({Sids});
		var sil = shiptoids.length;
		var sid = 0;
		function nextshipto(dir) {
			if (sil<2) return;
			if (dir=="-") {
				if (sid>0) {
					sid--;
				} else {
					sid = (sil - 1);
				}
			} else {
				if (sid < sil - 1) { 
					sid++;
				} else {
					sid=0; 
				}
			}
			xajax_showShipadress(shiptoids[sid],"{Q}");
		}
	var f1 = null;
	function toolwin(tool) {
		leftpos=Math.floor(screen.width/2);
		f1=open(tool,'LxO-Tool','height=250,width=350,left='+leftpos+',top=50,status=no,toolbar=no,menubar=no,location=no,titlebar=no,scrollbars=no,fullscreen=no;')
	}
	//-->
	</script>
	</head>
<body onLoad="submenu('{kdview}'); showCall(0);">
<p class="listtop">Detailansicht {FAART} <span title="Wichtige Mitteilung">{Cmsg}&nbsp;</span></p>
<form name="kdhelp">
<div style="position:absolute; top:3.3em; left:1.2em;  width:50em;">
	<ul id="maintab" class="shadetabs">
	<li class="selected"><a href="firma1.php?Q={Q}&id={FID}" id="aktuell">Stammdaten</a></li>
	<li><a href="firma2.php?Q={Q}&fid={FID}">Ansprechpartner</a></li>
	<li><a href="firma3.php?Q={Q}&fid={FID}">Ums&auml;tze</a></li>
	<li><a href="firma4.php?Q={Q}&fid={FID}">Dokumente</a></li>
	<li><select style="visibility:{chelp}" name="kdhelp" onChange="KdHelp()">
<!-- BEGIN kdhelp -->
		<option value="{cid}">{cname}</option>
<!-- END kdhelp -->
	</select>
	</ul>
</div>
<div style="position:absolute; top:1.3em; left:51em;  width:30em;">
	<img src="tools/rechner.png"  onClick="toolwin('tools/Rechner.html')" title="einfacher Tischrechner"> &nbsp;
	<img src="tools/notiz.png"  onClick="toolwin('postit.php?popup=1')" title="Postit Notizen"> &nbsp;
	<img src="tools/kalender.png"  onClick="toolwin('tools/kalender.php')" title="Kalender"> &nbsp;
	<a href="javascript:void(s=prompt('Geben%20Sie%20einen%20Begriff%20zum%20&Uuml;bersetzen%20ein.',''));if(s)leow=open('http://dict.leo.org/?lp=ende&search='+escape(s),'LEODict','width=750,height=550,scrollbars=yes,resizeable=yes');if(leow)leow.focus();"><img src="tools/leo.png"  title="LEO Englisch/Deutsch" border="0"></a> &nbsp;
</div>
</form>

<span style="position:absolute; left:1em; top:5.7em; width:99%;">
<!-- Begin Code --------------------------------------------- -->
<span style="float:left; width:46em; height:38em; text-align:center; border: 1px solid black;">
	<div style="position:absolute; width:46em;">
		<div style="float:left; width:64%; height:14.0em; text-align:left; border-bottom: 0px solid black; padding:2px;" class="gross">
			{Fname1}<br />
			{Fdepartment_1}<br />
			{Fdepartment_2}
			{Strasse}<br />
			<span class="mini">&nbsp;<br /></span>
			{Land}-{Plz} {Ort}<br />
			<span class="smal">{Bundesland}<br /></span>
			{Fcontact}<br />
			<font color="#444444">Tel:</font> {Telefon}	&nbsp;&nbsp;&nbsp;	<font color="#444444">Fax:</font> {Fax}<br />	
			[<a href="mail.php?TO={eMail}&KontaktTO=C{FID}">&nbsp;{eMail}</a>]<br />
			<a href="{Internet}" target="_blank">&nbsp;{Internet}</a>
		</div>
		<div style="float:left; width:33%; height:14.0em; text-align:right; border-bottom: 0px solid black; padding:2px;" class="gross">
			{customernumber}<br />
			{IMG}<br /><br />
				<form action="../oe.pl" method="post">
				<input type="hidden" name="path" value="bin/mozilla">
				<input type="hidden" name="login" value="{login}">
				<input type="hidden" name="action" value="add">
				<input type="hidden" name="type" value="sales_order">
				<input type="hidden" name="password" value="{password}">
				<input type="hidden" name="customer_id" value="{FID}">
				<input type="image" src="image/auftrag.png" value="Auftrag" title="neuen Auftrag eingeben" style="visibility:{zeige};">
				<input type="hidden" name="type" value="sales_quotation">
				<input type="image" src="image/angebot.png" value="Angebot" title="neues Angebot erstellen" style="visibility:{zeige};">

				<img src="image/kreuzchen.gif" title="Gesperrter Kunde"style="visibility:{verstecke};" >
				&nbsp;
				<a href="#" onCLick="anschr(1);" title="Briefanschrift &amp; Etikett"><img src="image/brief.png" alt="Etikett drucken" border="0" /></a><br>
				&nbsp;<br>
				<a href="extrafelder.php?owner={Q}{FID}" target="_blank" title="Extra Daten" style="visibility:{zeigeextra};"><img src="image/extra.png" alt="Extras" border="0" /></a>
				&nbsp;<br><br>
				<span style="visibility:{zeigeplan};"><a href="{KARTE}" target="_blank"><img src="image/karte.gif" title="Ortsplan" border="0"></a></span>&nbsp;</form>

		</div>
	</div>
	<div style="position:absolute; width:45.5em; height:1.4em; text-align:left; padding-left:0.5em; border-top: 1px solid black;left:0px; top:19em;">
		<ul id="submenu" class="subshadetabs">
			<li id="sublie"><a href="#" onClick="submenu('lie')">Lieferadresse</a></li>
			<li id="subnot"><a href="#" onClick="submenu('not')">Notizen</a></li>
			<li id="subinf"><a href="#" onClick="submenu('inf')">sonst.Infos</a></li>
			<li><a href="vcardexp.php?Q={Q}&fid={FID}">VCard</a></li>
			<li><a href="karte.php?Q={Q}&fid={FID}">Kartei</a></li>
			<li><a href="firmen3.php?Q={Q}&id={FID}&edit=1">Bearbeiten</a></li>
		</ul>
	</div>

	<span id="lie" style="visibility:visible; position:absolute; text-align:left;width:48%; left:1.2em; top:22.0em;" >
		<div class="smal" >
		<span id="shiptoname">{Sname1}</span> &nbsp;&nbsp;<a href="#" onCLick="anschr(2);"><img src="image/brief.png" alt="Etikett drucken" border="0" /></a>&nbsp; &nbsp; 
		Anzahl Anschriften:{Scnt} <a href="javascript:nextshipto('-');"><img src="image/leftarrow.png" border="0"></a> 
		<span id="SID">{Sshipto_id}</span> <a href="javascript:nextshipto('+');"><img src="image/rightarrow.png" border="0"></a><br />
		<span id="shiptodepartment_1">{Sdepartment_1}</span> &nbsp; &nbsp; <span id="shiptodepartment_2">{Sdepartment_2}</span> <br />
		<span id="shiptostreet">{SStrasse}</span><br />
		<span class="mini">&nbsp;<br /></span>
		<span id="shiptocountry">{SLand}</span>-<span id="SPlz">{SPlz}</span> <span id="shiptoSOrt">{SOrt}</span><br />
		<span id="shiptobland">{SBundesland}</span><br />
		<span class="mini">&nbsp;<br /></span>
		<span id="shiptocontact">{Scontact}</span><br />
		Tel: <span id="shiptophone">{STelefon}</span><br />
		Fax: <span id="shiptofax">{SFax}</span><br />
		<span id="shiptoemail"><a href="mail.php?TO={SeMail}&KontaktTO=C{FID}">{SeMail}</a></span>
		</div>
	</span>

	<span id="not" style="visibility:hidden;position:absolute;  text-align:left;width:47%; left:1.2em; top:22.0em;">
		<div class="smal" >
		Checkbox: <span class="value">{sonder}</span><br />
		Branche: <span class="value">{branche}</span><br />
		Stichworte: <span class="value">{sw}</span><br />
		Bemerkungen: <span class="value">{notiz}</span> <br />	
		</div>
	</span>	

	<span id="inf" style="visibility:hidden;position:absolute; text-align:left;width:48%; left:1.2em; top:22.0em;">
		<div class="smal" >
		Kundentyp: <span class="value">{kdtyp}</span> &nbsp;&nbsp;&nbsp; Quelle:<span class="value">{lead} {leadsrc}</span><br />
		Rabatt: <span class="value">{rabatt}</span> &nbsp;&nbsp;&nbsp; Preisgruppe: <span class="value">{preisgrp}</span><br /><br />
		Erstelldatum: <span class="value">{erstellt}</span> &nbsp;&nbsp;&nbsp; Ge&auml;ndert:<span class="value">{modify}</span <br />
		Steuer-Nr.: <span class="value">{Taxnumber}</span> &nbsp;&nbsp;&nbsp; UStId: <span class="value">{USTID}</span><br />
		Steuerzone: <span class="value">{Steuerzone}</span><br /><br />
		Zahlungsziel: <span class="value">{terms}</span> Tage &nbsp;&nbsp;&nbsp;Kreditlimit: <span class="value">{kreditlim}</span><br />
		Offene Posten: <span class="value">{op}</span> &nbsp;&nbsp;&nbsp;offene Auftr&auml;ge: <span class="value">{oa}</span><br /><br />
		Bankname: <span class="value">{bank}</span><br />
		Blz: <span class="value">{blz}</span> &nbsp;&nbsp;&nbsp; Konto: <span class="value">{konto}</span>
		</div>
	</span>
</span>

<span style="float:left; width:46%; height:38em; text-align:left; border: 1px solid black; border-left:0px;">
	<table class="calls" width='99%' id="calls">
	</table>
	<!--span style="float:left;  text-align:left; border:0px solid black"-->	
	<span style="position:absolute; bottom:10px; visibility:{none};">
		<form name="ksearch"> &nbsp; 
		<img src="image/leftarrow.png" align="middle" border="0" title="zur&uuml;ck" onClick="showCall(-1);"> 
		<img src="image/reload.png" align="middle" border="0" title="reload" onClick="showCall(0);"> 
		<img src="image/rightarrow.png" align="middle" border="0" title="mehr" onClick="showCall(1);">&nbsp;
		<input type="text" name="suchwort" size="20">
		<input type="hidden" name="Q" value="{Q}">
		<input type="submit" src="image/suchen_kl.png" name="ok" value="suchen" align="middle" border="0"> 
		</form>
	</span>

</span>
<!-- End Code --------------------------------------------- -->
</span>
</body>
</html>

