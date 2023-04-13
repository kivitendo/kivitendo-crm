$(document).ready(function()
{
    function exists( obj ){
        return obj !== null && obj !== undefined;
    }

    function isIterable( obj ){
        return exists( obj ) && obj.hasOwnProperty('length');
    }

    function isEmpty( obj ){
        return isIterable( obj ) && obj.length === 0;
    }

    $( '#crm-tabs-main' ).tabs();
    $( '#crm-tabs-infos' ).tabs();

    $( '#crm-wf-edit').html( kivi.t8( 'Edit' ) );
    $( '#crm-wf-scan').html( kivi.t8( 'Car from scan' ) );

    /* flag to switch app lxcars functionality  */
    var lxcars = false;
    crmGetLxcarsVer();
    crmGetHistory();

    /*********************************************
    * Check lxcars tables exists and then get
    * last version
    *********************************************/
    function crmGetLxcarsVer(){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'getLxcarsVer' },
            success: function( data ){
                    lxcars = data.lxcars;
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'lxcars()', xhr.responseText );
            }
        });
    }

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

        if( lxcars ){
            $('#crm-cars-table').html('');
            if( exists( data.cars) ){
                let listrow0 = false;
                $.each(data.cars, function(key, value){
                    $('#crm-cars-table').append('<tr class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '"><td>' +  value.ln + '</td><td>' + value.manuf  + '</td><td>' + value.ctype  + '</td><td>' + value.cart + '</td></tr>');
                });
                $('#crm-wx-cars').show();
            }
        }

        $('#crm-offers-table').html('');
        if( exists( data.off ) ){
            let listrow0 = false;
            $.each(data.off, function(key, value){
                $('#crm-offers-table').append('<tr id="' + value.id +'" class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.amount  + '</td><td>' + value.number + '</td></tr>');
            });
        }

        $('#crm-orders-table').html('');
        if( exists( data.ord ) ){
            let listrow0 = false;
            $.each(data.ord, function(key, value){
                $('#crm-orders-table').append('<tr id="' + value.id +'" class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.amount  + '</td><td>' + value.number + '</td></tr>');
            });
        }

        $('#crm-deliveries-table').html('');
        if( exists( data.del ) ){
            let listrow0 = false;
            $.each(data.del, function(key, value){
                $('#crm-deliveries-table').append('<tr id="' + value.id +'" class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.deldate  + '</td><td>' + value.donumber + '</td></tr>');
            });
        }

        $('#crm-invoices-table').html('');
        if( exists( data.inv) ){
            let listrow0 = false;
            $.each(data.inv, function(key, value){
                $('#crm-invoices-table').append('<tr id="' + value.id +'" class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.amount  + '</td><td>' + value.number + '</td></tr>');
            });
        }

        if( exists( data.cv ) ){
            $('#crm-wx-title').html(kivi.t8('Detail view:') + ' ' + ((data.cv.src == 'C')? kivi.t8('Customer') : kivi.t8('Vendor') ));
            $('#crm-wf-edit').attr('data-src', data.cv.src);
            $('#crm-wf-edit').attr('data-id', data.cv.id);
        }
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
        if(max_rows > crmFormModel.length) max_rows = crmFormModel.length;
        for( let i = 0; i < max_rows; i++ ){
            let item = crmFormModel[i];
            tabledata += '<tr>';
            let addItem = function( item ){
                if( item.hasOwnProperty( 'spacing' ) ) tabledata += '<td style="padding-left: 10px"> </td>';
                if( item.type == 'headline' ) tabledata += '<td colspan="2"><b>' + kivi.t8( item.label ) + '</b>';
                if( item.type == 'checkbox' ) tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><input type="checkbox" id="' + item.name + '" name="'+ item.name + '" value="true" title="' + kivi.t8( item.tooltip ) + '"></input>';
                if( item.type == 'input' ){
                    tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><input type="text" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '"></input>';
                    if(item.check) tabledata += '<input type="checkbox" id="' + item.check + '" name="'+ item.check + '" title="' + kivi.t8( 'Check imput' ) + '"></input>';
                }
                if( item.type == 'textarea' ) tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><textarea id="' + item.name + '" name="'+ item.name + '" cols="' + item.cols + '" rows="' + item.rows + '" title="' + kivi.t8( item.tooltip ) + '"></textarea>';
                if( item.type == 'password' ) tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><input type="password" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '"></input>';
                if( item.type == 'select' ){
                    tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><select type="select" id="' + item.name + '" name="'+ item.name + '" title="' + kivi.t8( item.tooltip ) + '">';
                    $.each( item.data, function( i, item ){ tabledata += '<option value="' + item + '">' + kivi.t8( item ) + '</option>'; } );
                    tabledata += '</select></input>';
                }
                if( item.hasOwnProperty( 'info' ) ){
                    tabledata += '<button id="' + item.info + '">' + kivi.t8( 'Info' ) + '</button>';
                }
                tabledata += '</td>';
            }
            addItem( item );
            if( max_rows + i < crmFormModel.length){
                    item = crmFormModel[i + max_rows]
                    item.spacing = true;
                    addItem( item );
            }
            tabledata += '</tr>';
        }
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

    var crmData = {};
    var lxcarsData = {};
    var dbUpdateData = {};

    function crmGetCustomerForEdit( src, id, new_car, fx ){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'getCustomerForEdit', data: { 'src': src, 'id': id } },
            success: function( data ){
                crmData = data;
                crmShowCustomerDialog( new_car );
                crmShowCustomerForEdit();
                if( fx ) fx( src, id );
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'getCustomerForEdit()', xhr.responseText );
            }
        });
    }

    function crmShowCustomerForEdit(){
        $( '#billaddr-greetings' ).html( '' );
        $( '#billaddr-greetings' ).append( '<option value="">' + kivi.t8( "Salutation as below" ) + '</option>' );
        if( isIterable( crmData.greetings ) ){
            for( let description of crmData.greetings ) $( '#billaddr-greetings' ).append( '<option value="' + description.description + '">' + description.description + '</option>' );
        }

        $( '#billaddr-business' ).html( '' );
        for( let business of crmData.business ) $( '#billaddr-business' ).append( '<option value="' + business.id + '">' + business.name + '</option>' );

        if( exists( crmData.cv ) ){
            $.each( crmData.cv, function( key, value ){
                if( value ){
                    $( '#billaddr-' + key ).val( value );
                }
                else{
                    $( '#billaddr-' + key ).val( '' );
                }
            });
            $( '#billaddr-direct_debit' ).prop( 'checked', crmData.cv.direct_debit );
        }

        if( isIterable( crmData.deladdr ) ){
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

       if( exists( crmData.branches ) ){
           for( let branche of crmData.branches ){
               $( '#billaddr-branches' ).append( '<option value="' + branche.name + '">' + branche.name + '</option>' );
           }
           if( exists( crmData.cv ) && exists( crmData.cv.branche ) ) $( '#billaddr-branches' ).val( crmData.cv.branche );
           $( '#billaddr-branche' ).val( '' );
       }

       if( exists( crmData.employees ) ){
           for( let employee of crmData.employees ){
               $( '#billaddr-salesperson' ).append( '<option value="' + employee.id + '">' + employee.name + '</option>' );
           }
           if( exists( crmData.cv ) && exists( crmData.cv.employee ) ) $( '#billaddr-salesperson' ).val( crmData.cv.employee );
       }

       if( exists( crmData.payment_terms ) ){
           for( let payment_term of crmData.payment_terms ){
               $( '#billaddr-payment_terms' ).append( '<option value="' + payment_term.id + '">' + payment_term.description + '</option>' );
           }
           if( exists( crmData.cv ) && exists( crmData.cv.payment_id ) ) $( '#billaddr-payment_terms' ).val( crmData.cv.payment_id );
       }

       if( exists( crmData.tax_zones ) ){
           for( let tax_zone of crmData.tax_zones ){
               $( '#billaddr-tax_zone' ).append( '<option value="' + tax_zone.id + '">' + tax_zone.description + '</option>' );
           }
           if( exists( crmData.cv ) && exists( crmData.cv.taxzone_id ) ) $( '#billaddr-tax_zone' ).val( crmData.cv.taxzone_id );
       }

       if( exists( crmData.languages ) ){
           for( let lang of crmData.languages ){
               $( '#billaddr-lang' ).append( '<option value="' + lang.id + '">' + lang.description + '</option>' );
           }
           if( exists( crmData.cv ) && exists( crmData.cv.language_id ) ) $( '#billaddr-lang' ).val( crmData.cv.language_id );
       }

       if( exists( crmData.leads ) ){
           for( let lead of crmData.leads ){
               $( '#billaddr-leads' ).append( '<option value="' + lead.id + '">' + lead.lead + '</option>' );
           }
           if( exists( crmData.cv ) && exists( crmData.cv.lead ) ) $( '#billaddr-leads' ).val( crmData.cv.lead );
       }

       if( exists( crmData.vars_conf ) ){
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

        if( exists( crmData.cv ) ){
            $( '#billaddr-business' ).val( crmData.cv.business_id );
            $( '#billaddr-country' ).change();
            $( '#billaddr-bland' ).val( crmData.cv.bland );
            $( '#deladdr-shiptocountry' ).change();
        }
    }

    function crmShowCustomerDialog( new_with_car ){
        crmInitForm( billaddrFormModel, '#billaddr-form' );
        crmInitForm( deladdrFormModel, '#deladdr-form' );
        crmInitForm( banktaxFormModel, '#banktax-form' );
        crmInitForm( extraFormModel, '#extras-form' );

        if( new_with_car ){
            $( '#car-form' ).show();
            crmInitFormEx( carFormModel, '#car-form', 21 );
        }
        else{
            $( '#car-form' ).hide();
        }

        $( '#billaddr-country' ).change(function(){
            crmChangeBlandList( 'billaddr-bland', $( '#billaddr-country' ).val() );
        });
        $( '#deladdr-shiptocountry' ).change(function(){
            crmChangeBlandList( 'deladdr-shiptobland', $( '#deladdr-shiptocountry' ).val() );
        });

        $( '#crm-wx-customer-dialog' ).dialog({
            autoOpen: false,
            resizable: true,
            width: 'auto',
            height: 'auto',
            modal: true,
            title: kivi.t8('Edit customer'),
            position: { my: "top", at: "top+250" },
            open: function(){
                $( this ).css( 'maxWidth', window.innerWidth );
            },
            buttons:[{
                text: kivi.t8( 'Take' ),
                click: function(){
                    console.info( 'Take' );
                    dbUpdateData[ 'customer' ] = {};
                    for(let item of billaddrFormModel){
                        let columnName = item.name.split( '-' );
                        dbUpdateData[ 'customer' ][ columnName[ 1 ] ] = $( '#' + item.name ).val();
                    }
                    dbUpdateData[ 'shipto' ] = {};
                    for(let item of deladdrFormModel){
                        let columnName = item.name.split( '-' );
                        dbUpdateData[ 'shipto' ][ columnName[ 1 ] ] = $( '#' + item.name ).val();
                    }
                    for(let item of banktaxFormModel){
                        let columnName = item.name.split( '-' );
                        dbUpdateData[ 'customer' ][ columnName[ 1 ] ] = $( '#' + item.name ).val();
                    }
                    for(let item of extraFormModel){
                        let columnName = item.name.split( '-' );
                        dbUpdateData[ 'customer' ][ columnName[ 1 ] ] = $( '#' + item.name ).val();
                    }
                    dbUpdateData[ 'lxc_cars' ] = {};
                    for(let item of carFormModel){
                        let columnName = item.name.split( '-' );
                        dbUpdateData[ 'lxc_cars' ][ columnName[ 1 ] ] = $( '#' + item.name ).val();
                    }
                     console.info( dbUpdateData );
                    $( this ).dialog( "close" );
                }
            },{
                text: kivi.t8( 'Delete' ),
                click: function(){
                    $( this ).dialog( "close" );
                }
            },{
                text: kivi.t8( 'Cancel' ),
                click: function(){
                    $( this ).dialog( "close" );
                }
            }]
        }).dialog( 'open' ).resize();
    }

    function crmGetCarLicense( regNum ){
        let rn = ( isEmpty( regNum ) )? 0 : regNum.split( ' ' );
        if( isIterable( rn ) && rn.length > 1 ){
            let rs = '';
            rs = rn[0] + '-' + rn[1];
            for( let i = 2; i < rn.length; i++ ){
                rs += rn[i];
            }
            return rs;
        }
        return regNum;
    }

    function crmNewCarFromScan(){
     let fsmax = 24; // only for show
        //new car or new car and new customer
      $.ajax({
          url: 'crm/ajax/crm.app.php',
          data: { action: 'getScans', data:{ 'fsmax': fsmax } },
          type: "POST",
          success: function( data ){
            $( '#crm-fsscan-dlg' ).dialog({
                autoOpen: false,
                resizable: true,
                width: 'auto',
                height: 'auto',
                modal: true,
                title: kivi.t8( 'FS-Scan' ),
                position: { my: "top", at: "top+250" },
                open: function(){
                    $(this).css( 'maxWidth', window.innerWidth );
                },
                buttons:[{
                    text: kivi.t8( 'Close' ),
                    click: function(){
                        $(this).dialog( "close" );
                    }
                }]
            }).dialog( 'open' ).resize();

            var tableContent = '';
            let listrow0 = false;
            console.info( 'FromScan' );
            console.info( data );
            crmData = data;
            if( isIterable( data.db_scans ) ){
                data.db_scans.forEach( function( item ){
                    tableContent += '<tr class="' + ( ( listrow0 = !listrow0 )? "listrow0": "listrow1" ) + '" id="' + item.scan_id + '"><td style="text-align: right; padding-right: 15px;">' + item.myts + '</td><td>' + item.firstname + '</td><td>' + item.name1 + '</td><td>' + item.registrationnumber + '</td>';
                });
            }
            $( '#crm-fsscan-list' ).empty().append( tableContent );
            $( '#crm-fsscan-list tr' ).click( function(){
                $.ajax({
                    url: 'crm/ajax/crm.app.php',
                    data: { action: 'getFsData', data:{ 'id': this.id  } },
                    type: "POST",
                    success: function( data ){
                        lxcarsData = data;
                      //  alert(  data.firstname + ' ' + data.name1 + '\n' + data.address1 + '\n' + data.address2 + '\n\n' + data.registrationNumber + '\n' + data.hsn + '\n' + data.field_2_2 + '\n' + data.field_14_1 + '\n' + data.ez + '\n' + data.hu + '\n' + data.vin + ' ' + data.field_3 );
                        $( '#crm-fsscan-dlg' ).dialog( 'close' );
                        $( '#crm-fsscan-customer-dlg' ).dialog({
                            autoOpen: false,
                            resizable: true,
                            width: 'auto',
                            height: '600',
                            modal: true,
                            title: kivi.t8( 'Select customer' ),
                            position: { my: "top", at: "top+250" },
                            open: function(){
                                $( this ).css( 'maxWidth', window.innerWidth );
                            },
                            buttons:[{
                                text: kivi.t8( 'New' ),
                                click: function(){
                                        $.ajax({
                                            url: 'crm/ajax/crm.app.php',
                                            type: 'POST',
                                            data:  { action: 'getCVDialogData' },
                                            success: function( data ){
                                                 //console.info('New');
                                                //console.info(lxcarsData);
                                                crmData = data;
                                                $( '#crm-fsscan-customer-dlg' ).dialog( "close" );
                                                crmShowCustomerDialog( true );
                                                crmShowCustomerForEdit();
                                                $( '#billaddr-name' ).val( lxcarsData.firstname + ' ' + lxcarsData.name1 );
                                                $( '#billaddr-street' ).val( lxcarsData.address1 );
                                                const city = ( isEmpty( lxcarsData.adress2 ) )? 0 : lxcarsData.address2.split(' ');
                                                if( isIterable( city ) && city.length > 1 ){
                                                    if(!isNaN( city[0] ) ){
                                                        $( '#billaddr-zipcode' ).val( city[0] );
                                                    }else{
                                                        $( '#billaddr-city' ).val( city[0] );
                                                    }
                                                    for( let i = 1; i < city.length; i++ ){
                                                        $( '#billaddr-city' ).val( $( '#billaddr-city' ).val() + city[i] );
                                                    }
                                                }else{
                                                    $( '#billaddr-city' ).val( lxcarsData.address2 );
                                                }
                                                $( '#car-c_ln' ).val( crmGetCarLicense( lxcarsData.registrationNumber ) );
                                                $( '#car-c_2' ).val( lxcarsData.hsn );
                                                $( '#car-c_3' ).val( lxcarsData.field_2_2 );
                                                $( '#car-c_em' ).val( lxcarsData.field_14_1 );
                                                $( '#car-c_d' ).val( lxcarsData.ez );
                                                $( '#car-c_hu' ).val( lxcarsData.hu );
                                                $( '#car-c_fin' ).val( lxcarsData.vin );
                                                $( '#car-c_finchk' ).val( lxcarsData.field_3 );
                                                dbUpdateData = { 'action': 'insert' };
                                            },
                                            error: function( xhr, status, error ){
                                                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'lxcars()', xhr.responseText );
                                            }
                                        });
                                    }
                                },{
                                text: kivi.t8( 'Close' ),
                                click: function(){
                                        $(this).dialog( "close" );
                                }
                            }]
                        }).dialog( 'open' ).resize();

                        $( '#crm-fsscan-edit-customer' ).val( data.firstname + ' ' + data.name1 );

                        crmSearchCustomerForScan( data.firstname + ' ' + data.name1 );

                       $( '#crm-fsscan-edit-customer' ).keyup( function(){
                            crmSearchCustomerForScan( $( '#crm-fsscan-edit-customer' ).val() );
                        });

                    },
                    error: function( xhr, status, error ){
                        $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewCarFromScan().getFsData', xhr.responseText );
                    }
                })
              })
          },
          error: function( xhr, status, error ){
              $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewCarFromScan().getScans', xhr.responseText );
          }
      })
    }

    function crmSearchCustomerForScan( name ){
        $.ajax({
             url: 'crm/ajax/crm.app.php',
             type: 'POST',
             data:  { action: 'searchCustomerForScan', data: { 'name': name } },
             success: function( data ){
                if( !data ){
                    $( '#crm-fsscan-customer-list' ).empty();
                    return;
                }
                var tableContent = '';
                let listrow0 = false;
                data.forEach( function( item ){
                    tableContent += '<tr class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '" id="' + item.id + '"><td style="text-align: right; padding-right: 15px;">' + item.name + '</td><td>' + item.street + '</td><td>' + item.zipcode + '</td><td>' + item.city + '</td>';
                });
                $( '#crm-fsscan-customer-list' ).empty().append( tableContent );
                $( '#crm-fsscan-customer-list tr' ).click( function(){
                    $( '#crm-fsscan-customer-dlg' ).dialog( 'close' );
                        crmGetCustomerForEdit( 'C', this.id, true, function( src, id ){
                        $( '#car-c_ln' ).val( lxcarsData.registrationNumber );
                        $( '#car-c_2' ).val( lxcarsData.hsn );
                        $( '#car-c_3' ).val( lxcarsData.field_2_2 );
                        $( '#car-c_em' ).val( lxcarsData.field_14_1 );
                        $( '#car-c_d' ).val( lxcarsData.ez );
                        $( '#car-c_hu' ).val( lxcarsData.hu );
                        $( '#car-c_fin' ).val( lxcarsData.vin );
                        $( '#car-c_finchk' ).val( lxcarsData.field_3 );
                        getCVPA( src, id );
                    });
                });
             },
             error: function( xhr, status, error ){
                 $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewCarFromScan().getCustomerForScan', xhr.responseText );
             }
        });
    }

    $( '#crm-wf-edit' ).click( function(){
        crmGetCustomerForEdit( $( '#crm-wf-edit' ).attr( 'data-src' ), $( '#crm-wf-edit' ).attr( 'data-id' ) );
    });

    $( '#crm-wf-scan' ).click( function() {
       crmNewCarFromScan();
    });

    $( '#crm-wf-offer' ).click( function() {
        alert("Angebot erstellen!");
    });

    $( '#crm-wf-order' ).click( function() {
        alert("Auftrag erstellen!");
    });

    $( '#crm-wf-bill' ).click( function() {
        alert("Rechnung erstellen!");
    });
});
