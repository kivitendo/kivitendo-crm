/***************************************
* Get CV date from database
* includes data for dialog
***************************************/
function crmGetCustomerForEdit( src, id, new_car, fx ){
    let paramData = { 'src': src, 'id': id };
    if( new_car ){
        paramData['hsn'] = lxcarsData.hsn;
        paramData['tsn'] = lxcarsData.field_2_2;
        paramData['d2'] =  lxcarsData.d2_1 + getValueNotNull( lxcarsData.d2_2 ) + getValueNotNull( lxcarsData.d2_3 ) + getValueNotNull( lxcarsData.d2_4 );
    }
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getCustomerForEdit', data: paramData },
        success: function( data ){
            crmEditCuVeView( data, new_car );
            crmOpenView( 'crm-wx-customer-view', null, ' - ' + kivi.t8( 'Basedata' ) );
            crmShowCuVeForEdit( data );
            if( fx ) fx( src, id );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'getCustomerForEdit()', xhr.responseText );
        }
    });
}

const crmCVPtypeEnum = { Customer: 'C', Vendor: 'V', Person: 'P' };

function crmNewCVP( crmCVPtype ){
    if( crmCVPtype == crmCVPtypeEnum.Person ){
        crmEditPersonView( null );
        crmOpenView( 'crm-contact-person-view' );
        return;
    }

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getCVInitData', data: {} },
        success: function( data ){
            crmEditCuVeView( data );
            $( '#billaddr-src' ).val( crmCVPtype );
            crmEditCuVeViewAction = 'newCV';
            crmShowCuVeForEdit( data );
            crmOpenView( 'crm-wx-customer-view', ( crmCVPtypeEnum.Customer == crmCVPtype )? kivi.t8( 'New customer' ) : kivi.t8( 'New vendor' ) );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewCVP().getCVDialogData', xhr.responseText );
        }
    });
}

