//KBA und Fahrzeuge anlegen:
//Das Anlegen eines Autos in der DB vom FS-Scan ist in der Funktion crmEditCuVeView( crmData, new_with_car ) in der cvp.js implementiert,
//Dialoge bzw. Views zur Auswahl der Scans und Kunden werden in dieser Datei implementiert (crmNewCarFromScan und crmNewCarFromScanNewCuView)
//Die Regeln für den Umgang mit der DB ist in appendQueryWithKba in der Datei ajax/crm.app.php beschrieben

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
    //alert( 'crmFormatCarLicense()' + regNum );
    return regNum;// Wann tritt dieser Fall auf??? Wenn die Registrierungsnummer z.B. nicht eingescannt wurde.

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
    return ln.match(/^(?=.{1,9}$)[A-ZÄÖÜ]{1,3}-[A-Z]{1,2}[0-9]{1,4}[HE]{0,1}$/);
    // Erklärung:
    // (?=.{1,8}$) - Stellt sicher, dass das Kennzeichen inklusive Bindestrich maximal 8 Zeichen lang ist
    // [A-ZÄÖÜ]{1,3} - Ortskennung: Erlaubt 1 bis 3 Buchstaben inkl. deutscher Umlaute (z. B. MOL, B)
    // - - Bindestrich zwischen Ortskennung und Serien-/Nummernteil
    // [A-Z]{1,2} - Serienkennung: Erlaubt 1 oder 2 Buchstaben
    // [0-9]{1,4} - Nummernteil: Erlaubt 1 bis 4 Ziffern
    // [HE]? - Optional: Erlaubt entweder ein H (historisches Fahrzeug) oder ein E (Elektroauto)
}

function crmCheckHsn( hsn ){
    return hsn.match(/^[0-9]{4}$/);
}

function crmCheckTsn( tsn ){
    return tsn.match(/^([0-9A-Z]{3,10})$/);
}

function crmCheckEm( em ){
    if( em == '' || em == '-' ) return true; //EM kann leer sein
    return em.match(/^[0-9]{0,2}[0-9A-Z]{4}$/);
}

function crmCheckHu( hu ){
    if( hu == '' ) return true; //HU kann leer sein
    return hu.match(/^[\d]{1,2}[.][\d]{1,2}[.][\d]{0,4}$/);
}

function crmCheckD( d ){
    return d.match(/^[\d]{1,2}[.][\d]{1,2}[.][\d]{1,4}$/);
}

