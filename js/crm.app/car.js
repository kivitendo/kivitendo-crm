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
* Check der Eingaben
***************************************/

function crmCheckFin( fin, cn ){
     sum = 0;
    if(cn=='-'){return true;}
    if(cn==''){return false;}
    mult = new Array(9,8,7,6,5,4,3,2,10,9,8,7,6,5,4,3,2);
    for(i in mult){
        sum+=(mult[i])*(crmEBtoNum(fin[i]));
   }
   check=sum%11;
    if(check==10){checkchar='X';}
    else{checkchar=check;}
    if(cn==checkchar){return true;}
    else{return false;}
}

function crmEBtoNum( fin ){
    if(fin=='O'||fin=='0'){return 0;}
    if(fin=='A'||fin=='J'||fin=='1'){return 1;}
    if(fin=='B'||fin=='K'||fin=='S'||fin=='2'){return 2;}
    if(fin=='C'||fin=='L'||fin=='T'||fin=='3'){return 3;}
    if(fin=='D'||fin=='M'||fin=='U'||fin=='4'){return 4;}
    if(fin=='E'||fin=='N'||fin=='V'||fin=='5'){return 5;}
    if(fin=='F'||fin=='W'||fin=='6'){return 6;}
    if(fin=='G'||fin=='P'||fin=='X'||fin=='7'){return 7;}
    if(fin=='H'||fin=='Q'||fin=='Y'||fin=='8'){return 8;}
    if(fin='I'||fin=='R'||fin=='Z'||fin=='9'){return 9;}
    else{alert("EBtoFin Error!!!");}
}

function crmCheckLn( ln ){
    return ln.match(/^[A-Z ÜÄÖ]{1,3}-[A-Z]{1,2}[0-9]{1,4}[H]{0,1}$/);
}

function crmCheckHsn( hsn ){
    return hsn.match(/^[0-9]{4}$/);
}

function crmCheckTsn( tsn ){
    return tsn.match(/^([0-9A-Z]{3,10})$/);
}

function crmCheckEm( em ){
    return em.match(/^[0-9]{0,2}[0-9A-Z]{4}$/);
}

function crmCheckHu( hu ){
    return hu.match(/^[\d]{1,2}[.][\d]{1,2}[.][\d]{0,4}$/);
}

function crmCheckD( d ){
    return d.match(/^[\d]{1,2}[.][\d]{1,2}[.][\d]{1,4}$/);
}

const crmDoCheckLn = function ( chk_c_ln, c_ln, dialog, unique = false ){
    if( $( chk_c_ln ).prop( 'checked' ) && !crmCheckLn( $( c_ln ).val() ) ){
        $( dialog ).crmDialogShowError( 'edit-car-ln-check', 'Kennzeichen fehlerhaft! Folgendes Format verwenden: MOL-RK73 oder MOL-DS88H für Oldtimer.' );
        $( c_ln ).focus();
        return false;
    }
    else{
        $( dialog ).crmDialogRemoveError( 'edit-car-ln-check' );
    }
    if( unique ){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'checkCarLicense', data: { 'c_ln': $( c_ln ).val() } },
            success: function( crmData ){
                //Fehlermeldung wird im Dialog in einem 'div' angezeigt und nach dem er behoben wurde wieder entfernt (siehe entsprechende Funktionen in app.js):
                if( crmData.ln_exists !== 'false'  ) $( dialog ).crmDialogShowError( 'edit-car-ln-check', 'Ein Auto mit dem Kennzeichen existiert bereits und gehöhrt ' + crmData.name );
                else $( dialog ).crmDialogRemoveError( 'edit-car-ln-check' );
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmDoCheckLn().checkCarLicense', xhr.responseText );
            }
        });
    }
    return true;
}

const crmDoCheckHsn = function ( chk_c_2, c_2, dialog){
    if( $( chk_c_2 ).prop( 'checked' ) && !crmCheckHsn( $( c_2 ).val() ) ){
        $( dialog ).crmDialogShowError( 'edit-car-hsn-check', 'Die Schlüsselnummer zu 2.1 ist fehlerhaft! Folgendes Format verwenden: 0600' );
        $( c_2 ).focus();
        return false;
    }
    $( dialog ).crmDialogRemoveError( 'edit-car-hsn-check' );
    return true;
}

const crmDoCheckTsn = function ( chk_c_3, c_3, dialog ){
    if( $( chk_c_3 ).prop( 'checked' ) && !crmCheckTsn( $( c_3 ).val() ) ){
        $( dialog ).crmDialogShowError( 'edit-car-tsn-check', 'Die Schlüsselnummer zu 2.2 ist fehlerhaft! Folgendes Format verwenden: ABL1277L3 oder 300' );
        $( c_3 ).focus();
        return false;
    }
    $( dialog ).crmDialogRemoveError( 'edit-car-tsn-check' );
    return true;
}