/***************************************
* Prepares dialog to edit CV
***************************************/
 function crmShowCuVeForEdit( crmData ){
    $( '#billaddr-greetings' ).html( '' );
    $( '#billaddr-greetings' ).append( '<option value="">' + kivi.t8( "Salutation as below" ) + '</option>' );
    if( isIterable( crmData.greetings ) ){
        for( let description of crmData.greetings ) $( '#billaddr-greetings' ).append( '<option value="' + description.description + '">' + description.description + '</option>' );
    }

    $( '#billaddr-business_id' ).html( '' );
    for( let business of crmData.business ) $( '#billaddr-business_id' ).append( '<option value="' + business.id + '">' + business.name + '</option>' );

    if( exists( crmData.cv ) ){
        $.each( crmData.cv, function( key, value ){
            if( value ){
                $( '#billaddr-' + key ).val( value );
            }
            else{
                $( '#billaddr-' + key ).val( '' );
            }
        });
        $( '#billaddr-direct_debit' ).prop( 'checked', crmData.cv.direct_debit );
    }

    $( '#deladdr-list' ).html( '' );
    $( '#deladdr-list' ).append( '<option value="">' + kivi.t8( 'New' ) + '</option>' );
    if( isIterable( crmData.deladdr ) ){
        for (let i = 0; i < crmData.deladdr.length; i++){
            $( '#deladdr-list' ).append( '<option value="' + i + '">' + existsOrEmptyString( crmData.deladdr[i].shiptoname ) + '</option>' );
        }
        $( '#deladdr-list' ).change( function(){
            if ( '' == $( this ).val() ){
                for( let item of deladdrFormModel){
                    $( '#' + item.name ).val( '' );
                }
                $( '#deladdr_shipto_id' ).val( '' );
                return;
            }
            $.each( crmData.deladdr[$( this ).val()], function( key, value ){
               if( value ){
                   $( '#deladdr-' + key ).val( value );
               }
               else{
                   $( '#deladdr-' + key ).val( '' );
               }
            });
            $( '#deladdr_shipto_id' ).val( crmData.deladdr[$( this ).val()].shipto_id );
        });

        $( '#deladdr-list' ).change();
   }

   if( exists( crmData.branches ) ){
       for( let branche of crmData.branches ){
           $( '#billaddr-branches' ).append( '<option value="' + branche.name + '">' + branche.name + '</option>' );
       }
       if( exists( crmData.cv ) && exists( crmData.cv.branche ) ) $( '#billaddr-branches' ).val( crmData.cv.branche );
       $( '#billaddr-branche' ).val( '' );
   }

   if( exists( crmData.employees ) ){
       for( let employee of crmData.employees ){
           $( '#billaddr-salesman_id' ).append( '<option value="' + employee.id + '">' + employee.name + '</option>' );
       }
       if( exists( crmData.cv ) && exists( crmData.cv.employee ) ) $( '#billaddr-salesman_id' ).val( crmData.cv.employee );
   }

   if( exists( crmData.payment_terms ) ){
       for( let payment_term of crmData.payment_terms ){
           $( '#billaddr-payment_id' ).append( '<option value="' + payment_term.id + '">' + payment_term.description + '</option>' );
       }
       if( exists( crmData.cv ) && exists( crmData.cv.payment_id ) ) $( '#billaddr-payment_id' ).val( crmData.cv.payment_id );
   }

   if( exists( crmData.tax_zones ) ){
       for( let tax_zone of crmData.tax_zones ){
           $( '#billaddr-taxzone_id' ).append( '<option value="' + tax_zone.id + '">' + tax_zone.description + '</option>' );
       }
       if( exists( crmData.cv ) && exists( crmData.cv.taxzone_id ) ) $( '#billaddr-taxzone_id' ).val( crmData.cv.taxzone_id );
   }

   if( exists( crmData.languages ) ){
       for( let lang of crmData.languages ){
           $( '#billaddr-languages' ).append( '<option value="' + lang.id + '">' + lang.description + '</option>' );
       }
       if( exists( crmData.cv ) && exists( crmData.cv.language_id ) ) $( '#billaddr-language' ).val( crmData.cv.language_id );
   }

   if( exists( crmData.leads ) ){
       for( let lead of crmData.leads ){
           $( '#billaddr-leads' ).append( '<option value="' + lead.id + '">' + lead.lead + '</option>' );
       }
       if( exists( crmData.cv ) && exists( crmData.cv.lead ) ) $( '#billaddr-leads' ).val( crmData.cv.lead );
   }

    if( exists( crmData.vars_conf ) ){
        for( let var_conf of crmData.vars_conf ){
            if( var_conf.type ) {
                switch( var_conf.type ){
                case 'select':
                    if( var_conf.data ) {
                        var_conf.data = var_conf.data.split( '##' );
                        var_conf.data.unshift('');
                    }
                    break;
                case 'text':
                    var_conf.type = 'input';
                    break;
                case 'textfield':
                    var_conf.type = 'textarea';
                    break;
                case 'customer':
                    var_conf.type = 'customer';
                    break;
                case 'vendor':
                    var_conf.type = 'vendor';
                    break;
                case 'part':
                    var_conf.type = 'part';
                    break;
                case 'number':
                    var_conf.type = 'number';
                    break;
                case 'bool':
                    var_conf.type = 'checkbox';
                    break;
                }
            }
        }
        crmInitFormEx( crmData.vars_conf, '#vars-form' );

        for( let var_conf of crmData.vars_conf ){
            $( '#' + var_conf.name ).attr( 'config_id', var_conf.id );
            if( 'date' == var_conf.type ) $( '#' + var_conf.name ).datepicker();
            else if( 'part' == var_conf.type ){
                $( '#' + var_conf.name + '-label' ).autocomplete({
                    delay: crmAcDelay,
                    source: "crm/ajax/crm.app.php?action=findPart&filter=offer",
                    select: function( e, ui ) {
                        $( e.target ).val( ui.item.vendornumber + ' ' + ui.item.value );
                        const target = $( e.target ).parent().find( '#' + $( e.target ).attr( 'name' ) );
                        target.val( ui.item.id );
                        return true;
                    }
                });
            }
            else if( 'customer' == var_conf.type ){
                $( '#' + var_conf.name + '-label' ).autocomplete({
                    delay: crmAcDelay,
                    source: "crm/ajax/crm.app.php?action=searchCustomer",
                    select: function( e, ui ) {
                        $( e.target ).val( ui.item.customernumber + ' ' + ui.item.value );
                        const target = $( e.target ).parent().find( '#' + $( e.target ).attr( 'name' ) );
                        target.val( ui.item.id );
                        return false;
                    }
                });
            }
            else if( 'vendor' == var_conf.type ){
                $( '#' + var_conf.name + '-label' ).autocomplete({
                    delay: crmAcDelay,
                    source: "crm/ajax/crm.app.php?action=searchVendor",
                    select: function( e, ui ) {
                        const target = $( e.target ).parent().find( '#' + $( e.target ).attr( 'name' ) );
                        target.val( ui.item.id );
                        return true;
                    }
                });
            }
        }

        if( exists( crmData.custom_vars ) ){
            for( let custom_var of  crmData.custom_vars ){
                if( 'select' == custom_var.type ){
                    $('#' + custom_var.name + ' option').filter( function(){
                        $( this ).attr( 'value', $( this ).text() );
                        if( $( this ).text() == custom_var.text_value ) $( this ).attr( 'selected', 'selected' );
                    });
                }
                else if( 'bool' == custom_var.type ){
                    $( '#' + custom_var.name ).prop( 'checked', custom_var.bool_value );
                }
                else if( 'number' == custom_var.type ){
                    $( '#' + custom_var.name ).val( custom_var.number_value );
                }
                else if( 'date' == custom_var.type && exists( custom_var.timestamp_value ) ){
                    $( '#' + custom_var.name ).val( kivi.format_date( new Date( custom_var.timestamp_value ) ) );
                }
                else if( 'part' == custom_var.type && exists( custom_var.number_value ) ){
                    $( '#' + custom_var.name ).val( custom_var.number_value );
                }
                else if( 'customer' == custom_var.type && exists( custom_var.number_value ) ){
                    $( '#' + custom_var.name ).val( custom_var.number_value );
                }
                else if( 'vendor' == custom_var.type && exists( custom_var.number_value ) ){
                    $( '#' + custom_var.name ).val( custom_var.number_value );
                }
                else{
                    $( '#' + custom_var.name ).val( custom_var.text_value );
                }
                $('#' + custom_var.name ).attr( 'custom_var_id', custom_var.id );
            }
        }
   }

    if( exists( crmData.cv ) ){
        $( '#billaddr-business_id' ).val( crmData.cv.business_id );
        $( '#billaddr-country' ).change();
        $( '#billaddr-bland' ).val( crmData.cv.bland );
    }

    $( '#crm-wx-customer-view' ).crmDialogClearErrors();
}

