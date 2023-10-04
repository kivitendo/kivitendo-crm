function crmEditContactPerson( personId ){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getContactPerson', data: { 'id': personId} },
        success: function( data ){
            crmEditPersonView( data );
            crmOpenView( 'crm-contact-person-view' );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewCVP().getCVDialogData', xhr.responseText );
        }
    });
}

function crmEditPersonView( crmData ){
    crmInitFormEx( contactPersonFormModel, '#contact-person-form', 0, '#crm-contact-person-hidden' );

    $( '#contacts-cp_givenname' ).change( function(){
        let name = $( '#contacts-cp_givenname' ).val();
        name = name.split(' ')[0];
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'firstnameToGender', data: { 'name': name } },
            success: function( data ){
                let greeting = '';
                if( 'F' == data.gender ) greeting = 'Frau';
                else if( 'M' == data.gender ) greeting = 'Herr';
                if( exists( data.gender ) ) $( '#contacts-cp_title' ).val( greeting );
            }
        });
    });

    $( '#contacts-cp_zipcode' ).autocomplete({
        delay: crmAcDelay,
        source: "crm/ajax/crm.app.php?action=zipcodeToLocation",
        select: function( e, ui ) {
            $( '#contacts-cp_city' ).val( ui.item.ort );
        }
    });

    // change customer or vendor:
    $( '#contacts_company_name' ).catcomplete({
        delay: crmAcDelay,
        source: "crm/ajax/crm.app.php?action=searchCustomerVendor",
        select: function( e, ui ) {
            assert( 'djfd', ui.item.label );
            $( '#contacts_src' ).val( ui.item.src );
            $( '#contacts-cp_cv_id' ).val( ui.item.id );
            $( '#contacts_company_name' ).val( ui.item.label );
        }
    });

    if( exists( crmData ) ){
        for( let item of contactPersonFormModel ){
            let columnName = item.name.split( '-' )[1];
            $( '#' + item.name ).val( crmData[columnName] );
        }

        $( '#contacts_cp_id' ).val( crmData['cp_id'] );
        $( '#contacts_src' ).val( $( '#crm-cvpa-src' ).val() );
        $( '#contacts_company_name' ).val( crmData['contacts_company_name'] );
    }
}

$( '#crm-edit-contact-person-save-btn' ).click( function(){
    let dbUpdateData = {};
    dbUpdateData['contacts'] = {};
    for( let item of contactPersonFormModel ){
        let columnName = item.name.split( '-' )[1];
        if( exists( columnName ) ) dbUpdateData['contacts'][columnName] = $( '#' + item.name ).val();
    }
    dbUpdateData['contacts']['WHERE'] = {};
    dbUpdateData['contacts']['WHERE'] = 'cp_id = ' + $( '#contacts_cp_id' ).val();

    const onSuccess = function( data ){
        crmRefreshAppView( $( '#contacts_src' ).val(), $( '#contacts-cp_cv_id' ).val() );
        crmCloseView( 'crm-contact-person-view' );
    }
    crmUpdateDB( 'genericUpdateEx', dbUpdateData, onSuccess );
});

$( '#crm-edit-contact-person-cancel-btn' ).click( function(){
    crmCloseView( 'crm-contact-person-view' );
});
