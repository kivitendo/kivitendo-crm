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
            $( "#sercontent_{Q}" ).dialog( "open" );
            $( "#sercontent_{Q}" ).load("sermail.php?src=F");
            return false;
        });
        //$( "input[type=button]" ).button();
        $("#treffer_{Q}")
            .tablesorter({
		        theme : "jui",
		        //headerTemplate : "{content} {icon}", //Wird von der Templateengine zerstört
		        widgets : ["uitheme", "zebra"],
		        widgetOptions : {
		            zebra   : ["even", "odd"]
		        }
            }).tablesorterPager({
                container: $("#pager_{Q}"), 
                size: 20,
                positionFixed: false
            });
    });
</script>

<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">.:search result:. .:{FAART}:.</p>

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

