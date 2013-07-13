<script language="JavaScript">
	<!--
	function showK (id,tbl) {
		{no}
		uri="firma2.php?Q="+tbl+"&id=" + id;
		location.href=uri;
	}
	function showK__ (id) {
		{no}
		uri="kontakt.php?id=" + id;
		location.href=uri;
	}
	//-->
	</script>
<script>
	$(function() {
		$("#treffer")
			.tablesorter({widthFixed: true, widgets: ['zebra']})
			.tablesorterPager({container: $("#pager"), size: 20});

        $( "#sercontent" ).dialog({
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
        $( "input[type=button]" ).button();
        $( "#modify_search_pers" ).click(function() {
            //alert( "Modify Serr´arch ");
            $( "#suchfelder_pers").show();
            $( "#results_pers").hide();
            $( "#name_pers" ).focus();
        });
        $( "#butetikett" ).click(function() {
            $( "#sercontent" ).dialog( "option", "maxWidth", 400 );
            $( "#sercontent" ).dialog( "open" );
            $( "#sercontent" ).dialog( { title: "Etiketten" } );
            $( "#sercontent" ).load("etiketten.php?src=P");
        });
        $( "#butvcard" ).click(function() {
            $( "#sercontent" ).dialog( "option", "maxWidth", 400 );
            $( "#sercontent" ).dialog( "open" );
            $( "#sercontent" ).dialog( { title: "V-Cards" } );
            $( "#sercontent" ).load("servcard.php?src=P");
        });
        $( "#butbrief" ).click(function() {
            $( "#sercontent" ).dialog( "option", "minWidth", 600 );
            $( "#sercontent" ).dialog( "open" );
            $( "#sercontent" ).dialog( { title: "Serienbrief" } );
            $( "#sercontent" ).load("serdoc.php?src=P");
        });
    });
</script>
    

<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">.:search result:. .:Contacts:.</p>
<table id="treffer" class="tablesorter" width="90%">  
    <thead>
		<tr>
			<th>Name</th>
			<th>Plz</th>
			<th>Ort</th>
			<th>Telefon</th>
			<th>E-Mail</th>
			<th>Firma</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
<!-- BEGIN Liste -->
	<tr onClick='{js}'>
		<td>{Name}</td><td>&nbsp;{Plz}</td><td>{Ort}</td><td>&nbsp;{Telefon}</td><td>&nbsp;{eMail}</td><td>&nbsp;{Firma}</td><td>&nbsp;{insk}</td></tr>
<!-- END Liste -->
   </tbody>
</table>
<span id="pager" class="pager">
	<form>
		<img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/first.png" class="first"/>
		<img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/prev.png" class="prev"/>
		<input type="text" class="pagedisplay"/>
		<img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/next.png" class="next"/>
		<img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/last.png" class="last"/>
		<select class="pagesize" id='pagesize'>
			<option value="10">10</option>
			<option value="20" selected>20</option>
			<option value="30">30</option>
			<option value="40">40</option>
		</select>
    <input type="button" name="modify_search" id="modify_search_pers" value=".:modify search:." >&nbsp;
	<input type="button" name="etikett" id="butetikett" value=".:label:." >&nbsp;
	<input type="button" name="brief"   id="butbrief"   value=".:serdoc:." >&nbsp;
	<input type="button" name="vcard"   id="butvcard"   value=".:servcard:." >&nbsp;
	<a href="sermail.php"><input type="button" name="email" value=".:sermail:."></a>
	</form>
</span>
<div id="sercontent"></div>
    