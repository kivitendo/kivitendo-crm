// Verknüpft über das Dropdown-Menü: Bearbeiten -> Auftragsuche

// Letzte Parameter sind optional: 1. Mazimale Zeilen, danach wird eine zweite Spalte hunzugefügt; 2. id für die Versteckten Eingabefelder
crmInitFormEx( searchOrderFormModel, '#search-order-form', 4, null /* '#search-order-hidden' */ );
$( '#search_order-status' ).append( new Option( 'alle', 'alle' ) );
$( '#search_order-status' ).append( new Option( 'angenommen', 'angenommen' ) );
$( '#search_order-status' ).append( new Option( 'bearbeitet', 'bearbeitet' ) );
$( '#search_order-status' ).append( new Option( 'abgerechnet', 'abgerechnet' ) );
$( '#search_order-status' ).append( new Option( 'nicht abgerechnet', 'nicht abgerechnet' ) );
$( '#search_order-status' ).val( 'nicht abgerechnet' );

$( '#search_order-date_from' ).datepicker({});
$( '#search_order-date_to' ).datepicker({});

$( '#search_order-customer_name' ).autocomplete({
    source: "crm/ajax/crm.app.php?action=searchCustomer",
    close: function( e, ui ) {
        crmSearchOrder();
    }
})

$( '#search_order-car_license' ).autocomplete({
    source: "crm/ajax/crm.app.php?action=searchCarLicense",
    close: function( e, ui ) {
        crmSearchOrder();
    }
})

$( '#search_order-car_manuf' ).autocomplete({
    source: "crm/ajax/crm.app.php?action=searchCarManuf",
    close: function( e, ui ) {
        crmSearchOrder();
    }
})

$( '#search_order-car_type' ).autocomplete({
    source: "crm/ajax/crm.app.php?action=searchCarType",
    close: function( e, ui ) {
        crmSearchOrder();
    }
})

$( '#search_order-car_brand' ).autocomplete({
    source: "crm/ajax/crm.app.php?action=searchCarBrand",
    close: function( e, ui ) {
        crmSearchOrder();
    }
})

for( let item of searchOrderFormModel ){
    $( '#' + item.name ).keyup( function(){
        crmSearchOrder();
    }).change( function(){
        crmSearchOrder();
    });
}

function crmSearchOrder( onSuccess = null ){
    dbData = {};
    for( let item of searchOrderFormModel ){
        let columnName = item.name.split( '-' );
        let val = $( '#' + item.name ).val();
        if( exists(val) ){
            dbData[columnName[1]] = val;
        }
    }

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'searchOrder', data: dbData },
        success: function( data ){

            $( '#crm-search-order-table' ).html( '' );
            let listrow0 = false;
            $.each( data.rs, function( key, value ){
                $( '#crm-search-order-table' ).append( '<tr id="' + value.id +'" class="' + ( ( listrow0 =! listrow0 ) ? "listrow0" : "listrow1" ) + '"><td>' +  value.owner + '</td><td>' + value.c_ln  + '</td><td>' + value.description  + '</td><td>' + getValueNotNull( value.car_manuf ) + '</td><td>' + getValueNotNull( value.car_type ) + '</td><td>' + value.transdate + '</td><td>' + value.ordnumber + '</td><td>' + value.status + '</td></tr>' );
            });
            $( '#crm-search-order-table tr' ).click( function(){
                $.ajax({
                    url: 'crm/ajax/crm.app.php',
                    type: 'POST',
                    data:  { action: 'getOrder', data: { 'id': this.id } },
                    success: function( crmData ){
                        crmRefreshAppView( 'C', crmData.order.common.customer_id );
                        crmEditOrderDlg( crmData );
                    },
                    error: function( xhr, status, error ){
                        $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmSearchOrder().getOrder', xhr.responseText );
                    }
                });
            });

            if( null != onSuccess) onSuccess();
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmSearchOrder()', xhr.responseText );
        }
    });
}

function crmClearSearchOrderDlg(){
    for( let item of searchOrderFormModel ){
         $( '#' + item.name ).val( '' );
    }
    $( '#search_order-status' ).val( 'nicht abgerechnet' );
}
console.info( window.innerWidth );
const crmSearchOrderDlg = function(){
    for( let item of searchOrderFormModel ){
        if( 'search_order-status' == item.name ){
            $( '#search_order-status' ).val( 'nicht abgerechnet' );
        }
        else{
            $( '#' + item.name ).val( '' );
        }
    }

    //Dialog Auftragssuche
    $( '#crm-search-order-dialog' ).dialog({
        autoOpen: false,
        resizable: true,
        width: window.innerWidth * 0.8 + 'px', //Breite Dialog relativ zur Fenstergröße
        minWidt: '1024px',
        height: 'auto',
        modal: true,
        title: kivi.t8( 'Search Order' ),
        position: { my: "top", at: "top+250" },
        open: function(){
            //$( this ).css( 'minWidth', window.innerWidth * 0.5 + 'px' );
            $( this ).css( 'maxWidth', window.innerWidth - 50 + 'px' );
            $( this ).css( 'minHeight', window.innerHeight - 400 + 'px' );
        },
        close: function(){
            crmClearSearchOrderDlg();
        },
        buttons:[{
            text: kivi.t8( 'Clear' ),
            click: function(){
                crmClearSearchOrderDlg();
                crmSearchOrder();
            }
        },{
            text: kivi.t8( 'Close' ),
            click: function(){
                $( this ).dialog( "close" );
            }
        }]
    }).dialog( 'open' ).resize();
}