const crmDoCheckLn = function ( chk_c_ln, c_ln, dialog, unique = false ){
    if( $( chk_c_ln ).prop( 'checked' ) && !crmCheckLn( $( c_ln ).val() ) ){
        $( dialog ).crmDialogShowError( 'edit-car-ln-check', 'Kennzeichen fehlerhaft! Folgendes Format verwenden: MOL-ID100 oder MOL-DS88E für Oldtimer.' );
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
    if( ( $( chk_c_2 ).prop( 'checked' ) || $( c_2 ).val().length > 4 ) && !crmCheckHsn( $( c_2 ).val() ) ){
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
    if( !crmCheckD( $( c_d ).val() ) ){
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
let orig_name = '';

function crmNewCarFromScan(){
    let fsmax = 24; // only for show
    //new car or new car and new customer
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        data: { action: 'getScans', data:{ 'fsmax': fsmax } },
        type: "POST",
        success: function( crmData ){
            crmOpenView( 'crm-fsscan-dlg', kivi.t8( 'FS-Scans' ) );

            var tableContent = '';
            let listrow0 = false;
            if( isIterable( crmData.db_scans ) ){
                crmData.db_scans.forEach( function( item ){
                    console.info( item ); //d1, d3, ccm, ez
                    tableContent += '<tr class="' + ((listrow0 = !listrow0) ? "listrow0" : "listrow1") + '" id="' + item.scan_id + '"><td style="text-align: right; padding-right: 15px;">' + item.myts + '</td><td>' + item.firstname + '</td><td>' + item.name1 + '</td><td>' + item.registrationnumber + '</td><td>' + item.d1 + '</td><td>' + item.d3 + '</td><td>' + item.ccm + (item.ccm != '' ? ' cm³' : '') + '</td>';
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
                        crmOpenView( 'crm-fsscan-customer-dlg', kivi.t8( 'New car from scan' ) );

                        /************************************
                        * Formatiert die Namen vom FS-Scan:
                        * Verkürtzt den Namen auf ein Vornamen und Nachnamen
                        * und sorgt für die richtige Groß-/Kleinschreibung
                        ************************************/
                        let name = null;
                        orig_name = ( exists( data.firstname ) && data.firstname.trim() != '' )? data.firstname + ' ' + getValueNotNull( data.name1 ) : getValueNotNull( data.name1 );
                        let name_parts = orig_name.split( ' ' );
                        if( name_parts.length > 1 ){
                            if( !( orig_name.toLowerCase().includes( ' gmbh' ) || orig_name.toLowerCase().includes( ' ohg' ) || orig_name.toLowerCase().includes( ' ag' ) ) ){
                                name_parts = [ name_parts[0], name_parts[name_parts.length - 1] ];
                                for( let str of name_parts ){
                                    if( name === null ) name = ''; else name += ' ';
                                    name += str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
                                }
                            }
                            else{
                                name = data.name1;
                            }
                        }
                        if( !exists( name ) )  name = '';
                        $( '#crm-fsscan-edit-customer' ).val( name.trim() );
                        if( '' == orig_name ) orig_name = "xxxxxxxxxxxxxxxxxxxxxxx"; //Todo: den Fall das die erste Seite nicht mitgescannt wurde
                        crmSearchCustomerForScan( name, orig_name );

                        $( '#crm-fsscan-edit-customer' ).keyup( function(){
                            crmSearchCustomerForScan( $( '#crm-fsscan-edit-customer' ).val(), orig_name );
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

/******************************************
* Wird beim klick auf den Button 'Neuer Kunde'
* im 'crm-fsscan-customer-dlg' aufgerufen
******************************************/
function crmNewCarFromScanNewCuView(){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getCVDialogData', data:{ 'hsn': lxcarsData.hsn, 'tsn': lxcarsData.field_2_2, 'd2': lxcarsData.d2_1 + getValueNotNull( lxcarsData.d2_2 ) + getValueNotNull( lxcarsData.d2_3 ) + getValueNotNull( lxcarsData.d2_4 ) } },
        success: function( crmData ){
            crmEditCuVeView( crmData, true );
            crmOpenView( 'crm-wx-customer-view', null, ' - ' + kivi.t8( 'Basedata' ) );
            crmShowCuVeForEdit( crmData ); // Wird in 'cvp.js' definiert
            $( '#billaddr-name' ).val( $( '#crm-fsscan-edit-customer' ).val() );
            $( '#billaddr-name' ).change();

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

            crmAutoSelectBland();

            $( '#car-c_ln' ).val( crmFormatCarLicense( lxcarsData.registrationnumber ) );
            $( '#car-c_2' ).val( lxcarsData.hsn );
            $( '#car-c_3' ).val( ( '' != lxcarsData.field_2_2 )? lxcarsData.field_2_2.replace( /[^a-zA-Z0-9]/g, '' ) : '' );
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
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmNewCarFromScanNewCuView()', xhr.responseText );
        }
    });
}

//Wird in der Funktion crmNewCarFromScanNewCuView() und crmSearchCustomerForScan() aufgerufen
//um das Bundesland zu ermitteln
function crmAutoSelectBland( ){
    if( $('#billaddr-bland').val() != '') return;
    $.ajax({
        url: 'crm/ajax/crm.app.php?action=zipcodeToLocation&term=' + $( '#billaddr-zipcode' ).val(),
        type: 'GET',
        success: function( data ){
            if( exists( data ) && data.length > 0 ){
                $('#billaddr-bland option:contains(' + data[0].bundesland + ')').attr('selected', 'selected');
                $('#billaddr-bland').val( $('#billaddr-bland option:contains(' + data[0].bundesland + ')').val() );
                $('#billaddr-bland').change();
            }
        },
        error: function( xhr, status, error ){
        }
    });
}

function crmNewCarFromScanCancelView1(){
    crmCloseView( 'crm-fsscan-dlg' );
}

function crmNewCarFromScanCancelView2(){
    crmOpenView( 'crm-fsscan-dlg', kivi.t8( 'FS-Scan' ) );
}

/***************************************
* Fast search for customer from car scan
* data
***************************************/
function crmSearchCustomerForScan( name, orig_name ){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'searchCustomerForScan', data: { 'name': name, 'orig_name': orig_name } },
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
                crmGetCustomerForEdit( 'C', this.id, true, function( src, id ){
                    $( '#car-c_ln' ).val( crmFormatCarLicense( lxcarsData.registrationnumber ) );
                    $( '#car-c_2' ).val( lxcarsData.hsn );
                    $( '#car-c_3' ).val( ( '' != lxcarsData.field_2_2 )? lxcarsData.field_2_2.replace( /[^a-zA-Z0-9]/g, '' ) : '' );
                    $( '#car-c_em' ).val( lxcarsData.field_14_1 );
                    $( '#car-c_d' ).val( lxcarsData.ez );
                    //Wird nicht benötigt, da Datum invalide
                    //$( '#car-c_hu' ).val( lxcarsData.hu );
                    $( '#car-c_fin' ).val( lxcarsData.vin );
                    $( '#car-c_finchk' ).val( lxcarsData.field_3 );

                    if( $( '#billaddr-greetings' ).val() == '' ) $( '#billaddr-name' ).change();
                    crmAutoSelectBland();

                    crmRefreshAppView( src, id );
                });
            });
         },
         error: function( xhr, status, error ){
             $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmSearchCustomerForScan', xhr.responseText );
         }
    });
}

function crmFindCarKbaData( c_2, c_3, kba_id, formModel ){
    $( c_3 ).catcomplete({
        delay: crmAcDelay,
        source: function(request, response) {
            if( $( c_3 ).val().length > 2 && $( c_2 ).val().length > 0 ){
                $.get('crm/ajax/crm.app.php?action=findCarKbaData', { 'hsn': $( c_2 ).val(), 'tsn':  $( c_3 ).val() }, function(data) {
                    response(data);
                });
            }
        },
        select: function( e, ui ) {
            $( kba_id ).val( ui.item.id );
            for( let item of formModel){
                let columnName = item.name.split( '-' );
                if( exists( ui.item[columnName[1]] ) ) $( '#' + item.name ).val( ui.item[columnName[1]] );
            }
            return false;
        }
    });
}

function crmEditCarDlg( crmData = null ){
    //console.info( crmData  );
    //crmSetMainTitle( crmData.cv.name + ' ,', ' ' + kivi.t8( 'Car' ) + ': ' +  crmData.car.c_ln );
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


    crmFindCarKbaData( '#edit_car-c_2', '#edit_car-c_3', '#edit_car-kba_id', editCarKbaFormModel );

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

    //$( '#car-c_ow' ).val( $( '#crm-cvpa-id' ).val() );
    //$( '#edit_car_customer_name' ).val( $( '#crm-contact-name' ).text() );

    if( exists( crmData ) ){
        $( '#edit_car_customer_name' ).val( crmData.cv.name );//Kundenname setzen
        $( '#od-customer-id' ).val( crmData.cv.id ); //Kunden-ID setzen ToDo: Autoansicht
        //Ist es nicht sinnvoller die Kunden-ID und den Kundenname in der Funktion crmRefreshAppView() zu setzen?
        //crmRefreshAppView() brächte dann einen dritten Parameter (c_id) mit der Car-ID
        crmRefreshAppView( 'C', crmData.cv.id ); //Kundenansicht anzeigen
        if( exists( crmData.car ) ){
            $( '#edit_car_kba_edit' ).click( function(){
                //console.info( 'Edit kba' );
                //console.info( crmData );
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

            if(  crmData.car.c_hu !== '' ){ //Färbt bei fälliger HU die Zeile rot ein, Moments ist dein Freund!!!
                const c_hu = moment( crmData.car.c_hu, "DD.MM.YYYY" );   //c_hu: 1714514400000 === 01.05.2024
                const currentMonth = moment().startOf( 'month' ); //currentMonth: 1706742000000 === 20.02.2014
                if( c_hu.isSameOrBefore( currentMonth, 'month' ) ) $('#edit_car-c_hu').css( 'background-color', '#FF69B4' ); //#FF007F  #FFA500
                //$( '#edit_car-c_hu' ).parent().parent().css( 'background-color', 'red' ); //Färbt die ganze Zeile
            }
        }

        if( exists( crmData.car_ord ) ){
            if( exists( crmData.car_ord ) ){
                let listrow0 = false;
                $.each( crmData.car_ord, function( key, value ){
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
        $( '#edit_car_register_btn' ).show();
        $( '#edit_car_special_btn' ).show();
    }
    else{
        $( '#edit_car_new_order_btn' ).hide();
        $( '#crm-edit-car-orders-table-div' ).hide();
        $( '#edit_car_register_btn' ).hide();
        $( '#edit_car_special_btn' ).hide();
    }
    $( '#crm-edit-car-dialog' ).crmDialogClearErrors();

    crmOpenView( 'crm-edit-car-dialog' );
}

function crmEditCarNewOrder(){
    crmCloseView( 'crm-edit-car-dialog' );
    crmNewOrderForCar( $( '#edit_car-c_id' ).val() );
}

function crmEditCarSaveView(){
    //console.info( 'Save car' );

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
            //console.info( 'crmData check' );
            //console.info( crmData );

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
            //console.info( dbUpdateData );
            const onSuccess = function(){
                crmRefreshAppViewAction();
                crmCloseView( 'crm-edit-car-dialog' );
            }
            if( '' == $( '#edit_car-c_id' ).val() ){
                let data = {};
                data['record'] = dbUpdateData;
                data['record']['lxc_cars']['c_ow'] = $( '#car-c_ow' ).val();
                if( '' == data['record']['lxc_cars']['c_zrk'] ) data['record']['lxc_cars']['c_zrk'] = 0;
                //console.info( data );
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
                //console.info( 'Save kba 1' );
                //console.info( $( '#edit_kba-id' ).val() );
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
                //console.info( dbData );
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

$( '#edit_car_register_btn' ).click( function(){
    window.open( 'crm/lxcars/carreg.php?c_id=' + $( '#edit_car-c_id' ).val() + '&owner=' + $( '#crm-cvpa-id' ).val() + '&task=3', '_blank');
});

$( '#edit_car_special_btn' ).click( function(){
    window.open( 'crm/lxcars/special/special.phtml?c_id=' + $( '#edit_car-c_id' ).val() + '&owner=' + $( '#crm-cvpa-id' ).val() + '&task=1', '_blank');
});

$( '#od_lxcars_to_car, #od_lxcars_to_car_btn' ).click( function(){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getCar', data: { 'id': $( '#od-lxcars-c_id' ).val() } },
        success: function( crmData ){
            crmEditCarDlg( crmData );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'showCVPA().getCar', xhr.responseText );
        }
    });
});



$(document).on('click', '#edit_car-c_st_print', function(){
    //alert('Etikettendruck des Sommerreifens');
    // Checke, ob die Felder ausgefüllt sind
    const missing = [];
    if ($('#edit_car-c_st').val().trim() === '')   missing.push('Reifengröße');
    if ($('#edit_car-c_st_l').val().trim() === '') missing.push('Lagerort');

    if (missing.length > 0) {
    // Erzeuge aus jedem fehlenden Feldnamen ein <li>-Element und füge alle zu einem einzigen HTML-String zusammen
    const feldListe = missing.map(f => `<li>${f}</li>`).join('');

    $('#message-dialog')
        .html(
        `<p>Bitte fülle zuerst folgende Felder aus:</p>
        <ul>${feldListe}</ul>`
        )
        .dialog({
        modal:     false,
        title:     'Reifenetikett drucken',
        resizable: false,
        width:     'auto',
        height:    'auto',
        })
        .dialog('open')
        .resize();
        return;
    }
    // Wenn alle Felder ausgefüllt sind, führe den Ajax-Request aus
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'printTyreLabel', data: { 'name': $( '#edit_car_customer_name' ).val(), 'c_ln': $( '#edit_car-c_ln' ).val(), 'dim': $( '#edit_car-c_st' ).val(), 'location': $( '#edit_car-c_st_l' ).val() } },
        success: function( data ){
            alert( data.result )
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'showCVPA().printTyreLabel', xhr.responseText );
        }
    });
});

$(document).on('click', '#edit_car-c_wt_print', function(){
  alert('Etikettendruck des Winterreifens');
});//edit-car-fin-check




$(document).on('click', '#edit_car-c_ln_info', function(){
    const kza = ['A','AA','AB','ABG','AC','AE','AIC','AK','AM','AN','ANA','ANG','ANK','AÖ','AP','APD','ARN','ART','AS','ASL','ASZ','AT','AU','AUR','AW','AZ','AZE','B','BA','BAD','BAR','BB','BBG','BBL','BC','BD','BED','BEL','BER','BGL','BI','BIR','BIT','BIW','BL','BLK','BM','BN','BNA','BO','BÖ','BOR','BOT','BP','BRA','BRB','BRG','BS','BSK','BT','BTF','BÜS','BÜZ','BW','BWL','BYL','BZ','C','CA','CB','CE','CHA','CLP','CO','COC','COE','CUX','CW','D','DA','DAH','DAN','DAU','DB','DBR','DD','DE','DEG','DEL','DGF','DH','DL','DLG','DM','DN','DO','DON','DS','DU','DÜW','DW','DZ','E','EA','EB','EBE','ED','EE','EF','EH','EI','EIC','EIL','EIS','EL','EM','EMD','EMS','EN','ER','ERB','ERH','ES','ESA','ESW','EU','EW','F','FB','FD','FDS','FF','FFB','FG','FI','FL','FLÖ','FN','FO','FOR','FR','FRG','FRI','FRW','FS','FT','FTL','FÜ','FW','G','GA','GAP','GC','GDB','GE','GER','GF','GG','GHA','GHC','GI','GL','GM','GMN','GNT','GÖ','GP','GR','GRH','GRM','GRS','GRZ','GS','GT','GTH','GÜ','GUB','GVM','GW','GZ','H','HA','HAL','HAM','HAS','HB','HBN','HBS','HC','HD','HDH','HDL','HE','HEF','HEI','HEL','HER','HET','HF','HG','HGN','HGW','HH','HHM','HI','HIG','HL','HM','HN','HO','HOL','HOM','HOT','HP','HR','HRO','HS','HSK','HST','HU','HV','HVL','HWI','HX','HY','HZ','IGB','IK','IL','IN','IZ','J','JB','JE','JL','K','KA','KB','KC','KE','KEH','KF','KG','KH','KI','KIB','KL','KLE','KLZ','KM','KN','KO','KÖT','KR','KS','KT','KU','KÜN','KUS','KW','KY','KYF','L','LA','LAU','LB','LBS','LBZ','LC','LD','LDK','LDS','LER','LEV','LG','LI','LIB','LIF','LIP','LL','LM','LN','LÖ','LÖB','LOS','LSA','LSN','LSZ','LU','LUK','LWL','M','MA','MAB','MB','MC','MD','ME','MEI','MEK','MER','MG','MGN','MH','MHL','MI','MIL','MK','MKK','ML','MM','MN','MOL','MOS','MQ','MR','MS','MSP','MST','MTK','MÜ','MÜR','MVL','MW','MYK','MZ','MZG','N','NAU','NB','ND','NDH','NE','NEA','NEB','NES','NEW','NF','NH','NI','NK','NL','NM','NMB','NMS','NOH','NOL','NOM','NP','NR','in NWR','NU','NVP','NW','NWM','NY','NZ','OA','OAL','OB','OBG','OC','OD','OE','OF','OG','OH','OHA','OHV','OHZ','OK','OL','OPR','OR','OS','OSL','OVL','OVP','OZ','P','PA','PAF','PAN','PB','PCH','PE','PER','PF','PI','PIR','PK','PL','PLÖ','PM','PN','PR','PS','PW','PZ','QFT','QLB','R','RA','RC','RD','RDG','RE','REG','RG','RH','RIE','RL','RM','RN','RO','ROS','ROW','RP','RPL','RS','RSL','RT','RU','RÜD','RÜG','RV','RW','RZ','S','SAD','SAL','SAW','SB','SBG','SBK','SC','SCZ','SDH','SDL','SDT','SE','SEB','SEE','SFA','SFB','SFT','SG','SGH','SH','SHA','SHG','SHK','SHL','SI','SIG','SIM','SK','SL','SLF','SLN','SLS','SLZ','SM','SN','SO','SOK','SÖM','SON','SP','SPB','SPN','SR','SRB','SRO','ST','STA','STB','STD','STL','SU','SÜW','SW','SZ','SZB','TBB','TET','TF','TG','THL','THW','TIR','TO','TÖL','TP','TR','TS','TÜ','TUT','UE','UEM','UER','UH','UL','UM','UN','V','VB','VEC','VER','VIE','VK','VS','W','WAF','WAK','WB','WBS','WDA','WE','WEN','WES','WF','WHV','WI','WIL','WIS','WK','WL','WLG','WM','WMS','WN','WND','WO','WOB','WR','WRN','WSF','WST','WSW','WT','WTM','WÜ','WUG','WUN','WUR','WW','WZL','X','Y','Z','ZE','ZI','ZP','ZR','ZS','ZW','ZZ'];
    const lks = ['Augsburg in Bayern','in BayernernAalen Ostalbkreis in Baden-Württemberg','Aschaffenburg in Bayernern','Altenburger in Thüringen','Aachen in NWR','Auerbach in Sachsen','Aichach-Friedberg in Bayern','Altenkirchen/Westerwald Rheinland Pfalz','Amberg in Bayern','Ansbach in Bayern','Annaberg in Sachsen','Angermünde im Land Brandenburg-','Anklam in Mecklenburg Vorpommern-','Altötting in Bayern','Apolda - Weimarer in Thüringen','Apolda in Thüringen','Arnstadt in Thüringen','Artern in Thüringen','Amberg-Sulzbach in Bayern','Aschersleben in Sachsen Anhalt','Aue-Schwarzenberg in Sachsen','Altentreptow in Mecklenburg Vorpommern-','Aue in Sachsen','Aurich in Niedersachsen','Bad Neuenahr-Ahrweiler Rheinland Pfalz','Alzey-Worms Rheinland Pfalz','Anhalt-Zerbst in Sachsen Anhalt','Berlin Land Berlin','Bamberg in Bayern','Baden-Baden in Baden-Württemberg','Barnim im Land Brandenburg','Böblingen in Baden-Württemberg','Bernburg in Sachsen Anhalt','im Land Brandenburg Landesregierung und Landtag','Biberach/Riß in Baden-Württemberg','Bundestag, Bundesrat, Bundesregierung','im Land Brandenburgd-Erbisdorf in Sachsen','Belzig im Land Brandenburg','Bernau bei Berlin im Land Brandenburg','Berchtesgadener Land in Bayern','Bielefeld in NWR','Birkenfeld/Nahe und Idar-Oberstein Rheinland Pfalz','Bitburg-Prüm Rheinland Pfalz','Bischofswerda in Sachsen','Zollernalbkreis / Balingen in Baden-Württemberg','Burgenlandkreis in Sachsen Anhalt','Erftkreis / Bergheim in NWR','Bonn in NWR','Borna in Sachsen','Bochum in NWR','Bördekreis-Oschersleben in Sachsen Anhalt','Borken / Ahaus in NWR','Bottrop in NWR','Bundespolizei','Wesermarsch / Brake in Niedersachsen','im Land Brandenburg im Land Brandenburg','Burg in Sachsen Anhalt-','Braunschweig in Niedersachsen','Beeskow im Land Brandenburg','in Bayernreuth in Bayern','Bitterfeld in Sachsen Anhalt','Büsingen am Hochrhein in Baden-Württemberg','Bützow in Mecklenburg Vorpommern-','Bundes-Wasser- und Schiffahrtsverwaltung','Baden-Württemberg Landesregierung und Landtag','in Bayernern Landesregierung und Landtag','Bautzen in Sachsen','Chemnitz in Sachsen','Calau im Land Brandenburg','Cottbus im Land Brandenburg','Celle in Niedersachsen','Cham/Oberpfalz in Bayern','Cloppenburg in Niedersachsen','Coburg in Bayern','Cochem-Zell/Mosel Rheinland Pfalz','Coesfeld/Westfalen in NWR','Cuxhaven in Niedersachsen','Calw in Baden-Württemberg','Düsseldorf in NWR','Darmstadt-Dieburg Hess','Dachau in Bayern','Lüchow-Dannenberg in Niedersachsen','Daun Eifel Rheinland Pfalz','Deutsche Bahn','Bad Doberan in Mecklenburg Vorpommern','Dresden in Sachsen','Dessau in Sachsen Anhalt','Deggendorf in Bayern','Delmenhorst in Niedersachsen','Dingolfing-Landau in Bayern','Diepholz-Syke in Niedersachsen','Döbeln in Sachsen','Dillingen/Donau in Bayern','Demmin in Mecklenburg Vorpommern','Düren in NWR','Dortmund in NWR','Donau-Ries / Donauwörth in Bayern','Dahme-Spreewald im Land Brandenburg','Duisburg in NWR','Bad Dürkheim / Neustadt/Weinstraße Rheinland Pfalz','Dippoldiswalde-Weißeritzkreis in Sachsen','Delitzsch in Sachsen','Essen in NWR','Eisenach in Thüringen','Eilenburg in Sachsen','Ebersberg in Bayern','Erding in Bayern','Elbe-Elster im Land Brandenburg','Erfurt in Thüringen','Eisenhüttenstadt im Land Brandenburg','Eichstätt in Bayern','Eichsfeld in Thüringen','Eisleben in Sachsen Anhalt-','Eisenberg in Thüringen','Emsland / Meppen in Niedersachsen','Emmendingen in Baden-Württemberg','Emden in Niedersachsen','Rhein-Lahn-Kreis / Bad Ems Rheinland Pfalz','Ennepe-Ruhr-Kreis / Schwelm in NWR','Erlangen/Stadt in Bayern','Odenwaldkreis / Erbach Hess','Erlangen-Höchstadt in Bayern','Esslingen/Neckar in Baden-Württemberg','Eisenach in Thüringen','Werra-Meißner-Kreis / Eschwege Hess','Euskirchen in NWR','Eberswalde im Land Brandenburg','Frankfurt/Main Hess','Wetteraukreis / Friedberg Hess','Fulda Hess','Freudenstadt in Baden-Württemberg','Frankfurt/Oder im Land Brandenburg','Fürstenfeldbruck in Bayern','Freiberg/Sachsen in Sachsen','Finsterwalde im Land Brandenburg','Flensburg in Schleswig-Holstein','Flöha in Sachsen','Bodenseekreis / Friedrichshafen in Baden-Württemberg','Forchheim in Bayern','Forst im Land Brandenburg','Freiburg/Breisgau in Baden-Württemberg','Freyung-Grafenau in Bayern','Friesland / Jever in Niedersachsen','Bad Freienwalde im Land Brandenburg','Freising in Bayern','Frankenthal/Pfalz Rheinland Pfalz','Freital in Sachsen','Fürth in Bayern','Fürstenwalde im Land Brandenburg','Gera in Thüringen','Gardelegen in Sachsen Anhalt-','Garmisch-Partenkirchen in Bayern','Glauchau - Chemnitzer Land in Sachsen','Gadebusch in Mecklenburg Vorpommern-','Gelsenkirchen in NWR','Germersheim Rheinland Pfalz','Gifhorn in Niedersachsen','Groß-Gerau Hess','Geithain in Sachsen','Gräfenhainichen in Sachsen Anhalt-','Gießen Hess','Rheinisch-Bergischer Kreis / Bergisch Gladbach in NWR','Oberbergischer Kreis / Gummersbach in NWR','Grimmen in Mecklenburg Vorpommern-','Genthin in Sachsen Anhalt-','Göttingen in Niedersachsen','Göppingen in Baden-Württemberg','Görlitz in Sachsen','Grossenhain in Sachsen','Grimma in Sachsen','Gransee im Land Brandenburg','Greiz in Thüringen','Goslar in Niedersachsen','Gütersloh / Rheda-Wiedenbrück in NWR','Gotha in Thüringen','Güstrow in Mecklenburg Vorpommern','Guben im Land Brandenburg','Grevesmühlen in Mecklenburg Vorpommern-','Greifswald/Landkreis in Mecklenburg Vorpommern-','Günzburg in Bayern','Hannover in Niedersachsen','Hagen/Westfalen in NWR','Halle/Saale in Sachsen Anhalt','Hamm/Westfalen in NWR','Haßberge / Haßfurt in Bayern','Hansestadt Bremen und Bremerhaven Bre','Hildburghausen in Thüringen','Halberstadt in Sachsen Anhalt','Hainichen in Sachsen','Rhein-Neckar-Kreis und Heidelberg in Baden-Württemberg','Heidenheim/Brenz in Baden-Württemberg','Haldensleben in Sachsen Anhalt-','Helmstedt in Niedersachsen','Bad Hersfeld-Rotenburg Hess','Dithmarschen / Heide/Holstein in Schleswig-Holstein','Hessen Landesregierung und Landtag','Herne in NWR','Hettstedt in Sachsen Anhalt-','Herford / Kirchlengern in NWR','Hochtaunuskreis / Bad Homburg v.d.H. Hess','Hagenow in Mecklenburg Vorpommern-','Hansestadt Greifswald in Mecklenburg Vorpommern','Hansestadt Hamburg Hbg','Hohenmölsen in Sachsen Anhalt-','Hildesheim in Niedersachsen','Heiligenstadt in Thüringen','Hansestadt Lübeck in Schleswig-Holstein','Hameln-Pyrmont in Niedersachsen','Heilbronn/Neckar in Baden-Württemberg','Hof/Saale in Bayern','Holzminden in Niedersachsen','Saar-Pfalz-Kreis / Homburg/SaarlandSaarland','Hohenstein-Ernstthal in Sachsen','Bergstraße / Heppenheim Hess','Schwalm-Eder-Kreis / Homberg Hess','Hansestadt Rostock in Mecklenburg Vorpommern','Heinsberg in NWR','Hochsauerlandkreis / Meschede in NWR','Hansestadt Stralsund in Mecklenburg Vorpommern','Hanau Hess','Havelberg in Sachsen Anhalt-','Havelland im Land Brandenburg','Hansestadt Wismar in Mecklenburg Vorpommern','Höxter in NWR','Hoyerswerda in Sachsen','Herzberg im Land Brandenburg','St. Ingbert Saarland','Ilm-Kreis in Thüringen','Ilmenau in Thüringen','Ingolstadt/Donau in Bayern','Itzehoe in Schleswig-Holstein','Jena in Thüringen','Jüterbog im Land Brandenburg','Jessen in Sachsen Anhalt-','Jerichower Land in Sachsen Anhalt','Köln in NWR','Karlsruhe in Baden-Württemberg','Waldeck-Frankenberg / Korbach Hess','Kronach in Bayern','Kempten/Allgäu in Bayern','Kelheim in Bayern','Kaufbeuren in Bayern','Bad Kissingen in Bayern','Bad Kreuznach Rheinland Pfalz','Kiel in Schleswig-Holstein','Donnersberg-Kreis / Kirchheimbolanden Rheinland Pfalz','Kaiserslautern Rheinland Pfalz','Kleve in NWR','Klötze in Sachsen Anhalt-','Kamenz in Sachsen','Konstanz in Baden-Württemberg','Koblenz Rheinland Pfalz','Köthen in Sachsen Anhalt','Krefeld in NWR','Kassel Hess','Kitzingen in Bayern','Kulmbach in Bayern','Hohenlohe-Kreis / Künzelsau in Baden-Württemberg','Kusel Rheinland Pfalz','Königs-Wusterhausen im Land Brandenburg','Kyritz im Land Brandenburg','Kyffhäuserkreis in Thüringen','Leipzig in Sachsen','Landshut in Bayern','Nürnberger Land / Lauf/Pegnitz in Bayern','Ludwigsburg in Baden-Württemberg','Lobenstein in Thüringen','Lübz in Mecklenburg Vorpommern-','Luckau in Sachsen','Landau/Pfalz Rheinland Pfalz','Lahn-Dill-Kreis / Wetzlar Hess','Dahme-Spreewald im Land Brandenburg','Leer/Ostfriesland in Niedersachsen','Leverkusen in NWR','Lüneburg in Niedersachsen','Lindau/Bodensee in Bayern','Bad Liebenwerda im Land Brandenburg','Lichtenfels in Bayern','Lippe / Detmold in NWR','Landsberg/Lech in Bayern','Limburg-Weilburg/Lahn Hess','Lübben im Land Brandenburg','Lörrach in Baden-Württemberg','Löbau in Sachsen','Oder-Spree im Land Brandenburg','Sachsen-Anhalt Landesregierung und Landtag','Sachsen Landesregierung und Landtag','Bad Langensalza in Thüringen','Ludwigshafen/Rhein Rheinland Pfalz','Luckenwalde im Land Brandenburg','Ludwigslust in Mecklenburg Vorpommern','München in Bayern','Mannheim in Baden-Württemberg','Marienberg in Sachsen','Miesbach in Bayern','Malchin in Mecklenburg Vorpommern-','Magdeburg in Sachsen Anhalt','Mettmann in NWR','Meißen in Sachsen','Mittlerer Erzgebirgskreis in Sachsen','Merseburg in Sachsen Anhalt-','Mönchengladbach in NWR','Meiningen in Thüringen','Mülheim/Ruhr in NWR','Mühlhausen in Thüringen','Minden-Lübbecke/Westfalen in NWR','Miltenberg in Bayern','Märkischer Kreis / Lüdenscheid in NWR','Main-Kinzig-Kreis Hess','Mansfelder Land in Sachsen Anhalt','Memmingen in Bayern','Unterallgäu / Mindelheim in Bayern','Märkisch-Oderland im Land Brandenburg.','Neckar-Odenwald-Kreis / Mosbach in Baden-Württemberg','Merseburg-Querfurt in Sachsen Anhalt','Marburg-Biedenkopf/Lahn Hess','Münster/Westfalen in NWR','Main-Spessart-Kreis / Karlstadt in Bayern','Mecklenburg-Strelitz in Mecklenburg Vorpommern','Main-Taunus-Kreis / Hofheim Hess','Mühldorf am Inn in Bayern','Müritz in Mecklenburg Vorpommern','Mecklenburg-Vorpommern Landesregierung und Landtag','Mittweida in Sachsen','Mayen-Koblenz Rheinland Pfalz','Mainz-Bingen und Mainz Rheinland Pfalz','Merzig-Wadern Saarland','Nürnberg in Bayern','Nauen im Land Brandenburg','Neuim Land Brandenburg in Mecklenburg Vorpommern','Neuburg-Schrobenhausen/Donau in Bayern','Nordhausen in Thüringen','Neuss in NWR','Neustadt-Bad Windsheim/Aisch in Bayern','Nebra/Unstrut in Sachsen Anhalt-','Rhön-Grabfeld / Bad Neustadt/Saale in Bayern','Neustadt/Waldnaab in Bayern','Nordfriesland / Husum in Schleswig-Holstein','Neuhaus/Rennsteig in Thüringen','Nienburg/Weser in Niedersachsen','Neunkirchen/SaarlandSaarland','in Niedersachsen Landesregierung und Landtag','Neumarkt/Oberpfalz in Bayern','Naumburg/Saale in Sachsen Anhalt-','Neumünster in Schleswig-Holstein','Grafschaft Bentheim / Nordhorn in Niedersachsen','Niederschlesischer Oberlausitzkreis in Sachsen','Northeim in Niedersachsen','Neuruppin im Land Brandenburg','Neuwied/Rhein Rheinland Pfalz','Nordrhein-Westfalen Landesregierung und Landtag','Neu-Ulm in Bayern','Nordvorpommern in Mecklenburg Vorpommern','Neustadt/Weinstraße Rheinland Pfalz','Nordwestmecklenburg in Mecklenburg Vorpommern','Niesky in Sachsen','Neustrelitz in Mecklenburg Vorpommern-','Oberallgäu / Sonthofen in Bayern','Ostallgäu / Marktoberdorf in Bayern','Oberhausen/Rheinland in NWR','Osterburg in Sachsen Anhalt-','Oschersleben in Sachsen Anhalt-','Stormarn / Bad Oldesloe in Schleswig-Holstein','Olpe in NWR','Offenbach/Main Hess','Ortenaukreis / Offenburg in Baden-Württemberg','Ostholstein / Eutin in Schleswig-Holstein','Osterode/Harz in Niedersachsen','Oranienburg Oberhavel im Land Brandenburg','Osterholz-Scharmbeck in Niedersachsen','Ohre-Kreis in Sachsen Anhalt','Oldenburg in Niedersachsen','Ostprignitz-Ruppin im Land Brandenburg','Oranienburg im Land Brandenburg','Osnabrück in Niedersachsen','Senftenberg - Oberspreewald-Lausitz im Land Brandenburg','Obervogtland / Klingenthal und Ölsnitz in Sachsen','Ostvorpommern in Mecklenburg Vorpommern','Oschatz in Sachsen','Potsdam im Land Brandenburg','Passau in Bayern','Pfaffenhofen/Ilm in Bayern','Rottal-Inn / Pfarrkirchen in Bayern','Paderborn in NWR','Parchim in Mecklenburg Vorpommern','Peine in Niedersachsen','Perleberg im Land Brandenburg','Enzkreis und Pforzheim in Baden-Württemberg','Pinneberg in Schleswig-Holstein','Pirna - Sächsische Schweiz in Sachsen','Pritzwalk im Land Brandenburg','Plauen in Sachsen','Plön/Holstein in Schleswig-Holstein','Belzig - Potsdam-Mittelmark im Land Brandenburg','Pössneck in Thüringen','Prignitz / Perleberg im Land Brandenburg','Pirmasens Rheinland Pfalz','Pasewalk in Mecklenburg Vorpommern-','Prenzlau im Land Brandenburg','Querfurt in Sachsen Anhalt-','Quedlinburg in Sachsen Anhalt','Regensburg in Bayern','Rastatt in Baden-Württemberg','Reichenbach/Vogtland in Sachsen','Rendsburg-Eckernförde in Schleswig-Holstein','Ribnitz-Damgarten in Mecklenburg Vorpommern-','Recklinghausen / Marl in NWR','Regen in Bayernr. Wald in Bayern','Riesa-Großenhain in Sachsen','Roth/Rednitz in Bayern','Riesa in Sachsen','Rochlitz in Sachsen','Röbel/Müritz in Mecklenburg Vorpommern-','Rathenow im Land Brandenburg','Rosenheim in Bayern','Rostock/Landkreis in Mecklenburg Vorpommern-','Rotenburg/Wümme in Niedersachsen','Rhein-Pfalz-Kreis Rheinland Pfalz','Rheinland-Pfalz Landesregierung und Landtag','Remscheid in NWR','Rosslau/Elbe in Sachsen Anhalt-','Reutlingen in Baden-Württemberg','Rudolstadt in Thüringen','Rheingau-Taunus-Kreis / Rüdesheim Hess','Rügen / Bergen in Mecklenburg Vorpommern','Ravensburg in Baden-Württemberg','Rottweil in Baden-Württemberg','Herzogtum Lauenburg / Ratzeburg in Schleswig-Holstein','Stuttgart in Baden-Württemberg','Schwandorf in Bayern','Saarland Landesregierung und Landtag','Altmarkkreis - Salzwedel in Sachsen Anhalt','Saarbrücken Saarland','Strasburg in Mecklenburg Vorpommern-','Schönebeck/Elbe in Sachsen Anhalt','Schwabach in Bayern','Schleiz in Thüringen','Sondershausen in Thüringen','Stendalin Sachsen Anhalt','Schwedt/Oder im Land Brandenburg','Bad Segeberg in Schleswig-Holstein','Sebnitz in Sachsen','Seelow im Land Brandenburg','Soltau-Fallingbostel in Niedersachsen','Senftenberg im Land Brandenburg','Stassfurt in Sachsen Anhalt-','Solingen in NWR','Sangerhausen in Sachsen Anhalt','Schleswig-Holstein Landesregierung und Landtag','Schwäbisch Hall in Baden-Württemberg','Schaumburg / Stadthagen in Niedersachsen','Saale-Holzlandkreis in Thüringen','Suhl in Thüringen','Siegen-Wittgenstein in NWR','Sigmaringen in Baden-Württemberg','Rhein-Hunsrück-Kreis / Simmern Rheinland Pfalz','Saalkreis / Halle in Sachsen Anhalt','Schleswig-Flensburg in Schleswig-Holstein','Saalfeld-Rudolstadt in Thüringen','Schmölln in Thüringen','Saarlouis Saarland','Bad Salzungen in Thüringen','Schmalkalden-Meiningen in Thüringen','Schwerin in Mecklenburg Vorpommern','Soest in NWR','Saale-Orla-Kreis in Thüringen','Sömmerda in Thüringen','Sonneberg in Thüringen','Speyer Rheinland Pfalz','Spremberg im Land Brandenburg','Spree-Neiße im Land Brandenburg','Straubing-Bogen in Bayern','Strausberg im Land Brandenburg','Stadtroda in Thüringen','Steinfurt in NWR','Starnberg in Bayern','Sternberg in Mecklenburg Vorpommern-','Stade in Niedersachsen','Stollberg in Sachsen','Rhein-Sieg-Kreis / Siegburg in NWR','Südl. Weinstraße / Landau Rheinland Pfalz','Schweinfurt in Bayern','Salzgitter in Niedersachsen','Schwarzenberg in Sachsen','Main-Tauber-Kreis / Tauberbischofsheim in Baden-Württemberg','Teterow in Mecklenburg Vorpommern-','Teltow-Fläming im Land Brandenburg','Torgau in Sachsen','Thüringen Landesregierung und Landtag','Technisches Hilfswerk','Tirschenreuth in Bayern','Torgau-Oschatz in Sachsen','Bad Tölz-Wolfratshausen in Bayern','Templin/Uckermark im Land Brandenburg','Trier-Saarburg Rheinland Pfalz','Traunstein in Bayern','Tübingen in Baden-Württemberg','Tuttlingen in Baden-Württemberg','Uelzen in Niedersachsen','Ueckermünde in Mecklenburg Vorpommern-','Uecker-Randow in Mecklenburg Vorpommern','Unstrut-Hainich-Kreis in Thüringen','Alb-Donau-Kreis und Ulm in Baden-Württemberg','Uckermark im Land Brandenburg','Unna/Westfalen in NWR','Vogtlandkreis - Plauen in Sachsen','Vogelsbergkreis / Lauterbach Hess','Vechta in Niedersachsen','Verden/Aller in Niedersachsen','Viersen in NWR','Völklingen Saarland','Schwarzwald-Baar-Kreis / Villingen-Schwenningen in Baden-Württemberg','Wuppertal in NWR','Warendorf in NWR','Wartburgkreis in Thüringen','Wittenberg in Sachsen Anhalt','Worbis in Thüringen','Werdau in Sachsen','Weimar in Thüringen','Weiden/Oberpfalz in Bayern','Wesel / Mörs in NWR','Wolfenbüttel in Niedersachsen','Wilhelmshaven in Niedersachsen','Wiesbaden Hess','Bernkastel-Wittlich/Mosel Rheinland Pfalz','Wismar/Landkreis in Mecklenburg Vorpommern-','Wittstock im Land Brandenburg','Harburg / Winsen/Luhe in Niedersachsen','Wolgast/Usedom in Mecklenburg Vorpommern-','Weilheim-Schongau/Oberin Bayernern in Bayern','Wolmirstedt in Sachsen Anhalt-','Rems-Murr-Kreis / Waiblingen in Baden-Württemberg','St. Wendel Saarland','Worms Rheinland Pfalz','Wolfsburg in Niedersachsen','Wernigerode in Sachsen Anhalt','Waren/Müritz in Mecklenburg Vorpommern-','Weißenfels in Sachsen Anhalt','Ammerland / Westerstede in Niedersachsen','Weißwasser in Sachsen','Waldshut-Tiengen in Baden-Württemberg','Wittmund in Niedersachsen','Würzburg in Bayern','Weißenburg-Gunzenhausen in Bayern','Wunsiedel in Bayern','Wurzen in Sachsen','Westerwald / Montabaur Rheinland Pfalz','Wanzleben in Sachsen Anhalt-','Bundeswehr für NATO-Hauptquartiere','Bundeswehr','Zwickauer Land in Sachsen','Zerbst in Sachsen Anhalt-','Sächsischer Oberlausitzkreis Zittau in Sachsen','Zschopau in Sachsen','Zeulenroda in Thüringen','Zossen im Land Brandenburg','Zweibrücken Rheinland Pfalz','Zeitz in Sachsen Anhalt-'];

    const orgkz = $('#edit_car-c_ln').val().trim();
    const prefix = orgkz.split('-')[0];
    const idx = kza.indexOf(prefix);

    let message;
    if (idx === -1) {
        message = `Zum Kennzeichen <b>${orgkz}</b> existiert kein Landkreis!`;
    } else {
        message = 'Dieses Kennzeichen gehört zum Landkreis<br><b>' + lks[idx] + '</b>.';
    }

    $('#message-dialog')
        .html(message)
        .dialog({
        modal: false,
        title: 'Kennzeichen-Info'
    }).dialog( 'open' ).resize();
});
