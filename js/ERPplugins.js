$(document).ready(function() {
    var getUrl = window.location;
    if( !getUrl.toString().match( 'LoginScreen' ) && !getUrl.toString().match( 'Admin' ) ){ //Plugins nicht bei login und Admin anzeigen
        var yesterdayButton = true; // enable or disable yesterday-Button
        var fancyBox = true;        // enable or disable Fancybox
        var crmButton = true;       // enable or disable CRM-Button
        var ecTerminal = true;      //send amount to a terminal
        var postButton = true;      //postButton show/hide

        var kivi_global = jQuery.parseJSON( kivi.myconfig.global_conf );
        language = kivi.myconfig.countrycode;

        $('body')
            .append("<script type='text/javascript' src='crm/js/jquery.postitall.js'></script>")
            .append("<script type='text/javascript' src='crm/js/jquery.postitall.ajax.js'></script>")
            .append("<script type='text/javascript' src='crm/js/tools.js'></script>")
            .append("<script type='text/javascript' src='crm/jquery-plugins/jquery-calculator/jquery.plugin.js'></script>")
            .append("<script type='text/javascript' src='crm/jquery-plugins/jquery-calculator/jquery.calculator.js'></script>")
            .append("<script type='text/javascript' src='crm/nodejs/node_modules/jquery-minicolors/jquery.minicolors.min.js'></script>")
            .append("<script type='text/javascript' src='crm/jquery-plugins/fancybox/source/jquery.fancybox.pack.js'></script>")
            .append("<script type='text/javascript' src='crm/js/locale/" + language + ".js'></script>")
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
        if( fancyBox ){
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
                      //alert(data);
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
        }
        $( "h1:first" ).addClass( "tools" );
        $( "h1:first" ).css({
            marginTop: "20px",
            height: "20px"
        });
        var cust_vend_id;
        var cust_vend_tmp;
        if( $("input[name='customer_id']").val() ){
            cust_vend_tmp = "C";
            cust_vend_id = $("input[name='customer_id']").val();
        }
        else {
            cust_vend_tmp = "V";
            cust_vend_id = $("input[name='vendor_id']").val();
        }
        var kivi_global = jQuery.parseJSON( kivi.myconfig.global_conf );
        $('#message').val('Mit freundlichen Grüßen\n\n' + kivi_global.mandant);

        $.urlParam = function( name ){
          var results = new RegExp( '[\?&]' + name + '=([^&#]*)' ).exec( window.location.href );
          if( results == null );// alert( 'Parameter: "' + name + '" does not exist in "' + window.location.href + '"!' );
          else return decodeURIComponent( results[1] || 0 );
        }

        //ToDo: better read from a document
        var customer_id = $.urlParam( 'order.customer_id' );
        var cust_vend_type = 'C';
        if( typeof customer_id === "undefined" ) {
           customer_id = $.urlParam( 'order.vendor_id' )
           cust_vend_type = 'V';
        }if( typeof customer_id === "undefined" ){
          customer_id = $( '#previous_customer_id' ).val()
          cust_vend_type = 'C';
        }

        //postButton
        if( ( getUrl.toString().match("ap.pl") || getUrl.toString().match("ar.pl") || getUrl.toString().match("gl.pl") || getUrl.toString().match("is.pl") ) && postButton ){
          $("<input type='button' id='post_btn' value='" +  kivi.t8( 'Post') + "' style='margin-left: 10px; color: black;'>").appendTo( "#ui-tabs-basic-data" );
          $('#post_btn').click(function () {
            $( '.layout-actionbar-submit' ).filter( function( index ) { return $(this).text() === "Buchen" || $(this).text() === "Post" } ).trigger( 'click' );
          });
        }

        //CRM button in oe, is, do
        if( ( getUrl.toString().match("oe.pl" ) || getUrl.toString().match("is.pl") || getUrl.toString().match("do.pl") || getUrl.toString().match("type=sales_order&action=Order" ) ) && crmButton ){
            $("<input style='margin-left: 10px; height: 24px;' class='submit' type='button' name='crm' id='crm' value='CRM' onClick=\"window.location.href='crm/firma1.php?Q="+ cust_vend_type +"&id="+ customer_id+"'\">" ).appendTo( ".layout-actionbar" );
        }

        //EC-Terminal
        if( getUrl.toString().match("is.pl") && ecTerminal ){
          $( '.layout-actionbar-submit:contains("Drucken")' ).click( function(){
            $.ajax({
              url: 'crm/ajax/ecTerminalData.php',
              type: "POST",
              data: { 'action': 'getTerminalCustomerData', 'data': $( '#customer_id' ).val() },
              success: function( res ){
                var ip = res['ec_terminal_ip-adress'];
                var port = res['ec_terminal_port'];
                var passwd = res['ec_terminal_passwd'];
                var name = res['name'];
                $.ajax({
                  url: 'crm/ajax/ecTerminal.py',
                  type: "post",
                  timeout: 100,
                  data: { 'action':'pay', 'ip': ip,'port': port, 'passwd': passwd, 'amount': $( "th:contains('Summe')" ).parent().find( "td" ).text().replace( '.', '' ).replace( ',', '' ), 'name': name }
                });
              }
            })
          });
        } //endif

        // "Yesterday"-Button in is.pl
        if( getUrl.toString().match( 'is.pl' ) && yesterdayButton ){
            var dpLast = $( '[id^=datepaid_]:last' );
            var positionDpLast = dpLast.position() || 0;
            $( '<form><button id="yButton"></button></form>' ).insertBefore( dpLast ).css({ left: positionDpLast.left + 10, position:'absolute'}) ;
            $( '#yButton' ).html( kivi.t8( 'Yesterday' ) ).click( function(){
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
                if( day < 10 ) day = "0" + day;
                var month = date0.getMonth() + 1
                if( month < 10 ) month = "0" + month;
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
    }//endif

});
