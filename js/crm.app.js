$(document).ready(function()
{
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
	    $("#crm-widget-quicksearch").catcomplete({
	        source: "crm/ajax/crm.app.php?action=fastSearch",
	        select: function(e,ui) {
	            alert(ui.item.src + "/" + ui.item.id);
	        }
	    });
	});

	$('#crm-wf-edit').click(function() {
		alert("Berabeiten!");
	});

	$('#crm-wf-offer').click(function() {
		alert("Angebot erstellen!");
	});

	$('#crm-wf-order').click(function() {
		alert("Auftrag erstellen!");
	});

	$('#crm-wf-bill').click(function() {
		alert("Rechnung erstellen!");
	});
});

