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

		$('#crm-wf-edit').attr('data-src', data.cv.src);
		$('#crm-wf-edit').attr('data-id', data.cv.id);
		crmDelAddr = [];
	}

	function crmInitForm( crmFormModel, container ){
		var tabledata = '';
		$.each( crmFormModel, function( i, item ){
			if( item.type == 'headline' ) tabledata += '<tr><td colspan="2"><b>' + kivi.t8( item.label ) + '</b></td></tr>';
			if( item.type == 'checkbox' ) tabledata += '<tr><td>' + kivi.t8( item.label ) + '</td><td><input type="checkbox" id="' + item.name + '" name="'+ item.name + '" value="true" title="' + kivi.t8( item.tooltip ) + '"></input></td></tr>';
			if( item.type == 'input' )    tabledata += '<tr><td>' + kivi.t8( item.label ) + '</td><td><input type="text" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '"></input></td></tr>';
			if( item.type == 'textarea' )    tabledata += '<tr><td>' + kivi.t8( item.label ) + '</td><td><textarea id="' + item.name + '" name="'+ item.name + '" cols="' + item.cols + '" rows="' + item.rows + '" title="' + kivi.t8( item.tooltip ) + '"></textarea></td></tr>';
			if( item.type == 'password' ) tabledata += '<tr><td>' + kivi.t8( item.label ) + '</td><td><input type="password" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '"></input></td></tr>';
			if( item.type == 'select' ){
				tabledata += '<tr><td>' + kivi.t8( item.label ) + '</td><td><select type="select" id="' + item.name + '" name="'+ item.name + '" title="' + kivi.t8( item.tooltip ) + '">';
				$.each( item.data, function( i, item ){ tabledata += '<option value="' + item + '">' + kivi.t8( item ) + '</option>'; } );
				tabledata += '</select></input></td></tr>';
			}
		})
		$( container + " > tbody" ).html( ' ' );
		$( container + " > tbody" ).append( tabledata );
	}

	var crmDelAddr = [];

	function crmGetCustomerForEdit( src, id ){
		$.ajax({
			url: 'crm/ajax/crm.app.php',
			type: 'POST',
			data:  { action: 'getCustomerForEdit', data: { 'src': src, 'id': id } },
			success: function(data){
				console.info(data);
				crmShowCustomerDialog();

				$('#billaddr-greetings').html('');
				$('#billaddr-greetings').append('<option value="">' + kivi.t8( "Salutation as below" ) + '</option>');
				for(let description of data.greetings) $('#billaddr-greetings').append('<option value="' + description.description + '">' + description.description + '</option>');

				$('#billaddr-business').html('');
				for(let business of data.business) $('#billaddr-business').append('<option value="' + business.id + '">' + business.name + '</option>');

				$('#billaddr-bland').html('');
				$('#billaddr-bland').append('<option value=""></option>');
				for(let bland of data.bundesland) $('#billaddr-bland').append('<option value="' + bland.id + '" data-country="' + bland.country  + '">' + bland.name + '</option>');
				$('#deladdr-shiptobland').html('');
				$('#deladdr-shiptobland').append('<option value=""></option>');
				for(let bland of data.bundesland) $('#deladdr-shiptobland').append('<option value="' + bland.id + '" data-country="' + bland.country  + '">' + bland.name + '</option>');

				$.each( data.cv, function( key, value ){
					if( value ){
						$('#billaddr-' + key).val(value);
					}
					else{
						$('#billaddr-' + key).val('');
					}
				});

				crmDelAddr = data.deladdr;
				if(crmDelAddr && crmDelAddr.length){
					$('#deladdr-list').html('');
					$('#deladdr-list').append('<option value=""></option>');
					for(let deladdr of crmDelAddr){
						$('#deladdr-list').append('<option value="' + deladdr.shipto_id + '">' + deladdr.shiptoname + '</option>');
					}
					$('#deladdr-list').change(function(){
						alert("Test");
					});

					$.each( crmDelAddr[0], function( key, value ){
						if( value ){
							$('#deladdr-' + key).val(value);
						}
						else{
							$('#deladdr-' + key).val('');
						}
					});
				}

//				for(let deladdr of data.deladdr)
//				{
//					$.each( deladdr, function( key, value ){
//						if( value ){
//							$('#deladdr-' + key).val(value);
//						}
//						else{
//							$('#deladdr-' + key).val('');
//						}
//					});
//				}

				$('#billaddr-business').val(data.cv.business_id);
				//$('#billaddr-bland').val(data.cv.bland);
				//$('#deladdr-shiptobland').val(data.cv.shiptobland);
			},
			error: function(xhr, status, error){
				$('#message-dialog').showMessageDialog('error', kivi.t8('Connection to the server'), kivi.t8('Error: The server could not process the request!'), xhr.responseText);
	        }
		});
	}

	function crmShowCustomerDialog( ){
		crmInitForm( billaddrFormModel, '#billaddr-form' );
		crmInitForm( deladdrFormModel, '#deladdr-form' );
		crmInitForm( banktaxFormModel, '#banktax-form' );
		crmInitForm( extraFormModel, '#extras-form' );
		crmInitForm( varsFormModel, '#vars-form' );
	    $('#crm-wx-customer-dialog').dialog({
			autoOpen: false,
			resizable: true,
			width: 'auto',
			height: 'auto',
			modal: true,
			title: kivi.t8('Edit customer'),
			position: { my: "top", at: "top+250" },
			open: function(){
				$(this).css('maxWidth', window.innerWidth);
			},
			buttons:[{
				text: kivi.t8('Take'),
				click: function(){
				    $(this).dialog("close");
				}	
			}, {
				text: kivi.t8('Delete'),
				click: function(){
				    $(this).dialog("close");
				}
			}, {
				text: kivi.t8('Cancel'),
				click: function(){
				    $(this).dialog("close");
				}
			}]
		}).dialog('open').resize();
	}

	$('#crm-wf-edit').click(function() {
		crmGetCustomerForEdit( $('#crm-wf-edit').attr('data-src'), $('#crm-wf-edit').attr('data-id') );
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

