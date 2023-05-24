/***************************************
* Get CV date from database
* includes data for dialog
***************************************/
function crmGetCustomerForEdit( src, id, new_car, fx ){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getCustomerForEdit', data: { 'src': src, 'id': id } },
        success: function( data ){
            crmEditCuVeDlg( data, new_car );
            crmShowCuVeForEdit( data );
            if( fx ) fx( src, id );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'getCustomerForEdit()', xhr.responseText );
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

    if( isIterable( crmData.deladdr ) ){
        $('#deladdr-list').html( '' );
        $('#deladdr-list').append( '<option value=""></option>' );
        for (let i = 0; i < crmData.deladdr.length; i++){
            $( '#deladdr-list' ).append( '<option value="' + i + '">' + ( ( crmData.deladdr[i].shiptoname )? crmData.deladdr[i].shiptoname : '' ) + '</option>' );
        }
        $( '#deladdr-list' ).change( function(){
             if( !$( this ).val() ){
                 for( let e of deladdrFormModel ){
                     if( e.name.startsWith( 'deladdr-shipto' ) ) $( '#' + e.name ).val( '' );
                 }
                 $( '#deladdr-shiptocountry' ).change();
             }
             else{
                 $.each( crmData.deladdr[$( this ).val()], function( key, value ){
                    if( value ){
                        $( '#deladdr-' + key ).val( value );
                    }
                    else{
                        $( '#deladdr-' + key ).val( '' );
                    }
                 });
                 $( '#deladdr-shiptocountry' ).change();
                 $( '#deladdr-shiptobland' ).val( crmData.deladdr[$( this ).val()].shiptobland  );
             }
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
               }
           }
       }
       crmInitFormEx( crmData.vars_conf, '#vars-form' );
   }

    if( exists( crmData.cv ) ){
        $( '#billaddr-business_id' ).val( crmData.cv.business_id );
        $( '#billaddr-country' ).change();
        $( '#billaddr-bland' ).val( crmData.cv.bland );
        $( '#deladdr-shiptocountry' ).change();
    }
}

/***************************************
* Open dialog to edit CV
***************************************/
var crmEditCuVeDlgAction;

 function crmEditCuVeDlg( crmData, new_with_car ){
    crmInitFormEx( billaddrFormModel, '#billaddr-form', 0, '#crm-billaddr-cv' );
    crmInitFormEx( deladdrFormModel, '#deladdr-form' );
    crmInitFormEx( banktaxFormModel, '#banktax-form' );
    crmInitFormEx( extraFormModel, '#extras-form' );
    crmInitFormEx( carFormModel, '#car-form', 21 );

    $( '#billaddr-greetings' ).change( function(){
       $( '#billaddr-greeting' ).val( $( '#billaddr-greetings' ).val() );
    });

    $( '#billaddr-branches' ).change( function(){
       $( '#billaddr-branche' ).val( $( '#billaddr-branches' ).val() );
    });

    if( new_with_car ){
        $( '#car-form' ).show();
    }
    else{
        $( '#car-form' ).hide();
    }

    $( '#billaddr-country' ).change(function(){
        crmChangeBlandList( crmData, 'billaddr-bland', $( '#billaddr-country' ).val() );
    });
    crmChangeBlandList( crmData, 'billaddr-bland', 'D' );

    $( '#deladdr-shiptocountry' ).change(function(){
        crmChangeBlandList( crmData, 'deladdr-shiptobland', $( '#deladdr-shiptocountry' ).val() );
    });

    $( '#crm-wx-customer-dialog' ).dialog({
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
        modal: true,
        title: kivi.t8( 'Edit customer' ),
        position: { my: "top", at: "top+250" },
        open: function(){
            $( this ).css( 'maxWidth', window.innerWidth );
            crmEditCuVeDlgAction = 'updateCuWithNewCar';
        },
        buttons:[{
            text: kivi.t8( 'Save' ),
            click: function(){
                console.info( 'Save' );
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
                        if( exists(val) && val !== '' ) dbUpdateData[cvSrc][columnName[1]] = val;
                    }
                }
                if( exists( $( '#deladdr-list' ).val() ) && $( '#deladdr-list' ).val() !== '' ){
                    dbUpdateData['shipto'] = { 'shipto_id': $( '#deladdr-list' ).val() };
                    for(let item of deladdrFormModel){
                        let columnName = item.name.split( '-' );
                        let val = $( '#' + item.name ).val();
                        if( exists(val) && val !== '' ) dbUpdateData['shipto'][columnName[1]] = val;
                    }
                }
                for( let item of banktaxFormModel ){
                    let columnName = item.name.split( '-' );
                    let val = $( '#' + item.name ).val();
                    if( exists(val) && val !== '' ) dbUpdateData[cvSrc][columnName[1]] = val;
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
                        if( !item.name.startsWith( 'kba' ) ){
                            let columnName = item.name.split( '-' );
                            let val = $( '#' + item.name ).val();
                            if( exists(val) && val !== '' ) dbUpdateData['lxc_cars'][columnName[1]] = val;
                        }
                    }
                }
                console.info( 'dbUpdateData' );
                console.info( dbUpdateData );
                crmUpdateDB( crmEditCuVeDlgAction, dbUpdateData );
                $( this ).dialog( "close" );
            }
        },
        {
            text: kivi.t8( 'Cancel' ),
            click: function(){
                $( this ).dialog( "close" );
            }
        }]
    }).dialog( 'open' ).resize();
}

function crmNewCustomer(){
    alert( "Kunde erfassen in crmNewCustomer()!" );
}

function crmNewVendor(){
    alert( "Lierferant erfassen in crmNewVendor()!" );
}

function crmNewPerson(){
    alert( "Person erfassen in crmNewPerson()!" );
}
