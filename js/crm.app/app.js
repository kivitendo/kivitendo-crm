// Regulärer Auusdruck ( regex ) zum prüfen einer Telefonummer
// ^(|(\+|0)[0-9]([-.\s\(\)\/]{0,3}[0-9])*)$

//info ist die Überschrift, messageist der Fehler, assertion ist eine Bedingung
function assert( info, message = null, assertion = true ){
    if( assertion ){
        console.info( '-- ' + info + ' -->' );
        if( null != message ) console.info( message );
        console.info( '<--' );
    }
}

function writeLog( info = 'WriteLog!', message = null, assertion = true, reverse = false ){
    if( assertion ){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'writeLogFromJs', data: { 'info': info, 'message': message, 'reverse': reverse } },
            success: function(){
                console.info( '--> writeLog aufgerufen. Lies log/debug.log <---' );
            },
            error: function( xhr, status, error ){
                console.info( 'Aufruf von writeLog via Ajax fehlgeschlagen' );
            }
        });
    }
}

function assertSelector( selector, message = null ){
    assert( 'assertSelector: ' + selector, ( null == message )? 'do not exists' : message, 0 == $( selector ).length );
}

function assertSelectorCheck( selector ){
    message = $( selector ).length + ' elements found';
    assert( 'selectorCheck: ' + selector, message );
}

function exists( obj ){
    return obj !== null && obj !== undefined;
}

function isIterable( obj ){
    return exists( obj ) && obj.hasOwnProperty('length');
}

function isEmpty( obj ){
    return isIterable( obj ) && obj.length === 0;
}

function getValueNotNull( value, default_value = '' ){
    return ( null == value )? default_value : value;
}

function existsOrEmptyString( value ){
    return ( exists( value ) )? value : '';
}

//Delay für Autocomplete und Catcomplete
const crmAcDelay = 100;

//Custom Font size
const crmFontSize = 14;

$( '.crm-fs' ).css({ fontSize: crmFontSize });
$( '.layout-actionbar div.layout-actionbar-action' ).css({ fontSize: crmFontSize });
$( '.nav-link' ).css({ fontSize: crmFontSize });
$( 'button' ).css({ fontSize: crmFontSize });
$( 'input' ).css({ fontSize: crmFontSize });
$( 'style' ).append('#content input, #content select, #content option, #content textarea { font-size: ' + crmFontSize + 'px }' );

$( '#crm-tabs-main' ).tabs();
$( '#crm-tabs-infos' ).tabs();

$( '#crm-wf-edit').html( kivi.t8( 'Base data' ) );
$( '#crm-wf-scan').html( kivi.t8( 'Car from scan' ) );

/* flag to switch app lxcars functionality  */
var lxcars = false;
crmGetLxcarsVer();

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
                crmGetHistory();
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'lxcars()', xhr.responseText );
            window.open( 'controller.pl?action=LoginScreen/user_login', '_self' );
        }
    });
}

function crmGetHistory( refresh = true ){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getHistory' },
        success: function( data ){
            $( '#crm-history-list' ).html('');
            if( data ){
                for( let entry of data ){
                    let id = 'crm-hist-entry-' + entry[2]  + entry[0];
                    $( '#crm-history-list' ).append( '<div class="layout-actionbar-action layout-actionbar-submit" data-src="' + entry[2] +'" data-id="' + entry[0] + '" id="' + id + '">' + entry[1] + '</div>');
                    $( '#' + id ).click( function(){
                        crmRefreshAppView( entry[2], entry[0] );
                        crmCloseView();
                    });
                }
                if( refresh ) getCVPA( data[0][2], data[0][0] );// ( CV, id )
            }
        },
        error: function(xhr, status, error){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'getHistory()', xhr.responseText );
        }
    });
}

$.widget("custom.catcomplete", $.ui.autocomplete, {
    _renderMenu: function (ul, items) {
        var that = this,
            currentCategory = "";
        $.each(items, function (index, item) {
            if (item.category != currentCategory) {
                ul.append("<li class='ui-autocomplete-category'>" + item.category + "</li>");
                currentCategory = item.category;
            }
            that._renderItemData(ul, item);
        });
    }
});

