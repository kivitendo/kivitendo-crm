/***************************************
* Format the registration number
* (car license) getting from scan,
* remove white spaces
***************************************/
 function crmFormatCarLicense( regNum ){
    let rn = ( isEmpty( regNum ) ) ? 0 : regNum.split( ' ' );
    if( isIterable( rn ) && rn.length > 1 ){
        let rs = '';
        rs = rn[0] + '-' + rn[1];
        for( let i = 2; i < rn.length; i++ ){
            rs += rn[i];
        }
        // * remove "*" from license plates from the federal state of Berlin, Schlimm diese Kleinstaatelei.. alias Förderalismus
        return rs.replace( '*', '' ).replace( '*', '' );
    }
    alert( 'crmFormatCarLicense()' + regNum );
    return regNum;// Wann tritt dieser Fall auf???

}

/***************************************
* Dialog to select scan (car data)
* and customer
* inklusive fast search,
* it is possible to add a new customer
***************************************/
var lxcarsData = {};

function crmNewCarFromScan(){
    let fsmax = 24; // only for show
    //new car or new car and new customer
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        data: { action: 'getScans', data:{ 'fsmax': fsmax } },
        type: "POST",
        success: function( crmData ){
          $( '#crm-fsscan-dlg' ).dialog({
              autoOpen: false,
              resizable: true,
              width: 'auto',
              height: 'auto',
              modal: true,
              title: kivi.t8( 'FS-Scan' ),
              position: { my: "top", at: "top+250" },
              open: function(){
                  $( this ).css( 'maxWidth', window.innerWidth );
              },
              buttons:[{
                  text: kivi.t8( 'Close' ),
                  click: function(){
                      $( this ).dialog( "close" );
                  }
              }]
          }).dialog( 'open' ).resize();

          var tableContent = '';
          let listrow0 = false;
          console.info( 'FromScan' );
          console.info( crmData );
          if( isIterable( crmData.db_scans ) ){
              crmData.db_scans.forEach( function( item ){
                  tableContent += '<tr class="' + ( ( listrow0 =! listrow0 ) ? "listrow0": "listrow1" ) + '" id="' + item.scan_id + '"><td style="text-align: right; padding-right: 15px;">' + item.myts + '</td><td>' + item.firstname + '</td><td>' + item.name1 + '</td><td>' + item.registrationnumber + '</td>';
              });
          }
          $( '#crm-fsscan-list' ).empty().append( tableContent );
          $( '#crm-fsscan-list tr' ).click( function(){
              $.ajax({
                  url: 'crm/ajax/crm.app.php',
                  data: { action: 'getFsData', data:{ 'id': this.id  } },
                  type: "POST",
                  success: function( data ){
                        lxcarsData = data;
                        console.info( 'lxcarsData' );
                        console.info( lxcarsData );
                        $( '#crm-fsscan-dlg' ).dialog( 'close' );
                        $( '#crm-fsscan-customer-dlg' ).dialog({
                            autoOpen: false,
                            resizable: true,
                            width: 'auto',
                            height: '600',
                            modal: true,
                            title: kivi.t8( 'Select customer' ),
                            position: { my: "top", at: "top+250" },
                            open: function(){
                                $( this ).css( 'maxWidth', window.innerWidth );
                            },
                            buttons:[{
                                /* Add new customer from car scan data */
                                text: kivi.t8( 'New' ),
                                click: function(){
                                        $.ajax({
                                            url: 'crm/ajax/crm.app.php',
                                            type: 'POST',
                                            data:  { action: 'getCVDialogData', data:{ 'hsn': lxcarsData.hsn, 'tsn': lxcarsData.field_2_2, 'd2': lxcarsData.d2_1 + getValueNotNull( lxcarsData.d2_2 ) + getValueNotNull( lxcarsData.d2_3 ) + getValueNotNull( lxcarsData.d2_4 ) } },
                                            success: function( crmData ){
                                                 //console.info('New');
                                                //console.info(lxcarsData);
                                                $( '#crm-fsscan-customer-dlg' ).dialog( "close" );
                                                crmEditCuVeDlg( crmData, true );
                                                crmShowCuVeForEdit( crmData );
                                                $( '#billaddr-name' ).val( $( '#crm-fsscan-edit-customer' ).val() );
                                                $( '#billaddr-street' ).val( lxcarsData.address1 );
                                                const city = ( isEmpty( lxcarsData.adress2 ) )? 0 : lxcarsData.address2.split(' ');
                                                if( isIterable( city ) && city.length > 1 ){
                                                    if(!isNaN( city[0] ) ){
                                                        $( '#billaddr-zipcode' ).val( city[0] );
                                                    }else{
                                                        $( '#billaddr-city' ).val( city[0] );
                                                    }
                                                    for( let i = 1; i < city.length; i++ ){
                                                        $( '#billaddr-city' ).val( $( '#billaddr-city' ).val() + city[i] );
                                                    }
                                                }else{
                                                    $( '#billaddr-city' ).val( lxcarsData.address2 );
                                                }
                                                $( '#car-c_ln' ).val( crmFormatCarLicense( lxcarsData.registrationnumber ) );
                                                $( '#car-c_2' ).val( lxcarsData.hsn );
                                                $( '#car-c_3' ).val( lxcarsData.field_2_2 );
                                                $( '#car-c_em' ).val( lxcarsData.field_14_1 );
                                                $( '#car-c_d' ).val( lxcarsData.ez );

                                                //Wird nicht benötigt, da Datum invalide
                                                //$( '#car-c_hu' ).val( lxcarsData.hu );
                                                $( '#car-c_fin' ).val( lxcarsData.vin );
                                                $( '#car-c_finchk' ).val( lxcarsData.field_3 );
                                                //Wird als Parameter für die Funktion js/app.js->dbUpdataDB verwendet
                                                //spiegelt den Namen des Ajax-Calls (Function) in ajax/xrm.app.php
                                                crmEditCuVeDlgAction = 'insertNewCuWithCar';
                                            },
                                            error: function( xhr, status, error ){
                                                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmNewCarFromScan()', xhr.responseText );
                                            }
                                        });
                                    }
                                },{
                                text: kivi.t8( 'Close' ),
                                click: function(){
                                        $( this ).dialog( "close" );
                                }
                            }]

                        }).dialog( 'open' ).resize();

                        const name = crmFormatName( getValueNotNull( data.firstname ) + ' ' + getValueNotNull( data.name1 ) );
                        $( '#crm-fsscan-edit-customer' ).val( name  );
                        crmSearchCustomerForScan( name );

                        $( '#crm-fsscan-edit-customer' ).keyup( function(){
                            crmSearchCustomerForScan( $( '#crm-fsscan-edit-customer' ).val() );
                        });
                    },
                    error: function( xhr, status, error ){
                        $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewCarFromScan().getFsData', xhr.responseText );
                    }
                })
            })
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewCarFromScan().getScans', xhr.responseText );
        }
    })
}

