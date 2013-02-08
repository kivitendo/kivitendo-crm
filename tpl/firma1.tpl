<html>
    <head><title></title>
    {STYLESHEETS}
    {JAVASCRIPTS}
    <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
    <link rel="stylesheet" type="text/css" href="{JQUERY}/jquery-ui/themes/base/jquery-ui.css">
    <script type="text/javascript" src="{JQUERY}jquery-ui/jquery.js"></script>
    <script type="text/javascript" src="{JQUERY}jquery-ui/ui/jquery-ui.js"></script>
    
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
    function ks() {
        sw=document.ksearch.suchwort.value;
        if (sw != "") 
            F1=open("suchKontakt.php?suchwort="+sw+"&Q=C&id={FID}","Suche","width=400, height=400, left=100, top=50, scrollbars=yes");
        return false;
    }
        function doLink() {
            if ( document.getElementById('actionmenu').selectedIndex > 0 ) {
                link = $('#actionmenu option:selected').val();
                if (link.substr(0,7) =='onClick') {
                    document.oe.type.value=link.substr(8);
                    document.oe.submit();
                } else {
                    lnk = document.getElementById('actionmenu').options[document.getElementById('actionmenu').selectedIndex].value;
                    if (link.substr(0,4) =='open') {
                        F1=open(link.substr(5),"CRM","width=350, height=400, left=100, top=50, scrollbars=yes");
                    } else {
                        window.location.href = lnk;
                    }
                }
            }
        }
    function KdHelp() {
        link = $('#kdhelp option:selected').val();
        f1=open("wissen.php?kdhelp=1&m="+link,"Wissen","width=750, height=600, left=50, top=50, scrollbars=yes");
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
    $(function(){
         $('button')
          .button()
          .click( function(event) { event.preventDefault();  document.location.href=this.getAttribute('name'); });
         $( "#fasubmenu" ).tabs({ heightStyle: "auto" });
         var index = $('#fasubmenu a[href="#{kdview}"]').parent().index();
         $('#fasubmenu').tabs('select', index);
    });
    </script>
    </head>
<body onLoad=" showCall(0);">
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:detailview:. {FAART} <span title=".:important note:.">{Cmsg}&nbsp;</span></p>
<br>
<div id='menubox1' >
    <form>
    <span style="float:left;" valign="bottom">
        <button name="firma1.php?Q={Q}&id={FID}">.:Custombase:.</button>
        <button name="firma2.php?Q={Q}&fid={FID}">.:Contacts:.</button>
        <button name="firma3.php?Q={Q}&fid={FID}">.:Sales:.</button>
        <button name="firma4.php?Q={Q}&fid={FID}">.:Documents:.</button>
    </span>
    <span style="float:left; vertical-alig:bottom">
        <select style="visibility:{chelp}" name="kdhelp" id="kdhelp" style="margin-top:0.5em;" onChange="KdHelp()">
<!-- BEGIN kdhelp -->
            <option value="{cid}">{cname}</option>
<!-- END kdhelp -->
        </select>
        <select id="actionmenu" onchange="doLink();" style="margin-top:0.5em;">
            <option>Aktionen</option>
            <option value='timetrack.php?tab={Q}&fid={FID}&name={Fname1}'>.:timetrack:.</option>
            <option value='open:extrafelder.php?owner={Q}{FID}'>.:extra data:.</option>
            <option value='vcardexp.php?Q={Q}&fid={FID}'>VCard</option>
            <option value='karte.php?Q={Q}&fid={FID}'>.:register:. .:develop:.</option>
            <option value='firmen3.php?Q={Q}&id={FID}&edit=1'>.:edit:.</option>
            <option value='onClick:{sales}_order'>.:order:. .:develop:.</option>
            <option value='onClick:{request}_quotation'>.:quotation:. .:develop:.</option>
        </select>
    </span>
    <span style="float:left; padding-left:3em; visibility:{tools};" >
        <img src="tools/rechner.png"  onClick="toolwin('tools/Rechner.html')" title=".:simple calculator:." style="margin-bottom:1.3em;"> &nbsp;
        <img src="tools/notiz.png"  onClick="toolwin('postit.php?popup=1')" title=".:postit notes:." style="margin-bottom:1.3em;"> &nbsp;
        <img src="tools/kalender.png"  onClick="toolwin('tools/kalender.php?Q={Q}&id={FID}')" title=".:calender:." style="margin-bottom:1.3em;"> &nbsp;
    <a href="javascript:void(s=prompt('.:ask leo:.',''));if(s)leow=open('http://dict.leo.org/?lp=ende&search='+escape(s),'LEODict','width=750,height=550,scrollbars=yes,resizeable=yes');if(leow)leow.focus();"><img src="tools/leo.png"  title="LEO .:english/german:." border="0" style="margin-bottom:1.3em;"></a> &nbsp;
    </span>
    </form>
</div>

<form action="../oe.pl" method="post" name="oe">
<input type="hidden" name="action" value="add">
<input type="hidden" name="vc" value="{CuVe}">
<input type="hidden" name="type" value="">
<input type="hidden" name="action_update" value="Erneuern" id="update_button">
<input type="hidden" name="{CuVe}_id" value="{FID}">
</form>
<span id='contentbox' style="padding-top:2em;" >
<!-- Begin Code --------------------------------------------- -->
<div style="float:left; width:45em; height:37em; text-align:center; border: 1px solid black;" >
        <div class="gross" style="float:left; width:64%; height:10em; text-align:left; border-bottom: 0px solid black; padding:0.2em;" >
            <span class="fett">{Fname1}</span><br />
            {Fdepartment_1}    {Fdepartment_2}<br />
            {Strasse}<br />
            <span class="mini">&nbsp;<br /></span>
            <span onClick="surfgeo()">{Land}-{Plz} {Ort}</span><br />
            <span class="klein">{Bundesland}</span>
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
            <span class="fett">{kdnr}</span><br />
            {IMG}<br /><br />
            <img src="image/kreuzchen.gif" title=".:locked address:." style="visibility:{verstecke};" >
            <br />
            <span style="visibility:{zeigeplan};"><a href="{KARTE1}" target="_blank"><img src="image/karte.gif" title=".:city map:." border="0"></a></span>
            &nbsp;
            <a href="#" onCLick="anschr(1);" title=".:print label:."><img src="image/brief.png" alt=".:print label:." border="0" /></a><br>
            <br />
            {begin_comment}<a href="lxcars/lxcmain.php?owner={FID}&task=1" title="KFZ-Daten"><img src="./lxcars/image/lxcmain.png" alt="Cars" border="1" /></a>{end_comment}
            &nbsp;
            {verkaeufer}
        </div>
</div>
<div id="fasubmenu" >
    <ul>
    <li><a href="#lie">.:shipto:.    </a></li>
    <li><a href="#not">.:notes:.     </a></li>
    <li><a href="#var">.:variablen:. </a></li>
    <li><a href="#fin">.:FinanzInfo:.</a></li>
    <li><a href="#inf">.:miscInfo:.  </a></li>
    </ul>

    <div id="lie" class="klein">
        <span class="fett" id="shiptoname"></span> &nbsp;&nbsp;&nbsp;&nbsp;
        .:shipto count:.:{Scnt} <img src="image/leftarrow.png" id='shipleft' border="0"> 
        <span id="SID"></span>  <img src="image/rightarrow.png" id='shipright' border="0">&nbsp; &nbsp;

        <a href="#" onCLick="anschr();" align="right"><img src="image/brief.png" alt=".:print label:." border="0"/></a>&nbsp; &nbsp; 
        <a href="" id='karte2' target="_blank" align="right"><img src="image/karte.gif" title=".:city map:." border="0"></a><br />

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

    <div id="not">
            <span class="labelLe klein">.:Catchword:.</span><span class="value">{sw}     </span><br />
            <span class="labelLe klein" valign="top">.:Remarks:.</span><span class="value">{notiz}</span>
    </div>    

    <div id="var" >
        <div  class="zeile klein">
<!-- BEGIN vars -->
         <span class="labelLe">{varname}</span><span class="value">{varvalue}</span><br />
<!-- END vars -->
        </div>
    </div>    

    <div id="inf"> 
            <span class="labelLe">.:Concern:.:</span>
            <span class="value"><a href="firma1.php?Q={Q}&id={konzern}">{konzernname}</a></span>
            <span> &nbsp; <a href="konzern.php?Q={Q}&fid={FID}">{konzernmember}</a></span><br />
            <br />
            <span class="labelLe">.:Industry:.  </span> <span class="value">{branche}  </span><br />
            <br />
            <span class="labelLe">.:headcount:.:</span> <br /><span class="value">{headcount}</span><br />
            <br />
            <span class="labelLe">.:language:.: </span> <span class="value">{language} </span><br />
            <br />
            <span class="labelLe">.:Init date:.:</span> <span class="value">{erstellt} </span>
            <span class="space"> &nbsp;&nbsp;&nbsp;&nbsp;</span>
            <span class="labelLe">.:update:.:   </span> <span class="value">{modify}   </span><br />
            <br />
    </div>    

    <div id="fin" >
         <table width="100%"><tr><td>
            <span class="labelLe">.:Source:.:</span>       <span class="value">{lead} {leadsrc}</span><br />
            <span class="labelLe">.:Business:.:</span>     <span class="value">{kdtyp}</span><br />
            <span class="labelLe">.:taxnumber:.:</span>    <span class="value">{Taxnumber}</span><br />
            <span class="labelLe">UStId:</span>            <span class="value">{USTID}</span><br />
            <span class="labelLe">.:taxzone:.:</span>      <span class="value">{Steuerzone}</span><br />
            <span class="labelLe">.:bankname:.:</span>     <span class="value">{bank}</span><br />
            <span class="labelLe">.:directdebit:.:</span>  <span class="value">{directdebit}</span><br />
            <span class="labelLe">.:bankcode:.:</span>     <span class="value">{blz}</span><br />
            <span class="labelLe">.:bic:.:</span>          <span class="value">{bic}</span><br />
            <span class="labelLe">.:account:.:</span>      <span class="value">{konto}</span><br />
            <span class="labelLe">.:iban:.:</span>         <span class="value">{iban}</span><br />
         </td><td valign="top">
            <span class="labelLe">.:Discount:.:</span>     <span class="value">{rabatt}</span><br />
            <span class="labelLe">.:Price group:.:</span>  <span class="value">{preisgrp}</span><br />
            <span class="labelLe">.:terms:.:</span>        <span class="value">{terms} .:days:.</span><br />
            <span class="labelLe">.:creditlimit:.:</span>  <span class="value">{kreditlim}</span><br />
            <span class="space">.:outstanding:. :</span><br />
            <span class="labelLe">- .:items:.:</span>
            <span class="value" onClick="showOP('{apr}');">{op}</span><br />
            <span class="labelLe">- .:orders:.:</span>
            <span class="value" onClick="showOP('oe');">{oa}</span></br />
         </td></tr></table>
    </div>
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

