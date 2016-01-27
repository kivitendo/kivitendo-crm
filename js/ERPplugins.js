document.write("<script type='text/javascript' src='crm/jquery-plugins/jquery-calculator/jquery.plugin.js'></script>");
document.write("<script type='text/javascript' src='crm/jquery-plugins/jquery-calculator/jquery.calculator.js'></script>");
document.write("<script type='text/javascript' src='crm/jquery-plugins/jquery-calculator/jquery.calculator-de.js'></script>");
document.write("<script type='text/javascript' src='crm/jquery-plugins/fancybox/source/jquery.fancybox.pack.js'></script>");
//document.write("<script type='text/javascript' src='crm/js/jquery.postitall.js'></script>");
//document.write("<script type='text/javascript' src='crm/nodejs/node_modules/trumbowyg/dist/trumbowyg.min.js'></script>");
//document.write("<script type='text/javascript' src='crm/nodejs/node_modules/jquery-minicolors/jquery.minicolors.min.js");
//document.write("<script type='text/javascript' src='crm/js/jquery.postitall.ajax.js");
document.write("<script type='text/javascript' src='crm/js/tools.js'></script>");

document.write("<link rel='stylesheet' type='text/css' href='crm/jquery-plugins/jquery-calculator/jquery.calculator.css'>");
document.write("<link rel='stylesheet' type='text/css' href='crm/nodejs/node_modules/trumbowyg/dist/ui/trumbowyg.css'>");

document.write("<link rel='stylesheet' type='text/css' href='crm/nodejs/node_modules/postitall/dist/jquery.postitall.css'>");
document.write("<link rel='stylesheet' type='text/css' href='crm/jquery-plugins/fancybox/source/jquery.fancybox.css'>");


document.write("<div class='fancybox' data-fancybox-type='iframe' href=''></div>");

$(document).ready(function() {
    //Rechnungen in Fancybox anzeigen
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
      
    $("h1:first").addClass( "ui-state-highlight ui-corner-all tools" );
    $( "h1:first" ).css({
        marginTop: "20px",
        height: "20px"
    });
});