/***************************************
* Fast search for customer from car scan
* data
***************************************/
function crmSearchCustomerForScan( name ){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'searchCustomerForScan', data: { 'name': name } },
        success: function( data ){
            if( !data ){
                $( '#crm-fsscan-customer-list' ).empty();
                return;
            }
            var tableContent = '';
            let listrow0 = false;
            data.forEach( function( item ){
                tableContent += '<tr class="' + ((listrow0 = !listrow0)? "listrow0": "listrow1") + '" id="' + item.id + '"><td style="text-align: right; padding-right: 15px;">' + item.name + '</td><td>' + item.street + '</td><td>' + item.zipcode + '</td><td>' + item.city + '</td>';
            });
            $( '#crm-fsscan-customer-list' ).empty().append( tableContent );
            $( '#crm-fsscan-customer-list tr' ).click( function(){
                $( '#crm-fsscan-customer-dlg' ).dialog( 'close' );
                    crmGetCustomerForEdit( 'C', this.id, true, function( src, id ){
                    $( '#car-c_ln' ).val( crmFormatCarLicense( lxcarsData.registrationnumber ) );
                    $( '#car-c_2' ).val( lxcarsData.hsn );
                    $( '#car-c_3' ).val( lxcarsData.field_2_2 );
                    $( '#car-c_em' ).val( lxcarsData.field_14_1 );
                    $( '#car-c_d' ).val( lxcarsData.ez );
                    //Wird nicht benötigt, da Datum invalide
                    //$( '#car-c_hu' ).val( lxcarsData.hu );
                    $( '#car-c_fin' ).val( lxcarsData.vin );
                    $( '#car-c_finchk' ).val( lxcarsData.field_3 );
                    crmRefreshAppView( src, id );
                });
            });
         },
         error: function( xhr, status, error ){
             $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewCarFromScan().getCustomerForScan', xhr.responseText );
         }
    });
}

