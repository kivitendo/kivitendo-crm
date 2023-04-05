$(document).ready(function()
{
    $( '#crm-tabs-main' ).tabs();
    $( '#crm-tabs-infos' ).tabs();

    crmGetHistory();

    function crmGetHistory( src, id ){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'getHistory' },
            success: function(data){
                $( '#crm-history-list' ).html('');
                if(data){
                    for(let entry of data){
                        let id = 'crm-hist-entry-' + entry[2]  + entry[0];
                        $('#crm-history-list').append('<div class="layout-actionbar-action layout-actionbar-submit" data-src="' + entry[2] +'" data-id="' + entry[0] + '" id="' + id + '">' + entry[1] + '</div>');
                        $('#' + id).click(function(){
                            getCVPA($('#' + id).attr('data-src'), $('#' + id).attr('data-id'));
                        });
                    }
                    var histlist = $('#crm-hist-last').clone();
                    $('#crm-hist-last').replaceWith($('#crm-hist-last').clone());
                    $('#crm-hist-last').click(function(e){
                        getCVPA(data[0][2], data[0][0]);
                    });
                    getCVPA(data[0][2], data[0][0]);
                }
            },
            error: function(xhr, status, error){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'getHistory()', xhr.responseText );
            }
        });
    }

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
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'getCVPA', xhr.responseText );
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

        $('#crm-wx-title').html(kivi.t8('Detail view:') + ' ' + ((data.cv.src == 'C')? kivi.t8('Customer') : kivi.t8('Vendor') ));
        $('#crm-wf-edit').attr('data-src', data.cv.src);
        $('#crm-wf-edit').attr('data-id', data.cv.id);
        crmDelAddr = 0;
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

    function crmInitFormEx( crmFormModel, container, max_rows ){
        var tabledata = '';
//        $.each( crmFormModel, function( i, item ){
        if(max_rows > crmFormModel.length) max_rows = crmFormModel.length;
        for( let i = 0; i < max_rows; i++ ){
            let item = crmFormModel[i];
            tabledata += '<tr>';
            let addItem = function( item ){
                if( item.type == 'headline' ) tabledata += '<td colspan="2"><b>' + kivi.t8( item.label ) + '</b></td>';
                if( item.type == 'checkbox' ) tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><input type="checkbox" id="' + item.name + '" name="'+ item.name + '" value="true" title="' + kivi.t8( item.tooltip ) + '"></input></td>';
                if( item.type == 'input' ){
                    tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><input type="text" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '"></input>';
                    if(item.check) tabledata += '<input type="checkbox" id="' + item.check + '" name="'+ item.check + '" title="' + kivi.t8( 'Check imput' ) + '"></input>';
                    tabledata += '</td>';
                }
                if( item.type == 'textarea' ) tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><textarea id="' + item.name + '" name="'+ item.name + '" cols="' + item.cols + '" rows="' + item.rows + '" title="' + kivi.t8( item.tooltip ) + '"></textarea></td>';
                if( item.type == 'password' ) tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><input type="password" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '"></input></td>';
                if( item.type == 'select' ){
                    tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><select type="select" id="' + item.name + '" name="'+ item.name + '" title="' + kivi.t8( item.tooltip ) + '">';
                    $.each( item.data, function( i, item ){ tabledata += '<option value="' + item + '">' + kivi.t8( item ) + '</option>'; } );
                    tabledata += '</select></input></td>';
                }
                if( item.hasOwnProperty( 'info' ) ) tabledata += 'Info123';
            }
            addItem( item );
            if( max_rows + i < crmFormModel.length){
                    item = crmFormModel[i + max_rows]
                    addItem( item );
            }
            tabledata += '</tr>';
        }
  //      })
        $( container + " > tbody" ).html( ' ' );
        $( container + " > tbody" ).append( tabledata );
    }

    function crmChangeBlandList( list, country ){
        $( '#' + list ).html( '' );
        $( '#' + list ).append( '<option value=""></option>' );
        for( let bland of crmData.bundesland ){
            if( country && bland.country && !bland.country.startsWith( country ) ) continue;
            $( '#' + list ).append( '<option value="' + bland.id + '" data-country="' + bland.country  + '">' + bland.name + '</option>' );
        }
    }

    var crmData = 0;

    function crmGetCustomerForEdit( src, id ){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'getCustomerForEdit', data: { 'src': src, 'id': id } },
            success: function( data ){
                console.info( data );
                crmData = data;
                crmShowCustomerDialog();
                crmShowCustomerForEdit();

            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'getCustomerForEdit()', xhr.responseText );
            }
        });
    }

    function crmShowCustomerForEdit(){
         $( '#billaddr-greetings' ).html( '' );
         $( '#billaddr-greetings' ).append( '<option value="">' + kivi.t8( "Salutation as below" ) + '</option>' );
         for( let description of crmData.greetings ) $( '#billaddr-greetings' ).append( '<option value="' + description.description + '">' + description.description + '</option>' );

         $( '#billaddr-business' ).html( '' );
         for( let business of crmData.business ) $( '#billaddr-business' ).append( '<option value="' + business.id + '">' + business.name + '</option>' );

         $.each( crmData.cv, function( key, value ){
             if( value ){
                 $('#billaddr-' + key).val(value);
             }
             else{
                 $('#billaddr-' + key).val('');
             }
         });
         $('#billaddr-direct_debit').prop('checked', crmData.cv.direct_debit);

         if( crmData.deladdr ){
             $('#deladdr-list').html( '' );
             $('#deladdr-list').append( '<option value=""></option>' );
             for (let i = 0; i < crmData.deladdr.length; i++){
                 $( '#deladdr-list' ).append( '<option value="' + i + '">' + ( ( crmData.deladdr[i].shiptoname )? crmData.deladdr[i].shiptoname : '' ) + '</option>' );
             }
             $( '#deladdr-list' ).change( function(){
                  if( !$( this ).val() ){
                      for( let e of deladdrFormModel ){
                          if( e.name.startsWith( 'deladdr-shipto' ) ) $( '#' + e.name ).val( '' );
                      }
                      $( '#deladdr-shiptocountry' ).change();
                  }
                  else{
                      $.each( crmData.deladdr[$( this ).val()], function( key, value ){
                         if( value ){
                             $( '#deladdr-' + key ).val( value );
                         }
                         else{
                             $( '#deladdr-' + key ).val( '' );
                         }
                      });
                      $( '#deladdr-shiptocountry' ).change();
                      $( '#deladdr-shiptobland' ).val( crmData.deladdr[$( this ).val()].shiptobland  );
                  }
              });

             $( '#deladdr-list' ).change();
        }

        if( crmData.branches ){
            for( let branche of crmData.branches ){
                $( '#billaddr-branches' ).append( '<option value="' + branche.name + '">' + branche.name + '</option>' );
            }
            if( crmData.cv.branche ) $( '#billaddr-branches' ).val( crmData.cv.branche );
            $( '#billaddr-branche' ).val( '' );
        }

        if( crmData.employees ){
            for( let employee of crmData.employees ){
                $( '#billaddr-salesperson' ).append( '<option value="' + employee.id + '">' + employee.name + '</option>' );
            }
            if( crmData.cv.employee ) $( '#billaddr-salesperson' ).val( crmData.cv.employee );
        }

        if( crmData.payment_terms ){
            for( let payment_term of crmData.payment_terms ){
                $( '#billaddr-payment_terms' ).append( '<option value="' + payment_term.id + '">' + payment_term.description + '</option>' );
            }
            if( crmData.cv.payment_id ) $( '#billaddr-payment_terms' ).val( crmData.cv.payment_id );
        }

        if( crmData.tax_zones ){
            for( let tax_zone of crmData.tax_zones ){
                $( '#billaddr-tax_zone' ).append( '<option value="' + tax_zone.id + '">' + tax_zone.description + '</option>' );
            }
            if( crmData.cv.taxzone_id ) $( '#billaddr-tax_zone' ).val( crmData.cv.taxzone_id );
        }

        if( crmData.languages ){
            for( let lang of crmData.languages ){
                $( '#billaddr-lang' ).append( '<option value="' + lang.id + '">' + lang.description + '</option>' );
            }
            if( crmData.cv.language_id ) $( '#billaddr-lang' ).val( crmData.cv.language_id );
        }

        if( crmData.leads ){
            for( let lead of crmData.leads ){
                $( '#billaddr-leads' ).append( '<option value="' + lead.id + '">' + lead.lead + '</option>' );
            }
            if( crmData.cv.lead ) $( '#billaddr-leads' ).val( crmData.cv.lead );
        }

        if( crmData.vars_conf ){
            for( let var_conf of crmData.vars_conf ){
                if( var_conf.type ) {
                    switch( var_conf.type ){
                    case 'select':
                        if( var_conf.data ) {
                            var_conf.data = var_conf.data.split( '##' );
                            var_conf.data.unshift('');
                        }
                        break;
                    case 'text':
                        var_conf.type = 'input';
                        break;
                    case 'textfield':
                        var_conf.type = 'textarea';
                    }
                }
            }
            crmInitForm( crmData.vars_conf, '#vars-form' );
        }

        $( '#billaddr-business' ).val( crmData.cv.business_id );
        $( '#billaddr-country' ).change();
        $( '#billaddr-bland' ).val( crmData.cv.bland );
        $( '#deladdr-shiptocountry' ).change();
    }

    function crmShowCustomerDialog( ){
        crmInitForm( billaddrFormModel, '#billaddr-form' );
        crmInitForm( deladdrFormModel, '#deladdr-form' );
        crmInitForm( banktaxFormModel, '#banktax-form' );
        crmInitForm( extraFormModel, '#extras-form' );
        crmInitFormEx( carFormModel, '#car-form', 20 );

        $( '#billaddr-country' ).change(function(){
            crmChangeBlandList( 'billaddr-bland', $( '#billaddr-country' ).val() );
        });
        $( '#deladdr-shiptocountry' ).change(function(){
            crmChangeBlandList( 'deladdr-shiptobland', $( '#deladdr-shiptocountry' ).val() );
        });

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
