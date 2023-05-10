$( document ).ready( function()
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

    function crmGetHistory(){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'getHistory' },
            success: function( data ){
                $( '#crm-history-list' ).html('');
                if( data ){
                    for( let entry of data ){
                        let id = 'crm-hist-entry-' + entry[2]  + entry[0];
                        //console.info( entry );
                        $( '#crm-history-list' ).append( '<div class="layout-actionbar-action layout-actionbar-submit" data-src="' + entry[2] +'" data-id="' + entry[0] + '" id="' + id + '">' + entry[1] + '</div>');
                        $( '#' + id ).click( function(){
                            crmRefreshAppView( entry[2], entry[0] );
                        });
                    }
                    var histlist = $('#crm-hist-last').clone();
                    $( '#crm-hist-last' ).replaceWith($( '#crm-hist-last' ).clone() );
                    $( '#crm-hist-last' ).click( function(){

                        getCVPA( data[0][2], data[0][0] );
                    });
                    getCVPA( data[0][2], data[0][0] );// ( CV, id )
                }
            },
            error: function(xhr, status, error){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'getHistory()', xhr.responseText );
            }
        });
    }

    $.widget( "custom.catcomplete", $.ui.autocomplete,{
        _renderMenu: function( ul, items ){
            var that = this,
            currentCategory = "";
            $.each( items, function( index, item ){
                if ( item.category != currentCategory ){
                    ul.append( "<li class=\'ui-autocomplete-category\'>" + item.category + "</li>" );
                    currentCategory = item.category;
                }
                that._renderItemData( ul, item );
            });
         }
     });

    $( function(){
        $( "#crm-widget-quicksearch" ).catcomplete({
            source: "crm/ajax/crm.app.php?action=fastSearch",
            select: function( e, ui ) {
                crmRefreshAppView( ui.item.src, ui.item.id );
            }
        });
    });

    function getCVPA( src, id ){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'getCVPA', data: { 'src': src, 'id': id } },
            success: function( data ){
                showCVPA( data );
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'getCVPA', xhr.responseText );
            }
        });
    }

    function crmRefreshAppView( src, id ){
        getCVPA( src, id );
        crmGetHistory();
    }

    $( '#message-dialog' ).dialog({
        autoOpen: false,
        resizable: false,
        width: (window.innerWidth > 800)? 800 : window.innerWidth,
        height: 'auto',
        modal: true,
        position: { my: "top", at: "top+250" },
        open: function(){
            innerWidth = (window.innerWidth > 800)? 800 : window.innerWidth;
            $( this ).css( 'maxWidth', innerWidth );
        },
        buttons:[{
            text: 'Ok',
            click: function(){
                $( '#message-dialog-text' ).html('');
                $( '#message-dialog-debug' ).html('');
                $( '#message-dialog-error' ).hide();
                $( this ).parent().removeClass( 'ui-state-error' );
                $( this ).parent().removeClass( 'ui-state-success' );
                $( this ).dialog( 'close' );
            }
        }]
    });

    $.fn.showMessageDialog = function( style, title, message, debug = null ){
        $( this ).dialog( 'option', 'title', title ).dialog( 'open' ).parent().addClass( 'ui-state-' + style );
        if( style === 'error' ) $( '#message-dialog-error' ).show();
        $( '#message-dialog-text' ).html( message );
        if( debug != null ) $( '#message-dialog-debug' ).html( '<pre>' + debug + '</pre>' ).css( 'display', '' );
        $( this ).resize();
    }

    function showCVPA( data ){
        if( data.cv ){
            console.info( 'customer/vendor src: ' +  data.cv.src + ', id: ' + data.cv.id );

            $( '#crm-wx-contact' ).show();
            $.each( data.cv, function( key, value ){
                if( value ){
                    $( '#crm-contact-' + key ).html( value );
                    $( '#crm-contact-' + key ).show();
                }
                else{
                    $( '#crm-contact-' + key ).hide();
                }
            });

            if( data.cv.phone1 ) $( '#crm-wx-contact-phone1' ).show();
            else $( '#crm-wx-contact-phone1' ).hide();
            if( data.cv.phone2 ) $( '#crm-wx-contact-phone2' ).show();
            else $( '#crm-wx-contact-phone2' ).hide();
            if( data.cv.phone1 ) $('#crm-wx-contact-email').show();
            else $( '#crm-wx-contact-email' ).hide();
        }
        else{
            $( '#crm-wx-contact' ).hide();
        }

        if( lxcars ){
            $( '#crm-cars-table' ).html('');
            if( exists( data.cars ) ){
                let listrow0 = false;
                $.each( data.cars, function( key, value ){
                    $( '#crm-cars-table' ).append( '<tr class="' + ( ( listrow0 =! listrow0 ) ? "listrow0" : "listrow1" ) + '" id="' + value.c_id + '"><td>' +  value.c_ln + '</td><td>' + value.hersteller  + '</td><td>' + value.name  + '</td><td>' + value.mytype + '</td></tr>' );
                });
                $( '#crm-cars-table tr' ).click( function(){
                    $.ajax({
                        url: 'crm/ajax/crm.app.php',
                        type: 'POST',
                        data:  { action: 'getCar', data: { 'id': this.id } },
                        success: function( crmData ){
                            console.info( 'getCar' );
                            console.info( crmData );
                            crmEditCarDlg( crmData );
                        },
                        error: function( xhr, status, error ){
                            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'showCVPA().getCar', xhr.responseText );
                        }
                    });
                });
                $( '#crm-wx-cars' ).show();
            }
        }

        $( '#crm-offers-table' ).html('');
        if( exists( data.off ) ){
            let listrow0 = false;
            $.each( data.off, function( key, value ){
                $( '#crm-offers-table' ).append( '<tr id="' + value.id +'" class="' + ( ( listrow0 =! listrow0 ) ? "listrow0" : "listrow1" ) + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.amount  + '</td><td>' + value.number + '</td></tr>' );
            });
        }

        $( '#crm-orders-table' ).html('');
        if( exists( data.ord ) ){
            let listrow0 = false;
            $.each( data.ord, function( key, value ){
                $( '#crm-orders-table' ).append( '<tr id="' + value.id +'" class="' + ( ( listrow0 =! listrow0 ) ? "listrow0" : "listrow1" ) + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.amount  + '</td><td>' + value.number + '</td></tr>' );
            });
            $( '#crm-orders-table tr' ).click( function(){
                $.ajax({
                    url: 'crm/ajax/crm.app.php',
                    type: 'POST',
                    data:  { action: 'getOrder', data: { 'id': this.id } },
                    success: function( crmData ){
                        console.info( 'getOrder' );
                        crmEditOrderDlg( crmData );
                    },
                    error: function( xhr, status, error ){
                        $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'showCVPA().getOrder', xhr.responseText );
                    }
                });
            });
        }

        $( '#crm-deliveries-table' ).html('');
        if( exists( data.del ) ){
            let listrow0 = false;
            $.each( data.del, function( key, value ){
                $( '#crm-deliveries-table' ).append( '<tr id="' + value.id +'" class="' + ( ( listrow0 =! listrow0 ) ? "listrow0" : "listrow1" ) + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.deldate  + '</td><td>' + value.donumber + '</td></tr>' );
            });
        }

        $( '#crm-invoices-table' ).html('');
        if( exists( data.inv) ){
            let listrow0 = false;
            $.each( data.inv, function( key, value ){
                $( '#crm-invoices-table' ).append( '<tr id="' + value.id +'" class="' + ( ( listrow0 =! listrow0 ) ? "listrow0" : "listrow1" ) + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.amount  + '</td><td>' + value.number + '</td></tr>' );
            });
        }

        if( exists( data.cv ) ){
            $( '#crm-wx-title' ).html( kivi.t8( 'Detail view:' ) + ' ' + ( ( data.cv.src == 'C' ) ? kivi.t8( 'Customer' ) : kivi.t8( 'Vendor' ) ) );
            $( '#crm-wf-edit' ).attr( 'data-src', data.cv.src );
            $( '#crm-wf-edit' ).attr( 'data-id', data.cv.id );
        }
        crmDelAddr = 0;
    }

    /********************************************************
    * @param crmFormModel - form data (id's, ...)
    * @param table - id for HTML Table element (use tbody element)
    * @param max_rows - two columns, if greater then 0, defines max rows of the first column
    * @param container - the HTML div element id, which contains the hidden input fields
    *
    * Attention:
    * Hidden fields must be on then end of the form model !
    *********************************************************/
    function crmInitFormEx( crmFormModel, table, max_rows = 0, container = null){
        let tabledata = '';
        let hiddenFields = '';
        if(max_rows <= 0 || max_rows > crmFormModel.length) max_rows = crmFormModel.length;
        for( let i = 0; i < max_rows; i++ ){
            let item = crmFormModel[i];
            tabledata += '<tr>';
            let addItem = function( item ){
                if( item.type == 'hidden' ) hiddenFields += '<input type="hidden" id="' + item.name + '" name="' + item.name + '" value="' + ( ( exists(item.data) )? item.data : '' ) + '">';
                if( item.hasOwnProperty( 'spacing' ) ) tabledata += '<td style="padding-left: 10px"> </td>';
                if( item.type == 'headline' ) tabledata += '<td colspan="2"><b>' + kivi.t8( item.label ) + '</b>';
                if( item.type == 'checkbox' ) tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><input type="checkbox" id="' + item.name + '" name="'+ item.name + '" value="true" title="' + kivi.t8( item.tooltip ) + '"></input>';
                if( item.type == 'input' ){
                    tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><input type="text" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '" value="' + ( ( exists(item.data) )? item.data : '' ) + '"></input>';
                    if( item.check ) tabledata += '<input type="checkbox" id="' + item.check + '" name="'+ item.check + '" title="' + kivi.t8( 'Check imput' ) + '"></input>';
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
        if( container != null ){
            $( container ).html( ' ' );
            $( container ).append( hiddenFields );
        }
        $( table + " > tbody" ).html( ' ' );
        $( table + " > tbody" ).append( tabledata );
    }

    /***************************************
    * Change list of Bundesland dependent
    * on Country code
    ***************************************/
     function crmChangeBlandList( crmData, list, country ){
        $( '#' + list ).html( '' );
        $( '#' + list ).append( '<option value=""></option>' );
        for( let bland of crmData.bundesland ){
            if( country && bland.country && !bland.country.startsWith( country ) ) continue;
            $( '#' + list ).append( '<option value="' + bland.id + '" data-country="' + bland.country  + '">' + bland.name + '</option>' );
        }
    }

    function crmUpdateDB( call, dbUpdataData ){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: call, data: dbUpdateData },
            success: function( data ){
                console.info( 'crmUpdateDB' );
                console.info( data );
                dbUpdateData = {};
                if( exists( data.success ) && !data.success ) $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'DB update error' ), kivi.t8( 'Error in: ' ) + 'crmUpdateDB()', ( ( exists( data.debug )? data.debug : null) ) );
                if( exists( data.src ) && exists( data.id ) ) crmRefreshAppView( data.src, data.id );
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmUpdateDB()', xhr.responseText );
            }
        });
    }

    /***************************************
    * Get CV date from database
    * includes data for dialog
    ***************************************/
    function crmGetCustomerForEdit( src, id, new_car, fx ){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'getCustomerForEdit', data: { 'src': src, 'id': id } },
            success: function( data ){
                crmEditCuVeDlg( data, new_car );
                crmShowCuVeForEdit( data );
                if( fx ) fx( src, id );
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'getCustomerForEdit()', xhr.responseText );
            }
        });
    }

    /***************************************
    * Prepares dialog to edit CV
    ***************************************/
     function crmShowCuVeForEdit( crmData ){
        $( '#billaddr-greetings' ).html( '' );
        $( '#billaddr-greetings' ).append( '<option value="">' + kivi.t8( "Salutation as below" ) + '</option>' );
        if( isIterable( crmData.greetings ) ){
            for( let description of crmData.greetings ) $( '#billaddr-greetings' ).append( '<option value="' + description.description + '">' + description.description + '</option>' );
        }

        $( '#billaddr-business_id' ).html( '' );
        for( let business of crmData.business ) $( '#billaddr-business_id' ).append( '<option value="' + business.id + '">' + business.name + '</option>' );

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
               $( '#billaddr-salesman_id' ).append( '<option value="' + employee.id + '">' + employee.name + '</option>' );
           }
           if( exists( crmData.cv ) && exists( crmData.cv.employee ) ) $( '#billaddr-salesman_id' ).val( crmData.cv.employee );
       }

       if( exists( crmData.payment_terms ) ){
           for( let payment_term of crmData.payment_terms ){
               $( '#billaddr-payment_id' ).append( '<option value="' + payment_term.id + '">' + payment_term.description + '</option>' );
           }
           if( exists( crmData.cv ) && exists( crmData.cv.payment_id ) ) $( '#billaddr-payment_id' ).val( crmData.cv.payment_id );
       }

       if( exists( crmData.tax_zones ) ){
           for( let tax_zone of crmData.tax_zones ){
               $( '#billaddr-taxzone_id' ).append( '<option value="' + tax_zone.id + '">' + tax_zone.description + '</option>' );
           }
           if( exists( crmData.cv ) && exists( crmData.cv.taxzone_id ) ) $( '#billaddr-taxzone_id' ).val( crmData.cv.taxzone_id );
       }

       if( exists( crmData.languages ) ){
           for( let lang of crmData.languages ){
               $( '#billaddr-languages' ).append( '<option value="' + lang.id + '">' + lang.description + '</option>' );
           }
           if( exists( crmData.cv ) && exists( crmData.cv.language_id ) ) $( '#billaddr-language' ).val( crmData.cv.language_id );
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
           crmInitFormEx( crmData.vars_conf, '#vars-form' );
       }

        if( exists( crmData.cv ) ){
            $( '#billaddr-business_id' ).val( crmData.cv.business_id );
            $( '#billaddr-country' ).change();
            $( '#billaddr-bland' ).val( crmData.cv.bland );
            $( '#deladdr-shiptocountry' ).change();
        }
    }

    /***************************************
    * Open dialog to edit CV
    ***************************************/
    var crmEditCuVeDlgAction;

     function crmEditCuVeDlg( crmData, new_with_car ){
        crmInitFormEx( billaddrFormModel, '#billaddr-form', 0, '#crm-billaddr-cv' );
        crmInitFormEx( deladdrFormModel, '#deladdr-form' );
        crmInitFormEx( banktaxFormModel, '#banktax-form' );
        crmInitFormEx( extraFormModel, '#extras-form' );
        crmInitFormEx( carFormModel, '#car-form', 21 );

        $( '#billaddr-greetings' ).change( function(){
           $( '#billaddr-greeting' ).val( $( '#billaddr-greetings' ).val() );
        });

        $( '#billaddr-branches' ).change( function(){
           $( '#billaddr-branche' ).val( $( '#billaddr-branches' ).val() );
        });

        if( new_with_car ){
            $( '#car-form' ).show();
        }
        else{
            $( '#car-form' ).hide();
        }

        $( '#billaddr-country' ).change(function(){
            crmChangeBlandList( crmData, 'billaddr-bland', $( '#billaddr-country' ).val() );
        });
        crmChangeBlandList( crmData, 'billaddr-bland', 'D' );

        $( '#deladdr-shiptocountry' ).change(function(){
            crmChangeBlandList( crmData, 'deladdr-shiptobland', $( '#deladdr-shiptocountry' ).val() );
        });

        $( '#crm-wx-customer-dialog' ).dialog({
            autoOpen: false,
            resizable: true,
            width: 'auto',
            height: 'auto',
            modal: true,
            title: kivi.t8( 'Edit customer' ),
            position: { my: "top", at: "top+250" },
            open: function(){
                $( this ).css( 'maxWidth', window.innerWidth );
                crmEditCuVeDlgAction = 'updateCuWithNewCar';
            },
            buttons:[{
                text: kivi.t8( 'Save' ),
                click: function(){
                    console.info( 'Save' );
                    dbUpdateData = {};
                    let cvSrc = ( $( '#billaddr-src' ).val() == 'V' )? 'vendor' : 'customer';
                    dbUpdateData[cvSrc] = {};
                    let billaddr_id = $( '#billaddr-id' ).val();
                    if( '' !== billaddr_id  ){
                        dbUpdateData[cvSrc]['WHERE'] = {};
                        dbUpdateData[cvSrc]['WHERE']['id'] = $( '#billaddr-id' ).val();
                    }
                    for( let item of billaddrFormModel){
                        let columnName = item.name.split( '-' );
                        if( columnName[1] !== "src" && columnName[1] !== "id" && columnName[1] !== "greetings" ){
                            let val = $( '#' + item.name ).val();
                            if( exists(val) && val !== '' ) dbUpdateData[cvSrc][columnName[1]] = val;
                        }
                    }
                    if( exists( $( '#deladdr-list' ).val() ) && $( '#deladdr-list' ).val() !== '' ){
                        dbUpdateData['shipto'] = { 'shipto_id': $( '#deladdr-list' ).val() };
                        for(let item of deladdrFormModel){
                            let columnName = item.name.split( '-' );
                            let val = $( '#' + item.name ).val();
                            if( exists(val) && val !== '' ) dbUpdateData['shipto'][columnName[1]] = val;
                        }
                    }
                    for( let item of banktaxFormModel ){
                        let columnName = item.name.split( '-' );
                        let val = $( '#' + item.name ).val();
                        if( exists(val) && val !== '' ) dbUpdateData[cvSrc][columnName[1]] = val;
                    }
                    for( let item of extraFormModel ){
                        let columnName = item.name.split( '-' );
                        if( columnName[1] !== 'branches' ){
                            let val = $( '#' + item.name ).val();
                            if( exists(val) && val !== '' ) dbUpdateData[cvSrc][columnName[1]] = val;
                        }
                    }
                    if( $( '#car-form' ).is(':visible' ) ){
                        dbUpdateData['lxc_cars'] = {};
                        for(let item of carFormModel){
                            if( !item.name.startsWith( 'kba' ) ){
                                let columnName = item.name.split( '-' );
                                let val = $( '#' + item.name ).val();
                                if( exists(val) && val !== '' ) dbUpdateData['lxc_cars'][columnName[1]] = val;
                            }
                        }
                    }
                    console.info( 'dbUpdateData' );
                    console.info( dbUpdateData );
                    crmUpdateDB( crmEditCuVeDlgAction, dbUpdateData );
                    $( this ).dialog( "close" );
                }
            },
            {
                text: kivi.t8( 'Cancel' ),
                click: function(){
                    $( this ).dialog( "close" );
                }
            }]
        }).dialog( 'open' ).resize();
    }

    /***************************************
    * Format the registration number
    * (car license) getting from scan,
    * remove white spaces
    ***************************************/
     function crmFormatCarLicense( regNum ){
        let rn = ( isEmpty( regNum ) ) ? 0 : regNum.split( ' ' );
        if( isIterable( rn ) && rn.length > 1 ){
            let rs = '';
            rs = rn[0] + '-' + rn[1];
            for( let i = 2; i < rn.length; i++ ){
                rs += rn[i];
            }
            // * remove "*" from license plates from the federal state of Berlin, Schlimm diese Kleinstaatelei.. alias Förderalismus
            return rs.replace( '*', '' ).replace( '*', '' );
        }
        alert( 'crmFormatCarLicense()' + regNum );
        return regNum;// Wann tritt dieser Fall auf???

    }

    function crmFormatName( name ){
        let rs = null;
        for( let str of name.split( ' ' ) ){
            if( rs === null ) rs = ''; else rs += ' '
            rs += str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        }
        return rs;
    }

    /***************************************
    * Dialog to select scan (car data)
    * and customer
    * inklusive fast search,
    * it is possible to add a new customer
    ***************************************/
    var lxcarsData = {};

    function crmNewCarFromScan(){
        let fsmax = 24; // only for show
        //new car or new car and new customer
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            data: { action: 'getScans', data:{ 'fsmax': fsmax } },
            type: "POST",
            success: function( crmData ){
              $( '#crm-fsscan-dlg' ).dialog({
                  autoOpen: false,
                  resizable: true,
                  width: 'auto',
                  height: 'auto',
                  modal: true,
                  title: kivi.t8( 'FS-Scan' ),
                  position: { my: "top", at: "top+250" },
                  open: function(){
                      $( this ).css( 'maxWidth', window.innerWidth );
                  },
                  buttons:[{
                      text: kivi.t8( 'Close' ),
                      click: function(){
                          $( this ).dialog( "close" );
                      }
                  }]
              }).dialog( 'open' ).resize();

              var tableContent = '';
              let listrow0 = false;
              console.info( 'FromScan' );
              console.info( crmData );
              if( isIterable( crmData.db_scans ) ){
                  crmData.db_scans.forEach( function( item ){
                      tableContent += '<tr class="' + ( ( listrow0 =! listrow0 ) ? "listrow0": "listrow1" ) + '" id="' + item.scan_id + '"><td style="text-align: right; padding-right: 15px;">' + item.myts + '</td><td>' + item.firstname + '</td><td>' + item.name1 + '</td><td>' + item.registrationnumber + '</td>';
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
                                  /* Add new customer from car scan data */
                                  text: kivi.t8( 'New' ),
                                  click: function(){
                                          $.ajax({
                                              url: 'crm/ajax/crm.app.php',
                                              type: 'POST',
                                              data:  { action: 'getCVDialogData' },
                                              success: function( crmData ){
                                                   //console.info('New');
                                                  //console.info(lxcarsData);
                                                  $( '#crm-fsscan-customer-dlg' ).dialog( "close" );
                                                  crmEditCuVeDlg( crmData, true );
                                                  crmShowCuVeForEdit( crmData );
                                                  $( '#billaddr-name' ).val( $( '#crm-fsscan-edit-customer' ).val() );
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
                                                  $( '#car-c_ln' ).val( crmFormatCarLicense( lxcarsData.registrationNumber ) );
                                                  $( '#car-c_2' ).val( lxcarsData.hsn );
                                                  $( '#car-c_3' ).val( lxcarsData.field_2_2 );
                                                  $( '#car-c_em' ).val( lxcarsData.field_14_1 );
                                                  $( '#car-c_d' ).val( lxcarsData.ez );
                                                  //Wird nicht benötigt, da Datum invalide
                                                  //$( '#car-c_hu' ).val( lxcarsData.hu );
                                                  $( '#car-c_fin' ).val( lxcarsData.vin );
                                                  $( '#car-c_finchk' ).val( lxcarsData.field_3 );
                                                  crmEditCuVeDlgAction = 'insertNewCuWithCar';
                                              },
                                              error: function( xhr, status, error ){
                                                  $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'lxcars()', xhr.responseText );
                                              }
                                          });
                                      }
                                  },{
                                  text: kivi.t8( 'Close' ),
                                  click: function(){
                                          $( this ).dialog( "close" );
                                  }
                              }]
                          }).dialog( 'open' ).resize();

                          $( '#crm-fsscan-edit-customer' ).val( crmFormatName( data.firstname + ' ' + data.name1 ) );

                          crmSearchCustomerForScan( crmFormatName( data.firstname + ' ' + data.name1 ) );

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

    /***************************************
    * Fast search for customer from car scan
    * data
    ***************************************/
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
                        $( '#car-c_ln' ).val( crmFormatCarLicense( lxcarsData.registrationNumber ) );
                        $( '#car-c_2' ).val( lxcarsData.hsn );
                        $( '#car-c_3' ).val( lxcarsData.field_2_2 );
                        $( '#car-c_em' ).val( lxcarsData.field_14_1 );
                        $( '#car-c_d' ).val( lxcarsData.ez );
                        //Wird nicht benötigt, da Datum invalide
                        //$( '#car-c_hu' ).val( lxcarsData.hu );
                        $( '#car-c_fin' ).val( lxcarsData.vin );
                        $( '#car-c_finchk' ).val( lxcarsData.field_3 );
                        crmRefreshAppView( src, id );
                    });
                });
             },
             error: function( xhr, status, error ){
                 $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewCarFromScan().getCustomerForScan', xhr.responseText );
             }
        });
    }

    function crmEditCarDlg( crmData ){
        crmInitFormEx( editCarFormModel, '#edit-car-form', 21, '#edit-car-hidden' );
        for( let item of editCarFormModel){
            let columnName = item.name.split( '-' );
            if( exists( crmData[columnName[1]] ) ) $( '#' + item.name ).val( crmData[columnName[1]] );
            if( item.check ){
                columnName = item.check.split( '-' );
                if( exists( crmData[columnName[1]] ) ) $( '#' + item.check ).prop( 'checked', crmData[columnName[1]] );
            }
        }

        $( '#crm-edit-car-dialog' ).dialog({
            autoOpen: false,
            resizable: true,
            width: 'auto',
            height: 'auto',
            modal: true,
            title: kivi.t8( 'Edit customer' ),
            position: { my: "top", at: "top+250" },
            open: function(){
                $( this ).css( 'maxWidth', window.innerWidth );
            },
            buttons:[{
                text: kivi.t8( 'Save' ),
                click: function(){
                    console.info( 'Save car' );
                    dbUpdateData = {};
                    dbUpdateData['lxc_cars'] = {};
                    dbUpdateData['lxc_cars']['WHERE'] = {};
                    dbUpdateData['lxc_cars']['WHERE']['c_id'] = $( '#edit_car-c_id' ).val();
                    for( let item of editCarFormModel ){
                        let columnName = item.name.split( '-' );
                        let val = $( '#' + item.name ).val();
                        if( exists(val) && val !== '' ){
                            if( item.name !== 'edit_car-c_id' ) dbUpdateData['lxc_cars'][columnName[1]] = val;
                        }
                        if( item.check ){
                            val = $( '#' + item.name ).val();
                            columnName = item.check.split( '-' );
                            if( exists(val) && val !== '' ) dbUpdateData['lxc_cars'][columnName[1]] = $( '#' + item.check ).prop( 'checked' );
                        }
                    }
                    console.info( dbUpdateData );
                    crmUpdateDB('genericUpdate', dbUpdateData );
                    $( this ).dialog( "close" );
                }
            },
            {
                text: kivi.t8( 'Cancel' ),
                click: function(){
                    $( this ).dialog( "close" );
                }
            }]
        }).dialog( 'open' ).resize();
    }

    /*****************************************************
    *
    * @var crmOrderItemLists - contains the list of worker and units
    *****************************************************/
    var crmOrderItemLists;

    function crmCalcOrderPos(){
        $( '#edit-order-table > tbody > tr').each( function( key, pos ){
            $( pos ).find( '[class=od-item-position]' )[0].innerText = key + 1;
            if( !isEmpty( $( pos ).find( '[class=od-item-partnumber]' ).text() ) ){
                $( $( pos ).find( '[class=od-ui-edit-btn]' )[0] ).html('<button>Edit</button>');
            }
            crmCalcOrderPrice( pos );
       });
    }

    function crmCalcOrderPrice( pos ){
        let qty = kivi.parse_amount( $( pos ).find( '[class=od-item-qty]' )[0].value );
        if( isNaN( qty ) ) qty = 0;
        let sellprice = kivi.parse_amount( $( pos ).find( '[class=od-item-sellprice]' )[0].value );
        if( isNaN( sellprice ) ) sellprice = 0;
        let discount = kivi.parse_amount( $( pos ).find( '[class=od-item-discount]' )[0].value );
        if( isNaN( discount ) ) discount = 0;
        let marge_total = qty * sellprice;
        if( discount > 0 ){
            discount  = marge_total * ( discount / 100 );
            marge_total -= discount;
        }
        $( pos ).find( '[class=od-item-marge_total]' )[0].value = kivi.format_amount( marge_total, 2 );
    }

    function crmAddOrderItem( dataRow ){
        let tableRow;
        tableRow += '<tr ' + ( ( exists( dataRow.id ) )? ('id="' + dataRow.id + '"') : 'class="od-item-pin"') + '><td class="od-item-position"></td>' +
                    '<td><img src="image/updown.png" alt="umsortieren"></td>' +
                    '<td><img class="od-ui-del" src="image/close.png" alt="löschen"></td>' +
                    '<td class="od-ui-edit-btn"></td>' +
                    '<td class="od-item-partnumber">' + ( ( exists( dataRow.partnumber ) )? dataRow.partnumber : '' ) + '</td>' +
                    '<td class="od-item-type">';
        let orderType = '';
        if( dataRow.instruction )  orderType = 'I';
        else if( 'part' === dataRow.part_type ) orderType = 'P';
        else if( 'service' === dataRow.part_type ) orderType = 'S';
        tableRow += '<input class="od-item_type" type="hidden" value="' + orderType + '"></input>';
        tableRow += kivi.t8(orderType);
        tableRow += '</td>' +
                    '<td><input class="od-item-description" type="text" size="40" value="' + ( ( exists( dataRow.description ) )? dataRow.description : '' ) + '"></input></td>' +
                    '<td><input class="od-item-longdescription" type="text" size="40" value="' + ( ( exists( dataRow.longdescription ) )? dataRow.longdescription : '' )  + '"></input>' +
                    '</td><td><input class="od-item-qty" type="text" size="5" value="' + kivi.format_amount( ( exists( dataRow.qty ) )? dataRow.qty : '0' ) + '"></input></td>';

        // Unit is readonly now:
        tableRow += '<td><input class="od-item-unit" type="text" size="5" readonly="readonly" value="' + ( ( exists( dataRow.unit ) )? dataRow.unit : '' ) + '"></input></td>';

        tableRow += '<td><input class="od-item-sellprice" type="text" size="5" value="' + kivi.format_amount( ( exists( dataRow.sellprice ) )? dataRow.sellprice : '0', 2 ) + '"></input></td>' +
                    '<td><input class="od-item-discount" type="text" size="5" value="' + kivi.format_amount( ( exists( dataRow.discount ) )? dataRow.discount : '0' ) + '"></input></td><td><button>100%</button></td>' +
                    '<td><input class="od-item-marge_total" type="text" size="5" value="' + kivi.format_amount( ( exists( dataRow.marge_total ) )? dataRow.marge_total : '0', 2 ) + '"></input></td>';

        tableRow += '<td><select type="select">';
        tableRow += '<option value=""></option>';
        for( let worker of crmOrderItemLists.workers ){
            tableRow += '<option value="' + worker.name  + '"';
            if(dataRow.u_id === worker.name) tableRow += ' selected'
            tableRow += '>' + worker.name + '</option>';
        }
        tableRow += '</select></td>';

        //tableRow += '<td>' + ( ( exists( dataRow.status ) )? dataRow.status : '' ) + '</td></tr>';
        const statusList = [ 'gelesen', 'Bearbeitung', 'erledigt' ];
        tableRow += '<td><select type="select">';
        for( let status of statusList ){
            tableRow += '<option value="' + status  + '"';
            if(dataRow.status === status) tableRow += ' selected'
            tableRow += '>' + status  + '</option>';
        }
        tableRow += '</select></td>';



        $( '#edit-order-table > tbody' ).append(tableRow);

        $( '.od-item-description' ).catcomplete({
            source: "crm/ajax/crm.app.php?action=findPart",
            select: function( e, ui ){
                $( ':focus' ).parent().parent().find( '[class=od-item-partnumber]' ).text( ui.item.partnumber );
                $( ':focus' ).parent().parent().find( '[class=od-item-qty]' ).val( ui.item.qty );
                $( ':focus' ).parent().parent().find( '[class=od-item-unit]' ).val( ui.item.unit );
                $( ':focus' ).parent().parent().find( '[class=od-item-sellprice]' ).val( ui.item.sellprice );
                //Bug or feature, can't do otherwise:
                $( ':focus' ).parent().parent()[0].className = "";

                const list = $( '.od-item-description' );
                if( list[list.length - 1].value !== '' ){
                    crmAddOrderItem( { } );
                }
                crmCalcOrderPos();
            }
        });

        crmCalcOrderPos();
    }

    function crmSaveOrder(){
        let dbUpdateData = { }
        dbUpdateData['oe'] = {};
        dbUpdateData['customer'] = [];
        dbUpdateData['lxc_cars'] = [];
        dbUpdateData['orderitems'] = [];
        dbUpdateData['instructions'] = [];

        $( '.od-common :input' ).each( function( key, pos ){
            dbUpdateData['oe'][pos.id.split( '-' )[2]] = ( 'checkbox' === pos.type )? $( pos ).prop( 'checked' ) : $( pos ).val();;
        });

        dbUpdateData['customer']['notes'] = $( '#od-customer-notes' ).val();
        dbUpdateData['lxc_cars']['c_text'] = $( '#od-lxcars-c_text' ).val();
        dbUpdateData['oe']['intnotes'] = $( '#od-lxcars-c_text' ).val();

        $( '#edit-order-table > tbody > tr').each( function( key, pos ){
            let itemType;
            let dataRow = { };
            $( pos ).find( '[class^=od-item]' ).each( function( i, item ){
                let columnName = item.className.split( ' ' )[0].split( '-' )[2];
                if( !exists( columnName ) ) return;
                if( 'type' === columnName) itemType = item.innerText;
                else if( exists( item.value ) ) dataRow[columnName] = item.value;
                else if( exists( item.innerText ) ) dataRow[columnName] = item.innerText;
            });
            dataRow.qty = kivi.parse_amount( dataRow.qty );
            dataRow.sellprice = kivi.parse_amount( dataRow.sellprice );
            dataRow.discount = kivi.parse_amount( dataRow.discount );
            dataRow.marge_total = kivi.parse_amount( dataRow.marge_total );
            if( exists( pos.id ) ){
                if( 'P' === itemType  ){
                    dataRow['WHERE'] = {};
                    dataRow['WHERE']['id'] = pos.id;
                    dbUpdateData['orderitems'].push( dataRow );
                }
                if( 'S' === itemType  ){
                    dataRow['WHERE'] = {};
                    dataRow['WHERE']['id'] = pos.id;
                    dbUpdateData['orderitems'].push( dataRow );
                }
                if( 'I' === itemType  ){
                    dataRow['WHERE'] = {};
                    dataRow['WHERE']['id'] = pos.id;
                    dbUpdateData['instructions'].push( dataRow );
                }
            }
        });

        dbUpdateData['oe']['WHERE'] = {};
        dbUpdateData['oe']['WHERE']['id'] = $( '#od-oe-id' ).val();

        dbUpdateData['customer']['WHERE'] = {};
        dbUpdateData['customer']['WHERE']['id'] = $( '#od-customer-id' ).val();

           dbUpdateData['lxc_cars']['WHERE'] = {};
        dbUpdateData['lxc_cars']['WHERE']['c_id'] = $( '#od-lxcars-c_id' ).val();

        console.info( dbUpdateData );
     }

    function crmEditOrderDlg( crmData ){
        console.info( 'Edit order' );
        console.info( crmData );
        crmOrderItemLists = { };
        crmOrderItemLists['units'] = crmData.order.units;
        crmOrderItemLists['workers'] = crmData.workers;
        $( '#edit-order-table > tbody' ).html( '' );
        for( let dataRow of crmData.order.orderitems ){
           crmAddOrderItem( dataRow );
        }
        crmAddOrderItem( { } );
        $( '#edit-order-table > tbody' ).sortable({
            cancel: '.od-item-pin, .od-ui-del, input, select, button',
            update: function(){
                crmCalcOrderPos();
            }
        });

        //$( '.od-item-editable' ).

        $( '#od-customer-id' ).val( crmData.order.common.customer_id );
        $( '#od-lxcars-c_id' ).val( crmData.order.common.c_id );
        $( '#od-oe-id' ).val( crmData.order.common.id );
        $( '#od-customer-name' ).html( crmData.order.common.customer_name );
        $( '#od-oe-ordnumber' ).html( crmData.order.common.ordnumber );
        $( '#od-oe-finish_time' ).val( crmData.order.common.finish_time );
        $( '#od-oe-km_stnd' ).val( crmData.order.common.km_stnd );
        $( '#od-oe-employee_name' ).html( crmData.order.common.employee_name );
        $( '#od-lxcars-c_ln' ).html( crmData.order.common.c_ln );
        $( '#od-oe-mtime' ).html( crmData.order.common.mtime );
        $( '#od-oe-internalorder' ).prop( 'checked', crmData.order.common.internalorder );
        $( '#od-oe-itime' ).html( crmData.order.common.itime );
        $( '#od-oe-car_status' ).val( crmData.order.common.car_status );
        $( '#od-oe-status' ).val( crmData.order.common.status );
        $( '#od-lxcars-c_text' ).val( crmData.order.common.int_car_notes );
        $( '#od-customer-notes' ).val( crmData.order.common.int_cu_notes );
        $( '#od-oe-intnotes' ).val( crmData.order.common.intnotes );


        $( '#crm-edit-order-dialog' ).dialog({
            autoOpen: false,
            resizable: true,
            width: 'auto',
            height: 'auto',
            modal: true,
            title: kivi.t8( 'Edit order' ),
            position: { my: "top", at: "top+250" },
            open: function(){
                $( this ).css( 'maxWidth', window.innerWidth );
            },
            buttons:[{
                text: kivi.t8( 'Save' ),
                click: function(){
                    console.info( 'Save order' );
                    crmSaveOrder();
                    //$( this ).dialog( "close" );
                }
            },
            {
                text: kivi.t8( 'Cancel' ),
                click: function(){
                    $( this ).dialog( "close" );
                }
            }]
        }).dialog( 'open' ).resize();
    }

    $( '#crm-wf-edit' ).click( function(){
        crmGetCustomerForEdit( $( '#crm-wf-edit' ).attr( 'data-src' ), $( '#crm-wf-edit' ).attr( 'data-id' ) );
    });

    $( '#crm-wf-scan' ).click( function() {
       crmNewCarFromScan();
    });

    $( '#crm-wf-offer' ).click( function() {
        alert( "Angebot erstellen!" );
    });

    $( '#crm-wf-order' ).click( function() {
        alert( "Auftrag erstellen!" );
    });

    $( '#crm-wf-bill' ).click( function() {
        alert( "Rechnung erstellen!" );
    });


    $( '#tel1_dialog_button, #tel2_dialog_button' ).click( function( data ){
        data.stopImmediatePropagation();
        //alert( "ClickToCall Dialog");
        var dialog_id = this.id.replace( '_button', '' );
        //console.info( dialog_id );
        $( '#' + dialog_id ).dialog({
            modal: true,
            title: kivi.t8('Dialog for ' + dialog_id.replace( '_dialog', '' ) ),//kivi.t8( 'Phone Dialog'), //ToDo
            width: 'auto',
            resizable: false,
            open: function( event, ui ){
                $.ajax({
                    url: 'ajax/clickToCall.php?action=getPhones',
                    type: 'GET',
                    success: function ( data ){
                        var external_contexts_array = data['external_contexts'].split( ',');
                        var internal_phones_array = data['internal_phones'].split( ',');
                        var selected_context = typeof data['user_external_context'] !== 'undefined' ?  data['user_external_context'] : '';
                        var selected_phone = typeof data['user_internal_phone'] !== 'undefined' ?  data['user_internal_phone'] : '';
                        var selected = '';
                        var dynamic_html = '<table><tr><td>' + kivi.t8( 'External Context:' ) + '</td><td> <select id="user_external_context"  style="width:100%;">';
                        $.each( external_contexts_array, function( key, value ){
                            selected = value == selected_context ? 'selected' : '';
                            dynamic_html +=  '<option value="' + value + '"' + selected + '>' + value + '</option>'
                        })
                        dynamic_html += '</select></td></tr>';
                        dynamic_html += '<tr><td>' + kivi.t8( 'Internal Phone:' ) + '</td><td> <select id="user_internal_phone"  style="width:100%;">';
                        $.each( internal_phones_array, function( key, value ){
                            selected = value == selected_phone ? 'selected' : '';
                            dynamic_html +=  '<option value="' + value + '"' + selected + '>' + value + '</option>'
                        })
                        dynamic_html += '</select></td></tr></table>';
                        $( '#' + dialog_id ).html( dynamic_html );
                        //console.info(  dynamic_html );
                        $( '#user_external_context, #user_internal_phone' ).change( function( data ){
                            var dataObj = {};
                            dataObj[this.id] = $(this).val();
                            $.ajax({
                                url: 'ajax/clickToCall.php',
                                type: 'POST',
                                data: { action: 'saveClickToCall', data: dataObj },
                                success: function ( data ) {
                                    //if( data ) alert( );
                                },
                                error: function () {
                                    alert( 'Error: saveClickToCall!' );
                                }
                            });

                        })
                        //console.info( dialog_id );
                    },
                    error: function (){
                        alert( 'Error: ajax/clickToCall.php?action=getPhones' );
                    }
                })
            },
            buttons: [{
                text: kivi.t8( 'cancel' ),
                click: function(){
                    $( this ).dialog( "close" );
                }
            },{
                text: kivi.t8( 'call' ),
                click: function(){
                    $( '#' + dialog_id.replace( '_dialog', '' ) ).click();
                    $( this ).dialog( "close" );
                }
            }],

        })
    }).button();

});
