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
			F1=open("getCall.php?Q=CC&pid="+pid+"&Bezug="+id,"Caller","width=680, height=680, left=100, top=50, scrollbars=yes");
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
		function KdHelp() {
			id=document.kdhelp.kdhelp.options[document.kdhelp.kdhelp.selectedIndex].value;
			f1=open("wissen.php?kdhelp=1&m="+id,"Wissen","width=750, height=600, left=50, top=50, scrollbars=yes");
			document.kdhelp.kdhelp.selectedIndex=0;
		}
	var f1 = null;
	function toolwin(tool) {
		leftpos=Math.floor(screen.width/2);
		f1=open(tool,"Adresse","width=350, height=200, left="+leftpos+", top=50, status=no,toolbar=no,menubar=no,location=no,titlebar=no,scrollbars=no,fullscreen=no");
	}
	//-->
	</script>
<body onLoad="{INIT}">
<p class="listtop">Detailansicht {FAART} <span title="Wichtige Mitteilung">{Cmsg}</span></p>
<form name="kdhelp">
<div style="position:absolute; top:1.5em; left:1.1em;  width:60em;">
    <div style="float:left; padding-top:1.2em; ";>
	<ul id="maintab" class="shadetabs">
	<li><a href="{Link1}">Kundendaten</a><li>
	<li class="selected"><a href="{Link2}" id="aktuell">Ansprechpartner</a></li>
	<li><a href="{Link3}">Ums&auml;tze</a></li>
	<li><a href="javascript:doclink();">Dokumente</a></li>
	<li><select style="visibility:{chelp}" name="kdhelp" onChange="KdHelp()">
<!-- BEGIN kdhelp -->
		<option value="{cid}">{cname}</option>
<!-- END kdhelp -->
	</select>
	</ul>
    </div>
    <div style="float:left; padding-left:1em; ">
	<img src="tools/rechner.png"  onClick="toolwin('tools/Rechner.html')" title="einfacher Tischrechner"> &nbsp;
	<img src="tools/notiz.png"  onClick="toolwin('postit.php?popup=1')" title="Postit Notizen"> &nbsp;
	<img src="tools/kalender.png"  onClick="toolwin('tools/kalender.php')" title="Kalender"> &nbsp;
	<a href="javascript:void(s=prompt('Geben%20Sie%20einen%20Begriff%20zum%20&Uuml;bersetzen%20ein.',''));if(s)leow=open('http://dict.leo.org/?lp=ende&search='+escape(s),'LEODict','width=750,height=550,scrollbars=yes,resizeable=yes');if(leow)leow.focus();"><img src="tools/leo.png"  title="LEO Englisch/Deutsch" border="0"></a> &nbsp;
    </div>
</div>
</form>
<span style="position:absolute; left:1em; top:4.3em; width:99%;">
<!-- Beginn Code ------------------------------------------->
<div style="float:left; width:32em; height:32em;  border: 1px solid black;" >
     	<div style="position:absolute; left:0em; width:32em;" >	
		<div style="float:left; width:32em; height:4.0em; text-align:left; border-bottom: 1px solid black;">
			<span class="klein fett">
			<form name="contact">
			<input type="hidden" name="cp_id" id="cp_id" value="{PID}">
			<input type="hidden" name="Q" value="{Q}">
			&nbsp;{Fname1} &nbsp; &nbsp; KdNr.: {customernumber} &nbsp; &nbsp;
			<select name="liste" id="liste" style="visibility:{moreC}; width:150px;" onChange="showContact();">
			{kontakte}</select><br />
			&nbsp;{Fdepartment_1}<br /> 
			&nbsp;{Plz} {Ort}</form><br />
			</span>
		</div>
		<div style="float:left; width:70%; height:13em; text-align:left; border-bottom: 0px solid black;" >
			&nbsp;<span id="cp_greeting"></span> <span id="cp_title"></span><br />
			&nbsp;<span id="cp_givenname"></span> <span id="cp_name"></span><br />
			&nbsp;<span id="cp_street"></span><br />
			<span class="mini">&nbsp;<br /></span>
			&nbsp;<span id="cp_country"></span><span id="cp_zipcode"></span> <span id="cp_city"></span><br />
			<span class="mini">&nbsp;<br /></span>
			&nbsp;<img src="image/telefon.gif" style="visibility:{none};"> <span id="cp_phone1"></span> <span id="cp_phone2"></span><br />
			&nbsp;&nbsp;<img src="image/mobile.gif" style="visibility:{none};"> <span id="cp_mobile1"></span> <span id="cp_mobile2"></span><br />
			&nbsp;<img src="image/fax.gif" style="visibility:{none};"> <span id="cp_fax"></span><br />
			&nbsp;<span id="cp_email"></span><br />
			&nbsp;<span id="cp_homepage"></span><br /><br />
		</div>
		<div style="float:left; width:29%; height:13em; text-align:right; border-bottom: 0px solid black;" >
			<a href="#" onCLick="anschr();"><img src="image/brief.png" border="0" style="visibility:{none};"></a><br />
			<span id="cp_grafik" style="padding-right:1px;"></span></br >
			<span id="cp_birthday" style="padding-right:1px;"></span></br />
			<span id="cp_position" style="padding-right:1px;"></span><br />
			<span id="cp_abteilung" style="padding-right:1px;"></span><br />
			<span id="cp_vcard" style="padding-right:1px;"></span><br />
		</div>
    	<!--/div>
	<div style="position:absolute;top:21em; left:0em; width:38em;  text-align:left; border-bottom: 1px solid black;"-->
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<hr width="100%">
		&nbsp;<span id="cp_sonder"></span><br>
		&nbsp;<span id="cp_notes" class="klein" style="width:36em;"></span><br>
		<hr>
		&nbsp;<span id="cp_privatphone"></span> <span id="cp_privatemail"></span><br />
	</div>
	<div style="position:absolute; text-align:center; left:0em; bottom:0.6em; width:32em; ">
		<!--[<a href="firma2.php?fid={PID}&ldap=1&Quelle=F">LDAP</a>]-->
		<span style="visibility:{none};">[<a href="javascript:vcard()">VCard</a>] </span> 
		<b>Kontakt:</b> [<a href="javascript:cedit(1);">{Edit}</a>] 
		[<a href="#" onClick="cedit(0);">eingeben</a>] [<a href="#" onClick="sellist();">aus Liste</a>]
	</div>
</div>
<div style="float:left; width:45%; height:32em; text-align:left; border: 1px solid black; border-left:0px;" >
	<div class="calls" width='99%' id="tellcalls">
	</div>
	<!--span style="float:left;  text-align:left; border:0px solid black"-->	
	<span style="position:absolute; bottom:0.6em; visibility:{none};">
		<form name="ksearch"> &nbsp; 
		<img src="image/leftarrow.png" align="middle" border="0" title="zur&uuml;ck" onClick="showCall(-1);"> 
		<img src="image/reload.png" align="middle" border="0" title="reload" onClick="showCall(0);"> 
		<img src="image/rightarrow.png" align="middle" border="0" title="mehr" onClick="showCall(1);">&nbsp;
		<input type="text" name="suchwort" size="20">
		<input type="hidden" name="Q" value="{Q}">
		<input type="submit" src="image/suchen_kl.png" name="ok" value="suchen" align="middle" border="0"> 
		</form>
	</span>
</div>

<!-- End Code ------------------------------------------->
</span>
</body>
</html>
