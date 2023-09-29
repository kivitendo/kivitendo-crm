$( '#crm-phonecall-list-view-close-btn' ).click( function(){
    crmCloseView( 'crm-phonecall-list-view' );
});

function crmPhoneCallListView(){
    crmOpenView( 'crm-phonecall-list-view' );  // 'crm-phonecall-list-view' ist ein 'div-container' in app.php

    //Aktuallisieren der Hauptansicht: crmRefreshAppViewAction( src, id ) in js/crm.app/crm.app.js
    // src ist 'C' für Customer, 'V' für Vendor und id die DB-Tabellen id
}
