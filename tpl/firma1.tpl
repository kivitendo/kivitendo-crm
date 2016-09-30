<html>
<html>
<head><title>CRM Firma: {Fname1}</title>
{STYLESHEETS}
{CRMCSS}
{JAVASCRIPTS}
{THEME}
{JQTABLE}
{JQCALCULATOR}
{FANCYBOX}
{QRCODE}
{JQUERY}
{JQUERYUI}
{BASEPATH}
{TRANSLATION}


<script type="text/javascript" src="{BASEPATH}lxcars/jQueryAddOns/date-time-picker.js"></script>
<script type="text/javascript" src="{BASEPATH}lxcars/jQueryAddOns/german-date-time-picker.js"></script>
<script type="text/javascript" src="{BASEPATH}crm/jquery-ui/jquery.js"></script>
<!--<link type="text/css" REL="stylesheet" HREF="../../css/{ERPCSS}"></link>
<link rel="stylesheet" type="text/css" href="{BASEPATH}crm/jquery-ui/themes/base/jquery-ui.css">-->

<script language="javascript" type="text/javascript" src="translation/all.lng"></script>

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
                        content += '<tr class="verlauf" group="tc" onClick="showItem('+data.items[i].id+');">';
                        content += '<td>' + data.items[i].calldate + '</td>';
                        content += '<td>' + data.items[i].id + '</td>';
                        content += '<td nowrap>' + data.items[i].type_of_contact;
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
                    });
                    $("#calls").trigger('update');
                    $("#calls").trigger("appendCache");
                }
            });
        }

       function showItem(id) {
        var id = id;

        $("#contactsdialog").dialog("open").html('<p> <form id="contacts"> <label>' + langData[language]['SUBJECT'] + '</label>'+
            '<input type="text" name="cause" id="cause">'+
            '<label>' +  langData[language]['DATE'] + ' / ' + langData[language]['TIME'] + '</label>' +
            '<input type="text" name="calldate" id="calldate" >' +
            '<input type="text" name="caller_id" id="caller_id" maxlength="3" size="3" value={FID} hidden="hidden">' +
            '<p><label>' + langData[language]['COMMENTS'] + '</label> <textarea name="cause_long" id="cause_long" rows="10" cols="60" wrap="hard"></textarea> </p>'+
            '<p> <fieldset> <legend>' + langData[language]['TYPE_OF_CONTACT'] + '</legend>'+
            '<input type="radio" name="type_of_contact" id="radio-1" value="T" checked="checked">  <label for="radio-1">' + langData[language]['PHONE'] + '</label>'+
                '<input type="radio" name="type_of_contact" id="radio-2" value="M">  <label for="radio-2">' + langData[language]['EMAIL'] + '</label>'+
                '<input type="radio" name="type_of_contact" id="radio-3" value="L">  <label for="radio-3">' + langData[language]['LETTER'] + '</label>'+
                '<input type="radio" name="type_of_contact" id="radio-4" value="P">  <label for="radio-4">' + langData[language]['PERSONAL'] + '</label>'+
                '<input type="radio" name="type_of_contact" id="radio-5" value="F">  <label for="radio-5">' + langData[language]['FILE'] + '</label>'+
                '<input type="radio" name="type_of_contact" id="radio-6" value="R">  <label for="radio-6">' + langData[language]['TERM'] + '</label> </fieldset> </p>'+
               '<p> <fieldset> <legend>' + langData[language]['DIRECTION'] + '</legend>'+
                '<input type="radio" name="inout" id="radio-7" value="i">  <label for="radio-7">' + langData[language]['FROM'] + ' ' + langData[language]['CUSTOMER_LABEL'] + '</label>'+
                '<input type="radio" name="inout" id="radio-8" value="o" >  <label for="radio-8">' + langData[language]['TO'] + ' ' + langData[language]['CUSTOMER_LABEL'] + '</label>'+
                '<input type="radio" name="inout" id="radio-9" value="-" checked="checked">  <label for="radio-9">' + langData[language]['UNASSIGNED'] + '</label>'+
                '<input type="hidden" name="id" id="id" value="' + id + '">' +
            '</fieldset> </form></p> </p>');

        if (id != 0) getSingleRow(id);

        $("#calldate").datetimepicker({
            //dateFormat: 'yy-mm-dd',
            stepMinute: 5,
            hour: 1,
            hourMin: 6,
            hourMax: 19,
            //timeSuffix: ' Uhr',
            timeText: langData[language]['TIME'],
            hourText: langData[language]['HOUR'],
            closeText: langData[language]['CLOSE'],
            currentText: langData[language]['NOW']
        });

    }



    function dhl() {
        F1=open("dhl.php?Q={Q}&fid={FID}&popup=1","Caller","width=770, height=680, left=100, top=50, scrollbars=yes");
    }


    function anschr(A) {
        $( "#dialogwin" ).dialog( "option", "width", 400 );
        $( "#dialogwin" ).dialog( "option", "minWidth", 300 );
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

    function doOe(type) { //Auftrag
        window.location.href = '../oe.pl?action=add&vc={CuVe}&{CuVe}_id={FID}&type=' + type;
    }

    function newOrder( href ){ // Auftrag
        window.location.href = href;
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
        window.location.href = '../controller.pl?action=Letter%2fadd&letter.customer_id={FID}';
    }

    function doLxCars() {
        uri='lxcars/lxcmain.php?owner={FID}&task=1'
        window.location.href=uri;
    }

    function getSingleRow(id) {
        $.ajax({
            dataType: 'json',
            url: 'ajax/contact.php?action=getData',
            method: "GET",
            success: function( json ) {
                for (var i = 0; i < json.length; i++) {
                    row = json[i];
                    if (row.id == id)  {
                        $("#contacts #cause").val(row.cause);
                        var calldate = mkCallDate(row.calldate);
                        $("#contacts #calldate").val(calldate);
                        $("#contacts #caller_id").val(row.caller_id);
                        $("#contacts #employee").val(row.employee);
                        $("#contacts #cause_long").val(row.cause_long);
                        var rNumber;
                        switch(row.type_of_contact) {
                            case "T":
                                rNumber = 1;
                                break;
                            case "M":
                                rNumber = 2;
                                break;
                            case "L":
                                rNumber = 3;
                                break;
                            case "P":
                                rNumber = 4;
                                break;
                            case "F":
                                rNumber = 5;
                                break;
                            case "R":
                                rNumber = 6;
                                break;
                             default:
                                rNumber = 1;
                        };


                        var checkedTocBtn = "radio-" + rNumber;
                        $("#" + checkedTocBtn + " ").attr("checked","checked");

                        rNumber = 6;
                        switch(row.inout) {
                            case "i":
                                rNumber += 1;
                                break;
                            case "o":
                                rNumber += 2;
                                break;
                            case "-":
                                rNumber += 3;
                                break;
                             default:
                                rNumber += 3;
                        };
                        var checkedIOBtn = "radio-" + rNumber;
                        $("#" + checkedIOBtn + " ").attr("checked","checked");
                    }
                }
            },
            error:  function(){
                alert(langData[language]['GET_ERROR']);
            }
        })
    }

    function mkCallDate(callDate) {
        var calld = callDate;
        var yr = calld.substring(0,4);
        var mth = calld.substring(5,7);
        var d = calld.substring(8,10);
        var h = calld.substring(11,13);
        var m = calld.substring(14,16);
        var s = calld.substring(17,19);
        var calldate = d + '.' + mth + '.' + yr + ' ' + h + ':' + m + ':' +s;
        return calldate;
    }

    $(document).ready(function(){

        language = kivi.myconfig.countrycode;   // Variable language muss global sein!
        $( ".lang" ).each( function(){
            var key = $( this ).attr( "data-lang" );
            if( $( this ).is( ":input" ) ) $( this ).attr( 'title',  typeof( langData[language][key] ) != 'undefined' ? langData[language][key] : 'LNG ERR'  );
                else $( this ).text( typeof( langData[language][key] ) != 'undefined' ? langData[language][key] : 'LNG ERR'  );
            });

            if ('{Q}' == 'C') {
                $.ajax({
                    dataType: 'json',
                    url: 'ajax/contact.php?action=openInvoice&data={FID}',
                    method: "GET",
                    success: function( json ) {
                        if (json) {
                            $( "#openInvoice" ).append( "<span style='color:red'>Offene Rechnung vorhanden !</span>" );
                        }
                    },
                    error:  function(){
                        alert(langData[language]['GET_ERROR']);
                    }
                });
            }

        showCall();

        function saveData() {
            var obj = {};
            var arr = $('#contacts').serializeArray();
            // Ein object aus dem array machen
            $.each(arr, function(index, item) {
                obj[item.name] = item.value;
            });
            $.ajax({
                data: { action: "newContact", data: JSON.stringify(obj)},
                dataType: 'json',
                type: 'POST',
                url: "ajax/contact.php",
                success: function(){
                    showCall();
                },
                error:  function(){
                    alert(langData[language]['SEND_ERROR']);
                }
            })
        }

        function showSearch(id) {
            //alert("Show Search");
            var id=id;
            var fid = {FID};
            var cUrl="getCall.php?Q=C&fid=" + fid + "&hole="+id;
            $("#showsearchdialog").dialog({
               width:      "auto",
               height:     "auto",
               autoOpen:   "false"
            }).html('<iframe src="' + cUrl + '" width="610px" height="600px" scrollbars="yes"></iframe>');
        }



        // Aus Tabelle kopieren,  weiter verbessern!

        $("td").click(function() {
            $(this).select();
            document.execCommand("copy");
        });
        $("#shipleft").click(function(){ nextshipto('-'); });
        $("#shipright").click(function(){ nextshipto('+'); });
        nextshipto('o');
        $('#ks').button().click(function(event) {
            event.preventDefault();
            //name = this.getAttribute('name');
            var sw = $('#suchwort').val();
            $("#searchdialog").dialog({
                height: "auto",
                width: "auto",
                title: ".:search result:.",
                open:function (event, ui) {
                    $('#searchdialog table.tablesorter').tablesorter();
                },
                buttons: [{
                    text: langData[language]['CLOSE'],
                    click: function(){
                        $(this).dialog("close");
                        return false;
                    }
                }]
            }).html('<table width="100%" class="tablesorter">' +
                '<thead><tr><th>'+langData[language]["DATE"]+'</th>'+
                '<th width="150px">'+langData[language]["SUBJECT"]+'</th>'+
                '</tr></thead><tbody></tbody><tfoot></tfoot></table>');
            $.ajax({
                dataType: 'json',
                data: {action: "getSearch", data: {sw: sw, Q: "C", fid: {FID}}},
                url: 'ajax/contact.php',
                method: "GET",
                success: function( json ) {
//                    $("#searchdialog table").addClass("tablesorter");
                    $("#searchdialog tbody").empty();
                    var row='';
                    for (var i = 0; i < json.length; i++) {
                        var sContent='';
                        row = json[i];
                        var id = row.id;
                        var calldate = mkCallDate(row.calldate);
                        sContent += '<tr> <td>' + calldate + '</td>';
                        sContent += '<td>' + row.cause + '</td></tr>';
                        $("#searchdialog tbody").append(sContent);
                    }
                    $("#searchdialog tbody tr").click(function() {
                        showSearch(id);
                    });
                },
                error:  function(){
                    alert(langData[language]['GET_ERROR']);
                }
            });
        });

        $("#fasubmenu").tabs({
            heightStyle: "auto",
            active: {kdviewli}
        });

        $(function() {
            $( "#right_tabs" ).tabs({
                cache: true, //helpful?
                active: {kdviewre},
                beforeLoad: function( event, ui ) {
                    ui.jqXHR.error(function() {
                        ui.panel.html(langData[language]['TABLOAD_ERROR'] );
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

        $(".firmabutton").button().click(function( event ){
            if ( this.getAttribute('name') != 'extra' && this.getAttribute('name') != 'karte' && this.getAttribute('name') != 'lxcars') {
                event.preventDefault();
            };
        });

        $("#actionmenu").selectmenu({
            change: function( event, ui ) {
                if ($('#actionmenu option:selected').attr('id') == 1) {
                    window.location.href = 'firmen3.php?Q={Q}&id={FID}&edit=1';
                }
                else if ($('#actionmenu option:selected').attr('id') == 2) {
                    window.location.href = 'timetrack.php?tab={Q}&fid={FID}&name={Fname1}';
                }
                else if ($('#actionmenu option:selected').attr('id') == 3) {
                    F1=open('extrafelder.php?owner={Q}{FID}',"CRM","width=350, height=400, left=100, top=50, scrollbars=yes");
                }
                else if ($('#actionmenu option:selected').attr('id') == 4) {
                    window.location.href = 'karte.php?Q={Q}&fid={FID}';
                }
                else if ($('#actionmenu option:selected').attr('id') == 5) {
                    window.location.href = '../oe.pl?action=add&vc={CuVe}&{CuVe}_id={FID}&type=' + $('#actionmenu option:selected').val();
                }
                else if ($('#actionmenu option:selected').attr('id') == 6) {
                    window.location.href = '../oe.pl?action=add&vc={CuVe}&{CuVe}_id={FID}&type=' + $('#actionmenu option:selected').val();
                }
                else if ($('#actionmenu option:selected').attr('id') == 7) {
                    var type = '{Q}' == 'C' ? 'sales_delivery_order' : 'purchase_delivery_order';
                    window.location.href = '../do.pl?action=add&vc={CuVe}&{CuVe}_id={FID}&type=' + type;
                }
                else if ($('#actionmenu option:selected').attr('id') == 8) {
                    var file = '{Q}' == 'C' ? '../is.pl' : '../ir.pl';
                    window.location.href = file + '?action=add&type=invoice&vc={CuVe}&{CuVe}_id={FID}';
                }
            }
        });

        // --------   QR Code wird durch Jquery erstellt
        $("#qrbutt").button().click(function( event ) {
            $.ajax({
                type: "GET",
                url: "vcardexp.php?Q={Q}&fid={FID}",
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



        $("#contactsdialog").dialog({
            autoOpen: false,
            modal: true,
            width:600,
            height:550,
            buttons: [{
                text: langData[language]['SAVE'],
                id: 'saveBtn',
                click: function(){
                   saveData();
                   $(this).dialog("close");
                   return false;
                }
            },
            {
                text: langData[language]['CLOSE'],
                id: 'cancelBtn',
                click: function(){
                    //alert("Close");
                    $(this).dialog("close");
                    return false;

                }
            }]
        });

    });


</script>

<style>
    #contacts input {margin-left: 5px; margin-right: 10px}
    .tablesorter { width:auto; cursor:pointer; widgets: ['zebra'];}
</style>

</head>

<body>


{PRE_CONTENT}
{START_CONTENT}
    <div class="ui-widget-content" style="height:722px" >
        <p class="ui-state-highlight ui-corner-all tools" style="margin-top: 20px; padding: 0.6em;" >.:detailview:. {FAART} <span title=".:important note:.">{Cmsg}&nbsp;</span></p>
        <br>
        <div id='menubox1' >
            <form>
                <span style="float:left;" valign="bottom">
                <!-- <div class="fancybox" rel="group" href="tmp/qr_{loginname}.png"><img src="" alt="" /></div> -->
                    <div id="qrcode" class="fancybox" rel="group"><img src="" alt="" /></div>
                    <button id="firma1Btn" name="firma1.php?Q={Q}&id={FID}" >.:Custombase:.</button>
                    <button id="firma2Btn" name="firma2.php?Q={Q}&fid={FID}" >.:Contacts:.</button>
                    <button id="firma3Btn" name="firma3.php?Q={Q}&fid={FID}" >.:Sales:.</button>
                    <button id="firma4Btn" name="firma4.phtml?Q={Q}&kdnr={kdnr}&fid={FID}" >.:Documents:.</button>
                </span>
                <span style="float:left; vertical-alig:bottom; padding-left:8em">
                    <!--         <select style="visibility:{chelp}" name="kdhelp" id="kdhelp" style="margin-top:0.5em;" onChange="KdHelp()"> -->
                    <!-- BEGIN kdhelp -->
                    <!--         <option value="{cid}">{cname}</option> -->
                    <!-- END kdhelp -->
                    <!--     </select> -->
                    <select id="actionmenu" style="margin-top:0.5em;">
                        <option id= '0' class='lang' data-lang='ACTIONS'>Aktionen</option>
                        <option id= '1' value='firmen3.php?Q={Q}&id={FID}&edit=1'>.:edit:.</option>
                        <option id= '2' value='timetrack.php?tab={Q}&fid={FID}&name={Fname1}'>.:timetrack:.</option>
                        <option id= '3' value='extrafelder.php?owner={Q}{FID}'>.:extra data:.</option>
                        <option id= '4' value='karte.php?Q={Q}&fid={FID}'>.:register:. .:develop:.</option>
                        <option id= '5' value='{request}_quotation'>.:quotation:. .:develop:.</option>
                        <option id= '6' value='{sales}_order'>.:order:. .:develop:.</option>
                        <option id= '7' value='delivery_order'>.:delivery order:. .:develop:.</option>
                        <option id= '8' value='invoice'>.:invoice:. .:develop:.</option>
                    </select>
                </span>
            </form>
        </div>

        <div id="contactsdialog" title=".:contact:."></div>
        <div id="searchdialog" title=".:search result:."></div>
        <div id="showsearchdialog" width="610px" height="600px"></div>

        <div id='contentbox'>
            <div style="float:left; width:45em; height:37em; text-align:center; border: 1px solid lightgray;" >
            <div class="gross" style="float:left; width:55%; height:25em; text-align:left; border: 0px solid black; padding:0.2em;" >
                <span class="fett">{Fname1}</span><br />
                {Fdepartment_1} {Fdepartment_2}<br />
                {Strasse}<br />
                <span class="mini">&nbsp;<br /></span>
                <span onClick="surfgeo()">{Land}-{Plz} {Ort}</span><br />
                <span class="klein">{Bundesland}</span><br /><br />
                <div id="openInvoice"></div>
                {Fcontact}
                <span class="mini"><br />&nbsp;<br /></span>
                <font color="#444444"> .:tel:.:</font> <a href="tel:{Telefon}">{Telefon}</a><br />
                <font color="#444444"> .:fax:.:</font> <a href="tel:{Fax}">{Fax}</a><br />
                <span class="mini">&nbsp;<br /></span>
                &nbsp;[<a href="{mail_pre}{eMail}{mail_after}">{eMail}</a>]<br />
                &nbsp;<a href="{Internet}" target="_blank">{Internet}</a>
            </div>
            <div style="float:left; width:43%; height:25em; text-align:right; border: 0px solid black; padding:0.2em;">
                <span valign="top"><span class="fett">{kdnr}</span><img src="image/kreuzchen.gif" title=".:locked address:." style="visibility:{verstecke};" > {verkaeufer}
                {IMG} <br /> </span>
                <br class= 'mini'>
                    {ANGEBOT_BUTTON}
                    {AUFTRAG_BUTTON}
                    {LIEFER_BUTTON}
                    {RECHNUNG_BUTTON}
                <br />
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
                <br />
                <br />
                <span style="visibility:{zeige_bearbeiter};" >.:employee:.: {bearbeiter}</span>
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
                    .:shipto count:.:{Scnt} <img src="image/leftarrow.png" id="shipleft" border="0">
                    <span id="SID"></span> <img src="image/rightarrow.png" id="shipright" border="0">&nbsp; &nbsp;
                    <a href="#" onCLick="anschr();"><img src="image/brief.png" alt=".:print label:." border="0"/></a>&nbsp; &nbsp;
                    <a href="" id="karte2 target="_blank"><img src="image/karte.gif" alt="karte" title=".:city map:." border="0"></a><br />
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
                            <tr><td width="20%" class="lang" data-lang="CATCHWORD">.:Catchword:.</td><td>{sw}</td></tr>
                            <tr><td width="20%" class="lang" data-lang="COMMENTS">.:Remarks:.</td><td>{notiz}</td></tr>
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
                            <tr><td width="20%" class="lang" data-lang="CONCERN">.:Concern:.:</td><td width="25%"><a href="firma1.php?Q={Q}&id={konzern}">{konzernname}</td><td width="25%"><a href="konzern.php?Q={Q}&fid={FID}">{konzernmember}</a></td><td></td></tr>
                            <tr><td width="20%" class="lang" data-lang="INDUSTRY">.:Industry:. </td><td width="25%">{branche}</td><td width="25%"></td><td></td></tr>
                            <tr><td width="20%" class="lang" data-lang="HEADCOUNT">.:headcount:.:</td><td width="25%">{headcount}</td><td width="25%"></td><td></td></tr>
                            <tr><td width="20%" class="lang" data-lang="LANGUAGE">.:language:.:</td><td width="25%">{language} </td><td width="25%"></td><td></td></tr>
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
                        <li><a href="jqhelp/get_doc.php?Q={Q}&fid={FID}&type=quo">.:quotations:.</a></li>
                        <li><a href="jqhelp/get_doc.php?Q={Q}&fid={FID}&type=ord">.:orders:.</a></li>
                        <li><a href="jqhelp/get_doc.php?Q={Q}&fid={FID}&type=del">.:delivery order:.</a></li>
                        <li><a href="jqhelp/get_doc.php?Q={Q}&fid={FID}&type=inv">.:invoice:.</a></li>
                    </ul>
                    <div id="contact">
                        <table id="calls" class="tablesorter" width="100%" style='margin:0px; cursor:pointer;'>
                            <thead><tr><th>.:date:.</th><th>id</th><th class="{ sorter: false } ">.:type:. / .:direction:.</th><th>.:subject:.</th><th>.:contact:.</th></tr></thead>
                                <tbody id="tbshow">
                                    <tr onClick="showItem(0)" class='verlauf'><td></td><td>0</td><td></td><td>.:newItem:.</td><td></td></tr>
                                </tbody>
                        </table><br>
                        <div id="pager" class="pager" style='position:absolute;'>
                            <form name="ksearch" onSubmit="false ks();"> &nbsp;
                                <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/first.png" class="first">
                                <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/prev.png" class="prev">
                                <input type="text" id='suchwort' name="suchwort" size="20"><input type="hidden" name="Q" value="{Q}">
                                <button id='ks' name='ks'>.:search:.</button>
                                <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/next.png" class="next">
                                <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/last.png" class="last">
                                <select class="pagesize" id='pagesize'>
                                    <option value="10">10</option>
                                    <option value="15" selected="selected">15</option>
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
{TOOLS}
</body>
</html>