$(function () {
    let lastItems = []; // merken der letzten Vorschläge

    $("#crm-widget-quicksearch").catcomplete({
        delay: crmAcDelay,
        minLength: 3,
        source: function (request, responseCallback) {
            $.getJSON("crm/ajax/crm.app.php?action=fastSearch", request, function (data) {
                lastItems = data;
                responseCallback(data);
            });
        },

        select: function (e, ui) {
            crmRefreshAppView(ui.item.src, ui.item.id);
            crmCloseView();
        }
    }).on("keydown", function (e) {
        if (e.key === "Enter") {
            const $input = $(this);
            const menu = $input.catcomplete("widget");
            const focused = menu.find(".ui-state-focus");
            const items = menu.find("li.ui-menu-item:visible:not(.ui-autocomplete-category)");

            if (!focused.length && items.length && lastItems.length) {
                const first = items.first();
                const itemData = first.data("ui-autocomplete-item");

                if (itemData) {
                    const instance = $input.data("custom-catcomplete");
                    if (instance) {
                        instance._trigger("select", e, { item: itemData });
                        e.preventDefault();
                        $input.val(""); //Hier wird das Eingabefeld geleert
                        instance.close(); //Menü einklappen
                    }
                }
            }
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
            //writeLog( 'getCVPA', data );
            if( exists( data.car ) ){
                crmEditCarDlg( data );
            }
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'getCVPA', xhr.responseText );
        }
    });
}

//Aktuallisieren der Hauptansicht: crmRefreshAppViewAction( src, id ) in js/crm.app/crm.app.js
// src ist 'C' für Customer, 'V' für Vendor und id die DB-Tabellen id
function crmRefreshAppView( src, id ){
    getCVPA( src, id );
    crmGetHistory( false );
}

//Aktuallisieren der Hauptansicht mit dem Aktuellen Kunden/Liefernaten
function crmRefreshAppViewAction( ){
    const src = $( '#crm-cvpa-src' ).val();
    const id = $( '#crm-cvpa-id' ).val();
    if( '' != src && '' != id) getCVPA( src, id );
}

function crmGetCVSrc(){
    return $( '#crm-cvpa-src' ).val();
}

function crmGetCVId(){
    return $( '#crm-cvpa-id' ).val();
}

$( '#crm-widget-quicksearch' ).focus();

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

