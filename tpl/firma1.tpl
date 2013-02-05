<html>
        <head><title></title>
        {STYLESHEETS}
    <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
    <link rel="stylesheet" type="text/css" href="{JQUERY}/jquery-ui/themes/base/jquery-ui.css">
    <script type="text/javascript" src="{JQUERY}jquery-ui/jquery.js"></script>
    <script type="text/javascript" src="{JQUERY}jquery-ui/ui/jquery-ui.js"></script>
    
    {JAVASCRIPTS}
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
        };
        $.ajax({
           url: "jqhelp/firmaserver.php?task=showCalls&id={FID}&start="+start,
           dataType: 'json',
           success: function(data){
                        $('#tellcalls').empty();
                        var content = '<table class="calls" width="99%">';
                        var lc = 0;
                        $.each(data.items, function(i) {
                             content += '<tr class="calls'+lc+'" onClick="showItem('+data.items[i].id+');">'
                             content += '<td>' + data.items[i].calldate + '</td>';
                             content += '<td>' + data.items[i].id;
                             if (data.items[i].inout == 'o') {
                                  content += ' &gt;</td>';
                             } else if (data.items[i].inout == 'i') {
                                  content += ' &lt;</td>';
                             } else {
                                  content += ' -</td>';
                             }
                             if ( data.items[i].new == 1 ) {
                                 content += '<td><b>' + data.items[i].cause + '</b></td>';
                            } else {
                                 content += '<td>' + data.items[i].cause + '</td>';
                            }
                            content += '<td>' + data.items[i].cp_name + '</td></tr>';
                            lc = ( lc==0 )?1:0;
                        })
                        content += "</table>"
                        $('#tellcalls').append(content);
                        if (data.max>0) max = data.max
                    }
           });
        setTimeout('showCall(0)',{interv});
    }
    function showItem(id) {
        F1=open("getCall.php?Q={Q}&fid={FID}&Bezug="+id,"Caller","width=770, height=680, left=100, top=50, scrollbars=yes");
    }
    function anschr(A) {
        if (A==1) {
            F1=open("showAdr.php?Q={Q}&fid={FID}","Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
        } else {
            sid = document.getElementById('SID').firstChild.nodeValue;
	    if ( sid ) 
                F1=open("showAdr.php?Q={Q}&sid="+sid,"Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
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
        function doLink() {
            if ( document.getElementById('actionmenu').selectedIndex > 0 ) {
                lnk = document.getElementById('actionmenu').options[document.getElementById('actionmenu').selectedIndex].value;
                window.location.href = lnk;
            }
        }
    var last = 'lie';
    function submenu(id) {
        document.getElementById(last).style.visibility='hidden';
        document.getElementById(id).style.visibility='visible';
        men='sub' + id; 
        //document.getElementById('sub'+last).className="subshadetabs";
        document.getElementById('sub'+last).className="";
        document.getElementById('sub'+id).className="selected";
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
        if ( dir == 'o' ) {
	    sid = 0;
	} else {
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
	}
        $.ajax({
           url: "jqhelp/firmaserver.php?task=showShipadress&id="+shiptoids[sid]+"&Q={Q}",
           dataType: 'json',
           success: function(data){
                        var adr = data.adr;
                        $('#SID').empty().append(adr.shipto_id);
                        $('#shiptoname').empty().append(adr.shiptoname);
                        $('#shiptodepartment_1').empty().append(adr.shiptodepartment_1);
                        $('#shiptodepartment_2').empty().append(adr.shiptodepartment_2);
                        $('#shiptostreet').empty().append(adr.shiptostreet);
                        $('#shiptocountry').empty().append(adr.shiptocountry);
                        $('#shiptobland').empty().append(adr.shiptobland);
                        $('#shiptozipcode').empty().append(adr.shiptozipcode);
                        $('#shiptocity').empty().append(adr.shiptocity);
                        $('#shiptocontact').empty().append(adr.shiptocontact);
                        $('#shiptophone').empty().append(adr.shiptophone);
                        $('#shiptofax').empty().append(adr.shiptofax);
                        $('#shiptoemail').empty().append(data.mail);
                        $('a#karte2').attr("href",data.karte);
                    }
           })
    }
    var f1 = null;
    function toolwin(tool) {
        leftpos=Math.floor(screen.width/2);
        f1=open(tool,"Adresse","width=350, height=200, left="+leftpos+", top=50, status=no,toolbar=no,menubar=no,location=no,titlebar=no,scrollbars=yes,fullscreen=no");
    }
    function showOP(was) {
                F1=open("op_.php?Q={Q}&fa={Fname1}&op="+was,"OP","width=950, height=450, left=100, top=50, scrollbars=yes");
        }
    function surfgeo() {
        if ({GEODB}) {
            F1=open("surfgeodb.php?plz={Plz}&ort={Ort}","GEO","width=550, height=350, left=100, top=50, scrollbars=yes");
        } else {
            alert("GEO-Datenbank nicht aktiviert");
        }
    }
    //-->
    </script>
    <script>
    $(document).ready(
        function(){
            $("#left").click(function(){ showCall(-1); }) 
        });
    $(document).ready(
        function(){
            $("#right").click(function(){ showCall(1); }) 
        });
    $(document).ready(
        function(){
            $("#reload").click(function(){ showCall(0); }) 
        });
    $(document).ready(
        function(){
            $("#shipleft").click(function(){ nextshipto('-'); }) 
        });
    $(document).ready(
        function(){
            $("#shipright").click(function(){ nextshipto('+'); })
        });
    $(document).ready( function(){ nextshipto('o'); } );
	    
    </script>
    </head>
<body onLoad="submenu('{kdview}'); showCall(0);">
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:detailview:. {FAART} <span title=".:important note:.">{Cmsg}&nbsp;</span></p>
<form name="kdhelp">
<!--div style="position:relative; top:-2.5em; left:0.2em; "-->
<div id='menubox1'>
    <span style="float:left;" class="top1">
    <ul id="maintab" class="shadetabs">
    <li class="selected"><a href="firma1.php?Q={Q}&id={FID}">.:Custombase:.</a></li>
    <li><a href="firma2.php?Q={Q}&fid={FID}">.:Contacts:.</a></li>
    <li><a href="firma3.php?Q={Q}&fid={FID}">.:Sales:.</a></li>
    <li><a href="firma4.php?Q={Q}&fid={FID}">.:Documents:.</a></li>
    <li><select style="visibility:{chelp}" name="kdhelp" onChange="KdHelp()">
<!-- BEGIN kdhelp -->
        <option value="{cid}">{cname}</option>
<!-- END kdhelp -->
    </select>
        <select id="actionmenu" onchange="doLink();">
            <option>Aktionen</option>
            <option value='vcardexp.php?Q={Q}&fid={FID}'>VCard</option>
            <option value='karte.php?Q={Q}&fid={FID}'>.:register:.</option>
            <option value='firmen3.php?Q={Q}&id={FID}&edit=1'>.:edit:.</option>
        </select>
    </ul>
    </span>
    <span style="float:left; padding-left:3em; padding-bottom:1em; visibility:{tools};" >
    <img src="tools/rechner.png"  onClick="toolwin('tools/Rechner.html')" title=".:simple calculator:."> &nbsp;
    <img src="tools/notiz.png"  onClick="toolwin('postit.php?popup=1')" title=".:postit notes:."> &nbsp;
    <img src="tools/kalender.png"  onClick="toolwin('tools/kalender.php?Q={Q}&id={FID}')" title=".:calender:."> &nbsp;
    <a href="javascript:void(s=prompt('.:ask leo:.',''));if(s)leow=open('http://dict.leo.org/?lp=ende&search='+escape(s),'LEODict','width=750,height=550,scrollbars=yes,resizeable=yes');if(leow)leow.focus();"><img src="tools/leo.png"  title="LEO .:english/german:." border="0"></a> &nbsp;
    </span>
</div>
</form>

<!--span style="position:absolute; left:0.2em; top:5.5em; width:99%;" -->
<span id='contentbox' >
<!-- Begin Code --------------------------------------------- -->
<div style="float:left; width:35em; height:37em; text-align:center; border: 1px solid black;" >
    <div style="position:absolute; left:0.2em; width:35em; " >
        <div style="float:left; width:64%; height:10em; text-align:left; border-bottom: 0px solid black; padding:0.2em;" >
            <span class="gross">{Fname1}</span><br />
            {Fdepartment_1}    {Fdepartment_2}<br />
            {Strasse}<br />
            <span class="mini">&nbsp;<br /></span>
            <span onClick="surfgeo()">{Land}-{Plz} {Ort}</span><br />
            <span class="klein">
            {Bundesland}
            <span class="mini"><br />&nbsp;<br /></span>
            {Fcontact}
            <span class="mini"><br />&nbsp;<br /></span>
            <font color="#444444"> .:tel:.:</font> {Telefon}<br />
            <font color="#444444"> .:fax:.:</font> {Fax}<br />    
            <span class="mini">&nbsp;<br /></span>
            &nbsp;[<a href="mail.php?TO={eMail}&KontaktTO=C{FID}">{eMail}</a>]<br />
            &nbsp;<a href="{Internet}" target="_blank">{Internet}</a></span>
        </div>
        <div style="float:left; width:33%; height:10em; text-align:right; border-bottom: 0px solid black; padding:2px;">
            {kdnr}<br />
            {IMG}<br /><br />
                <form action="../oe.pl" method="post" name="oe">
                <img src="image/kreuzchen.gif" title=".:locked address:." style="visibility:{verstecke};" >
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="vc" value="{CuVe}">
                <input type="hidden" name="type" value="">
                <input type="hidden" name="action_update" value="Erneuern" id="update_button">
                <input type="hidden" name="{CuVe}_id" value="{FID}">
                <button type="submit" title="neuen Auftrag eingeben" style="visibility:{zeige};" onClick="document.oe.type.value='{sales}_order'; submit()">
                                <img src="image/auftrag.png"></button> 
                <button type="submit" title="Angebot/Anfrage erstellen" style="visibility:{zeige};" onClick="document.oe.type.value='{request}_quotation'; submit()">
                                <img src="image/angebot.png"></button> 
                </form><br />
                <span style="visibility:{zeigeplan};"><a href="{KARTE1}" target="_blank"><img src="image/karte.gif" title=".:city map:." border="0"></a></span>
                &nbsp;
                <a href="#" onCLick="anschr(1);" title=".:print label:."><img src="image/brief.png" alt=".:print label:." border="0" /></a><br>
                <br />
                {begin_comment}<a href="lxcars/lxcmain.php?owner={FID}&task=1" title="KFZ-Daten"><img src="./lxcars/image/lxcmain.png" alt="Cars" border="1" /></a>{end_comment}
                <a href="extrafelder.php?owner={Q}{FID}" target="_blank" title=".:extra data:." style="visibility:{zeigeextra};"><img src="image/extra.png" alt="Extras" border="0" /></a>
                &nbsp;
                <a href="timetrack.php?tab={Q}&fid={FID}&name={Fname1}" title=".:timetrack:."><img src="image/timer.png" alt="Stoppuhr" border="0" /></a><br />
                {verkaeufer}

        </div>
    </div>
    <div style="position:absolute; width:35.0em; height:1.4em; text-align:left;  border-top: 1px solid black;left:0px; top:18.5em;">
                <ul id="submenu" class="subshadetabs" style="padding-left:5px;">
            <li id="sublie"><a href="#" onClick="submenu('lie')">.:shipto:.</a></li>
            <li id="subnot"><a href="#" onClick="submenu('not')">.:notes:.</a></li>
            <li id="subvar"><a href="#" onClick="submenu('var')">.:variablen:.</a></li>
            <li id="subfin"><a href="#" onClick="submenu('fin')">.:FinanzInfo:.</a></li>
            <li id="subinf"><a href="#" onClick="submenu('inf')">.:miscInfo:.</a></li>
        </ul>
    </div>

    <span id="lie" style="visibility:visible; position:absolute; text-align:left;width:35em; left:1.2em; top:20.5em;" >
        <div  class="klein">
        <span id="shiptoname"></span> &nbsp;&nbsp;<a href="#" onCLick="anschr();"><img src="image/brief.png" alt=".:print label:." border="0" /></a>&nbsp; &nbsp; 
        .:shipto count:.:{Scnt} <img src="image/leftarrow.png" id='shipleft' border="0"> 
        <span id="SID"></span> <img src="image/rightarrow.png" id='shipright' border="0">
        <span ><a href="" id='karte2' target="_blank"><img src="image/karte.gif" title=".:city map:." border="0"></a></span>
                <br />
        <span id="shiptodepartment_1"></span> &nbsp; &nbsp; <span id="shiptodepartment_2"></span> <br />
        <span id="shiptostreet"></span><br />
        <span class="mini">&nbsp;<br /></span>
        <span id="shiptocountry"></span>-<span id="shiptozipcode"></span> <span id="shiptocity"></span><br />
        <span id="shiptobundesland"></span><br />
        <span class="mini">&nbsp;<br /></span>
        <span id="shiptocontact"></span><br />
        .:tel:.: <span id="shiptophone"></span><br />
        .:fax:.: <span id="shiptofax"></span><br />
        <span id="shiptoemail"></span>
        </div>
    </span>

    <span id="not" style="visibility:hidden;position:absolute;  text-align:left;width:35em; left:1.2em; top:20.5em;">
        <div  class="zeile klein">
            <span class="labelLe">.:Catchword:.</span><span class="value">{sw}     </span>
        <div  class="zeile klein">
        </div>
            <span class="labelLe" valign="top">.:Remarks:.</span><span class="value">{notiz}</span>
        </div>
    </span>    

    <span id="var" style="visibility:hidden;position:absolute;  text-align:left;width:32em; left:1.2em; top:20.5em;">
        <div  class="zeile klein">
<!-- BEGIN vars -->
         <span class="labelLe">{varname}</span><span class="value">{varvalue}</span><br />
<!-- END vars -->
        </div>
    </span>    

    <span id="inf" style="visibility:hidden;position:absolute;  text-align:left;width:35em; left:1.2em; top:20.5em;">
        <div  class="zeile klein">
            <span class="labelLe">.:Concern:.:</span>
            <span class="value"><a href="firma1.php?Q={Q}&id={konzern}">{konzernname}</a></span>
            <span> &nbsp; <a href="konzern.php?Q={Q}&fid={FID}">{konzernmember}</a></span>
        </div>
        <div  class="zeile klein">
            <span class="labelLe">.:headcount:.:</span><span class="value">{headcount}</span>
        </div>
        <br />
        <div  class="zeile klein">
            <span class="labelLe">.:language:.:</span><span class="value">{language}</span>
        <div  class="zeile klein">
        </div>
            <span class="labelLe">.:Industry:.</span><span class="value">{branche}</span>
        </div>
        <br />
        <div  class="zeile klein">
            <span class="labelLe">.:Init date:.:</span>    <span class="value">{erstellt}</span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="labelLe">.:update:.:</span><span class="value">{modify}</span>
        </div>
    </span>    

    <span id="fin" style="visibility:hidden;position:absolute; text-align:left;width:35em; left:1.2em; top:20.5em;">
        <div  class="zeile klein">
            <span class="labelLe">.:Business:.:</span>
            <span class="value">{kdtyp}</span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="labelLe">.:Source:.:</span>
            <span class="value">{lead} {leadsrc}</span>
        </div>
        <div  class="zeile klein">
             <span class="labelLe">.:Discount:.:</span>
            <span class="value">{rabatt}</span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="labelLe">.:Price group:.:</span>
            <span class="value">{preisgrp}</span>
        </div>
        <br />
        <div  class="zeile klein">
            <span class="labelLe">.:taxnumber:.:</span>
            <span class="value">{Taxnumber}</span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="labelLe">UStId:</span>
            <span class="value">{USTID}</span>
        </div>
        <div  class="zeile klein">
            <span class="labelLe">.:taxzone:.:</span>
            <span class="value">{Steuerzone}</span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
        </div>
        <br />
        <div  class="zeile klein">
            <span class="labelLe">.:terms:.:</span>
            <span class="value">{terms} .:days:.</span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="labelLe">.:creditlimit:.:</span>
            <span class="value">{kreditlim}</span>
        </div>
        <div  class="zeile">
            <span class="space mini">.:outstanding:.</span>
        </div>
        <div  class="zeile klein">
            <span class="labelLe">- .:items:.:</span>
            <span class="value" onClick="showOP('{apr}');">{op}</span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="labelLe">- .:orders:.:</span>
            <span class="value" onClick="showOP('oe');">{oa}</span>
        </div>
        <br />
        <div  class="zeile klein">
            <span class="labelLe">.:bankname:.:</span>
            <span class="value">{bank}</span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="labelLe">.:directdebit:.:</span><span class="value">{directdebit}</span>
        </div>
        <div  class="zeile klein">
            <span class="labelLe">.:bankcode:.:</span>
            <span class="value">{blz}</span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="labelLe">.:bic:.:</span>
            <span class="value">{bic}</span>
        </div>
        <div  class="zeile klein">
            <span class="labelLe">.:account:.:</span>
            <span class="value">{konto}</span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="labelLe">.:iban:.:</span>
            <span class="value">{iban}</span>
        </div>
    </span>
</div>

<div style="float:left;  width:45%; height:37em; text-align:left; border: 1px solid black; border-left:0px; ">
    <div class="calls" width='99%' id="tellcalls" >
    </div>
    <!--span style="float:left;  text-align:left; border:0px solid black"-->    
    <span style="position:absolute; bottom:1em; visibility:{none};">
        <form name="ksearch" onSubmit="return ks();"> &nbsp; 
        <img src="image/leftarrow.png" align="middle" border="0" id="left" title="zur&uuml;ck" onClick="showCall(-1);"> 
        <img src="image/reload.png" align="middle" border="0" title="reload" id="reload" onClick="showCall(0);"> 
        <img src="image/rightarrow.png" align="middle" border="0" title="mehr" id="right" onClick="showCall(1);">&nbsp;
        <input type="text" name="suchwort" size="20">
        <input type="hidden" name="Q" value="{Q}">
        <input type="submit" src="image/suchen_kl.png" name="ok" value=".:search:." align="middle" border="0"> 
        </form>
    </span>

</div>
<!-- End Code --------------------------------------------- -->
</span>
{END_CONTENT}
</body>
</html>

