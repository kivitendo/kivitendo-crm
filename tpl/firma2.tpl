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
			F1=open("showAdr.php?Q={Q}&pid="+pid+"{ep}","Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
		}
		function notes() {
			pid=document.contact.cp_id.value;
                        F1=open("showNote.php?Q={Q}&pid="+pid,"Notes","width=400, height=400, left=100, top=50, scrollbars=yes");
                }
		function vcard(){
			pid=document.contact.cp_id.value;
			document.location.href="vcardexp.php?Q={Q}&pid="+pid;
		}		
		function cedit(ed){				
			pid=false;
			if (ed)	pid=document.contact.cp_id.value;
			parent.main_window.location.href="personen3.php?id="+pid+"&edit="+ed+"&Quelle={Q}&fid={FID}";
		}
		function sellist(){				
			pid=document.contact.cp_id.value;
			parent.main_window.location.href="personen1.php?fid={FID}&Quelle={Q}";
		}
		function doclink(){				
			pid=document.contact.cp_id.value;
			parent.main_window.location.href="firma4.php?Q={Q}&fid={FID}&pid="+pid;
		}
		var start = 0;
		var max = 0;
		var y = 0;
		function showCall(dir) {
			if (dir<0) {
				if(start>19) { start-=19; }
				else { start=0; }; }
			else if (dir>0) {
				if ((start+19)<max) { start+=19; } 
				else if (max<19) { start=0; }
				else { start=max-19; }; 
			}
			xajax_showCalls(y,start);
		}
		function showOne(id) {
            		xajax_showContactadress(id);
			xajax_showCalls(id,0);
		}
		function showContact() {
			x=document.contact.liste.selectedIndex;
			y=document.contact.liste.options[x].value;
            		xajax_showContactadress(y);
			xajax_showCalls(y,0);
			setTimeout('showCall(0)',{interv});
		}
	//-->
	</script>
<body onLoad="{INIT}">
<p class="listtop">Detailansicht {FAART}</p>
<div style="position:absolute; top:3.3em; left:1.2em;  width:60em;">
	<ul id="maintab" class="shadetabs">
	<li><a href="{Link1}">Kundendaten</a><li>
	<li class="selected"><a href="{Link2}" id="aktuell">Ansprechpartner</a></li>
	<li><a href="{Link3}">Ums&auml;tze</a></li>
	<li><a href="javascript:doclink();">Dokumente</a></li>
	<span title="Wichtige MItteilung">{Cmsg}</span>
	</ul>
</div>

<span style="position:absolute; left:1em; top:5.7em; width:99%;">
<!-- Beginn Code ------------------------------------------->
<div style="float:left; width:53%; height:38em; text-align:center;  border: 1px solid black;">
	<div style="float:left; width:100%; height:4.5em; text-align:left; border-bottom: 1px solid black;" class="fett">
		<form name="contact">
		<input type="hidden" name="cp_id" id="cp_id" value="{PID}">
		<input type="hidden" name="Q" value="{Q}">
		&nbsp;{Fname1} &nbsp; &nbsp; KdNr.: {customernumber} &nbsp; &nbsp;
		<select name="liste" id="liste" style="visibility:{moreC}; width:150px;" onChange="showContact();">
		{kontakte}</select><br />
		&nbsp;{Fdepartment_1}<br /> 
		&nbsp;{Plz} {Ort}</form><br />
	</div>
	<div style="float:left; width:70%; height:14em; text-align:left; border-bottom: 1px solid black;" class="gross">
		&nbsp;<span id="cp_greeting"></span> <span id="cp_title"></span><br />
		&nbsp;<span id="cp_givenname"></span> <span id="cp_name"></span><br />
		&nbsp;<span id="cp_street"></span><br />
		<span class="mini">&nbsp;<br /></span>
		&nbsp;<span id="cp_country"></span><span id="cp_zipcode"></span> <span id="cp_city"></span><br />
		<span class="mini">&nbsp;<br /></span>
		<img src="image/telefon.gif" style="visibility:{none};"> <span id="cp_phone1"></span> <span id="cp_phone2"></span><br />
		&nbsp;<img src="image/mobile.gif" style="visibility:{none};"> <span id="cp_mobile1"></span> <span id="cp_mobile2"></span><br />
		<img src="image/fax.gif" style="visibility:{none};"> <span id="cp_fax"></span><br />
		&nbsp;<span id="cp_email"></span><br />
		&nbsp;<span id="cp_homepage"></span><br /><br />
	</div>
	<div style="float:left; width:30%; height:14em; text-align:right; border-bottom: 1px solid black;" class="gross">
		<a href="#" onCLick="anschr();"><img src="image/brief.png" border="0" style="visibility:{none};"></a><br />
		<span id="cp_grafik"></span></br >
		<span id="cp_birthday"></span></br />
		<span id="cp_position"></span><br />
		<span id="cp_abteilung"></span><br />
	</div>
	<div style="float:none; width:100%; height:32em; text-align:left; border-bottom: 1px solid black;" class="normal">
		&nbsp;<span id="cp_sonder"></span><br>
		&nbsp;<span id="cp_notes" class="klein"></span><br>
		<hr>
		&nbsp;<span id="cp_privatphone"></span> <span id="cp_privatemail"></span><br />
	</div><br>
	<div style="position:absolute; bottom:0.6em; width:49%; " class="normal">
	<!--div style="float:none; width:100%; he)ight:15px; text-align:center; border: 0px solid black;" class="normal"-->
		<!--[<a href="firma2.php?fid={PID}&ldap=1&Quelle=F">LDAP</a>]-->
		<span style="visibility:{none};">[<a href="javascript:vcard()">VCard</a>] </span> 
		<b>Kontakt:</b> [<a href="javascript:cedit(1);">{Edit}</a>] 
		[<a href="#" onClick="cedit(0);">eingeben</a>] [<a href="#" onClick="sellist();">aus Liste</a>]
	</div>
</div>
<span style="float:left; width:46%; height:38em; text-align:left; border: 1px solid black; border-left:0px;">
	<table class="calls" width='99%' id="calls">
	</table>
	<!--span style="float:left;  text-align:left; border:0px solid black"-->	
	<span style="position:absolute; bottom:0.9em; visibility:{none};">
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

<!-- End Code ------------------------------------------->
</span>
</body>
</html>