/***************************************
* Open dialog to edit CV
***************************************/
//Wird als Parameter für die Funktion js/app.js->dbUpdataDB verwendet
//spiegelt den Namen des Ajax-Calls (Action/Function) in ajax/crm.app.php
var crmEditCuVeViewAction;

function crmEditCuVeView( crmData, new_with_car ){
    crmInitFormEx( billaddrFormModel, '#billaddr-form', 0, '#crm-billaddr-cv' );
    crmInitFormEx( deladdrFormModel, '#deladdr-form', 0, '#crm-deladdr-hidden' );
    crmInitFormEx( banktaxFormModel, '#banktax-form' );
    crmInitFormEx( extraFormModel, '#extras-form' );
    crmInitFormEx( carFormModel, '#car-form', 0, '#car-form-hidden' );
    crmInitFormEx( carKbaFormModel, '#car-kba-form', 33 );

    $( '.car_kba-hidden' ).hide();
    $( '#car_kba_hide_show' ).click( function(){
        if( $( '.car_kba-hidden' ).is(':visible' ) ){
            $( '.car_kba-hidden' ).hide();
            $( '#car_kba_hide_show' ).text( "Show extra fields" );
        }
        else{
            $( '.car_kba-hidden' ).show();
            $( '#car_kba_hide_show' ).text( "Hide extra fields" );
        }
    });

    $( '#billaddr-greetings' ).change( function(){
       $( '#billaddr-greeting' ).val( $( '#billaddr-greetings' ).val() );
    });

    $( '#billaddr-branches' ).change( function(){
       $( '#billaddr-branche' ).val( $( '#billaddr-branches' ).val() );
    });

    $( '#billaddr-name' ).change( function(){
        let name = $( '#billaddr-name' ).val();
        name = name.split(' ')[0];
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'firstnameToGender', data: { 'name': name } },
            success: function( data ){
                let greeting = '';
                if( 'F' == data.gender ) greeting = 'Frau';
                else if( 'M' == data.gender ) greeting = 'Herr';
                if( exists( data.gender ) ) $( '#billaddr-greeting' ).val( greeting );
            }
        });
    });

    $( '#billaddr-zipcode' ).autocomplete({
        delay: crmAcDelay,
        source: "crm/ajax/crm.app.php?action=zipcodeToLocation",
        select: function( e, ui ) {
            $( '#billaddr-city' ).val( ui.item.ort );
            $('#billaddr-bland option:contains(' + ui.item.bundesland + ')').attr('selected', 'selected');
            $('#billaddr-bland').val( $('#billaddr-bland option:contains(' + ui.item.bundesland + ')').val() );
            $('#billaddr-bland').change();
        }
    });

    $( '#deladdr-shiptozipcode' ).autocomplete({
        delay: crmAcDelay,
        source: "crm/ajax/crm.app.php?action=zipcodeToLocation",
        select: function( e, ui ) {
            $( '#deladdr-shiptocity' ).val( ui.item.ort );
            $('#deladdr-shiptobland option:contains(' + ui.item.bundesland + ')').attr('selected', 'selected');
            $('#deladdr-shiptobland').val( $('#deladdr-shiptobland option:contains(' + ui.item.bundesland + ')').val() );
            $('#deladdr-shiptobland').change();
        }
    });

    $( '#change_car_kba_d2' ).text( kivi.t8( 'Change' ) );
    $( '#change_car_kba_d2' ).click( function(){
        $( '#crm-change-car-name-dialog' ).dialog({
            modal: true,
            title: kivi.t8( 'Change KBA' ),
            width: 'auto',
            resizable: false,
            open: function( event, ui ){
                $( '#crm-change-car-name' ).val( $( '#car_kba-name' ).val() );
                $( '#crm-change-car-name-id' ).val( $('#car-kba_id' ).val() );
                if( '' == $('#car-kba_id' ).val() ) $( '#crm-change-car-name-btn' ).attr( 'disabled', 'disabled' );
            }
        });
    });

    crmFindCarKbaData( '#car-c_2', '#car-c_3', '#car-kba_id', carKbaFormModel );

    if( new_with_car ){
        $( '#car-form' ).show();
        $( '#car-kba-form' ).show();

        $( '#chk_c_ln' ).prop( 'checked', true );
        $( '#chk_c_2' ).prop( 'checked', true );
        $( '#chk_c_3' ).prop( 'checked', true );
        $( '#chk_c_em' ).prop( 'checked', true );
        $( '#chk_c_fin' ).prop( 'checked', true );

        for( let item of carKbaFormModel){
            let columnName = item.name.split( '-' )[1];
            $( '#' + item.name ).val( lxcarsData[columnName] );
        }
        $( '#car_kba-hersteller' ).val( ( null != lxcarsData.field_2 )? lxcarsData.field_2 : lxcarsData.maker );
        $( '#car_kba-d2' ).val( lxcarsData.d2_1 );
        $( '#car_kba-name' ).val( lxcarsData.model );
        $( '#car_kba-hubraum' ).val( lxcarsData.p1 );
        $( '#car_kba-leistung' ).val( lxcarsData.p2_p4 );
        $( '#car_kba-kraftstoff' ).val( lxcarsData.p3 );
        $( '#car_kba-achsen' ).val( lxcarsData.l );
        $( '#car_kba-masse' ).val( lxcarsData.f1 );

        //Kba-Daten aus der lxckba-Tabelle in der DB werden in der Funktion crmGetCustomerForEdit() geholt (ajax: getCustomerForEdit in crm.app.php).
        //Die Daten vom FS-Scan wurden vorher in die Formularfelder geschrieben siehe oben.
        if( exists( crmData.kba ) ){
            $.each( crmData.kba , function( key, value ){
                if( '' == $( '#car_kba-' + key ).val() ) $( '#car_kba-' + key ).val( value );
            });
            if( crmData.kba.exists ){
                $( '#car-kba_id' ).val( crmData.kba.id );
                $( '#car_kba_edit' ).show()
            }
            else{
                $( '#car-kba_id' ).val( '' );
                $( '#car_kba-d2' ).val( lxcarsData.d2_1 );
                $( '#car_kba_edit' ).hide()
            }
            for( let item of carKbaFormModel){
                $( '#' + item.name ).prop( 'readonly', crmData.kba.exists );
            }
            $( '#car_kba-fhzart' ).prop( 'disabled', crmData.kba.exists );
            $( '#car-c_2' ).val( 'Test' );
        }
    }
    else{
        $( '#car-form' ).hide();
        $( '#car-kba-form' ).hide();
    }

    $( '#billaddr-country' ).change(function(){
        crmChangeBlandList( crmData, 'billaddr-bland', $( '#billaddr-country' ).val() );
    });
    crmChangeBlandList( crmData, 'billaddr-bland', 'D' );

    $( '#deladdr-country' ).change(function(){
        crmChangeBlandList( crmData, 'deladdr-shiptobland', $( '#deladdr-country' ).val() );
    });
    crmChangeBlandList( crmData, 'deladdr-shiptobland', 'D' );

    crmEditCuVeViewAction = 'updateCuWithNewCar';
}

