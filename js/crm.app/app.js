function exists( obj ){
    return obj !== null && obj !== undefined;
}

function isIterable( obj ){
    return exists( obj ) && obj.hasOwnProperty('length');
}

function isEmpty( obj ){
    return isIterable( obj ) && obj.length === 0;
}

function getValueNotNull( value ){
    return ( null == value )? '' : value;
}

$( '#crm-tabs-main' ).tabs();
$( '#crm-tabs-infos' ).tabs();

$( '#crm-wf-edit').html( kivi.t8( 'Base data' ) );
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
        data:  { action: 'isLxcars' },
        success: function( data ){
                lxcars = data.lxcars;
                console.info( 'activate lxcars: ' + lxcars );
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
            console.info( ui );
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

function crmRefreshAppViewAction( ){
    const src = $( '#crm-cvpa-src' ).val();
    const id = $( '#crm-cvpa-id' ).val();
    if( '' != src && '' != id) getCVPA( src, id );
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
// ToDo: Erzeugt das resize-Problem beim Neuen-Auftrag-Dialog
//    $( this ).dialog( 'option', 'title', title ).dialog( 'open' ).parent().addClass( 'ui-state-' + style );
//    if( style === 'error' ) $( '#message-dialog-error' ).show();
//    $( '#message-dialog-text' ).html( message );
//    if( debug != null ) $( '#message-dialog-debug' ).html( '<pre>' + debug + '</pre>' ).css( 'display', '' );
//    $( this ).resize();
    let text = '------------------------------------------\n' + title + '\n------------------------------------------\n\n' + message;
    if( debug != null ) text += '\n\n' + debug;
    alert( text );
}

function showCVPA( data ){
    if( data.cv ){
        console.info( 'customer/vendor src: ' +  data.cv.src + ', id: ' + data.cv.id );
        $( '#crm-cvpa-src' ).val( data.cv.src );
        $( '#crm-cvpa-id' ).val( data.cv.id );

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
        if( !data.cv.phone1 ) $( '.clickToCall1' ).hide();
        else $( '.clickToCall1' ).show();
        if( !data.cv.phone2 ) $( '.clickToCall2' ).hide();
        else $( '.clickToCall2' ).show();
    }
    else{
        $( '#crm-wx-contact' ).hide();
    }

    if( lxcars ){
        $( '#crm-cars-table' ).html('');
        if( exists( data.cars ) ){
            let listrow0 = false;
            $.each( data.cars, function( key, value ){
                $( '#crm-cars-table' ).append( '<tr class="' + ( ( listrow0 =! listrow0 ) ? "listrow0" : "listrow1" ) + '" id="' + value.c_id + '"><td>' +  value.c_ln + '</td><td class="kba-hersteller">' + value.hersteller + '</td><td class="kba-name">' + value.name + '</td><td class="kba-mytype">' + value.mytype + '</td></tr>' );
            });
            $( '#crm-cars-table tr' ).click( function(){
                $.ajax({
                    url: 'crm/ajax/crm.app.php',
                    type: 'POST',
                    data:  { action: 'getCar', data: { 'id': this.id } },
                    success: function( crmData ){
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
        $( '#crm-invoices-table tr' ).click( function(){
            $.ajax({
                url: 'crm/ajax/crm.app.php',
                type: 'POST',
                data:  { action: 'getInvoice', data: { 'id': this.id } },
                success: function( crmData ){
                    console.info( 'invoice' );
                    console.info( crmData );
                    crmEditOrderDlg( crmData, crmOrderTypeEnum.Invoice );
                },
                error: function( xhr, status, error ){
                    $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'showCVPA().getInvoice', xhr.responseText );
                }
            });
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
                tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><input type="text" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '" value="' + ( ( exists(item.data) )? item.data : '' ) + '"' +( (item.readonly)? ' readonly' : '' )  + '></input>';
                if( item.check ) tabledata += '<input type="checkbox" id="' + item.check + '" name="'+ item.check + '" title="' + kivi.t8( 'Check imput' ) + '"></input>';
            }
            if( item.type == 'textarea' ) tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><textarea id="' + item.name + '" name="'+ item.name + '" cols="' + item.cols + '" rows="' + item.rows + '" title="' + kivi.t8( item.tooltip ) + '"></textarea>';
            if( item.type == 'password' ) tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><input type="password" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '"></input>';
            if( item.type == 'select' ){
                tabledata += '<td>' + kivi.t8( item.label ) + '</td><td><select type="select" id="' + item.name + '" name="'+ item.name + '" title="' + kivi.t8( item.tooltip ) + '"' + ( (item.disabled)? ' disabled' : '' )  + '>';
                $.each( item.data, function( key, value ){ tabledata += '<option value="' + key + '">' + kivi.t8( value ) + '</option>'; } );
                tabledata += '</select>';
            }
            if( item.hasOwnProperty( 'info' ) ){
                tabledata += '<button id="' + item.info + '">' + kivi.t8( 'Info' ) + '</button>';
            }
            if( item.type == 'button' ) tabledata += '<td></td><td><button id="' + item.name + '">' + kivi.t8( item.label ) + '</button>';
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

function crmUpdateDB( call, dbUpdataData, onSuccess = null ){
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
            if( null != onSuccess ) onSuccess();
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmUpdateDB()', xhr.responseText );
        }
    });
}

/************************************
* Formatiert die Namen vom FS-Scan:
* Verkürtzt den Namen auf ein Vornamen und Nachnamen
* und sorgt für die richtige Groß-/Kleinschreibung
************************************/
function crmFormatName( name ){
    let rs = null;
    let parts = name.split( ' ' );
    if( parts.length > 1 ) parts = [ parts[0], parts[parts.length - 1] ];
    for( let str of parts ){
        if( rs === null ) rs = ''; else rs += ' ';
        rs += str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }
    return rs;
}

function crmDateTimePickerAddButton( input ){
    setTimeout( function(){  //Timeout to force this handler to load after pageLoad for shorter initial loading time
        var buttonPane = $( input ).datepicker( "widget" ).find( ".ui-datepicker-buttonpane" );
        var btn = $( '<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button"> Wartet</button>' );
        btn.appendTo( buttonPane );
        btn.bind( "click", function(){
            $( "#od-oe-finish_time" ).val("Kunde wartet! SOFORT anfangen!").change();
        });
    }, 1 );
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

$( '#crm-wf-new-order' ).click( function() {
    alert( "Auftrag ohne Auto erstellen!" );
});

$( '#crm-wf-new-customer' ).click( function() {
    crmNewCustomer("0");
});

$( '#crm-wf-new-vendor' ).click( function() {
    crmNewCustomer("1");
});

$( '#crm-wf-new-person' ).click( function() {
    crmNewPerson();
});

$( '#crm-wf-search-order' ).click( function() {
   crmSearchOrder( crmSearchOrderDlg );
});
