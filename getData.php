<?php
ob_start();
    require_once("inc/stdLib.php");
    include("inc/crmLib.php");
    $menu = $_SESSION['menu'];
    //$head = mkHeader();
?>
<html>
<head><title></title>
<?php
echo $menu['stylesheets'];
echo $menu['javascripts'];
echo $head['CRMCSS'];
echo $head['JQTABLE'];
echo $head['THEME'];

?>
    <script language="JavaScript">
        $.urlParam = function( name ){
            var results = new RegExp( '[\?&]' + name + '=([^&#]*)' ).exec( window.location.href );
            if( results == null ) return//alert( 'Parameter: "' + name + '" does not exist in "' + window.location.href + '"!' );
            else return decodeURIComponent( results[1] || 0 );
        }
        var number = $.urlParam( 'number' )
        function chgTab() {
            first = false;
        };
        function showD( src, id, number ) {
           if      ( src=="C" ) uri = number ? "firmen3.php?edit=1&number=" + number + "&Q=C&id=" + id : "firma1.php?Q=C&id=" + id;
           else if ( src=="V" ) uri = number ? "firmen3.php?edit=1&number=" + number + "&Q=V&id=" + id : "firma1.php?Q=V&id=" + id;
           else if ( src=="E" ) uri = "user1.php?id=" + id;
           else if ( src=="K" ) uri = number ? "personen3.php?&edit=1&number=" + number + "&id=" + id : "kontakt.php?id=" + id;
           window.location.href = uri;
        }
        function showItem(id,Q,FID) {
            F1=open("<?php echo $_SESSION["baseurl"]; ?>crm/getCall.php?Q="+Q+"&fid="+FID+"&hole="+id,"Caller","width=670, height=600, left=100, top=50, scrollbars=yes");
        }
    </script>
<?php
echo '
    <script>
        $.widget("custom.catcomplete", $.ui.autocomplete, {
            _renderMenu: function(ul,items) {
                var that = this,
                currentCategory = "";
                $.each( items, function( index, item ) {
                    if ( item.category != currentCategory ) {
                        ul.append( "<li class=\'ui-autocomplete-category\'>" + item.category + "</li>" );
                        currentCategory = item.category;
                    }
                    that._renderItemData(ul,item);
                });
            }
        });
        $(function() {
            $("#ac0").catcomplete({
                source: "jqhelp/autocompletion.php?case=name",
                minLength: '.$_SESSION['feature_ac_minlength'].',
                delay: '.$_SESSION['feature_ac_delay'].',
                disabled:'.($_SESSION['feature_ac']?'false':'true').',
                select: function(e,ui) {
                    showD(ui.item.src,ui.item.id, number);
                }
            });

        });
    </script>
';
?>
<style>
    #jui_dropdown {
        height: 400px;
    }
    #jui_dropdown button {
        padding: 3px !important;
    }
    #jui_dropdown ul li {
        background: none;
        display: inline-block;
        list-style: none;
    }
    .drop_container {
        margin: 10px 10px 10px 10px ;
        display: inline-block;
    }
    .menu {
        position: absolute;
        width: 240px !important;
        margin-top: 3px !important;
    }
    .ui-autocomplete-category {
        font-weight: bold;
        padding: .2em .4em;
        margin: .8em 0 .2em;
        line-height: 1.5;
    }