$( '#crm-change-car-name' ).catcomplete({
    delay: crmAcDelay,
    source: function(request, response) {
        if( $( '#car-c_2' ).val().length > 0 ){
            $.get('crm/ajax/crm.app.php?action=findCarKbaDataWithName', { 'hsn': $( '#car-c_2' ).val(), 'name': $( '#crm-change-car-name' ).val() }, function(data) {
                response(data);
            });
        }
    },
    select: function( e, ui ) {
        $( '#crm-change-car-name-id' ).val( ui.item.id );
        $( '#crm-change-car-name-btn' ).removeAttr( 'disabled' );
    }
});

$( '#crm-change-car-name-btn' ).click( function(){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getCarKbaDataById', data: { 'id': $( '#crm-change-car-name-id' ).val() } },
        success: function( data ){
            for( let item of carKbaFormModel){
                let columnName = item.name.split( '-' );
                if( exists( data[columnName[1]] ) ) $( '#' + item.name ).val( data[columnName[1]] );
            }
            $('#car-kba_id' ).val( $( '#crm-change-car-name-id' ).val() );
            $( '#car-c_3' ).val( data.tsn );
            $( '#crm-change-car-name-dialog' ).dialog( 'close' );
        }
    });
});

$( '#crm-change-car-name-cancel-btn' ).click( function(){
    $( '#crm-change-car-name-dialog' ).dialog( 'close' );
});

