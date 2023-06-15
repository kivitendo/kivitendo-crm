// Verknüpft über das Dropdown-Menü: Bearbeiten -> Auftragsuche

// Letzte Parameter sind optional: 1. Mazimale Zeilen, danach wird eine zweite Spalte hunzugefügt; 2. id für die Versteckten Eingabefelder
crmInitFormEx( searchOrderFormModel, '#search-order-form', 4, null /* '#search-order-hidden' */ );
$( '#search_order-status' ).append( new Option( 'alle', 'alle' ) );
$( '#search_order-status' ).append( new Option( 'angenommen', 'angenommen' ) );
$( '#search_order-status' ).append( new Option( 'bearbeitet', 'bearbeitet' ) );
$( '#search_order-status' ).append( new Option( 'abgerechnet', 'abgerechnet' ) );
$( '#search_order-status' ).append( new Option( 'nicht abgerechnet', 'nicht abgerechnet' ) );

$( '#search_order-date_from' ).datepicker({});
$( '#search_order-date_to' ).datepicker({});

$( '#search_order-customer_name' ).autocomplete({
    source: "crm/ajax/crm.app.php?action=searchCustomer",
    close: function( e, ui ) {
        console.info( ui );
        crmSearchOrder();
    }
})

$( '#search_order-car_license' ).autocomplete({
    source: "crm/ajax/crm.app.php?action=searchCarLicense",
    close: function( e, ui ) {
        console.info( ui );
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
    console.info( 'Search order' );

    dbData = {};
    for( let item of searchOrderFormModel ){
        let columnName = item.name.split( '-' );
        let val = $( '#' + item.name ).val();
        if( exists(val) ){
            dbData[columnName[1]] = val;
        }
    }
    console.info( dbData );

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'searchOrder', data: dbData },
        success: function( data ){
            console.info( data );

            $( '#crm-search-order-table' ).html( '' );
            let listrow0 = false;
            $.each( data.rs, function( key, value ){
                $( '#crm-search-order-table' ).append( '<tr id="' + value.id +'" class="' + ( ( listrow0 =! listrow0 ) ? "listrow0" : "listrow1" ) + '"><td>' +  value.owner + '</td><td>' + value.c_ln  + '</td><td>' + value.description  + '</td><td>' + value.car_manuf + '</td><td>' + value.car_type + '</td><td>' + value.transdate + '</td><td>' + value.ordnumber + '</td><td>' + value.status + '</td></tr>' );
            });
            $( '#crm-search-order-table tr' ).click( function(){
                $.ajax({
                    url: 'crm/ajax/crm.app.php',
                    type: 'POST',
                    data:  { action: 'getOrder', data: { 'id': this.id } },
                    success: function( crmData ){
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

const crmSearchOrderDlg = function(){
    for( let item of searchOrderFormModel ){
         $( '#' + item.name ).val( '' );
    }
    $( '#search_order-status' ).val( 'nicht abgerechnet' );

    $( '#crm-search-order-dialog' ).dialog({
        autoOpen: false,
        resizable: true,
        minWidth: 1402,
        width: 'auto',
        minHeight: 900,
        height: 'auto',
        modal: true,
        title: kivi.t8( 'Search Order' ),
        position: { my: "top", at: "top+250" },
        open: function(){
            $( this ).css( 'maxWidth', window.innerWidth );
        },
        close: function(){
        },
        buttons:[{
            text: kivi.t8( 'Clear' ),
            click: function(){
               for( let item of searchOrderFormModel ){
                    $( '#' + item.name ).val( '' );
               }
               $( '#search_order-status' ).val( 'nicht abgerechnet' );
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
