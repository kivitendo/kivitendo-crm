/*$( '.whatsapp' ).click( function( data ){
    //alert( $( '#crm-contact-phone' + this.id.substr( -1 ) ).text() );
    data.stopImmediatePropagation();// war wofür???
    phoneNumber = $( '#crm-contact-phone' + this.id.substr( -1 ) ).text();
    if( phoneNumber[0]  != "+" ){
        phoneNumber = "+49" + phoneNumber.slice(1);
    }
    window.open( 'https://api.whatsapp.com/send?phone=' + phoneNumber +  '&text=Hey ' + $( '#crm-contact-name' ).html() + ' im Anhang befinden sich die Dokument(e). %0D%0AMit freundlichem Grüßen %0D%0ADein / Ihr Autoprofis-Team','_blank');
    return false;
}).button().removeClass( "ui-widget ui-state-default ui-corner-all ui-button-text-only").tooltip();
*/
$( '.whatsapp' ).click( function( data ){
    
    // Verhindert, dass andere Click-Handler desselben Elements ausgeführt werden
    //data.stopImmediatePropagation();

    // Telefonnummer ermitteln (z. B. aus '#crm-contact-phone1', '#crm-contact-phone2' …)
    var phoneNumber = $( '#crm-contact-phone' + this.id.substr( -1 ) ).text();
    if( phoneNumber.charAt(0)  != "+" ){
        phoneNumber = "+49" + phoneNumber.slice(1);
    }

    // Nachricht als leere Variable vorbereiten
    var message = '';

    // Wenn der Button zusätzlich die Klasse 'oe_wa' hat, Text nach Schema 2
    if( $(this).hasClass('oe_wa') ){
        // Hier musst du die Selektoren anpassen, je nachdem, wo Anrede, Fahrzeug und Kennzeichen stehen:
        //alert($( '#od-customer-greeting' ).val() + 'Hello');
        var greeting = $( '#od-customer-greeting' ).val(); // Anrede
        var name = $( '#od_customer_name' ).val(); // Name des Kunden
        var hersteller = $( '#show_car_data-hersteller' ).val(); // Fahrzeughersteller
        var typ = $('#show_car_data-typ' ).val(); // Fahrzeugtyp
        var c_ln    = $( '#od_lxcars_c_ln' ).val();    //Kennzeichen "
        var employee_name = $( '#od-oe-employee_name' ).html(); // Name des Mitarbeitersod-oe-employee_name
        var amount = $( '#od-amount' ).val()
        

        message = 'Sehr geehrte(r) ' + greeting + ' ' + name + ', %0D%0A' +
                  'Ihr ' + hersteller + ' ' + typ + ' mit dem Kennzeichen ' + c_ln +
                  ' ist fertig und steht zur Abholung bereit. %0D%0A' +
                  'Der Rechnungsbetrag beträgt ' + amount + '€. %0D%0A' +
                  '%0D%0AMit freundlichen Grüßen %0D%0A' +
                  'Autoprofis - ' + employee_name;

        //alert( 'WhatsApp-Nachricht: ' + message ); // Debugging-Hilfe, kann entfernt werden
    }
    // Sonst Standard-Nachricht wie vorher
    else {
        var name = $( '#crm-contact-name' ).html();
        var employee_name = $( '#od-oe-employee_name' ).html()
        var greeting = $( '#od-customer-greeting' ).val(); // Ist noch etwas buggi
        /*message = 
            'Sehr geehrte(r) ' + greeting + ' ' + name + ' im Anhang befindet sich das Angebot bzw die Rechnung. ' +
            '%0D%0A%0D%0AMit freundlichem Grüßen %0D%0AAutoprofis - ' + employee_name;
        alert( 'Normaler Whatsapp Button: ' + message ); // Debugging-Hilfe, kann entfernt werden
        */
       message = 
            'Sehr geehrte(r) ' + name + ' im Anhang befindet sich das Angebot bzw. die Rechnung. ' +
            '%0D%0A%0D%0AMit freundlichem Grüßen %0D%0AIhr / Dein Autoprofis Team';
    }

    // Öffne WhatsApp-Fenster mit der passenden URL
    window.open(
        'https://api.whatsapp.com/send?phone=' + phoneNumber + '&text=' + message,
        '_blank'
    );
    return false;
}).button()
.removeClass("ui-widget ui-state-default ui-corner-all ui-button-text-only")
.tooltip();


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
