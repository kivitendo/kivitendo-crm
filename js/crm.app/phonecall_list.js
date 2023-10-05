$( '#crm-phonecall-list-view-close-btn' ).click( function(){
    crmCloseView( 'crm-phonecall-list-view' );
});

function crmPhoneCallListView(){
    crmOpenView( 'crm-phonecall-list-view' );  // 'crm-phonecall-list-view' ist ein 'div-container' in app.php

    //Aktuallisieren der Hauptansicht: crmRefreshAppViewAction( src, id ) in js/crm.app/crm.app.js
    // src ist 'C' für Customer, 'V' für Vendor und id die DB-Tabellen id
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getPhoneCallList' },
        success: function( data ){
            $( '#phonecall-list-table').html( '' );
            $.each( data, function( key, value ){
                $( '#phonecall-list-table').append( '<tr caller_id ="' + value.crmti_caller_id + '" caller_src="' + value.crmti_caller_typ + '" caller_number="' + value.crmti_src + '"><td>' + getValueNotNull( value.call_date ) + '</td><td>' + getValueNotNull( value['crmti_src'] ) + '</td><td>' + getValueNotNull( value['crmti_dst'] ) + '</td><td>' + getValueNotNull( value['crmti_number'] ) + '</td><td>' + getValueNotNull( value['crmti_caller_typ'] ) + '</td><td>' + getValueNotNull( value['crmti_direction'] ) + '</td><td><button>Aufzeichnung</button><button>Anrufen</button></td></tr>' );
            });

            $( '#phonecall-list-table tr' ).click( function(){
                if( $( this ).attr( 'caller_src' ) != 'K' && $( this ).attr( 'caller_src' ) != 'X' ){
                    crmRefreshAppView( $( this ).attr( 'caller_src' ), $( this ).attr( 'caller_id' ) );
                    crmCloseView( 'crm-phonecall-list-view' );
                }
                else if( $( this ).attr( 'caller_src' ) != 'X' ){
                    crmEditContactPerson( $( this ).attr( 'caller_id' ) );
                    crmCloseView( 'crm-phonecall-list-view' );
                }
                else{
                    crmAssignPhnoneNummber( $( this ).attr( 'caller_number' ) );
                }
            });
        }
    });
}

function crmAssignPhnoneNummber( number ){
    $( '#crm-contact-assign-phone-dialog' ).dialog({
        modal: true,
        title: kivi.t8( 'Assign phone number' ),
        width: 'auto',
        resizable: false,
        open: function( event, ui ){
            $( '#crm-contact-assign-phone-number' ).val( number )
            $( '#crm-assign-phone-contact' ).val( '' );
        }
    });
}

$( '#crm-assign-phone-contact' ).catcomplete({
    delay: crmAcDelay,
    source: "crm/ajax/crm.app.php?action=searchCVP",
    select: function( e, ui ) {
        $( '#crm-contact-assign-phone-id' ).val( ui.item.id );
        $( '#crm-contact-assign-phone-src' ).val( ui.item.src );
    }
});

$( '#crm-contact-assign-phone-btn' ).click( function(){
    const id = $( '#crm-contact-assign-phone-id' ).val();
    const src = $( '#crm-contact-assign-phone-src' ).val();

    crmRefreshAppView( src, id );
    $( '#crm-contact-assign-phone-dialog' ).dialog( 'close' );
    crmCloseView( 'crm-phonecall-list-view' );
    if( 'C' == src || 'V' == src ){
        $( "#crm-tabs-main" ).tabs( "option", "active", 0 );
        crmGetCustomerForEdit( src, id );
        if( '' == $( '#billaddr-phone' ).val() ){
            $( '#billaddr-phone' ).val( $( '#crm-contact-assign-phone-number' ).val() );
            $( '#billaddr-phone' ).css( 'color', 'red' );
        }
        else if( '' == $( '#billaddr-fax' ).val() ){
            $( '#billaddr-fax' ).val( $( '#crm-contact-assign-phone-number' ).val() )
            $( '#billaddr-fax' ).css( 'color', 'red' );
        }
        else{
            alert( 'Keine freie Telefonnummer gefunden!' );
        }
    }
    else{
        crmEditContactPerson( id );
    }
});

$( '#crm-contact-assign-phone-cancel-btn' ).click( function(){
    $( '#crm-contact-assign-phone-dialog' ).dialog( 'close' );
});