const crmDoCheckEm = function( chk_c_em, c_em, dialog ){
    if( $( chk_c_em ).prop( 'checked' ) && !crmCheckEm( $( c_em ).val() ) ){
        $( dialog ).crmDialogShowError( 'edit-car-em-check', 'Der Abgasschlüssel ist fehlerhaft! Folgendes Format verwenden: 0456 oder 010456' );
        $( c_em ).focus();
        return false;
    }
    $( dialog ).crmDialogRemoveError( 'edit-car-em-check' );
    return true;
}

const crmDoCheckD = function ( c_d, dialog ){
    if( !crmCheckD( $( '#edit_car-c_d' ).val() ) ){
        $( dialog ).crmDialogShowError( 'edit-car-d-check', 'Das Datum der Erstzulassung wurde fehlerhaft eingegeben! Folgendes Format verwenden: 12.8.73 oder 12.8.' );
        $( c_d ).focus();
        return false;
    }
    $( dialog ).crmDialogRemoveError( 'edit-car-d-check' );
    return true;
}

const crmDoCheckHu = function( c_hu, dialog ){
    if( !crmCheckHu( $( c_hu ).val() ) ){
        $( dialog ).crmDialogShowError( 'edit-car-hu-check', 'Das Datum der HU wurde fehlerhaft eingegeben! Folgendes Format verwenden: 12.8. oder 12.8.13' );
        $( c_hu ).focus();
        return false;
    }
    $( dialog ).crmDialogRemoveError( 'edit-car-hu-check' );
    return true;
}

const crmDoCheckFin = function( chk_fin, c_fin, c_finchk, dialog, unique = false ){
    if( $( chk_fin ).prop( 'checked' ) && !crmCheckFin( $( c_fin ).val(), $( c_finchk ).val() ) ){
        $( dialog ).crmDialogShowError( 'edit-car-fin-check', 'Die Fahrzeugidentnummer (FIN) ist fehlerhaft! Folgendes Format verwenden: WDB2081091X123456. Prüfziffer nicht vergessen. Falls unbekannt \'-\' eingeben' );
        $( c_finchk ).focus();
        return false;
    }
    else{
        $( dialog ).crmDialogRemoveError( 'edit-car-fin-check' );
    }
    if( unique ){
        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'checkCarFin', data: { 'fin': $( c_fin ).val() } },
            success: function( crmData ){
                if( crmData.fin_exists !== 'false'  ) $( dialog ).crmDialogShowError( 'edit-car-fin-check', 'Ein Auto mit der FIN existiert bereits und gehöhrt ' + crmData.name );
                else $( dialog ).crmDialogRemoveError( 'edit-car-fin-check' );
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmDoCheckFin().checkCarFin', xhr.responseText );
            }
        });
    }
    return true;
}

