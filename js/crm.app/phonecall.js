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
$('.whatsapp').click(function (data) {

    // Telefonnummer ermitteln (z. B. aus '#crm-contact-phone1', '#crm-contact-phone2' …)
    var phoneNumber = $('#crm-contact-phone' + this.id.substr(-1)).text();
    if (phoneNumber.charAt(0) != "+") {
        phoneNumber = "+49" + phoneNumber.slice(1);
    }
    // alles außer Ziffern und '+' entfernen
    phoneNumber = phoneNumber.replace(/[^+\d]/g, '');
    let emp_name = $('#crm-cvpa-emp_name').val();
    var message = '';

    // Wenn der Button zusätzlich die Klasse 'oe_wa' hat, Text nach Schema 2
    var greeting = $('#od-customer-greeting').val();
    var name = $('#od_customer_name').text()? $('#od_customer_name').val() : $('#crm-contact-name').val(); //crm-contact-name
    name = name.trim(); // Leerzeichen am Anfang und Ende entfernen
    //Daten aus OE
    var hersteller = $('#show_car_data-hersteller').val();
    var typ = $('#show_car_data-typ').val();
    var c_ln = $('#od_lxcars_c_ln').val();
    var employee_name = $('#od-oe-employee_name').val() ? $('#od-oe-employee_name').val() : emp_name;
    var amount = $('#od-amount').val();
    if ($(this).hasClass('oe_wa')) {
        //alert( name + 'OE_wa' );
        message = 'Sehr geehrte(r) ' + greeting + ' ' + name + ', %0D%0A' +
                  'Ihr ' + hersteller + ' ' + typ + ' mit dem Kennzeichen ' + c_ln +
                  ' ist fertig und steht zur Abholung bereit. %0D%0A' +
                  'Der Rechnungsbetrag beträgt ' + amount + '€. %0D%0A' +
                  '%0D%0AMit freundlichen Grüßen %0D%0A' +
                  'Autoprofis - ' + employee_name;
    } else {

        //alert( name + 'KEINE OE_wa'  );
        message =
            'Sehr geehrte(r) ' + name + ',%0D%0A im Anhang befindet sich das Angebot bzw. die Rechnung.' +
            '%0D%0A%0D%0AMit freundlichem Grüßen %0D%0A' + employee_name + ' - Autoprofis';
    }

    // OS-Erkennung
    //var whatsappUrl = "";
    var whatsappUrl = "https://web.whatsapp.com/send?phone=" + phoneNumber + "&text=" + message +
                          "&type=custom_url&app_absent=0&utm_campaign=wa_api_send_v2";
    /*
    if (navigator.userAgentData && navigator.userAgentData.platform) {
        const platform = navigator.userAgentData.platform.toLowerCase();
        if (platform.includes("windows")) {
            whatsappUrl = "https://wa.me/" + phoneNumber + "?text=" + message;
            //alert( 'Windows erkannt: ' + whatsappUrl );
        } else if (platform.includes("linux")) {
            whatsappUrl = "https://web.whatsapp.com/send?phone=" + phoneNumber + "&text=" + message +
                          "&type=custom_url&app_absent=0&utm_campaign=wa_api_send_v2";
            //alert( 'Linux erkannt: ' + whatsappUrl );
        } else {
            whatsappUrl = "https://wa.me/" + phoneNumber + "?text=" + message;
        }
    } else {
        // Normalfall
        var ua = navigator.userAgent;
        if (ua.indexOf("Windows") !== -1) {
            whatsappUrl = "https://web.whatsapp.com/send?phone=" + phoneNumber + "&text=" + message +
                          "&type=custom_url&app_absent=0&utm_campaign=wa_api_send_v2";
            alert( 'Windows ua erkannt: ' + whatsappUrl );
        } else if (ua.indexOf("Linux") !== -1) {
            whatsappUrl = "https://web.whatsapp.com/send?phone=" + phoneNumber + "&text=" + message +
                          "&type=custom_url&app_absent=0&utm_campaign=wa_api_send_v2";
            //alert( 'Linux ua erkannt: ' + whatsappUrl );
        } else {
            whatsappUrl = "https://wa.me/" + phoneNumber + "?text=" + message;
        }
    }
    */
    // WhatsApp öffnen
    window.open(whatsappUrl, '_blank');

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