function crmEditCuVeViewSave( ){
    dbUpdateData = {};
    let cvSrc = ( $( '#billaddr-src' ).val() == 'V' )? 'vendor' : 'customer';
    dbUpdateData[cvSrc] = {};
    let billaddr_id = $( '#billaddr-id' ).val();
    if( '' !== billaddr_id  ){
        dbUpdateData[cvSrc]['WHERE'] = {};
        dbUpdateData[cvSrc]['WHERE']['id'] = $( '#billaddr-id' ).val();
    }
    for( let item of billaddrFormModel){
        let columnName = item.name.split( '-' );
        if( columnName[1] !== "src" && columnName[1] !== "id" && columnName[1] !== "greetings" ){
            let val = $( '#' + item.name ).val();
            if( exists( val ) ) dbUpdateData[cvSrc][columnName[1]] = val;
        }
    }
    if( dbUpdateData[cvSrc]['bland'] === '' ){
        $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Error' ), kivi.t8( 'Select Bundesland please.' ) );
        return;
    }

    if( exists( $( '#deladdr-shiptoname' ).val() ) && $( '#deladdr-shiptoname' ).val() !== '' ){

        if( exists( $( '#deladdr_shipto_id' ).val() ) && $( '#deladdr_shipto_id' ).val() !== '' ){
            dbUpdateData['shipto'] = {};
            dbUpdateData['shipto']['WHERE'] = {};
            dbUpdateData['shipto']['WHERE']['shipto_id'] = $( '#deladdr_shipto_id' ).val();
            dbUpdateData['shipto']['trans_id'] = billaddr_id;
        }
        else{
            dbUpdateData['shipto'] = {};
            dbUpdateData['shipto']['trans_id'] = billaddr_id;
        }

        for(let item of deladdrFormModel){
            let columnName = item.name.split( '-' );
            if( !exists( columnName[1] ) || 'shipto_id' == columnName[1] || 'list' == columnName[1] ) continue;
            let val = $( '#' + item.name ).val();
            if( exists(val) && val !== '' ){
                dbUpdateData['shipto'][columnName[1]] = val;
            }
        }

    }

    for( let item of banktaxFormModel ){
        let columnName = item.name.split( '-' );
        let val = $( '#' + item.name ).val();
        if( 'direct_debit' == columnName[1] ) dbUpdateData[cvSrc][columnName[1]] = $( '#' + item.name ).is( ':checked' );
        else if( exists(val) && val !== '' ) dbUpdateData[cvSrc][columnName[1]] = val;
    }
    for( let item of extraFormModel ){
        let columnName = item.name.split( '-' );
        if( columnName[1] !== 'branches' ){
            let val = $( '#' + item.name ).val();
            if( exists(val) && val !== '' ) dbUpdateData[cvSrc][columnName[1]] = val;
        }
    }
    if( $( '#car-form' ).is(':visible' ) ){
        crmDoCheckLn( '#chk_c_ln', '#car-c_ln', '#crm-wx-customer-view', false );
        crmDoCheckHsn( '#chk_c_2', '#car-c_2', '#crm-wx-customer-view' );
        crmDoCheckTsn( '#chk_c_3', '#car-c_3', '#crm-wx-customer-view' );
        crmDoCheckEm( '#chk_c_em', '#car-c_em', '#crm-wx-customer-view' );
        crmDoCheckD( '#car-c_d', '#crm-wx-customer-view' );
        crmDoCheckHu( '#car-c_hu', '#crm-wx-customer-view' );
        crmDoCheckFin( '#car-chk_fin', '#car-c_fin', '#car-c_finchk', '#crm-wx-customer-view', false );
        if( $( '#crm-wx-customer-view' ).crmDialogHasErrors() ){
            alert( 'Es sind noch nicht behobene Fehler vorhanden' );
            return;
        };

        dbUpdateData['lxc_cars'] = {};
        for(let item of carFormModel){
            let columnName = item.name.split( '-' );
            let val = $( '#' + item.name ).val();
            if( exists(val) && val !== '' ) dbUpdateData['lxc_cars'][columnName[1]] = val;
        }

        dbUpdateData['lxckba'] = {};
        dbUpdateData['lxckba']['hsn'] = $( '#car-c_2' ).val();
        dbUpdateData['lxckba']['tsn'] = $( '#car-c_3' ).val().substring(0, 3);
        for(let item of carKbaFormModel){
            let columnName = item.name.split( '-' );
            if( !exists( columnName[1] ) ) continue;
            let val = $( '#' + item.name ).val();
            if( exists(val) ) dbUpdateData['lxckba'][columnName[1]] = val;
        }

        //Die KBA-Daten werden in der Funktion crmGetCustomerForEdit() geholt (ajax: getCustomerForEdit in crm.app.php)
        //und in der Function prepareKba in crm.app.php in die DB geschrieben
    }

    dbUpdateData['custom_variables'] = [];
    $( '#vars-form' ).find( ':input' ).each( function(){
        if( $( this ).hasClass( 'crm-ignore-field' ) ) return;
        let customVar = {}
        const custom_var_id = $( this ).attr( 'custom_var_id' );
        if( exists( custom_var_id ) && '' != custom_var_id ){
            customVar['WHERE'] = {}
            customVar['WHERE']['id'] = $( this ).attr( 'custom_var_id' );
        }
        customVar['config_id'] = $( this ).attr( 'config_id' );
        customVar['trans_id'] = billaddr_id;
        if( 'checkbox' == $( this ).attr( 'type' ) ) customVar['bool_value'] = $( this ).prop( 'checked' );
        else if( 'number' == $( this ).attr( 'type' ) ) customVar['number_value'] = ( '' != $( this ).val() )? $( this ).val() : '0.00';
        else if( $( this ).hasClass( 'hasDatepicker' ) ) customVar['timestamp_value'] = $( this ).val();
        else if( 'hidden' == $( this ).attr( 'type' ) ) customVar['number_value'] = ( '' != $( this ).val() )? $( this ).val() : '0';
        else customVar['text_value'] = $( this ).val();
        dbUpdateData['custom_variables'].push( customVar );
    });

    //assert( 'crmEditCuVeViewSave.dbUpdateData', dbUpdateData );

    const onSuccess = function( data ){
        crmCloseView( 'crm-wx-customer-view' );
    }
    crmUpdateDB( crmEditCuVeViewAction, dbUpdateData, onSuccess );
}

