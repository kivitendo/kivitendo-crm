<?php
ob_start();
    require_once("inc/stdLib.php");
    include("inc/crmLib.php");
    $menu = $_SESSION['menu'];
    $head = mkHeader();
?>
<html>
<head><title></title>
<?php
echo $menu['stylesheets'];
echo $menu['javascripts'];
echo $head['CRMCSS'];
echo $head['JQTABLE'];
echo $head['THEME'];
echo $head['JUI-DROPDOWN'];
?>
    <script language="JavaScript">
        var first = false;
        function chgTab() {
            first = false;
        };
        function showD (src,id) {
           if      (src=="C") { uri="firma1.php?Q=C&id=" + id }
           else if (src=="V") { uri="firma1.php?Q=V&id=" + id; }
           else if (src=="E") { uri="user1.php?id=" + id; }
           else if (src=="K") { uri="kontakt.php?id=" + id; }
           window.location.href=uri;
        }
        function showItem(id,Q,FID) {
            F1=open("<?php echo $_SESSION["baseurl"]; ?>crm/getCall.php?Q="+Q+"&fid="+FID+"&hole="+id,"Caller","width=670, height=600, left=100, top=50, scrollbars=yes");
        }
    </script>
<?php
//ToDo: Dialoge übersetzen!!!!!!
echo '
    <div id="dialog_no_sw" title="Kein Suchbegriff eingegeben">
        <p>Bitte geben Sie mindestens ein Zeichen ein.</p>
    </div>
    <div id="dialog_viele" title="Zu viele Suchergebnisse">
        <p>Die Anzahl der Suchergebnisse überschreitet das Listenlimit.</br>Bitte verändern Sie das Suchkriterium.</p>
    </div>
    <div id="dialog_keine" title="Nichts gefunden">
        <p>Dieser Suchbegriff ergibt kein Resultat.</br>Bitte verändern Sie das Suchkriterium.</p>
    </div>
    <style>
        .ui-autocomplete-category {
            font-weight: bold;
            padding: .2em .4em;
            margin: .8em 0 .2em;
            line-height: 1.5;
        }
    </style>
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
                    showD(ui.item.src,ui.item.id);
                }
            });

        });
        $("#dialog_no_sw,#dialog_viele,#dialog_keine").dialog({ autoOpen: false });

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
</style>
<script>
    $(function() {
        $( "#tabs" ).tabs({
            active: <?php echo $_SESSION["searchtab"] - 1;?>,
            beforeLoad: function( event, ui ) {
                //alert( $( "#tabs" ).tabs( "option", "active" )); für die Memfunction, in SESSION schreiben
                ui.jqXHR.error(function() {
                    ui.panel.html(".:Couldn't load this tab.:." );
                });
            }
        });
        var tabOpts = { select:chgTab };
        $( "#tabs" ).tabs(tabOpts);

        $("#results").css('height',300);

        $.ajax({
            url: "jqhelp/getHistory.php",
            context: $('#menu'),
            success: function(data) {
                $(this).html(data);
                $("#drop").jui_dropdown({
                    launcher_id: 'launcher',
                    launcher_container_id: 'launcher_container',
                    menu_id: 'menu',
                    containerClass: 'drop_container',
                    menuClass: 'menu',
                    launchOnMouseEnter:true,
                    onSelect: function(event, data) {
                        showD(data.id.substring(0,1), data.id.substring(1));
                    }
                });
            }
        });

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
    <div id="tabs">
        <ul>
            <li><a href="#tab-1">Schnellsuche</a></li>
            <li><a href="#tab-2">Kundensuche</a></li>
            <li><a href="#tab-3">Lieferantensuche</a></li>
            <li><a href="#tab-4">Personensuche</a></li>
        </ul>
        <div id="tab-1">
            <p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">'.translate('.:fast search customer/vendor/contacts and contact history:.','firma').'</p>
            <form name="suche" id="suche" action="" method="get">
                <input type="text" name="swort" size="25" id="ac0" autocomplete="off">
                <button id="adress"> '.translate('.:adress:.','firma').'</button>
                <button id="kontakt">'.translate('.:contact history:.','firma').'</button> <br>
                <span class="liste">'.translate('.:search keyword:.','firma').'</span>
            </form>
            <div id="drop">
                <div id="launcher_container">
                    <button id="launcher">'.translate('.:history tracking:.','firma').'</button>
                </div>
                <ul id="menu"> </ul>
            </div>
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

ob_end_flush();
?>
</body>
</html>
