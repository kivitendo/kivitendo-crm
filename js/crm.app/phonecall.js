$( '.whatsapp' ).click( function( data ){
    //alert( $( '#crm-contact-phone' + this.id.substr( -1 ) ).text() );
    data.stopImmediatePropagation();// war wofür???
    phoneNumber = $( '#crm-contact-phone' + this.id.substr( -1 ) ).text();
    if( phoneNumber[0]  != "+" ){
        phoneNumber = "+49" + phoneNumber.slice(1);
    }
    window.open( 'https://api.whatsapp.com/send?phone=' + phoneNumber +  '&text=Hey ' + $( '#crm-contact-name' ).html() + ' im Anhang befinden sich die Dokument(e). %0D%0AMit freundlichem Grüßen %0D%0ADein / Ihr Autoprofis-Team','_blank');
    return false;
}).button().removeClass( "ui-widget ui-state-default ui-corner-all ui-button-text-only").tooltip();

$( '#cp_phone1' ).click( function( data ){
    alert( 'test' );
});

function crmClickToCall( data, contact = null ){
    if( null == contact ) contact = $( '#crm-contact-name' ).html();
    $.ajax({
        url: 'crm/ajax/clickToCall.php',
        type: 'POST',
        data: { action: 'newCall', data: { 'number': data, 'name': contact } },
        success: function ( data ) {
            //alert( ' Name:' + $( '#crm-contact-name' ).html() );
        },
        error: function () {
            alert( 'Error clickToCall()!' )
        }
    });
    return false;
};

$( '#crm-contact-phone1, #crm-contact-phone2, #crm-contact-phone3, #crm_inv_contact_phone1, #crm_inv_contact_phone2, #crm_inv_contact_phone3, #crm_oe_contact_phone1, #crm_oe_contact_phone2, #crm_oe_contact_phone3, #crm_off_contact_phone1, #crm_off_contact_phone2, #crm_off_contact_phone3' ).click( function( data ){
    crmClickToCall( $( this ).text() );
}).button().removeClass( "ui-widget ui-state-default ui-corner-all ui-button-text-only").css({ width: '120px', 'text-align': 'left', 'padding-left': '0.3em' });

$( '.copy' ).click( function( data ){
    crmCopyToClipboard( $( '#crm-contact-phone' + this.id.substr( -1 ) ).text() );
}).button().removeClass( "ui-widget ui-state-default ui-corner-all ui-button-text-only" ).tooltip();

function crmCopyToClipboard( data ){
    navigator.clipboard.writeText( '' +  data );
}

function crmPhoneCallConfigDlgCall( phone_num ){
    crmClickToCall( phone_num );
    $( '#crm-phone-call-config-dialog' ).dialog( "close" );
}

function crmPhoneCallConfigDlgCancel( phone_num ){
    $( '#crm-phone-call-config-dialog' ).dialog( "close" );
}

function crmPhoneCallConfigDlg( phone_num ){
    $( '#crm-phone-call-config-dialog' ).dialog({
        modal: true,
        title: kivi.t8( 'Dialog for ' ) + phone_num, //kivi.t8( 'Phone Dialog'), //ToDo
        width: 'auto',
        resizable: false,
        open: function( event, ui ){
            $.ajax({
                url: 'crm/ajax/clickToCall.php?action=getPhones',
                type: 'GET',
                success: function ( data ){
                    var external_contexts_array = data['external_contexts'].split( ',');
                    var internal_phones_array = data['internal_phones'].split( ',');
                    var selected_context = typeof data['user_external_context'] !== 'undefined' ?  data['user_external_context'] : '';
                    var selected_phone = typeof data['user_internal_phone'] !== 'undefined' ?  data['user_internal_phone'] : '';
                    var selected = '';
                    var dynamic_html = '<table><tr><td>' + kivi.t8( 'External Context:' ) + '</td><td> <select id="user_external_context"  style="width:100%;">';
                    $.each( external_contexts_array, function( key, value ){
                        selected = value == selected_context ? 'selected' : '';
                        dynamic_html +=  '<option value="' + value + '"' + selected + '>' + value + '</option>'
                    })
                    dynamic_html += '</select></td></tr>';
                    dynamic_html += '<tr><td>' + kivi.t8( 'Internal Phone:' ) + '</td><td> <select id="user_internal_phone"  style="width:100%;">';
                    $.each( internal_phones_array, function( key, value ){
                        selected = value == selected_phone ? 'selected' : '';
                        dynamic_html +=  '<option value="' + value + '"' + selected + '>' + value + '</option>'
                    })
                    dynamic_html += '</select></td></tr></table>';
                    dynamic_html += '<div style="padding: 5px"><button style="margin-right: 5px" onclick="crmPhoneCallConfigDlgCall(' + '\'' + phone_num + '\'' + ')">Anrufen</button>';
                    dynamic_html += '<button onclick="crmPhoneCallConfigDlgCancel()">Abbrechen</button></div>';
                    $( '#crm-phone-call-config-dialog' ).html( dynamic_html );
                    //console.info(  dynamic_html );
                    $( '#user_external_context, #user_internal_phone' ).change( function( data ){
                        var dataObj = {};
                        dataObj[this.id] = $(this).val();
                        $.ajax({
                            url: 'crm/ajax/clickToCall.php',
                            type: 'POST',
                            data: { action: 'saveClickToCall', data: dataObj },
                            success: function ( data ) {
                                //if( data ) alert( );
                            },
                            error: function () {
                                alert( 'Error: saveClickToCall!' );
                            }
                        });

                    })
                    //console.info( dialog_id );
                },
                error: function (){
                    alert( 'Error: ajax/clickToCall.php?action=getPhones' );
                }
            })
        }
    })
}
$( '#crm-contact-phone1_dialog_button, #crm-contact-phone2_dialog_button, #crm-contact-phone3_dialog_button, #crm_inv_contact_phone1_dialog_button, #crm_inv_contact_phone2_dialog_button, #crm_inv_contact_phone3_dialog_button, #crm_oe_contact_phone1_dialog_button, #crm_oe_contact_phone2_dialog_button, #crm_oe_contact_phone3_dialog_button, #crm_off_contact_phone1_dialog_button, #crm_off_contact_phone2_dialog_button, #crm_off_contact_phone3_dialog_button' ).click( function(){
    crmPhoneCallConfigDlg( $( '#' + this.id.replace( '_dialog_button', '' ) ).text() );
}).button().removeClass( "ui-widget ui-state-default ui-corner-all ui-button-text-only");
