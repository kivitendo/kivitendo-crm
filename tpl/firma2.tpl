<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>
	{AJAXJS}
	<script language="JavaScript">
	<!--
		function showItem(id) {
			pid=document.contact.cp_id.value;
			F1=open("getCall.php?Q=CC&pid="+pid+"&Bezug="+id,"Caller","width=670, height=600, left=100, top=50, scrollbars=yes");
		}
		function anschr() {
			pid=document.contact.cp_id.value;
			F1=open("showAdr.php?pid="+pid+"{ep}","Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
		}
		function notes() {
			pid=document.contact.cp_id.value;
                        F1=open("showNote.php?pid="+pid,"Notes","width=400, height=400, left=100, top=50, scrollbars=yes");
                }
		function vcard(){
			pid=document.contact.cp_id.value;
			document.location.href="vcardexp.php?pid="+pid;
		}		
		function cedit(e){				
			pid=document.contact.cp_id.value;
			parent.main_window.location.href="personen3.php?id="+pid+"&edit="+e+"&Quelle=F";
		}
		function sellist(){				
			pid=document.contact.cp_id.value;
			parent.main_window.location.href="personen1.php?id="+pid+"&Quelle=F";
		}
		function doclink(){				
			pid=document.contact.cp_id.value;
			parent.main_window.location.href="firma4.php?fid={FID}&id="+pid;
		}
		var start = 0;
		var max = 0;
		function showCall(dir) {
			if (dir<0) {
				if(start>19) { start-=19; }
				else { start=0; }; }
			else if (dir>0) {
				if ((start+19)<max) { start+=19; } 
				else if (max<19) { start=0; }
				else { start=max-19; }; }
			xajax_showCalls(y,start);
		}
		function showContact() {
			x=document.contact.liste.selectedIndex;
			y=document.contact.liste.options[x].value;
                        xajax_showContactadress(y);
			xajax_showCalls(y,0);
		}
	//-->
	</script>
<body onLoad="{INIT}">
<p class="listtop">Detailansicht</p>
<div style="position:absolute; top:44px; left:10px;  width:770px;">
	<ul id="maintab" class="shadetabs">
	<li><a href="{Link1}">Kundendaten</a><li>
	<li class="selected"><a href="{Link2}" id="aktuell">Ansprechpartner</a></li>
	<li><a href="{Link3}">Ums&auml;tze</a></li>
	<li><a href="javascript:doclink();">Dokumente</a></li>
	<span title="Wichtige MItteilung">{Cmsg}</span>
	</ul>
</div>

<span style="position:absolute; left:10px; top:67px; width:99%;">
<!-- Beginn Code ------------------------------------------->
<div style="float:left; width:53%; height:450px; text-align:center; border: 1px solid black;">
	<div style="float:left; width:100%; height:55px; text-align:left; border-bottom: 1px solid black;" class="fett">
		<form name="contact">
		<input type="hidden" name="cp_id" id="cp_id" value="{PID}">
		{Fname1} &nbsp; &nbsp; {customernumber} &nbsp; &nbsp;
		<select name="liste" id="liste" style="visibility:{moreC}; width:150px;" onChange="showContact();">
		{kontakte}</select><br />
		{Fdepartment_1}<br /> 
		{Plz} {Ort}</form><br />
	</div>
	<div style="float:left; width:70%; height:210px; text-align:left; border-bottom: 1px solid black;" class="gross">
		<span id="cp_greeting">{Anrede}</span> <span id="cp_title">{Titel}</span><br />
		<span id="cp_givenname">{Vname}</span> <span id="cp_name">{Nname}</span><br />
		<span id="cp_street">{StreetC}</span><br />
		<span class="mini">&nbsp;<br /></span>
		<span id="cp_country">{LandC}</span><span id="cp_zipcode">{PlzC}</span> <span id="cp_city">{OrtC}</span><br />
		<span class="mini">&nbsp;<br /></span>
		<img src="image/telefon.gif" style="visibility:{none};"> <span id="cp_phone1">{Telefon}</span><br />
		&nbsp;<img src="image/mobile.gif" style="visibility:{none};"> <span id="cp_phone2">{Mobile}</span><br />
		<img src="image/fax.gif" style="visibility:{none};"> <span id="cp_fax">{Fax}</span><br />
		<span id="cp_email"><a href="mail.php?TO={eMail}&KontaktTO=K{PID}">{eMail}</a></span><br />
		<span id="cp_homepage"><a href="{www}" target="_blank">{www}</a></span><br /><br />
	</div>
	<div style="float:left; width:30%; height:210px; text-align:right; border-bottom: 1px solid black;" class="gross">
		<a href="#" onCLick="anschr();"><img src="image/brief.png" border="0" style="visibility:{none};"></a><br />
		<span id="cp_grafik">{IMG}</span></br >
		<span id="cp_birthday">{GDate}</span></br />
		<span id="cp_position">{Position}</span><br />
		<span id="cp_abteilung">{Abteilung}</span><br />
	</div>
	<div style="float:none; width:100%; height:385px; text-align:left; border-bottom: 1px solid black;" class="normal">
		<span id="cp_sonder">{Sonder}</span><br>
		<span id="cp_notes" class="klein">{Notiz}</span>
	</div><br>
	<div style="position:absolute; bottom:8px; width:49%; " class="normal">
	<!--div style="float:none; width:100%; he)ight:15px; text-align:center; border: 0px solid black;" class="normal"-->
		<!--[<a href="firma2.php?fid={PID}&ldap=1&Quelle=F">LDAP</a>]-->
		<span style="visibility:{none};">[<a href="javascript:vcard()">VCard</a>] </span> 
		<b>Kontakt:</b> [<a href="javascript:cedit(1);">{Edit}</a>] 
		[<a href="#" onClick="cedit(0);">eingeben</a>] [<a href="#" onClick="sellist();">aus Liste</a>]
	</div>
</div>
<span style="float:left; width:46%; height:450px; text-align:left; border: 1px solid black; border-left:0px;">
	<table class="calls" width='99%' id="calls">
	</table>
	<!--span style="float:left;  text-align:left; border:0px solid black"-->	
	<span style="position:absolute; bottom:10px; visibility:{none};">
		<form name="ksearch"> &nbsp; 
		<img src="image/leftarrow.png" align="middle" border="0" title="zur&uuml;ck" onClick="showCall(-1);"> 
		<img src="image/reload.png" align="middle" border="0" title="reload" onClick="showCall(0);"> 
		<img src="image/rightarrow.png" align="middle" border="0" title="mehr" onClick="showCall(1);">&nbsp;
		<input type="text" name="suchwort" size="20">
		<input type="submit" src="image/suchen_kl.png" name="ok" value="suchen" align="middle" border="0"> 
		</form>
	</span>
</span>

<!-- End Code ------------------------------------------->
</span>
</body>
</html>