function crmEditCuVeViewCancel(){
    crmCloseView( 'crm-wx-customer-view' );
}

function crmNewVendor(){
    alert( "Lierferant erfassen in crmNewVendor()!" );
}

function crmNewPerson(){
    alert( "Person erfassen in crmNewPerson()!" );
}

function crmCVDocumentsView( ){
    $( '#elfinder' ).elfinder( 'destroy' )

    $(function() {
        $('#elfinder').elfinder(
            // 1st Arg - options
            {
                // Disable CSS auto loading
                cssAutoLoad : false,

                // Base URL to css/*, js/*
                baseUrl : './',

                // Connector URL
                url : 'crm/jquery-plugins/elFinder2/php/connector.minimal.php?cv=' + crmGetCVSrc() + '&id=' + crmGetCVId(),

                // Callback when a file is double-clicked
                getFileCallback : function(file) {
                    // ...
                },
            },

            // 2nd Arg - before boot up function
            function(fm, extraObj) {
                // `init` event callback function
                fm.bind('init', function() {
                    // Optional for Japanese decoder "extras/encoding-japanese.min"
                    delete fm.options.rawStringDecoder;
                    if (fm.lang === 'ja') {
                        fm.loadScript(
                            [ fm.baseUrl + 'js/extras/encoding-japanese.min.js' ],
                            function() {
                                if (window.Encoding && Encoding.convert) {
                                    fm.options.rawStringDecoder = function(s) {
                                        return Encoding.convert(s,{to:'UNICODE',type:'string'});
                                    };
                                }
                            },
                            { loadType: 'tag' }
                        );
                    }
                });

                // Optional for set document.title dynamically.
                var title = document.title;
                fm.bind('open', function() {
                    var path = '',
                        cwd  = fm.cwd();
                    if (cwd) {
                        path = fm.path(cwd.hash) || null;
                    }
                    document.title = path? path + ':' + title : title;
                }).bind('destroy', function() {
                    document.title = title;
                });
            }
        );
    });

    crmOpenView( 'crm-plugin-elfinder', null, ' - Dokumente' );
}

function crmCVDocumentsCloseView(){
    crmCloseView( 'crm-plugin-elfinder' );
}
