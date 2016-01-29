$(document).ready(function() {
    //Rechnungen in Fancybox anzeigen
    var getUrl = window.location;
    if (!getUrl.toString().match('LoginScreen') && !getUrl.toString().match('Admin') ){
        if (kivi.myconfig.global_conf != undefined) {
            $('body').append("<script type=\'text/javascript\' src='\crm/js/jquery.postitall.js\'><\/script>");
            $('body').append("<script type=\'text/javascript\' src='\crm/js/jquery.postitall.ajax.js\'><\/script>");
        }
        $('body').append("<script type=\'text/javascript\' src='\crm/js/tools.js\'><\/script>");
        $('body').append("<script type=\'text/javascript\' src='\crm/jquery-plugins/jquery-calculator/jquery.plugin.js\'><\/script>");
        $('body').append("<script type=\'text/javascript\' src='\crm/jquery-plugins/jquery-calculator/jquery.calculator.js\'><\/script>");
        $('body').append("<script type=\'text/javascript\' src='\crm/jquery-plugins/jquery-calculator/jquery.calculator-de.js\'><\/script>");
        $('body').append("<script type=\'text/javascript\' src=\'crm/nodejs/node_modules/trumbowyg/dist/trumbowyg.min.js\'></script>");
        $('body').append("<script type=\'text/javascript\' src=\'crm/nodejs/node_modules/jquery-minicolors/jquery.minicolors.min.js\'></script>");
        $('body').append("<script type=\'text/javascript\' src='\crm/jquery-plugins/fancybox/source/jquery.fancybox.pack.js\'><\/script>");
        $('body').append("<link rel=\'stylesheet\' type=\'text/css\' href=\'crm/jquery-plugins/fancybox/source/jquery.fancybox.css\'>");
        $('body').append("<link rel=\'stylesheet\' type=\'text/css\' href=\'crm/nodejs/node_modules/postitall/dist/jquery.postitall.css\'>");
        $('body').append("<link rel=\'stylesheet\' type=\'text/css\' href=\'crm/nodejs/node_modules/trumbowyg/dist/ui/trumbowyg.css\'>");
        $('body').append("<link rel=\'stylesheet\' type=\'text/css\' href=\'crm/jquery-plugins/jquery-calculator/jquery.calculator.css\'>");
        $('body').append("<div class=\'fancybox\' data-fancybox-type=\'iframe\' href=\'\'></div>");
    // Rechnungen anzeigen, beim Doppeel auf die Rechnungsnummer
    $(".fancybox").fancybox();
    $( "input[name='invnumber']" ).dblclick( function(){
        var vendor_id = $("input[name='vendor_id']").val();
        var invoice_no = $("input[name='invnumber']").val();
        var data = '{"vendor_id":'+vendor_id+',"invoice_no":"'+invoice_no+'"}';
        //alert(data); 
        $.ajax({
            dataType: "json",
            url: "crm/jqhelp/showInvoice.php?action=showInvo&data=" + data,
            method: "GET",
            success : function (data){           
                  $(".fancybox").attr("href", data.link);
                  $(".fancybox").trigger('click');
                  $(".fancybox").empty();
                  $(".fancybox").attr("href", "");
            },
            error: function(){
                  alert('Keine Rechnung gefunden!');
            }
       });   
    });
    $("h1:first").addClass( "tools" );
    $( "h1:first" ).css({
        marginTop: "20px",
        height: "20px"
    });
    }
});
