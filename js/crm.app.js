$(document).ready(function()
{
	$( '#crm-tabs-main' ).tabs();
	$( '#crm-tabs-infos' ).tabs();

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

	function getCustomer(id) {
		
	}


    $('#message-dialog').dialog( {
		autoOpen: false,
		resizable: false,
		width: 'auto',
		height: 'auto',
		modal: true,
		position: { my: "top", at: "top+250" },
		open: function()
		{
			$(this).css('maxWidth', window.innerWidth);
		},
		buttons:
		[{
			text: 'Ok',
			click: function()
			{
				$('#message-dialog-text').html('');
				$('#message-dialog-debug').html('');
				$('#message-dialog-error').hide();
				$(this).parent().removeClass('ui-state-error');
				$(this).parent().removeClass('ui-state-success');
	            $(this).dialog("close");
			}
		}]
	});

	$.fn.showMessageDialog = function(style, title, message, debug=null) {
		$(this).dialog('option', 'title', title).dialog('open').parent().addClass('ui-state-' + style);
		if('error' === style) $('#message-dialog-error').css('display', '');
		$('#message-dialog-text').html(message);
		if(null != debug) $('#message-dialog-debug').html('<pre>' + debug + '</pre>').css('display', '');
		$(this).resize();
	}

	$('#message-dialog').showMessageDialog('error', kivi.t8('Connection to the server'), kivi.t8('The server could not process the request!'));

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