</style>
<script>
$(document).ready( function(){
        //ToDo: Dialoge übersetzen!!!!!!
        $( "#dialog_no_sw" ).dialog({
            autoOpen: false,
            title : "Kein Suchbegriff eingegeben"
        }).html( "<p>Bitte geben Sie mindestens ein Zeichen ein.</p>" );
        $( "#dialog_viele" ).dialog({
            autoOpen: false,
            title : "Zu viele Suchergebnisse"
        }).html( "Die Anzahl der Suchergebnisse überschreitet das Listenlimit.</br>Bitte verändern Sie das Suchkriterium." );
        $( "#dialog_keine" ).dialog({
            autoOpen: false,
            title : "Nichts gefunden"
        }).html( "Dieser Suchbegriff ergibt kein Resultat.</br>Bitte verändern Sie das Suchkriterium." );
        $( "#tabs" ).tabs({
            active: <?php echo $_SESSION["searchtab"] - 1;?>
        });
        $( "#tabs" ).tabs({ select:chgTab });
        $("#results").css('height',300);

        $("#drop").selectmenu({
            //style: 'dropdown',
            select: function( event, data ) {

               showD( data.item.value.substr( 0, 1 ), data.item.value.substr( 1 ), number );

            }
        });


        $.ajax({
            url: "ajax/getData.php",
            type: "POST",
            data: { action: "getHistory" },
            success: function( data){
                $.each( data, function( index, itemData ){
                    var selected = !index ? "selected='selected'" : "";
                    $("<option value='" + itemData[2] + itemData[0] + "'" + selected + " >" + itemData[1] + "</option>").appendTo("#drop");
                 });
                 //$("#drop").selectmenu("refresh");
                 //$("#drop").selectmenu("destroy").selectmenu({ style: "dropdown" });
                 $( "#drop" ).selectmenu( "open" );

            },
            error: function(){
                alert( 'Error: getHistory() in ajax/getData.php' );
            }

        })
        $("#adress").button().click(function() {
            $.ajax({
                type: "POST",
                url: "jqhelp/getDataResult.php",
                data: "swort=" + $("#ac0").val() + "&submit=adress",
                success: function(res) {
                    $('#ac0').catcomplete('close');
                    $("#results").html(res).focus();
                }
            });
            return false;
        });
        $("#kontakt").button().click(function() {
            $.ajax({
                type: "POST",
                url: "jqhelp/getDataResult.php",
                data: "swort=" + $("#ac0").val() + "&submit=kontakt",
                success: function(res) {
                    $('#ac0').catcomplete('close');
                    $("#results").html(res);
                }
            });
         return false;
        });

        $("#suchfelder_C").load('jqhelp/getCompanies1.php?Q=C');
        $("#suchfelder_V").load('jqhelp/getCompanies1.php?Q=V');
        $("#suchfelder_P").load('jqhelp/getPersons1.php');
    });

</script>
</head>
<?php
echo '<body onload="$(\'#ac0\').focus().val(\''.(isset($_SESSION['swort'])?preg_replace("#[ ].*#",'',$_SESSION['swort']):"").'\').select();">';
echo $menu['pre_content'];
echo $menu['start_content'];
echo '
    <div id="dialog_no_sw"></div>
    <div id="dialog_viele"></div>
    <div id="dialog_keine"></div>
    <div id="tabs">
        <ul>
            <li><a href="#tab-1">Schnellsuche</a></li>
            <li><a href="#tab-2">Kundensuche</a></li>
            <li><a href="#tab-3">Lieferantensuche</a></li>
            <li><a href="#tab-4">Personensuche</a></li>
        </ul>
        <div id="tab-1">
            <p class="ui-state-highlight ui-corner-all tools" style="margin-top: 20px; padding: 0.6em;">'.translate('.:fast search customer/vendor/contacts and contact history:.','firma').'</p>
            <form name="suche" id="suche" action="" method="get">
                <input type="text" name="swort" size="25" id="ac0" autocomplete="off">
                <button id="adress"> '.translate('.:adress:.','firma').'</button>
                <button id="kontakt">'.translate('.:contact history:.','firma').'</button> <br>
                <span class="liste">'.translate('.:search keyword:.','firma').'</span>
            </form>


             <select name="drop" id="drop"></select>

            <div id="results" class="tablesorter"></div>
        </div>

        <div id="tab-2">
            <div id="suchfelder_C"></div>
            <div id="companyResults_C"></div>
        </div>
        <div id="tab-3">
            <div id="suchfelder_V"></div>
            <div id="companyResults_V"></div>
        </div>
        <div id="tab-4">
            <div id="suchfelder_P"></div>
            <div id="results_pers"></div>
        </div>
    </div>
';

echo $menu['end_content'];
echo $head['TOOLS'];
ob_end_flush();
?>
</body>
</html>