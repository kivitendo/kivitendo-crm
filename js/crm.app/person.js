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

    for( let item of contactPersonFormModel ){
        let columnName = item.name.split( '-' )[1];
        $( '#' + item.name ).val( crmData[columnName] );
    }
}

$( '#crm-edit-contact-person-save-btn' ).click( function(){
    const onSuccess = function( data ){
        crmCloseView( 'crm-contact-person-view' );
    }

    let dbUpdateData = {};
    dbUpdateData['contacts'] = {};
    for( let item of contactPersonFormModel ){
        let columnName = item.name.split( '-' )[1];
        dbUpdateData['contacts'][columnName] = $( '#' + item.name ).val();
    }
    dbUpdateData['contacts']['WHERE'] = {};
    dbUpdateData['contacts']['WHERE'] = 'cp_id = ' + $( '#contacts_cp_id' ).val();

    crmUpdateDB( 'test', dbUpdateData, onSuccess );    
});

$( '#crm-edit-contact-person-cancel-btn' ).click( function(){
    crmCloseView( 'crm-contact-person-view' );
});