const crmDoCheckKba = function( kba_id, dialog ){
    if( '' == $( kba_id ).val() ){
        $( dialog ).crmDialogShowError( 'edit-car-kba-id-check', 'Es sind keine KBA Daten vorhanden!' );
    }
    else{
        $( dialog ).crmDialogRemoveError( 'edit-car-kba-id-check' );
    }
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
                                                crmEditCuVeView( crmData, true );
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
                                                        $( '#billaddr-city' ).val( $( '#billaddr-city' ).val() +  ( (i > 1)? ' ' : '' ) + city[i] );
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
                                                crmEditCuVeViewAction = 'insertNewCuWithCar';
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

    $( '#edit_car-chk_c_ln' ).prop( 'checked', true );
    $( '#edit_car-chk_c_2' ).prop( 'checked', true );
    $( '#edit_car-chk_c_3' ).prop( 'checked', true );
    $( '#edit_car-chk_c_em' ).prop( 'checked', true );
    $( '#edit_car-chk_fin' ).prop( 'checked', true );

    $( '#edit_car-c_ln' ).change( function(){
        crmDoCheckLn( '#edit_car-chk_c_ln', '#edit_car-c_ln', '#crm-edit-car-dialog', true );
    });
    $( '#edit_car-c_2' ).change( function(){
        crmDoCheckHsn( '#edit_car-chk_c_2', '#edit_car-c_2', '#crm-edit-car-dialog' );
    });
    $( '#edit_car-c_3' ).change( function(){
        crmDoCheckTsn( '#edit_car-chk_c_3', '#edit_car-c_3', '#crm-edit-car-dialog' );
    });
    $( '#edit_car-c_em' ).change( function(){
        crmDoCheckEm( '#edit_car-chk_c_em', '#edit_car-c_em', '#crm-edit-car-dialog' );
    });
    $( '#edit_car-c_d' ).change( function(){
        crmDoCheckD( '#edit_car-c_d', '#crm-edit-car-dialog' );
    });
    $( '#edit_car-c_hu' ).change( function(){
        crmDoCheckHu( '#edit_car-c_hu', '#crm-edit-car-dialog' );
    });
    $( '#edit_car-c_finchk' ).change( function(){
        crmDoCheckFin( '#edit_car-chk_fin', '#edit_car-c_fin', '#edit_car-c_finchk', '#crm-edit-car-dialog', true );
    });

    // changeCustomer:
    $( '#edit_car_customer_name' ).autocomplete({
        delay: crmAcDelay,
        source: "crm/ajax/crm.app.php?action=searchCustomer",
        select: function( e, ui ) {
            $( '#car-c_ow' ).val( ui.item.id );
            crmRefreshAppView( 'C', ui.item.id );
        }
    });

    $( '#edit_car-c_3' ).catcomplete({
        delay: crmAcDelay,
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

    $( '#car-c_ow' ).val( $( '#crm-cvpa-id' ).val() );
    $( '#edit_car_customer_name' ).val( $( '#crm-contact-name' ).text() );

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
                            crmCloseView( 'crm-edit-car-dialog' );
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

    $( this ).css( 'maxWidth', window.innerWidth );
    if( exists( crmData ) ){
        $( '#edit_car_new_order_btn' ).show();
        $( '#crm-edit-car-orders-table-div' ).show();
    }
    else{
        $( '#edit_car_new_order_btn' ).hide();
        $( '#crm-edit-car-orders-table-div' ).hide();
    }
    $( '#crm-edit-car-dialog' ).crmDialogClearErrors();

    crmOpenView( 'crm-edit-car-dialog' );
}

function crmEditCarSaveView(){
    console.info( 'Save car' );

    crmDoCheckLn( '#edit_car-chk_c_ln', '#edit_car-c_ln', '#crm-edit-car-dialog', false );
    crmDoCheckHsn( '#edit_car-chk_c_2', '#edit_car-c_2', '#crm-edit-car-dialog' );
    crmDoCheckTsn( '#edit_car-chk_c_3', '#edit_car-c_3', '#crm-edit-car-dialog' );
    crmDoCheckEm( '#edit_car-chk_c_em', '#edit_car-c_em', '#crm-edit-car-dialog' );
    crmDoCheckD( '#edit_car-c_d', '#crm-edit-car-dialog' );
    crmDoCheckHu( '#edit_car-c_hu', '#crm-edit-car-dialog' );
    crmDoCheckFin( '#edit_car-chk_fin', '#edit_car-c_fin', '#edit_car-c_finchk', '#crm-edit-car-dialog', false );
    crmDoCheckKba( '#edit_car-kba_id', '#crm-edit-car-dialog' );

    if( $( '#crm-edit-car-dialog' ).crmDialogHasErrors() ){
        alert( 'Es sind noch nicht behobene Fehler vorhanden' );
        return;
    };

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'checkCarLicenseAndFin', data: { 'fin': $( '#edit_car-c_fin' ).val(), 'c_ln': $( '#edit_car-c_ln' ).val() } },
        success: function( crmData ){
            console.info( 'crmData check' );
            console.info( crmData );

            if( '' == $( '#edit_car-c_id' ).val() ){
                if( 'false' !== crmData.fin_check.fin_exists ){
                    $( '#crm-edit-car-dialog' ).crmDialogShowError( 'edit-car-fin-check', 'Ein Auto mit der FIN existiert bereits und gehöhrt ' + crmData.fin_check.name );
                }
                if( 'false' !== crmData.ln_check.ln_exists ){
                    $( '#crm-edit-car-dialog' ).crmDialogShowError( 'edit-car-ln-check', 'Ein Auto mit dem Kennzeichen existiert bereits und gehöhrt ' + crmData.ln_check.name );
                }
                if( $( '#crm-edit-car-dialog' ).crmDialogHasErrors() ){
                    alert( 'Es sind noch nicht behobene Fehler vorhanden' );
                    return;
                };
            }

            dbUpdateData = {};
            dbUpdateData['lxc_cars'] = {};
            for( let item of editCarFormModel ){
                let columnName = item.name.split( '-' );
                if( !exists( columnName[1] ) ) continue;
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
                crmCloseView( 'crm-edit-car-dialog' );
            }
            if( '' == $( '#edit_car-c_id' ).val() ){
                let data = {};
                data['record'] = dbUpdateData;
                data['record']['lxc_cars']['c_ow'] = $( '#car-c_ow' ).val();
                if( '' == data['record']['lxc_cars']['c_zrk'] ) data['record']['lxc_cars']['c_zrk'] = 0;
                console.info( data );
                $.ajax({
                    url: 'crm/ajax/crm.app.php',
                    type: 'POST',
                    data:  { action: 'genericSingleInsert', data: data },
                    success: function( data ){
                        crmRefreshAppViewAction();
                        crmCloseView( 'crm-edit-car-dialog' );
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
         },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmEditCarDlg().checkCarLicenseAndFin', xhr.responseText );
        }
    });
}

function crmEditCarCloseView(){
    crmCloseView( 'crm-edit-car-dialog' );
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
