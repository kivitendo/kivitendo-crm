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
    delay: crmAcDelay,
    source: "crm/ajax/crm.app.php?action=searchCustomer",
    close: function( e, ui ) {
        crmSearchOrder();
    }
})

$( '#search_order-car_license' ).autocomplete({
    delay: crmAcDelay,
    source: "crm/ajax/crm.app.php?action=searchCarLicense",
    close: function( e, ui ) {
        crmSearchOrder();
    }
})

$( '#search_order-car_manuf' ).autocomplete({
    delay: crmAcDelay,
    source: "crm/ajax/crm.app.php?action=searchCarManuf",
    close: function( e, ui ) {
        crmSearchOrder();
    }
})

$( '#search_order-car_type' ).autocomplete({
    delay: crmAcDelay,
    source: "crm/ajax/crm.app.php?action=searchCarType",
    close: function( e, ui ) {
        crmSearchOrder();
    }
})

$( '#search_order-car_brand' ).autocomplete({
    delay: crmAcDelay,
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
            let bgcolor = null;
            $.each( data.rs, function( key, value ){
                if( 'angenommen' == value.status && 'Auto hier' == value.car_status ) bgcolor = 'rgb(255, 255, 0)';
                else if( 'angenommen' == value.status && 'Auto nicht hier' == value.car_status ) bgcolor = 'rgb(255, 0, 0)';
                else if( 'angenommen' == value.status && 'Bestellung' == value.car_status ) bgcolor = 'lightskyblue';
                else if( 'bearbeitet' == value.status && 'Auto hier' == value.car_status ) bgcolor = 'rgb(0, 128, 0)';
                else if( 'bearbeitet' == value.status && 'Auto nicht hier' == value.car_status ) bgcolor = 'grey';
                else bgcolor = null;
                $( '#crm-search-order-table' ).append( '<tr id="' + value.id +'" class="' + ( ( listrow0 =! listrow0 ) ? "listrow0" : "listrow1" ) + '"' +( (bgcolor != null)? 'style="background-color: ' + bgcolor + '"' : '' ) + '><td>' +  value.owner + '</td><td>' + value.c_ln  + '</td><td>' + value.description  + '</td><td>' + getValueNotNull( value.car_manuf ) + '</td><td>' + getValueNotNull( value.car_type ) + '</td><td>' + value.transdate + '</td><td>' + value.ordnumber + '</td><td>' + value.status + ( ( !isEmpty( value.status ) && !isEmpty( value.car_status ) )? ' / ' : '' ) + value.car_status + '</td></tr>' );
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

function crmClearSearchOrderView(){
    for( let item of searchOrderFormModel ){
         $( '#' + item.name ).val( '' );
    }
    $( '#search_order-status' ).val( 'nicht abgerechnet' );
}

const crmSearchOrderView = function(){
    for( let item of searchOrderFormModel ){
        if( 'search_order-status' == item.name ){
            $( '#search_order-status' ).val( 'nicht abgerechnet' );
        }
        else{
            $( '#' + item.name ).val( '' );
        }
    }

    crmOpenView( 'crm-search-order-view', kivi.t8( 'Search order' ), '' );
}

function crmSearchOrderClearView(){
    crmClearSearchOrderView();
    crmSearchOrder();
}

function crmSearchOrderCloseView(){
    crmCloseView( 'crm-search-order-view' );
    crmClearSearchOrderView();
}
