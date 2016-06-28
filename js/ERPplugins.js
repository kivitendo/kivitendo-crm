$(document).ready(function() {
    //Rechnungen in Fancybox anzeigen
    var getUrl = window.location;
    if (!getUrl.toString().match('LoginScreen') && !getUrl.toString().match('Admin') ){ //Plugins nicht bei login und Admin anzeigen
       if (kivi.myconfig.global_conf != undefined) {
            $('body')
            .append("<script type='text/javascript' src='crm/js/jquery.postitall.js'></script>")
            .append("<script type='text/javascript' src='crm/js/jquery.postitall.ajax.js'></script>");
        }
        $('body')
        .append("<script type='text/javascript' src='crm/js/tools.js'></script>")
        .append("<script type='text/javascript' src='crm/jquery-plugins/jquery-calculator/jquery.plugin.js'></script>")
        .append("<script type='text/javascript' src='crm/jquery-plugins/jquery-calculator/jquery.calculator.js'></script>")
        .append("<script type='text/javascript' src='crm/nodejs/node_modules/jquery-minicolors/jquery.minicolors.min.js'></script>")
        .append("<script type='text/javascript' src='crm/jquery-plugins/fancybox/source/jquery.fancybox.pack.js'></script>")
        .append("<link rel='stylesheet' type='text/css' href='crm/jquery-plugins/fancybox/source/jquery.fancybox.css'>")
        .append("<link rel='stylesheet' type='text/css' href='crm/nodejs/node_modules/postitall/dist/jquery.postitall.css'>")
        .append("<link rel='stylesheet' type='text/css' href='crm/nodejs/node_modules/trumbowyg/dist/ui/trumbowyg.css'>")
        .append("<link rel='stylesheet' type='text/css' href='crm/jquery-plugins/jquery-calculator/jquery.calculator.css'>")
        .append("<div class='fancybox' data-fancybox-type='iframe' href='\'></div>")
        .append("<div class='pluginDialog'></div>");
        if( kivi.myconfig.countrycode == 'de' )  $('body').append("<script type='text/javascript' src='crm/jquery-plugins/jquery-calculator/jquery.calculator-de.js'></script>");

        var pluginDialog = $( '.pluginDialog' ).dialog({
            autoOpen: false,
            buttons: [{
                text: "Ok",
                click: function() {
                    $( this ).dialog( "close" );

                }
            }],
         })
        // Rechnungen anzeigen, beim Doppelklick auf die Rechnungsnummer
        $(".fancybox").fancybox();
        $( "input[name='invnumber']" ).dblclick( function(){
            var vendor_id = $("input[name='vendor_id']").val();
            var invoice_no = $("input[name='invnumber']").val();
            var data = '{"vendor_id":'+vendor_id+',"invoice_no":"'+invoice_no+'"}';
            //alert(data);
            $.ajax({
                dataType: "json",
                url: "crm/ajax/ajaxErpPlugins.php?action=showInvo&data=" + data,
                method: "GET",
                success : function (data){
                      $(".fancybox").attr("href", data.link);
                      $(".fancybox").trigger('click');
                      $(".fancybox").empty();
                      $(".fancybox").attr("href", "");
                },
                error: function(){
                    pluginDialog.dialog({
                        title: 'Show Invoice',
                        show: true
                    });
                    $(".pluginDialog").html('Invoice is not in Folder');
                    pluginDialog.dialog( 'open' );
                }
            });
        });
        $( "h1:first" ).addClass( "tools" );
        $( "h1:first" ).css({
            marginTop: "20px",
            height: "20px"
        });
        var cust_vend_id;
        var cust_vend_tmp;
        if ($("input[name='customer_id']").val()) {
            cust_vend_tmp = "C";
            cust_vend_id = $("input[name='customer_id']").val();
        }
        else {
            cust_vend_tmp = "V";
            cust_vend_id = $("input[name='vendor_id']").val();
        }
        $("<input style='margin-right: 5px;' class='submit' type='button' name='crm' id='crm' value='CRM' onClick=\"window.location.href='crm/firma1.php?Q="+ cust_vend_tmp +"&id="+ cust_vend_id +"'\">" ).insertBefore( "#update_button" );
    }//endif
});
