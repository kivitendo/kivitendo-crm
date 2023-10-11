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
                //Timestamp to Date
                let callDate = new Date( value.call_date ).toLocaleDateString( kivi.myconfig.countrycode, { weekday: 'short', year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit' } );
                $( '#phonecall-list-table').append( '<tr caller_id ="' + value.crmti_caller_id + '" caller_src="' + value.crmti_caller_typ + '" caller_number="' + value.crmti_src + '"><td class="phonecall-list-item">' + callDate + '</td><td class="phonecall-list-item">' + getValueNotNull( value['crmti_src'] ) + '</td><td class="phonecall-list-item">' + getValueNotNull( value['crmti_dst'] ) + '</td><td class="phonecall-list-item">' + getValueNotNull( value['crmti_number'] ) + '</td><td class="phonecall-list-item">' + getValueNotNull( value['crmti_caller_typ'] ) + '</td><td class="phonecall-list-item">' + getValueNotNull( value['crmti_direction'] ) + '</td><td><button onclick="crmPlayPhoneCall(\'' + value['unique_call_id'] + '\')" ' + ( ( null == value['unique_call_id'] || '' == value['unique_call_id'] )? 'disabled': '' ) + '>Play</button><button onclick="crmClickToCall(\'' + value['crmti_number'] + '\','  + '\'' + value['crmti_src'] + '\' )">Anrufen</button></td></tr>' );
            });

            $( '.phonecall-list-item' ).click( function(){
                const col = $( this ).parent();
                if( col.attr( 'caller_src' ) != 'K' && col.attr( 'caller_src' ) != 'X' ){
                    crmRefreshAppView( col.attr( 'caller_src' ), col.attr( 'caller_id' ) );
                    crmCloseView( 'crm-phonecall-list-view' );
                }
                else if( col.attr( 'caller_src' ) != 'X' ){
                    crmEditContactPerson( col.attr( 'caller_id' ) );
                    crmCloseView( 'crm-phonecall-list-view' );
                }
                else{
                    crmAssignPhoneNummber( col.attr( 'caller_number' ) );
                }
            });
        }
    });
}

function crmAssignPhoneNummber( number ){
    $( '#crm-contact-assign-phone-dialog' ).dialog({
        modal: true,
        title: kivi.t8( 'Assign phone number' ),
        width: 'auto',
        resizable: false,
        open: function( event, ui ){
            $( '#crm-contact-assign-phone-number' ).val( number )
            $( '#crm-assign-phone-contact' ).val( '' );
            $( '#crm-contact-assign-phone-btn' ).attr( 'disabled', 'disabled' );
        }
    });
}

$( '#crm-assign-phone-contact' ).catcomplete({
    delay: crmAcDelay,
    source: "crm/ajax/crm.app.php?action=searchCVP",
    select: function( e, ui ) {
        $( '#crm-contact-assign-phone-id' ).val( ui.item.id );
        $( '#crm-contact-assign-phone-src' ).val( ui.item.src );
        $( '#crm-contact-assign-phone-btn' ).removeAttr( 'disabled' );
    }
});

$( '#crm-contact-assign-phone-btn' ).click( function(){
    const id = $( '#crm-contact-assign-phone-id' ).val();
    const src = $( '#crm-contact-assign-phone-src' ).val();

    crmRefreshAppView( src, id );
    $( '#crm-contact-assign-phone-dialog' ).dialog( 'close' );
    crmCloseView( 'crm-phonecall-list-view' );

    if( 'C' == src || 'V' == src ){

        const fx = function(){
            $( "#crm-tabs-main" ).tabs( "option", "active", 0 );
            if( '' == $( '#billaddr-phone' ).val() ){
                $( '#billaddr-phone' ).val( $( '#crm-contact-assign-phone-number' ).val() );
                $( '#billaddr-phone' ).css( 'color', 'red' );
            }
            else if( '' == $( '#billaddr-fax' ).val() ){
                $( '#billaddr-fax' ).val( $( '#crm-contact-assign-phone-number' ).val() )
                $( '#billaddr-fax' ).css( 'color', 'red' );
            }
            else if( '' == $( '#billaddr-phone3' ).val() ){
                $( '#billaddr-phone3' ).val( $( '#crm-contact-assign-phone-number' ).val() )
                $( '#billaddr-phone3' ).css( 'color', 'red' );
            }
            else{
                crmCopyToClipboard( $( '#crm-contact-assign-phone-number' ).val() );
                alert( 'Keine freie Telefonnummer gefunden!' );
            }
        }

        crmGetCustomerForEdit( src, id, false, fx );

    }
    else{

        const fx = function(){
            $( "#crm-tabs-main" ).tabs( "option", "active", 0 );
            if( '' == $( '#contacts-cp_phone1' ).val() ){
                $( '#contacts-cp_phone1' ).val( $( '#crm-contact-assign-phone-number' ).val() );
                $( '#contacts-cp_phone1' ).css( 'color', 'red' );
            }
            else if( '' == $( '#billaddr-fax' ).val() ){
                $( '#contacts-cp_phone2' ).val( $( '#crm-contact-assign-phone-number' ).val() )
                $( '#contacts-cp_phone2' ).css( 'color', 'red' );
            }
            else{
                crmCopyToClipboard( $( '#crm-contact-assign-phone-number' ).val() );
                alert( 'Keine freie Telefonnummer gefunden!' );
            }
        }

        crmEditContactPerson( id, fx );
    }
});

function crmPlayPhoneCall( id ){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'playPhoneCall', data: { 'id': id } },
        success: function( data ){
            if( exists( data.success ) && !data.success ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Play error' ), kivi.t8( ( ( exists( data.debug )? data.debug : null) ) ) );
                return;
            }
            window.open( 'crm/crmti/crm.app.playphonecall.php?file=' + data.filename, '_blank' );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmNewOrderAndInsertPos()', xhr.responseText );
        }
    });
}

$( '#crm-contact-assign-phone-cancel-btn' ).click( function(){
    $( '#crm-contact-assign-phone-dialog' ).dialog( 'close' );
});