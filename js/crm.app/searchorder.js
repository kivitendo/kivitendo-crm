// Verknüpft über das Dropdown-Menü: Bearbeiten -> Auftragsuche

// Letzte Parameter sind optional: 1. Mazimale Zeilen, danach wird eine zweite Spalte hunzugefügt; 2. id für die Versteckten Eingabefelder
crmInitFormEx( searchOrderFormModel, '#search-order-form' /*, 5, '#search-order-hidden' */ );

function crmSearchOrder(){
    console.info( 'Serach' );
    $( '#crm-search-order-dialog' ).dialog({
        autoOpen: false,
        resizable: true,
        width: 'auto',
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
            text: kivi.t8( 'Action' ),
            click: function(){
            }
        },{
            text: kivi.t8( 'Close' ),
            click: function(){
                $( this ).dialog( "close" );
            }
        }]
    }).dialog( 'open' ).resize();
}
