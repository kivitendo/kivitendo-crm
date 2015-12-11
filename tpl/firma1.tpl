<html>
<head><title>CRM Firma:{Fname1}</title>
{STYLESHEETS}
{CRMCSS}
{JAVASCRIPTS}
{THEME}
{JQTABLE}
{JQCALCULATOR}
<link rel="stylesheet" href="jquery-plugins/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="jquery-plugins/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
<script type="text/javascript" src="jquery-plugins/qrcode/jquery.qrcode-0.12.0.js"></script>
<script language="JavaScript" type="text/javascript">
    function showCall() {
        $('#calls tr[group="tc"]').remove();
        $.ajax({
            url: 'jqhelp/firmaserver.php?task=showCalls&firma=1&id={FID}',
            dataType: 'json',
            success: function(data){
                var content;
                $.each(data.items, function(i) {
                    content = '';
                    content += '<tr class="verlauf" group="tc" onClick="showItem('+data.items[i].id+');">'
                    content += '<td>' + data.items[i].calldate + '</td>';
                    content += '<td>' + data.items[i].id + '</td>';
                    content += '<td nowrap>' + data.items[i].kontakt;
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
                    $('#calls tr:last').after(content);
               })
               $("#calls").trigger('update');
            }
        });
        return false;
    }
    function dhl() {
        F1=open("dhl.php?Q={Q}&fid={FID}&popup=1","Caller","width=770, height=680, left=100, top=50, scrollbars=yes");
    }
    function showItem(id) {
        F1=open("getCall.php?Q={Q}&fid={FID}&Bezug="+id,"Caller","width=770, height=680, left=100, top=50, scrollbars=yes");
    }
    function anschr(A) {
        $( "#dialogwin" ).dialog( "option", "maxWidth", 400 );
        $( "#dialogwin" ).dialog( "option", "maxHeight", 600 );
        $( "#dialogwin" ).dialog( { title: "Adresse" } );
        if (A==1) {
            //$( "#dialogwin" ).load("showAdr.php?Q={Q}&fid={FID}&nojs=1");
            $('#iframe1').attr('src', 'showAdr.php?Q={Q}&fid={FID}&nojs=1');
        } else {
            sid = document.getElementById('SID').firstChild.nodeValue;
            if ( sid )
                //$( "#dialogwin" ).load("showAdr.php?Q={Q}&sid="+sid+"&nojs=1");
                $('#iframe1').attr('src', 'showAdr.php?Q={Q}&sid='+sid+'&nojs=1');
        }
        $( "#dialogwin" ).dialog( "open" );
    }
    function notes() {
            F1=open("showNote.php?fid={FID}","Notes","width=400, height=400, left=100, top=50, scrollbars=yes");
    }
    function doLink() {
        if ( document.getElementById('actionmenu').selectedIndex > 0 ) {
            link = $('#actionmenu option:selected').val();
            if (link.substr(0,7) =='onClick') {
                if ( link.substr(8) == 'invoice' ) {
                    doIr();
                } else if ( link.substr(8) == 'delivery_order'){
                    doDo();
                }
                else {
                    doOe(link.substr(8));
                }
            } else {
                lnk = document.getElementById('actionmenu').options[document.getElementById('actionmenu').selectedIndex].value;
                if (link.substr(0,4) =='open') {
                    F1=open(link.substr(5),"CRM","width=350, height=400, left=100, top=50, scrollbars=yes");
                } else {
                    window.location.href = lnk;
                }
            }
            document.getElementById('actionmenu').selectedIndex = 0;
       }
    }
    function doOe(type) {//Angebot / Auftrag
      window.location.href = '../oe.pl?action=add&vc={CuVe}&{CuVe}_id={FID}&type=' + type;
    }

    function doDo() { //neuer Lieferschein
      var type = '{Q}' == 'C' ? 'sales_delivery_order' : 'purchase_delivery_order';
      window.location.href = '../do.pl?action=add&vc={CuVe}&{CuVe}_id={FID}&type=' + type;
    }
    function doIr() { //neue Rechnung
      var file = '{Q}' == 'C' ? '../is.pl' : '../ir.pl';
      window.location.href = file + '?action=add&type=invoice&vc={CuVe}&{CuVe}_id={FID}';
    }
    function doIb() { //neuer Brief
      window.location.href = '../letter.pl?action=add';
    }
    function doLxCars() {
        uri='lxcars/lxcmain.php?owner={FID}&task=1'
        window.location.href=uri;
    }
    function KdHelp() {
        link = $('#kdhelp option:selected').val();
        if ( $('#kdhelp').prop("selectedIndex") > 0 ) {
            f1=open("wissen.php?kdhelp="+link,"Wissen","width=750, height=600, left=50, top=50, scrollbars=yes");
            $('#kdhelp option')[0].selected = true;
        }
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
                        $('#karte2').attr("href",data.karte);
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
    $(document).ready(
        function(){
            $("#shipleft").click(function(){ nextshipto('-'); })
            $("#shipright").click(function(){ nextshipto('+'); })
            nextshipto('o');
            $('button').button().click(
            function(event) {
                event.preventDefault();
                name = this.getAttribute('name');
                if ( name == 'ks' ) {
                    var sw = $('#suchwort').val();
                    F1=open("suchKontakt.php?suchwort="+sw+"&Q=C&id={FID}","Suche","width=400, height=400, left=100, top=50, scrollbars=yes");
                } else if ( name == 'reload' ) {
                    showCall();
                } else {
                    document.location.href = name;
                }
            });
            $("#fasubmenu").tabs({
                heightStyle: "auto",
                active: {kdviewli}
            });
            $("#calculator").calculator({
                useThemeRoller: true,
                layout: $.calculator.scientificLayout,
            });
            $("#calculator_dialog").dialog({
                autoOpen: false,
                title: 'Calcutator',
                width:'auto',
                resizable: false
            });
            $("#calculator_img").on("click", function () {
                $( "#calculator_dialog" ).dialog( "open" );
            });
            $("#calendar").on("click", function () {
                window.location.href = "calendar.phtml";
            });
            $(function() {
                $( "#right_tabs" ).tabs({
                    cache: true, //helpful?
                    active: {kdviewre},
                    beforeLoad: function( event, ui ) {
                        ui.jqXHR.error(function() {
                        ui.panel.html(
                            ".:Couldn't load this tab.:." );
                        });
                    }
                });
            });
            $("#dialogwin").dialog({
                autoOpen: false,
                show: {
                    effect: "blind",
                    duration: 300
                },
                hide: {
                    effect: "explode",
                    duration: 300
                },
            });
            $(".firmabutton").button().click(
            function( event ) {
                if ( this.getAttribute('name') != 'extra' && this.getAttribute('name') != 'karte' && this.getAttribute('name') != 'lxcars') {
                    event.preventDefault();
                };
            });
           // --------   QR Code wird durch Jquery erstellt
           $("#qrbutt").button().click(
              function( event ) {
                $.ajax({
                       type: "GET",
                      url: "vcardexp.php?qr=1&Q={Q}&fid={FID}",
                       success: function(strResponse){
                         $("#qrcode").qrcode({
                             "mode": 0,
                              "size": 250,
                              "color": "#3a3",
                              "text": strResponse
                        });
                       }
                 });
                 $(".fancybox").trigger('click');
                 $(".fancybox").empty();
            });
            $(".fancybox").fancybox();
        }
    );
</script>
<style>


</style>
</head>
<body onLoad="showCall(0);">
{PRE_CONTENT}
{START_CONTENT}
<div class="ui-widget-content" style="height:722px">

<div id="tools" style="position:absolute; top: +20; left:900;">
    <div id="calculator_dialog"><div id="calculator"></div></div>
    <img src="tools/rechner.png" id="calculator_img" title=".:simple calculator:.">
    <img src="tools/notiz.png" onClick="toolwin('postit.php?popup=1')" title=".:postit notes:." style="margin-left: 20;">
    <img src="tools/kalender.png" id="calendar" title=".:calendar:." style="margin-left: 20;">
    <img src="tools/leo.png" id="leo" title="LEO .:english/german:." style="margin-left: 20;">
</div>
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">.:detailview:. {FAART} <span title=".:important note:.">{Cmsg}&nbsp;</span></p>
<br>
<div id='menubox1' >
    <form>
    <span style="float:left;" valign="bottom">
        <!-- <div class="fancybox" rel="group" href="tmp/qr_{loginname}.png"><img src="" alt="" /></div> -->
        <div id="qrcode" class="fancybox" rel="group"><img src="" alt="" /></div>
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
            <option value='firmen3.php?Q={Q}&id={FID}&edit=1'>.:edit:.</option>
            <option value='timetrack.php?tab={Q}&fid={FID}&name={Fname1}'>.:timetrack:.</option>
            <option value='open:extrafelder.php?owner={Q}{FID}'>.:extra data:.</option>
            <option value='karte.php?Q={Q}&fid={FID}'>.:register:. .:develop:.</option>
            <option value='onClick:{request}_quotation'>.:quotation:. .:develop:.</option>
            <option value='onClick:{sales}_order'>.:order:. .:develop:.</option>
            <option value='onClick:delivery_order'>.:delivery order:. .:develop:.</option>
            <option value='onClick:invoice'>.:invoice:. .:develop:.</option>
        </select>
    </span>
    </form>
</div>


<div id='contentbox'>
    <div style="float:left; width:45em; height:37em; text-align:center; border: 1px solid lightgray;" >
        <div class="gross" style="float:left; width:55%; height:25em; text-align:left; border: 0px solid black; padding:0.2em;" >
            <span class="fett">{Fname1}</span><br />
            {Fdepartment_1} {Fdepartment_2}<br />
            {Strasse}<br />
            <span class="mini">&nbsp;<br /></span>
            <span onClick="surfgeo()">{Land}-{Plz} {Ort}</span><br />
            <span class="klein">{Bundesland}</span>
            <span class="mini"><br />&nbsp;<br /></span>
            {Fcontact}
            <span class="mini"><br />&nbsp;<br /></span>
            <font color="#444444"> .:tel:.:</font> <a href="tel:{Telefon}">{Telefon}</a><br />
            <font color="#444444"> .:fax:.:</font> <a href="tel:{Fax}">{Fax}</a><br />
            <span class="mini">&nbsp;<br /></span>
            &nbsp;[<a href="{mail_pre}{eMail}{mail_after}">{eMail}</a>]<br />
            &nbsp;<a href="{Internet}" target="_blank">{Internet}</a>
        </div>
        <div style="float:left; width:43%; height:25em; text-align:right; border: 0px solid black; padding:0.2em;">
            <span valign='top'><span class="fett">{kdnr}</span> <img src="image/kreuzchen.gif" title=".:locked address:." style="visibility:{verstecke};" > {verkaeufer}
            {IMG}<br /></span>
            <br class='mini'>
               {ANGEBOT_BUTTON}
               {AUFTRAG_BUTTON}
               {LIEFER_BUTTON}
               {RECHNUNG_BUTTON}<br />
            <br class='mini'>
               {EXTRA_BUTTON}
               {QR_BUTTON}
               {KARTE_BUTTON}
               {ETIKETT_BUTTON}
            <br />
            <br class='mini'>
               {DHL_BUTTON}
               {BRIEF_BUTTON}
               {LxCars_BUTTON}
            <br /><br />
            <span style="visibility:{zeige_bearbeiter};">.:employee:.: {bearbeiter}</span>
        </div>
        <br />
    </div>
    <div id="fasubmenu" >
        <ul>
            <li><a href="#lie">.:shipto:. </a></li>
            <li><a href="#not">.:notes:. </a></li>
            <li><a href="#var">.:variablen:. </a></li>
            <li><a href="#fin">.:financial:.</a></li>
            <li><a href="#inf">.:miscInfo:. </a></li>
        </ul>
        <div id="lie" class="klein">
            <span class="fett" id="shiptoname"></span> &nbsp;&nbsp;&nbsp;&nbsp;
            .:shipto count:.:{Scnt} <img src="image/leftarrow.png" id='shipleft' border="0">
            <span id="SID"></span> <img src="image/rightarrow.png" id='shipright' border="0">&nbsp; &nbsp;
            <a href="#" onCLick="anschr();"><img src="image/brief.png" alt=".:print label:." border="0"/></a>&nbsp; &nbsp;
            <a href="" id='karte2' target="_blank"><img src="image/karte.gif" alt="karte" title=".:city map:." border="0"></a><br />
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
            <table class="tablesorter" width="50%" style='margin:0px; cursor:pointer;'>
            <thead></thead>
            <tbody>
           <tr><td width="20%" >.:Catchword:.</td><td>{sw}</td></tr>
           <tr><td width="20%" >.:Remarks:.</td><td>{notiz}</td></tr>
            </tbody>
            </table>
        </div>
        <div id="var" >
            <div class="zeile klein">
            <table class="tablesorter" width="50%" style='margin:0px; cursor:pointer;'>
            <thead></thead>
<!-- BEGIN vars -->
           <tr><td width="20%" >{varname}</td><td>{varvalue}</td></tr>
<!-- END vars -->
            </table>
            </div>
        </div>
        <div id="inf">
          <table class="tablesorter" width="50%" style='margin:0px; cursor:pointer;'>
          <thead></thead>
          <tbody>
           <tr><td width="20%">.:Concern:.:</td><td width="25%"><a href="firma1.php?Q={Q}&id={konzern}">{konzernname}</td><td width="25%"><a href="konzern.php?Q={Q}&fid={FID}">{konzernmember}</a></td><td></td></tr>
           <tr><td width="20%">.:Industry:. </td><td width="25%">{branche}</td><td width="25%"></td><td></td></tr>
           <tr><td width="20%">.:headcount:.:</td><td width="25%">{headcount}</td><td width="25%"></td><td></td></tr>
           <tr><td width="20%">.:language:.:</td><td width="25%">{language} </td><td width="25%"></td><td></td></tr>
           <tr><td width="20%">.:Init date:.:</td><td width="25%">{erstellt} </td><td width="25%">.:update:.: </td><td>{modify} </td></tr>
          </tbody>
         </table>
        </div>
        <div id="fin" >
         <table class="tablesorter" width="50%" style='margin:0px; cursor:pointer;'>
          <thead></thead>
          <tbody>
           <tr><td width="20%">.:Source:.:</td><td width="35%">{lead} {leadsrc}</td><td width="21%">.:Discount:.:</td><td>{rabatt}</td></tr>
           <tr><td width="20%">.:{Q}Business:.:</td><td width="35%">{kdtyp}</td><td width="21%">.:Price group:.:</td><td>{preisgrp}</td></tr>
           <tr><td width="20%">.:taxnumber:.:</td><td width="35%">{Taxnumber}</td><td width="21%">.:terms:.:</td><td>{terms} .:days:.</td></tr>
           <tr><td width="20%">UStId:</td><td width="35%">{USTID}</td><td width="21%">.:creditlimit:.:</td><td>{kreditlim}</td></tr>
           <tr><td width="20%">.:taxzone:.:</td><td width="35%">{Steuerzone}</td><td width="21%">.:outstanding:. :</td><td></td></tr>
           <tr><td width="20%">.:bankname:.:</td><td width="35%">{bank}</td><td width="21%">- .:items:.:</td><td>{op}</td></tr>
           <tr><td width="20%">.:directdebit:.:</td><td width="35%">{directdebit}</td><td width="21%">- .:orders:.:</td><td>{oa}</td></tr>
           <tr><td width="20%">.:bankcode:.:</td><td width="35%">{blz}</td><td></td><td></td></tr>
           <tr><td width="20%">.:bic:.:</td><td width="35%">{bic}</td><td></td><td></td></tr>
           <tr><td width="20%">.:account:.:</td><td width="35%">{konto}</td><td></td><td></td></tr>
           <tr><td width="20%">.:iban:.:</td><td width="35%">{iban}</td><td></td><td></td></tr>
          </tbody>
         </table>
        </div>
    </div>

    <div style="float:left; width:45%; height:37em; text-align:left; border: 1px solid lightgrey; border-left:0px;">
        <div id="right_tabs">
            <ul>
                <li><a href="#contact">.:contact:.</a></li>
                <li><a href="jqhelp/get_doc.php?Q={Q}&fid={FID}&type=quo">.:Quotation:.</a></li>
                <li><a href="jqhelp/get_doc.php?Q={Q}&fid={FID}&type=ord">.:orders:.</a></li>
                <li><a href="jqhelp/get_doc.php?Q={Q}&fid={FID}&type=del">.:delivery order:.</a></li>
                <li><a href="jqhelp/get_doc.php?Q={Q}&fid={FID}&type=inv">.:invoice:.</a></li>
            </ul>
            <div id="contact">
                <table id="calls" class="tablesorter" width="100%" style='margin:0px; cursor:pointer;'>
                    <thead><tr><th>Datum</th><th>id</th><th class="{ sorter: false }"></th><th>Betreff</th><th>.:contact:.</th></tr></thead>
                    <tbody>
                        <tr onClick="showItem(0)" class='verlauf'><td></td><td>0</td><td></td><td>.:newItem:.</td><td></td></tr>
                    </tbody>
                </table><br>
                <div id="pager" class="pager" style='position:absolute;'>
                    <form name="ksearch" onSubmit="false ks();"> &nbsp;
                        <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/first.png" class="first">
                        <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/prev.png" class="prev">
                        <input type="text" id='suchwort' name="suchwort" size="20"><input type="hidden" name="Q" value="{Q}">
                        <button id='ks' name='ks'>.:search:.</button>
                        <button id='reload' name='reload'>reload</button>
                        <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/next.png" class="next">
                        <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/last.png" class="last">
                        <select class="pagesize" id='pagesize'>
                            <option value="10">10</option>
                            <option value="15" selected>15</option>
                            <option value="20">20</option>
                            <option value="25">25</option>
                            <option value="30">30</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="dialogwin">
<iframe id="iframe1" width='100%' height='450'  scrolling="auto" border="0" frameborder="0"><img src='image/wait.gif'></iframe>
</div>
</div>
{END_CONTENT}
</body>
</html>
