<script type="text/javascript" src="{CRMPATH}js/tablesorter.js"></script>
<script language="JavaScript">
$(document).ready(function() {
    $("#sercontent_pers").dialog({
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
    $("#modify_search_pers").button().click(function() {
        $("#suchfelder_pers").show();
        $("#results_pers").hide();
        $("#name_pers").focus();
        return false;
    });
    $("#butetikett_pers").button().click(function() {
        $("#sercontent_pers").dialog("option", "maxWidth", 400);
        $("#sercontent_pers").dialog("open");
        $("#sercontent_pers").dialog({
            title: "Etiketten"
        });
        $("#sercontent_pers").load("etiketten.php?src=P");
        return false;
    });
    $("#butvcard_pers").button().click(function() {
        $("#sercontent_pers").dialog("option", "maxWidth", 400);
        $("#sercontent_pers").dialog("open");
        $("#sercontent_pers").dialog({
            title: "V-Cards"
        });
        $("#sercontent_pers").load("servcard.php?src=P");
        return false;
    });
    $("#butbrief_pers").button().click(function() {
        $("#sercontent_pers").dialog("option", "minWidth", 600);
        $("#sercontent_pers").dialog("open");
        $("#sercontent_pers").dialog({
            title: "Serienbrief"
        });
        $("#sercontent_pers").load("serdoc.php?src=P");
        return false;
    });
    $("#email_pers").button().click(function() {
        $( "#sercontent_pers" ).dialog( "option", "minWidth", 800 );
        $( "#sercontent_pers" ).dialog( "open" ).html(
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
        $( "#sercontent_pers" ).dialog({
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
        return false;
    });
});
</script>


<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">.:search result:. .:Contacts:.</p>
<table id="treffer_pers" class="tablesorter">
    <thead>
        <tr>
            <th>.:name:.</th>
            <th>.:zipcode:.</th>
            <th>.:city:.</th>
            <th>.:phone:.</th>
            <th>.:email:.</th>
            <th>.:company:.</th>
            <th></th>
        </tr>
    </thead>
    <tbody style='cursor:pointer'>
<!-- BEGIN Liste -->
    <tr onClick='{js}'>
        <td>{Name}</td><td>&nbsp;{Plz}</td><td>{Ort}</td><td>&nbsp;{Telefon}</td><td>&nbsp;{eMail}</td><td>&nbsp;{Firma}</td><td>&nbsp;{insk}</td></tr>
<!-- END Liste -->
   </tbody>
</table>
<span id="pager_pers" class="pager">
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
    <button id="modify_search_pers"  >.:modify search:.</button>&nbsp;
    <button id="butetikett_pers" >.:label:.</button>&nbsp;
    <button id="butbrief_pers" >.:serdoc:.</button> &nbsp;
    <button id="butvcard_pers" >.:servcard:.</button>&nbsp;
    <button id="email_pers" >.:sermail:.</button>
    </form>
</span>
<div id="sercontent_pers" class="tablesorter">
