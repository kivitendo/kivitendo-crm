<script type="text/javascript" src="{CRMPATH}js/tablesorter.js"></script>
<script language="JavaScript">
    function showK (id) {
        if (id) {
            uri="firma1.php?Q={Q}&id=" + id;
            location.href=uri;
        }
    }
    $(document).ready(function() {
        $( "#modify_search_{Q}" ).button().click(function() {
            $( "#suchfelder_{Q}").show();
            $( "#companyResults_{Q}").hide();
             $(".tablesorter").trigger("refresh");
            $( "#name{Q}" ).focus();
            return false;
        });
        $( "#sercontent_{Q}" ).dialog({
            autoOpen: false,
            show: {
                effect: "blind",
                duration: 300
            },
            hide: {
                effect: "explode",
                duration: 300
            },
            //position: { my: "center top", at: "center", of: null }
        });

        $( "#butetikett_{Q}" ).button().click(function() {
            $( "#sercontent_{Q}" ).dialog( "option", "maxWidth", 400 );
            $( "#sercontent_{Q}" ).dialog( "open" );
            $( "#sercontent_{Q}" ).dialog( { title: "Etiketten" } );
            $( "#sercontent_{Q}" ).load("etiketten.php?src=F");
            return false;
        });
        $( "#butvcard_{Q}" ).button().click(function() {
            $( "#sercontent_{Q}" ).dialog( "option", "maxWidth", 400 );
            $( "#sercontent_{Q}" ).dialog( "open" );
            $( "#sercontent_{Q}" ).dialog( { title: "V-Cards" } );
            $( "#sercontent_{Q}" ).load("servcard.php?src=F");
            return false;
        });
        $( "#butbrief_{Q}" ).button().click(function() {
            $( "#sercontent_{Q}" ).dialog( "option", "minWidth", 600 );
            $( "#sercontent_{Q}" ).dialog( "open" );
            $( "#sercontent_{Q}" ).dialog( { title: "Serienbrief" } );
            $( "#sercontent_{Q}" ).load("serdoc.php?src=F");
            return false;
        });
        $( "#butsermail_{Q}" ).button().click(function() {
            $( "#sercontent_{Q}" ).dialog( "option", "minWidth", 800 );
            $( "#sercontent_{Q}" ).dialog( "open" ).html(
                '<center>' +
                    '<form id="mailform" name="mailform" enctype="multipart/form-data">' +
                    '<table>' +
                    '<INPUT TYPE="hidden" name="KontaktCC" value="{KontaktCC}">' +
                    '<tr>' +
                    	'<td width="60px"></td>' +
                    	'<td width="*x"></td>' +
                    	'<td width="*"></td>' +
                    '</tr>' +
                    '<tr>' +
                    	'<td>An:</td>' +
                    	'<td >Serienmail</td>' +
                    '</tr><tr>' +
                    	'<td>CC:</td>' +
                        '<td ><input type="text" name="CC" id="CC" value="{CC}" size="65" maxlength="125" tabindex="2"> <input type="button" name="scc" value="suchen" onClick="open(\'suchMail.php?name=\'+$(\'#CC\').val()+\'&adr=CC\',\'suche\',width=450,height=200,left=100,top=100);"></td>' +
                    '</tr><tr>' +
                    	'<td>Betreff:</td>' +
                    	'<td ><input type="text" id="Subject" name="Subject" size="67" maxlength="125" tabindex="3"></td>' +
                    '</tr><tr>' +
                    	'<td>Text:</td>' +
                    	'<td >' +
                    	'<textarea name="BodyText" id="BodyText" cols="91" rows="15" tabindex="4" />' +
                    	'</td>' +
                    '</tr><tr>' +
                    	'<td>Datei:</td>' +
                    	'<td><input type="file" name="Datei" size="55" maxlength="125"></td>' +
                    '</tr>' +
                    '</table>' +
                    '</form>' +
                    '<div id="sermailmsg" />' +
                '</center>'
            );
            $( "#sercontent_{Q}" ).dialog({
                autoOpen: false,
                modal: true,
                width:800,
                height:550,
                title: "Serienmail versenden",
                focus: function() {
                    $.ajax({
                        dataType: 'json',
                        url: 'ajax/getData.php?action=getMailSign',
                        method: "GET",
                        success: function ( data ) {
                            $('#BodyText').val(data);
                        }
                    });
                },
                buttons: [{
                    text: "Senden",
                    id: "ok",
                    name: "ok",
                    click: function(){
                        if($("#mailform #Subject").val() == "") {
                            alert("Kein Betreff");
                        }
                        else {
                            var formData = new FormData($('#mailform')[0]);
                            formData.append('action', 'sendSerienmail');
                            $.ajax({
                                url: 'ajax/getData.php',
                                type: 'POST',
                                dataType: 'html',
                                contentType: false,
                                data: formData,
                                processData:  false,
                                success: function ( data ) {
                                    $('#sermailmsg').html(data);
                                },
                                error: function() {
                                    alert("Senden fehlgeschlagen");
                                }
                            });

                            $(this).dialog("close");
                        }

                    }
                }]
            });
            //$( "#sercontent_{Q}" ).load("sermail.php?src=F");
            return false;
        });
        //$( "input[type=button]" ).button();

    });
</script>

<p class="ui-state-highlight ui-corner-all tools" style="margin-top: 20px; padding: 0.6em;">.:search result:. .:{FAART}:.</p>

<table id="treffer_{Q}" class="tablesorter">
    <thead>
        <tr>
            <th>Kd-Nr</th>
            <th>Name</th>
            <th>Plz</th>
            <th>Ort</th>
            <th>Strasse</th>
            <th>Telefon</th>
            <th>E-Mail</th>
            <th>.:obsolete:.</th>
        </tr>
    </thead>
    <tbody style='cursor:pointer'>
<!-- BEGIN Liste -->
    <tr onClick="showK({ID});">
        <td>{KdNr}</td><td>{Name}</td><td>{Plz}</td><td>{Ort}</td><td>{Strasse}</td><td>{Telefon}</td><td>{eMail}</td><td>{obsolete}</td></tr>
<!-- END Liste -->
    </tbody>
</table>
<span id="pager_{Q}" class="pager">
    <form>
        <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/first.png" class="first"/>
        <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/prev.png" class="prev"/>
        <input type="text" class="pagedisplay"/>
        <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/next.png" class="next"/>
        <img src="{CRMPATH}jquery-plugins/tablesorter-master/addons/pager/icons/last.png" class="last"/>
        <select class="pagesize">
            <option value="10">10</option>
            <option value="20" selected>20</option>
            <option value="30">30</option>
            <option value="40">40</option>
        </select>
    <button id="modify_search_{Q}" >.:modify search:.</button>&nbsp;
    <button id="butetikett_{Q}" >.:label:.</button>&nbsp;
    <button id="butbrief_{Q}" >.:serdoc:.</button>&nbsp;
    <button id="butvcard_{Q}" >.:servcard:.</button>&nbsp;
    <button id="butsermail_{Q}" >.:sermail:.</button>&nbsp;
    </form>
</span>
{report}
<div id="sercontent_{Q}">
<!-- Hier endet die Karte ------------------------------------------->
