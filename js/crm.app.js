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
				getCVPA(ui.item.src, ui.item.id);
	        }
	    });
	});

	function getCVPA( src, id ){
		$.ajax({
			url: 'crm/ajax/crm.app.php',
			type: 'POST',
			data:  { action: 'getCVPA', data: { 'src': src, 'id': id } },
			success: function(data){
				showCVPA(data);
			},
			error: function(xhr, status, error){
				$('#message-dialog').showMessageDialog('error', kivi.t8('Connection to the server'), kivi.t8('Error: The server could not process the request!'), xhr.responseText);
	        }
		});
	}


    $('#message-dialog').dialog({
		autoOpen: false,
		resizable: false,
		width: 'auto',
		height: 'auto',
		modal: true,
		position: { my: "top", at: "top+250" },
		open: function(){
			$(this).css('maxWidth', window.innerWidth);
		},
		buttons:[{
			text: 'Ok',
			click: function(){
				$('#message-dialog-text').html('');
				$('#message-dialog-debug').html('');
				$('#message-dialog-error').hide();
				$(this).parent().removeClass('ui-state-error');
				$(this).parent().removeClass('ui-state-success');
	            $(this).dialog("close");
			}
		}]
	});

	$.fn.showMessageDialog = function(style, title, message, debug=null){
		$(this).dialog('option', 'title', title).dialog('open').parent().addClass('ui-state-' + style);
		if('error' === style) $('#message-dialog-error').show();
		$('#message-dialog-text').html(message);
		if(null != debug) $('#message-dialog-debug').html('<pre>' + debug + '</pre>').css('display', '');
		$(this).resize();
	}

	function showCVPA(data){
		if(data.cv){
			$('#crm-wx-contact').show();
			$.each(data.cv, function(key, value){
				if(value){
					$('#crm-contact-' + key).html(value);
					$('#crm-contact-' + key).show();
				}
				else{
					$('#crm-contact-' + key).hide();
				}
			});

			if(data.cv.phone1) $('#crm-wx-contact-phone1').show();
			else $('#crm-wx-contact-phone1').hide();
			if(data.cv.phone2) $('#crm-wx-contact-phone2').show();
			else $('#crm-wx-contact-phone2').hide();
			if(data.cv.phone1) $('#crm-wx-contact-email').show();
			else $('#crm-wx-contact-email').hide();
		}
		else{
			$('#crm-wx-contact').hide();
		}

		$('#crm-cars-table').html('');
		if(data.cars){
			let listrow0 = false;
			$.each(data.cars, function(key, value){
				$('#crm-cars-table').append('<tr class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '"><td>' +  value.ln + '</td><td>' + value.manuf  + '</td><td>' + value.ctype  + '</td><td>' + value.cart + '</td></tr>');
			});
			$('#crm-wx-cars').show();
		}

		$('#crm-offers-table').html('');
		if(data.off){
			let listrow0 = false;
			$.each(data.off, function(key, value){
				$('#crm-offers-table').append('<tr id="' + value.id +'" class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.amount  + '</td><td>' + value.number + '</td></tr>');
			});
		}

		$('#crm-orders-table').html('');
		if(data.ord){
			let listrow0 = false;
			$.each(data.ord, function(key, value){
				$('#crm-orders-table').append('<tr id="' + value.id +'" class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.amount  + '</td><td>' + value.number + '</td></tr>');
			});
		}

		$('#crm-deliveries-table').html('');
		if(data.del){
			let listrow0 = false;
			$.each(data.del, function(key, value){
				$('#crm-deliveries-table').append('<tr id="' + value.id +'" class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.deldate  + '</td><td>' + value.donumber + '</td></tr>');
			});
		}

		$('#crm-invoices-table').html('');
		if(data.inv){
			let listrow0 = false;
			$.each(data.inv, function(key, value){
				$('#crm-invoices-table').append('<tr id="' + value.id +'" class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.amount  + '</td><td>' + value.number + '</td></tr>');
			});
		}
	}

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