function crmEditCarDlg( crmData = null ){
    crmInitFormEx( editCarFormModel, '#edit-car-form', 22, '#edit-car-hidden' );
    crmInitFormEx( editCarKbaFormModel, '#edit-car-kba-form', 33 );
    $( '#crm-edit-car-orders-table' ).html('');
    $( "#edit_car-c_d" ).datepicker();
    $( "#edit_car-c_hu" ).datepicker();
    $( '#edit_car-c_finchk' ).attr( 'maxlength', 1 );

    $( '#edit_car-c_3' ).catcomplete({
            source: function(request, response) {
                if( $( '#edit_car-c_3' ).val().length > 2 && $( '#edit_car-c_2' ).val().length > 0 ){
                    $.get('crm/ajax/crm.app.php?action=findCarKbaData', { 'hsn': $( '#edit_car-c_2' ).val(), 'tsn':  $( '#edit_car-c_3' ).val() }, function(data) {
                        response(data);
                    });
                }
            },
            select: function( e, ui ) {
                $( '#edit_car-kba_id' ).val( ui.item.id );
                for( let item of editCarKbaFormModel){
                    let columnName = item.name.split( '-' );
                    if( exists( ui.item[columnName[1]] ) ) $( '#' + item.name ).val( ui.item[columnName[1]] );
                }
            }
    });

    $( '.edit_car_kba-hidden' ).hide();
    $( '#edit_car_kba_hide_show' ).click( function(){
        if( $( '.edit_car_kba-hidden' ).is(':visible' ) ){
            $( '.edit_car_kba-hidden' ).hide();
            $( '#edit_car_kba_hide_show' ).text( "Show extra fields" );
        }
        else{
            $( '.edit_car_kba-hidden' ).show();
            $( '#edit_car_kba_hide_show' ).text( "Hide extra fields" );
        }
    });

    if( exists( crmData ) ){

        if( exists( crmData.car ) ){
            $( '#edit_car_kba_edit' ).click( function(){
                console.info( 'Edit kba' );
                console.info( crmData );
                crmEditKbaDlg( crmData.car );
            });

            for( let item of editCarFormModel){
                let columnName = item.name.split( '-' );
                if( exists( crmData.car[columnName[1]] ) ) $( '#' + item.name ).val( crmData.car[columnName[1]] );
                if( item.check ){
                    columnName = item.check.split( '-' );
                    if( exists( crmData.car[columnName[1]] ) ) $( '#' + item.check ).prop( 'checked', crmData.car[columnName[1]] );
                }
            }

            for( let item of editCarKbaFormModel){
                let columnName = item.name.split( '-' );
                if( exists( crmData.car[columnName[1]] ) ) $( '#' + item.name ).val( crmData.car[columnName[1]] );
            }
        }

        if( exists( crmData.ord ) ){
            if( exists( crmData.ord ) ){
                let listrow0 = false;
                $.each( crmData.ord, function( key, value ){
                    if( key > 10 ) return false;
                    $( '#crm-edit-car-orders-table' ).append( '<tr id="' + value.id +'" class="' + ( ( listrow0 =! listrow0 ) ? "listrow0" : "listrow1" ) + '"><td>' +  value.date + '</td><td>' + value.description  + '</td><td>' + value.amount  + '</td><td>' + value.number + '</td></tr>' );
                });
                $( '#crm-edit-car-orders-table tr' ).click( function(){
                    $.ajax({
                        url: 'crm/ajax/crm.app.php',
                        type: 'POST',
                        data:  { action: 'getOrder', data: { 'id': this.id } },
                        success: function( crmData ){
                            $( '#crm-edit-car-dialog' ).dialog( 'close' );
                            crmEditOrderDlg( crmData );
                        },
                        error: function( xhr, status, error ){
                            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmEditOrderDlg().getOrder', xhr.responseText );
                        }
                    });
                });
            }
        }

    }

    $( '#crm-edit-car-dialog' ).dialog({
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
        modal: true,
        title: ( exists( crmData ) )? kivi.t8( 'Edit car' ) : kivi.t8( 'New car' ),
        position: { my: "top", at: "top+250" },
        open: function(){
            $( this ).css( 'maxWidth', window.innerWidth );
            if( exists( crmData ) ){
                $( '#edit_car_new_order_btn' ).show();
                $( '#crm-edit-car-orders-table-div' ).show();
            }
            else{
                $( '#edit_car_new_order_btn' ).hide();
                $( '#crm-edit-car-orders-table-div' ).hide();
            }
        },
        buttons:[{
            text: kivi.t8( 'Save' ),
            click: function(){
                console.info( 'Save car' );
                dbUpdateData = {};
                dbUpdateData['lxc_cars'] = {};
                for( let item of editCarFormModel ){
                    let columnName = item.name.split( '-' );
                    let val = $( '#' + item.name ).val();
                    if( exists(val) ){
                        if( item.name !== 'edit_car-c_id' ) dbUpdateData['lxc_cars'][columnName[1]] = val;
                    }
                    if( item.check ){
                        val = $( '#' + item.name ).val();
                        columnName = item.check.split( '-' );
                        if( exists(val) && val !== '' ) dbUpdateData['lxc_cars'][columnName[1]] = $( '#' + item.check ).prop( 'checked' );
                    }
                }
                console.info( dbUpdateData );
                const onSuccess = function(){
                    crmRefreshAppViewAction();
                    $( '#crm-edit-car-dialog' ).dialog( "close" );
                }
                if( '' == $( '#edit_car-c_id' ).val() ){
                    console.info( $( '#crm-cvpa-id' ).val() );
                    let data = {};
                    data['record'] = dbUpdateData;
                    data['record']['lxc_cars']['c_ow'] = $( '#crm-cvpa-id' ).val();
                    if( '' == data['record']['lxc_cars']['c_zrk'] ) data['record']['lxc_cars']['c_zrk'] = 0;
                    console.info( data );
                    $.ajax({
                        url: 'crm/ajax/crm.app.php',
                        type: 'POST',
                        data:  { action: 'genericSingleInsert', data: data },
                        success: function( data ){
                            $( '#crm-edit-car-dialog' ).dialog( 'close' );
                            crmRefreshAppViewAction();
                        },
                        error: function( xhr, status, error ){
                            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmEditCarDlg().NewCar', xhr.responseText );
                        }
                    });
                }
                else{
                    dbUpdateData['lxc_cars']['WHERE'] = {};
                    dbUpdateData['lxc_cars']['WHERE']['c_id'] = $( '#edit_car-c_id' ).val();
                    crmUpdateDB('genericUpdate', dbUpdateData, onSuccess );
                }
            }
        },
        {
            id: 'edit_car_new_order_btn',
            text: kivi.t8( 'New Order' ),
            click: function(){
                $( this ).dialog( "close" );
                crmNewOrderForCar( $( '#edit_car-c_id' ).val() );
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

function crmEditKbaDlg( crmData ){
    crmInitFormEx( editKbaFormModel, '#edit-kba-form', 0, '#edit-kba-hidden' );

    for( let item of editKbaFormModel){
        let columnName = item.name.split( '-' );
        if( exists( crmData[columnName[1]] ) ) $( '#' + item.name ).val( crmData[columnName[1]] );
    }

    $( '#crm-edit-kba-dialog' ).dialog({
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
        modal: true,
        title: kivi.t8( 'Edit car' ),
        position: { my: "top", at: "top+250" },
        open: function(){
            $( this ).css( 'maxWidth', window.innerWidth );
        },
        buttons:[{
            text: kivi.t8( 'Save' ),
            click: function(){
                console.info( 'Save kba 1' );
                console.info( $( '#edit_kba-id' ).val() );
                let dbData = {};
                dbData['lxckba'] = {};
                dbData['lxckba']['WHERE'] = {};
                dbData['lxckba']['WHERE']['id'] = $( '#edit_kba-id' ).val();
                for( let item of editKbaFormModel ){
                    let columnName = item.name.split( '-' );
                    let val = $( '#' + item.name ).val();
                    if( exists(val) && val !== '' ){
                        if( item.name !== 'edit_kba-id' ) dbData['lxckba'][columnName[1]] = val;
                    }
                }
                console.info( dbData );
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