//Fehlermeldung wird im Dialog in einem 'div' angezeigt (im Dialog muss ein 'div' mit der Klasse 'crm-dialog-error-view' eingefügt werden):
$.fn.crmDialogShowError = function( id, text ){
    $( this ).find( '.crm-dialog-error-view' ).find( '#' + id ).remove();
    $( this ).find( '.crm-dialog-error-view' ).append( '<div id="' + id + '" class="crm-dialog-error" style="color: red">' + text + '</div>' );
}
//Fehlermeldung wird im Dialog wenn der Fehler behoben ist entfernt:
$.fn.crmDialogRemoveError = function( id ){
    $( this ).find( '.crm-dialog-error-view' ).find( '#' + id ).remove();
}
$.fn.crmDialogHasErrors = function(){
    return $( this ).find( '.crm-dialog-error-view' ).find( '.crm-dialog-error' ).length > 0;
}
$.fn.crmDialogClearErrors = function(){
    $( this ).find( '.crm-dialog-error-view' ).find( '.crm-dialog-error' ).remove();
}

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
    //console.info( 'showCVPA' );
    //console.info( data );
    if( data.cv ){
        $( '#crm-cvpa-src' ).val( data.cv.src );
        $( '#crm-cvpa-id' ).val( data.cv.id );
        $( '#crm-cvpa-name' ).val( data.cv.name + ' (' + ( ( data.cv.src == 'C' ) ? kivi.t8( 'Customer' ) : kivi.t8( 'Vendor' ) ) + ')' );
        $( '#crm-cvpa-emp_name' ).val( data.emp_name ? data.emp_name[0] : '' ); // Mitarbeitername

        $( '#crm-wx-contact' ).show();
        $.each( data.cv, function( key, value ){
            if( value ){
                $( '#crm-contact-' + key ).html( value );
                $( '#crm-contact-' + key ).val( value );
                $( '#crm_inv_contact_' + key ).html( value );
                $( '#crm_inv_contact_' + key ).val( value );
                $( '#crm_oe_contact_' + key ).html( value );
                $( '#crm_oe_contact_' + key ).val( value );
                $( '#crm_off_contact_' + key ).html( value );
                $( '#crm_off_contact_' + key ).val( value );
             }
            else{
                $( '#crm-contact-' + key ).html( '' );
                $( '#crm-contact-' + key ).val( '' );
                $( '#crm_inv_contact_' + key ).html( '' );
                $( '#crm_inv_contact_' + key ).val( '' );
                $( '#crm_oe_contact_' + key ).html( '' );
                $( '#crm_oe_contact_' + key ).val( '' );
                $( '#crm_off_contact_' + key ).html( '' );
                $( '#crm_off_contact_' + key ).val( '' );
            }
        });
        if( !data.cv.phone1 ) $( '.clickToCall1' ).hide();
        else $( '.clickToCall1' ).show();
        if( !data.cv.phone2 ) $( '.clickToCall2' ).hide();
        else $( '.clickToCall2' ).show();
        if( !data.cv.phone3 ) $( '.clickToCall3' ).hide();
        else $( '.clickToCall3' ).show();
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
        $( '#crm-offers-table tr' ).click( function(){
            $.ajax({
                url: 'crm/ajax/crm.app.php',
                type: 'POST',
                data:  { action: 'getOffer', data: { 'id': this.id } },
                success: function( crmData ){
                    crmEditOrderDlg( crmData, crmOrderTypeEnum.Offer );
                },
                error: function( xhr, status, error ){
                    $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'showCVPA().getOffer', xhr.responseText );
                }
            });
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
                    crmEditOrderDlg( crmData, crmOrderTypeEnum.Invoice );
                },
                error: function( xhr, status, error ){
                    $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'showCVPA().getInvoice', xhr.responseText );
                }
            });
        });
    }

    if( exists( data.cv ) ){
        if( 'crm-wx-base-data' == crmActiveView ) crmSetMainTitle( data.cv.name + ' (' + ( ( data.cv.src == 'C' ) ? kivi.t8( 'Customer' ) : kivi.t8( 'Vendor' ) ) + ')' );
        $( '#crm-wf-edit' ).attr( 'data-src', data.cv.src );
        $( '#crm-wf-edit' ).attr( 'data-id', data.cv.id );
    }

    $( '#crm-vars-table' ).html( '' );
    if( exists( data.custom_vars ) ){
        let listrow0 = false;
        $.each( data.custom_vars, function( key, value ){
            $( '#crm-vars-table' ).append( '<tr id="' + value.id +'" class="' + ( ( listrow0 = !listrow0 ) ? "listrow0" : "listrow1" ) + '"><td>' + value.description  + '</td><td>' + value.value  + '</td></tr>' );
        });
    }

    $( '#crm-contacts-table' ).html( '' );
    if( exists( data.contacts ) ){
        let listrow0 = false;
        $.each( data.contacts, function( key, value ){
            let tabline = '<div><div class="contact-box" style="width: 100%; min-height: 80px; margin: 5px;"><div style="padding-bottom: 1em"><button onclick="crmEditContactPerson( ' + value.cp_id + ' );">' + getValueNotNull( value.cp_givenname )  + ' ' + getValueNotNull( value.cp_name ) + '</button></div>';
            for( let i = 1; i < 3; i++ ){
                    if( exists( value['cp_phone' + i] ) ){
                        tabline += '<table><tr><td>Telefon:</td>'
                            + '<td><button id="cp_phone1" class="ui-contact-btn" onclick="crmClickToCall(' + value['cp_phone' + i] + ')">' + value['cp_phone' + i] + '</button></td>'
                            + '<td><button id="" class="clickToCall1 ui-contact-fx-btn" onclick="crmPhoneCallConfigDlg(' + '\'' + value['cp_phone' + i] + '\'' + ')">T</button></td>'
                            + '<td><button id="" class="copy clickToCall1 ui-contact-fx-btn" title="Copy" onclick="crmCopyToClipboard(' + value['cp_phone' + i] + ')">C</button></td>'
                            + '<td ><button id="" class="whatsapp clickToCall1 ui-contact-fx-btn" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td></tr>'
                            + '</table>';
                    }
                }
                if( exists( value.cp_email )  && '' != value.cp_email ){
                    tabline += '<table><tr><td>E-Mail:</td>'
                            + '<td><button>' + value.cp_email + '</button></td></table>';
                }
                tabline += '</div></div>';
                $( '#crm-contacts-table' ).append( tabline );
        });
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
function crmInitFormEx( crmFormModel, table, max_rows = 0, container = null, callback = null ){
    //alert( 'Bin in crmInitFormEx' );
    let tabledata = '';
    let hiddenFields = '';
    if(max_rows <= 0 || max_rows > crmFormModel.length) max_rows = crmFormModel.length;
    for( let i = 0; i < max_rows; i++ ){
        let item = crmFormModel[i];
        tabledata += '<tr>';
        let addItem = function( item ){
            if( item.type == 'hidden' ) hiddenFields += '<input type="hidden" id="' + item.name + '" name="' + item.name + '" value="' + ( ( exists(item.data) )? item.data : '' ) + '">';
            if( item.hasOwnProperty( 'spacing' ) ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + ' style="padding-left: 10px"> </td>';
            if( item.type == 'headline' ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + ' colspan="2"><b>' + kivi.t8( item.label ) + '</b>';
            if( item.type == 'checkbox' ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '>' + kivi.t8( item.label ) + '</td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><input type="checkbox" id="' + item.name + '" name="'+ item.name + '" value="true" title="' + kivi.t8( item.tooltip ) + '"></input>';
            if( item.type == 'input' ){
                tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '>' + kivi.t8( item.label ) + '</td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><input type="text" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '" value="' + ( ( exists(item.data) )? item.data : '' ) + '"' +( (item.readonly)? ' readonly' : '' )  + ' ' + ( (exists( item.autofocus ) )? 'autofocus' : '' ) + '></input>';
                if( item.check ) tabledata += '<input type="checkbox" id="' + item.check + '" name="'+ item.check + '" title="' + kivi.t8( 'Check imput' ) + '"></input>';
            }
            if( item.type == 'textarea' ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '>' + kivi.t8( item.label ) + '</td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><textarea id="' + item.name + '" name="'+ item.name + '" cols="' + item.cols + '" rows="' + item.rows + '" title="' + kivi.t8( item.tooltip ) + '"></textarea>';
            if( item.type == 'password' ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '>' + kivi.t8( item.label ) + '</td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><input type="password" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '"></input>';
            if( item.type == 'number' ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '>' + kivi.t8( item.label ) + '</td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><input type="number" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '"></input>';
            if( item.type == 'date' ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '>' + kivi.t8( item.label ) + '</td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><input type="text" id="' + item.name + '" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '"></input>';
            if( item.type == 'part' ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '>' + kivi.t8( item.label ) + '</td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><input type="hidden" id="' + item.name + '"></input><input type="text" id="' + item.name + '-label" name="'+ item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + ' " class="crm-ignore-field"' + ( (exists( item.autofocus ) )? 'autofocus' : '' ) + '></input>';
            if( item.type == 'customer' ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '>' + kivi.t8( item.label ) + '</td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><input type="hidden" id="' + item.name + '"></input><input type="text" id="' + item.name + '-label" name="' + item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '" class="crm-ignore-field"></input>';
            if( item.type == 'vendor' ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '>' + kivi.t8( item.label ) + '</td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><input type="hidden" id="' + item.name + '"></input><input type="text" id="' + item.name + '-label" name="' + item.name + '" size="' + item.size + '" title="' + kivi.t8( item.tooltip ) + '" class="crm-ignore-field"></input>';
            if( item.type == 'select' ){
                tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '>' + kivi.t8( item.label ) + '</td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><select type="select" id="' + item.name + '" name="'+ item.name + '" title="' + kivi.t8( item.tooltip ) + '"' + ( (item.disabled)? ' disabled' : '' )  + '>';
                $.each( item.data, function( key, value ){ tabledata += '<option value="' + key + '">' + kivi.t8( value ) + '</option>'; } );
                tabledata += '</select>';
            }
            if( item.hasOwnProperty( 'info' ) ){
                tabledata += '<button id="' + item.info + '">' + kivi.t8( 'Info' ) + '</button>';
            }
            if( item.hasOwnProperty( 'print' ) ){
                tabledata += '<button id="' + item.print + '">' + 'drucken' + '</button>';
            }
            if( item.type == 'button' ) tabledata += '<td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '></td><td' +  ( ( exists(item.class) )? ' class="' + item.class + '" ' : '' ) + '><button id="' + item.name + '">' + kivi.t8( item.label ) + '</button>';
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

var crmActiveView = 'crm-wx-base-data';
var crmPreView = 'crm-wx-base-data';
window.history.pushState( { 'view': crmActiveView }, '', location.protocol + '//' + location.host + location.pathname /*+ (location.search?location.search : '')*/ );

function crmCVPAgetTitle(){ //kann weg??
    return $( '#crm-cvpa-name' ).val();
}

// crmSetTitle (Suchbegriff)
// Setzt den Titel der App in der Navigationsleiste
function crmSetMainTitle( title = null, subtitle = null ){ //Ich würde es nicht zulassen dass die Funktion ohne Parameter aufgerufen wird!!!
    //console.info( 'crmSetMainTitle' );
    //console.info( title );
    //console.info( subtitle );
    setTimeout( function(){
        $( '#crm-wx-title' ).html( (  title == null)? $( '#crm-cvpa-name' ).val() : title );
        if(  subtitle != null ) $( '#crm-wx-subtitle' ).html( subtitle );
    }, 200 ); // Verzögerung von 200ms
    document.title = 'LxCars'; //( title == null )? crmCVPAgetTitle() : title;
}

function crmOpenView( id, title = null, subtitle = null ){
    crmSetMainTitle( title, subtitle );
    $( '#' + crmActiveView ).hide();
    $( '#' + id ).show();
    crmPreView = crmActiveView;
    crmActiveView = id;
    window.history.pushState( { 'view': crmPreView }, '', location.protocol + '//' + location.host + location.pathname /*+ (location.search?location.search : '') + '#' + id*/ );
}

function crmCloseView( id = null, next = 'crm-wx-base-data', title = null, subtitle = '' ){
    if( null != id ) crmActiveView = id;
    crmOpenView( next, title, subtitle );
}

window.onpopstate = function( e ){
    if( exists( e.state ) && exists( e.state.view ) ){
        crmRefreshAppViewAction( );
        crmOpenView( 'crm-wx-base-data', null, '' );
    }
    else{
        crmOpenView( 'crm-wx-base-data', null, '' );
    }
};


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

function crmUpdateDB( call, dbUpdateData, onSuccess = null ){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: call, data: dbUpdateData },
        success: function( data ){
            dbUpdateData = {};
            if( exists( data.success ) && !data.success ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'DB update error' ), kivi.t8( 'Error in: ' ) + 'crmUpdateDB()', ( ( exists( data.debug )? data.debug : null) ) );
                return;
            }
            if( exists( data.src ) && exists( data.id ) ) crmRefreshAppView( data.src, data.id );
            if( null != onSuccess ) onSuccess( data );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmUpdateDB()', xhr.responseText );
        }
    });
}

$( '#crm-wf-edit' ).click( function(){
    crmGetCustomerForEdit( $( '#crm-wf-edit' ).attr( 'data-src' ), $( '#crm-wf-edit' ).attr( 'data-id' ) );
});

$( '#crm-wf-scan' ).click( function() {
   crmNewCarFromScan();
});

$( '#crm-wf-new-car' ).click( function() {
    if( 'C' != $( '#crm-cvpa-src' ).val() ){
        alert( kivi.t8( 'Cars can only be assigned to customers!' ) );
        return;
    }
    crmEditCarDlg(); //in 'js/car.js'
});

$( '#crm-wf-new-offer' ).click( function() {
    crmNewOffer();
});

$( '#crm-wf-new-order' ).click( function() {
    alert( "Auftrag ohne Auto erstellen!" );
});

$( '#crm-wf-new-customer' ).click( function() {
    //crmNewCustomer("0");
    crmNewCVP( crmCVPtypeEnum.Customer );
});

$( '#crm-wf-new-vendor' ).click( function() {
    //crmNewCustomer("1");
    crmNewCVP( crmCVPtypeEnum.Vendor );
});

$( '#crm-wf-new-person' ).click( function() {
    crmNewCVP( crmCVPtypeEnum.Person );
});

$( '#crm-wf-search-order, #crm-wf-search-order-btn' ).click( function() {
   crmSearchOrder( crmSearchOrderView );
});

$( '#crm-phonecall-list-btn' ).click( function() {
    crmPhoneCallListView();
});

$( '#crm-wf-elfinder' ).click( function() {
    crmCVDocumentsView();
});

$( '#crm-wf-calendar' ).click( function() {
    crmCalendarView();
});

$( '#crm-test-ajax-btn' ).click( function() {
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data: { action: 'testFunction' },
        success: function( data ) {
            alert( JSON.stringify( data ) );
        },
        error: function() {
            alert( 'Error :testFunction() !' )
        }
    });
});

//Route zum Kunden oder Lieferanten anzeigen
$( '#crm-route' ).click( function(){
    let newWindow = window.open( '' );
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data: { action: 'getCompanyAdress' },
        success: function( data ) {
            var routeUrl = 'https://www.google.de/maps/place/' +
                encodeURIComponent($("#crm-contact-street").html() + ', ' +
                                   $("#crm-contact-zipcode").html() + ' ' +
                                   $("#crm-contact-city").html());

            return newWindow.location = routeUrl;
        },
        error: function() {
            alert( 'Error: getCompanyAdress()!' )
        }
    });
});

let qrTimeout = null;
let qrVisible = false;
let qrTimeoutDuration = 5000; // 5 Sekunden

$('#crm-route-qrcode').on('click', function () {
    const $container = $('#crm-qrcode-container');

    // Wenn bereits sichtbar → sofort ausblenden und abbrechen
    if (qrVisible) {
        clearTimeout(qrTimeout);
        $container.fadeOut(function () {
            $container.empty();
            qrVisible = false;
        });
        return;
    }

    // Adresse zusammensetzen
    const street = $('#crm-contact-street').html();
    const zip = $('#crm-contact-zipcode').html();
    const city = $('#crm-contact-city').html();

    const locationUrl = "https://www.google.de/maps/place/" +
                        encodeURIComponent(street + ", " + zip + " " + city);

    // QR-Code erzeugen
    $container.empty().show();
    new QRCode($container[0], {
        text: locationUrl,
        width: 200,
        height: 200,
        correctLevel: QRCode.CorrectLevel.H
    });

    qrVisible = true;

    // Nach qrTimeoutDuration Sekunden automatisch ausblenden
    clearTimeout(qrTimeout);
    qrTimeout = setTimeout(function () {
        $container.fadeOut(function () {
            $container.empty();
            qrVisible = false;
        });
    }, qrTimeoutDuration);
});

$("#crm-whatsapp-share").click(function () {
    var street = $("#crm-contact-street").html();
    var zip = $("#crm-contact-zipcode").html();
    var city = $("#crm-contact-city").html();
    var name = $("#crm-contact-name").html();
    let tel1 = $("#crm-contact-phone1").html();
    let tel2 = $("#crm-contact-phone2").html();
    let tel3 = $("#crm-contact-phone3").html();
    let note_phone1 = $('#crm-contact-note_phone1').val();
    let note_phone2 = $('#crm-contact-note_phone2').val();
    let note_phone3 = $('#crm-contact-note_phone3').val();

    var locationUrl = 'https://www.google.de/maps/place/' +
                    encodeURIComponent(street + ', ' + zip + ' ' + city);

    var message = "Hier ist der Standort von " + name + ":\n" +
                street + ", " + zip + " " + city + "\n" +
                "Google Maps Link: " + locationUrl + "\n" +
                (tel1 ? "\nTel: " + tel1 + (note_phone1 ? " (" + note_phone1 + ")" : "") : "") +
                (tel2 ? "\nTel: " + tel2 + (note_phone2 ? " (" + note_phone2 + ")" : "") : "") +
                (tel3 ? "\nTel: " + tel3 + (note_phone3 ? " (" + note_phone3 + ")" : "") : "") + '\n';


    var whatsappUrl = "";

    // Neue Browser liefern userAgentData (besser als userAgent-String parsen)
    if (navigator.userAgentData && navigator.userAgentData.platform) {
        const platform = navigator.userAgentData.platform.toLowerCase();

        if (platform.includes("windows")) {
            whatsappUrl = "https://wa.me/?text=" + encodeURIComponent(message);
        } else if (platform.includes("linux")) {
            whatsappUrl = "https://web.whatsapp.com/send/?text=" + encodeURIComponent(message) +
                          "&type=custom_url&app_absent=0&utm_campaign=wa_api_send_v2";
        } else {
            whatsappUrl = "https://wa.me/?text=" + encodeURIComponent(message);
        }
    } else {
        // Fallback über userAgent
        var ua = navigator.userAgent;

        if (ua.indexOf("Windows") !== -1) {
            whatsappUrl = "https://wa.me/?text=" + encodeURIComponent(message);
        } else if (ua.indexOf("Linux") !== -1) {
            whatsappUrl = "https://web.whatsapp.com/send/?text=" + encodeURIComponent(message) +
                          "&type=custom_url&app_absent=0&utm_campaign=wa_api_send_v2";
        } else {
            whatsappUrl = "https://wa.me/?text=" + encodeURIComponent(message);
        }
    }

    window.open(whatsappUrl, '_blank');
});




function crmCalendarView( ){
    //window.open("crm/calendar_test.html", "_blank" );
    document.getElementsByName('calendar-frame')[0].src = 'crm/app.plugins/calendar.php';
    crmOpenView( 'crm-plugin-calendar', null, ' - Dokumente' );
}

function crmCalendarCloseView(){
    crmCloseView( 'crm-plugin-calendar' );
}

$( '#crm-contact-email' ).click( function(){
    window.open( 'mailto:' + $( '#crm-contact-email' ).html() );
});

// Nachrichten vom Kalender-iFrame empfangen
window.addEventListener( 'message', function(event) {
    //console.info( 'Erhaltene Nachricht:', event.data );
    if( exists( event.data.openOrder ) ){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'getOrder', data: { 'id': event.data.openOrder } },
            success: function( crmData ){
                crmEditOrderDlg( crmData );
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'showCVPA().getOrder', xhr.responseText );
            }
        });
    }
    else if( exists( event.data.openCar ) ){
        //alert( 'Car: ' + event.data.openCar );
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'getCar', data: { 'id': event.data.openCar } },
            success: function( crmData ){
                //console.info( 'crmData: ', crmData );
                crmEditCarDlg( crmData );
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'showCVPA().getCar', xhr.responseText );
            }
        });
    }
    else if( exists( event.data.openCustomer ) ){
        getCVPA( event.data.openCustomer.src, event.data.openCustomer.id );
        crmCloseView( );
    }
});
