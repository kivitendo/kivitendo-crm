    <script language="JavaScript">
	function showK (id) {
		if (id) {
			uri="firma1.php?Q={Q}&id=" + id;
			location.href=uri;
		}
	}
    $(document).ready(function() {
        $( "#modify_search{Q}" ).click(function() {
            $( "#suchfelder_{Q}").show();
            $( "#companyResults_{Q}").hide();
            $( "#name{Q}" ).focus();
        });
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

        $( "#butetikett" ).click(function() {
            $( "#sercontent" ).dialog( "option", "maxWidth", 400 );
            $( "#sercontent" ).dialog( "open" );
            $( "#sercontent" ).dialog( { title: "Etiketten" } );
            $( "#sercontent" ).load("etiketten.php?src=F");
        });
        $( "#butvcard" ).click(function() {
            $( "#sercontent" ).dialog( "option", "maxWidth", 400 );
            $( "#sercontent" ).dialog( "open" );
            $( "#sercontent" ).dialog( { title: "V-Cards" } );
            $( "#sercontent" ).load("servcard.php?src=F");
        });
        $( "#butbrief" ).click(function() {
            $( "#sercontent" ).dialog( "option", "minWidth", 600 );
            $( "#sercontent" ).dialog( "open" );
            $( "#sercontent" ).dialog( { title: "Serienbrief" } );
            $( "#sercontent" ).load("serdoc.php?src=F");
        });
        $( "#butsermail" ).click(function() {
            $( "#sercontent" ).dialog( "option", "minWidth", 800 );
            $( "#sercontent" ).dialog( "open" );
            $( "#sercontent" ).load("sermail.php?src=F");
        });
        $( "input[type=button]" ).button();
        $("#treffer{Q}")
            .tablesorter({widthFixed: true, widgets: ["zebra"]})
            .tablesorterPager({container: $("#pager"), size: 20, positionFixed: false});
    });
</script>

<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">.:search result:. .:{FAART}:.</p>

<table id="treffer{Q}" class="tablesorter" width="90%">  
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
	<tbody>
<!-- BEGIN Liste -->
    <tr onClick="showK({ID});">
		<td>{KdNr}</td><td>{Name}</td><td>{Plz}</td><td>{Ort}</td><td>{Strasse}</td><td>{Telefon}</td><td>{eMail}</td><td>{obsolete}</td></tr>
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
    <input type="button" name="modify_search" id="modify_search{Q}" value=".:modify search:." >&nbsp;
	<input type="button" name="etikett" id="butetikett" value=".:label:." >&nbsp;
	<input type="button" name="brief"   id="butbrief"   value=".:serdoc:." >&nbsp;
	<input type="button" name="vcard"   id="butvcard"   value=".:servcard:." >&nbsp;
	<a href="sermail.php"><input type="button" name="email" value=".:sermail:."></a>&nbsp;
	</form>
</span>
{report}
<!-- Hier endet die Karte ------------------------------------------->
<div id="sercontent"></div>
