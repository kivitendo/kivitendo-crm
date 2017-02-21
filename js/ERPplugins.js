// translation/all.lng einbinden mit prepend(?)
//<script language="javascript" type="text/javascript" src="translation/all.lng"></script>

$(document).ready(function() {
    var yesterdayButton = true; // enable or disable yesterday-Button
    var fancyBox = true;        // enable or disable Fancybox
    var crmButton = true; // enable or disable CRM-Button
    var noTaxIncluded = true; // enable or disable Checkbox "MWst. inkl."

    language = kivi.myconfig.countrycode;
    $( ".lang" ).each( function(){
        var key = $( this ).attr( "data-lang" );
        if( $( this ).is( ":input" ) ) $( this ).attr( 'title',  typeof( langData[language][key] ) != 'undefined' ? langData[language][key] : 'LNG ERR'  );
            else $( this ).text( typeof( langData[language][key] ) != 'undefined' ? langData[language][key] : 'LNG ERR'  );
        });


    //Rechnungen in Fancybox anzeigen
    var getUrl = window.location;
    if (!getUrl.toString().match('LoginScreen') && !getUrl.toString().match('Admin') && fancyBox ){ //Plugins nicht bei login und Admin anzeigen
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
        var kivi_global = jQuery.parseJSON( kivi.myconfig.global_conf );
        $('#message').val('Mit freundlichen Grüßen\n\n' + kivi_global.mandant);
    }//endif

    //CRM Button in Order (experimental)
    if( getUrl.toString().match( 'action=Order' && crmButton ) ){
        var cust_vend_type =  $( '#order_customer_id_type' ).val() == 'customer' ? 'C' : 'V';
        var cust_vend_id   =  $( '#order_customer_id_type' ).val() == 'customer' ? $( '#order_customer_id' ).val() : $( '#order_vendor_id' ).val();
        $("<input style='margin-right: 5px;' class='submit' type='button' name='crm' id='crm' value='CRM' onClick=\"window.location.href='crm/firma1.php?Q="+ cust_vend_type +"&id="+ cust_vend_id +"'\">" ).insertAfter( "#action" );
        //$( '<input value="Save and Invoive" onclick="kivi.Order.save_and_invoice(1)" type="button">' ).insertAfter( "#crm" );
    }


    // "Yesterday"-Button
    if( getUrl.toString().match( 'is.pl' ) && yesterdayButton ){
       /* $(document).on('keydown', function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                $( '#update_button' ).click();
            };
        });*/
        var dpLast = $( '[id^=datepaid_]:last' );
        //Mach den Button zum Bild dann bleibt die Funktionalität erhalten.
        //Nun brauchst du nur noch das Bild eines beschrifteten Buttons in Abhängigkeit von der Sprache zu laden
        //$( '<img id="yButton" class="ui-datepicker-trigger" src="image/calendar.png">' ).insertBefore( dpLast );
        $( '<form><button id="yButton" data-lang="YESTERDAY" class="lang" style="margin-right: 5px">Gestern</button></form>' ).insertBefore( dpLast );
        $( '#yButton' ).click( function(){
            var token = /[.-/]/.exec( dpLast.val() );
            var today = dpLast.val().split( token );

            // new Date according to local date format
            switch( kivi.myconfig.dateformat ) {
                case 'mm/dd/yy':
                    fDate = today[2] + "/" + today[0] + "/" + today[1];
                    break;
                case 'dd/mm/yy':
                    var fDate = today[2] + "/" + today[1] + "/" + today[0];
                    break;
                case 'dd.mm.yy':
                    var fDate = today[2] + "/" + today[1] + "/" + today[0];
                    break;
                case 'yyyy-mm-dd':
                    fDate = today[0] + "/" + today[1] + "/" + today[2];
            }

            // building yesterday's date from milliseconds and extracting day, month, year
            var yesterday= new Date( fDate ).getTime() - 86400000;
            var date0 = new Date( yesterday );
            var day = date0.getDate();
            if (day < 10) {day = "0" + day};
                var month = date0.getMonth() + 1;
                if (month < 10) {month = "0" + month};
                var year = date0.getFullYear();

            // combining day, month, year according local date format and inserting into input field
            switch(kivi.myconfig.dateformat) {
                case 'mm/dd/yy':
                    dpLast.val( month + token + day + token + year );
                    break;
                case 'dd/mm/yy':
                    dpLast.val( day + token + month + token + year );
                    break;
                case 'dd.mm.yy':
                    dpLast.val( day + token + month + token + year );
                    break;
                case 'yyyy-mm-dd':
                    dpLast.val( year + token + month + token + day );
                default:
                    dpLast.val( year + token + month + token + day );
            }

            return false;
        });
    }

    if( getUrl.toString().match( 'gl.pl' ) && noTaxIncluded ) $( "#taxincluded" ).prop( "checked", false );

});
