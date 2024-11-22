// Verknüpft über das Dropdown-Menü: Bearbeiten -> Auftragsuche

// Letzte Parameter sind optional: 1. Mazimale Zeilen, danach wird eine zweite Spalte hunzugefügt; 2. id für die Versteckten Eingabefelder
crmInitFormEx( searchOrderFormModel, '#search-order-form', 4, null /* '#search-order-hidden' */ );
$( '#search_order-status' ).append( new Option( 'alle', 'alle' ) );
$( '#search_order-status' ).append( new Option( 'angenommen', 'angenommen' ) );
$( '#search_order-status' ).append( new Option( 'bearbeitet', 'bearbeitet' ) );
$( '#search_order-status' ).append( new Option( 'abgerechnet', 'abgerechnet' ) );
$( '#search_order-status' ).append( new Option( 'nicht abgerechnet', 'nicht abgerechnet' ) );
$( '#search_order-status' ).val( 'nicht abgerechnet' );

$( "#search_order-date_to, #search_order-date_from" ).datetimepicker({
    //onChangeDateTime: function( current_time, $input ){
        //alert( current_time.toLocaleDateString() );
    //},
    lang: kivi.myconfig.countrycode,
    format:'d.m.Y',
    timepicker: false
});

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

document.addEventListener('DOMContentLoaded', function() {
    const filterOrderList = document.getElementById('filter_order_list');
    const showHideOrderListFilterButton = document.getElementById('show_hide_orderlist_filter_button');
    const deleteFilterValuesButton = document.getElementById('delete_filter_values_button');
    // Setze den Filter auf 'none' beim Laden der Seite
    filterOrderList.style.display = 'none';
    deleteFilterValuesButton.style.display = 'none'; 
    // Initialer Text des Buttons basierend auf der Sichtbarkeit von filterOrderList
    showHideOrderListFilterButton.textContent = kivi.t8('Show filter');

    function toggleFilterOrderList() {
        if (filterOrderList.style.display === 'none') {
            filterOrderList.style.display = 'block';
            showHideOrderListFilterButton.textContent = kivi.t8('Hide filter');
            deleteFilterValuesButton.style.display = 'inline-block'; 
        } else {
            filterOrderList.style.display = 'none';
            showHideOrderListFilterButton.textContent = kivi.t8('Show filter');
            deleteFilterValuesButton.style.display = 'none'; 
        }
    }

    showHideOrderListFilterButton.addEventListener('click', toggleFilterOrderList);

    for (let item of searchOrderFormModel) {
        $('#' + item.name).keyup(function() {
            crmSearchOrder();
        }).change(function() {
            crmSearchOrder();
        });
    }
});

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
            let isSeparatorAdded = false; // Variabel, die prüft, ob der Separator schon hinzugefügt wurde
            $.each( data.rs, function( key, value ){
                if( 'angenommen' == value.status && 'Auto hier' == value.car_status ) bgcolor = 'rgb(255, 255, 0)';
                else if( 'angenommen' == value.status && 'Auto nicht hier' == value.car_status ) bgcolor = 'rgb(255, 0, 0)';
                else if( 'angenommen' == value.status && 'Bestellung' == value.car_status ) bgcolor = 'lightskyblue';
                else if( 'bearbeitet' == value.status && 'Auto hier' == value.car_status ) bgcolor = 'rgb(0, 128, 0)';
                else if( 'bearbeitet' == value.status && 'Auto nicht hier' == value.car_status ) bgcolor = 'grey';
                else bgcolor = null;
                
                

                if (moment(value.delivery_ts).isAfter(moment().endOf('day'))) {
                    // Füge die zukünftigen Aufträge hinzu
                    $('#crm-search-order-table').append('<tr id="' + value.id + '" class="' + ((listrow0 = !listrow0) ? "listrow0" : "listrow1") + '" style="opacity: 0.5;"><td>' + value.owner + '</td><td>' + value.c_ln + '</td><td>' + value.description + '</td><td>' + getValueNotNull(value.car_manuf) + '</td><td>' + getValueNotNull(value.car_type) + '</td><td>' + value.delivery_ts_formatted + '</td><td>' + value.ordnumber + '</td><td ' + ((bgcolor != null) ? 'style="background-color: ' + bgcolor + '"' : '') + '>' + value.status + ((!isEmpty(value.status) && !isEmpty(value.car_status)) ? ' / ' : '') + value.car_status + '</td></tr>');
                } else {
                    if (!isSeparatorAdded) {
                        // Füge die leere Zeile nur einmal hinzu, bevor die aktuellen Aufträge kommen
                        $('#crm-search-order-table').append('<tr class="empty-row" style="background-color: pink;"><td colspan="8" style="text-align: center; font-size: 24px;">💖💖💖💖💖 ALLES NUR FÜR DENNIS 💖💖💖💖💖</td></tr>');
                        isSeparatorAdded = true;
                    }
                    // Füge die aktuellen Aufträge hinzu
                    $('#crm-search-order-table').append('<tr id="' + value.id + '" class="' + ((listrow0 = !listrow0) ? "listrow0" : "listrow1") + '"><td>' + value.owner + '</td><td>' + value.c_ln + '</td><td>' + value.description + '</td><td>' + getValueNotNull(value.car_manuf) + '</td><td>' + getValueNotNull(value.car_type) + '</td><td>' + value.delivery_ts_formatted + '</td><td>' + value.ordnumber + '</td><td ' + ((bgcolor != null) ? 'style="background-color: ' + bgcolor + '"' : '') + '>' + value.status + ((!isEmpty(value.status) && !isEmpty(value.car_status)) ? ' / ' : '') + value.car_status + '</td></tr>');
                }
            });
            $('#crm-search-order-table tr.empty-row').click(function() {
                alert( '💖💖💖💖💖 ALLES NUR FÜR DENNIS 💖💖💖💖💖' );
            });
            $( '#crm-search-order-table tr' ).click( function(){
                if ($(this).hasClass('empty-row')) {
                    return; // Beende die Funktion, wenn die Zeile nicht die Klasse 'empty-row' hat
                }
                $.ajax({
                    url: 'crm/ajax/crm.app.php',
                    type: 'POST',
                    data:  { action: 'getOrder', data: { 'id': this.id } },
                    success: function( crmData ){
                        crmRefreshAppView( 'C', crmData.order.common.customer_id );
                        crmEditOrderDlg( crmData );
                        crmSetMainTitle( crmData.order.common.customer_name + ' ( ' + kivi.t8( 'Customer' ) + ' )' );
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

function getNextBusinessDay(){ // liefert die nächsten zwei (maxCount) Geschäftstage zurück
    let date = moment();
    let count = 0;
    const maxCount = 2; //Anzahl der Tage, die übersprungen werden sollen
    while (count < maxCount ){
        date = date.add( 1, 'days' );
        // Prüfen, ob der Tag ein Wochenende ist ( Sonntag = 0)
        if( date.day() !== 0 ) {
            count++;
        }
    }
    return kivi.myconfig.countrycode == 'de' ?  date.format( 'DD.MM.YYYY' ) : date.format( 'YYYY-MM-DD' );
}

function crmClearSearchOrderView(){
    for( let item of searchOrderFormModel ){
         $( '#' + item.name ).val( '' );
    }
    $( '#search_order-status' ).val( 'nicht abgerechnet' );
    //$( '#search_order-date_to' ).val( getNextBusinessDay() );

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
    $( '#search_order-date_to' ).val( getNextBusinessDay() );
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
