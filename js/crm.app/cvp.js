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
    if( crmCVPtype == crmCVPtypeEnum.Person ) return; //ToDo

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

    if( isIterable( crmData.contacts ) ){
        $( '#contacts-list' ).html( '' );
        $( '#contacts-list' ).append( '<option value="">' + kivi.t8( 'New' ) + '</option>' );
        for (let i = 0; i < crmData.contacts.length; i++){
            $( '#contacts-list' ).append( '<option value="' + i + '">' + existsOrEmptyString( crmData.contacts[i].cp_givenname ) + ' ' + existsOrEmptyString( crmData.contacts[i].cp_name ) + '</option>' );
        }
        $( '#contacts-list' ).change( function(){
            if ( '' == $( this ).val() ){
                for( let item of contactsFormModel){
                    $( '#' + item.name ).val( '' );
                }
                $( '#contacts_cp_id' ).val( '' );
                return;
            }
            $.each( crmData.contacts[$( this ).val()], function( key, value ){
               if( value ){
                   $( '#contacts-' + key ).val( value );
               }
               else{
                   $( '#contacts-' + key ).val( '' );
               }
            });
            $( '#contacts_cp_id' ).val( crmData.contacts[$( this ).val()].cp_id );
        });

        $( '#contacts-list' ).change();
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
               }
           }
       }
       crmInitFormEx( crmData.vars_conf, '#vars-form' );
   }

    if( exists( crmData.cv ) ){
        $( '#billaddr-business_id' ).val( crmData.cv.business_id );
        $( '#billaddr-country' ).change();
        $( '#billaddr-bland' ).val( crmData.cv.bland );
        $( '#contacts-shiptocountry' ).change();
    }
}

/***************************************
* Open dialog to edit CV
***************************************/
//Wird als Parameter für die Funktion js/app.js->dbUpdataDB verwendet
//spiegelt den Namen des Ajax-Calls (Function) in ajax/xrm.app.php
var crmEditCuVeViewAction;

function crmEditCuVeView( crmData, new_with_car ){
    crmInitFormEx( billaddrFormModel, '#billaddr-form', 0, '#crm-billaddr-cv' );
    crmInitFormEx( contactsFormModel, '#contacts-form', 0, '#crm-contacts-hidden' );
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
            },
            error: function( xhr, status, error ){
                 //$( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmEditCuVeView.firstnameToGender', xhr.responseText );
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

    $( '#contacts-shiptocountry' ).change(function(){
        crmChangeBlandList( crmData, 'contacts-shiptobland', $( '#contacts-shiptocountry' ).val() );
    });

    crmEditCuVeViewAction = 'updateCuWithNewCar';
}

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
    if( exists( $( '#contacts_cp_id' ).val() ) && $( '#contacts_cp_id' ).val() !== '' ){
        dbUpdateData['contacts'] = {};
        dbUpdateData['contacts']['WHERE'] = {};
        dbUpdateData['contacts']['WHERE']['cp_id'] = $( '#contacts_cp_id' ).val();
        dbUpdateData['contacts']['cp_cv_id'] = billaddr_id;
    }
    for(let item of contactsFormModel){
        let columnName = item.name.split( '-' );
        if( !exists( columnName[1] ) || 'cp_id' == columnName[1] || 'list' == columnName[1] ) continue;
        let val = $( '#' + item.name ).val();
        if( exists(val) && val !== '' ){
            if( !exists( dbUpdateData['contacts'] ) ){
                dbUpdateData['contacts'] = {};
                dbUpdateData['contacts']['cp_cv_id'] = billaddr_id;
            }
            dbUpdateData['contacts'][columnName[1]] = val;
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
    }
    //console.info( 'dbUpdateData' );
    //console.info( dbUpdateData );
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
