/*****************************************************
*
* @var crmOrderItemLists - contains the list of worker and units
*****************************************************/
var crmOrderItemLists;

function crmCalcOrderPos(){
    $( '#od-netamount' ).val( 0 );
    $( '#od-amount' ).val( 0 );
    let positions = $( '#edit-order-table > tbody > tr');
    if( 3 >  positions.length ){
        $( positions[0] ).find( '[class=od-ui-del]' ).hide();
    }
    else{
        $( positions[0] ).find( '[class=od-ui-del]' ).show();
    }
    positions.each( function( key, pos ){
        if( key % 2 == 0 ) $( pos ).addClass( 'listrow0' );
        else $( pos ).addClass( 'listrow1' );
        $( pos ).find( '[class=od-item-position]' )[0].innerText = key + 1;
        if( !isEmpty( $( pos ).find( '[class=od-hidden-item-partnumber]' ).text() ) ){
            $( $( pos ).find( '[class=od-ui-edit-article]' )[0] ).html('<button onclick="crmOrderEditArticle()">Edit</button>');
        }
        crmCalcOrderPrice( pos );
   });
}

function crmCalcOrderPrice( pos ){
    let qty = kivi.parse_amount( $( pos ).find( '[class=od-item-qty]' )[0].value );
    if( isNaN( qty ) ) qty = 0;
    let sellprice = kivi.parse_amount( $( pos ).find( '[class=od-item-sellprice]' )[0].value );
    if( isNaN( sellprice ) ) sellprice = 0;
    let discount = kivi.parse_amount( $( pos ).find( '[class=od-item-discount]' )[0].value );
    if( isNaN( discount ) ) discount = 0;
    let marge_total = qty * sellprice;
    if( discount > 0 ){
        discount  = marge_total * ( discount / 100 );
        marge_total -= discount;
    }
    $( pos ).find( '[class=od-item-marge_total]' )[0].value = kivi.format_amount( marge_total, 2 );

    if( 'I' !== $( pos ).find( '[class=od-item-type]' )[0].value ){
        let netamount = kivi.parse_amount( $( '#od-netamount' ).val() ) + marge_total;
        $( '#od-netamount' ).val( kivi.format_amount( netamount, 2 ) );
        let amount = kivi.parse_amount( $( '#od-amount' ).val() ) + marge_total * ( parseFloat( $( pos ).find( '[class=od-hidden-item-rate]' )[0].value ) + 1 );
        $( '#od-amount' ).val( kivi.format_amount( amount, 2 ) );
   }
}

function crmEditOrderOnChange(){
    crmCalcOrderPos();
    crmSaveOrder();
}

function crmEditOrderKeyup(e){
    if( e.which == 13 || e.which == 9 ){
        crmCalcOrderPos();
        let field = $( ':focus' ).parent().parent();
        const desc = field.find( '[name=od-item-description]' ).val();
        if( !isEmpty( desc ) ){
           if( 'od-empty-item-id' === field.attr( 'id' ) ){
                console.info( 'Position ungültig!' );
                field.css("background-color","red");
                $( '#edit_article-description' ).val( desc );
                crmEditArticleDlg( field );
            }
            else{
                field.css( "background-color", "" );
            }
        }
    }
}

function crmOrderEditArticle(){
    let field = $( ':focus' ).parent().parent();
    const parts_id = field.find( '[class=od-item-parts_id]' ).val();
    const desc = field.find( '[name=od-item-description]' ).val();
    const unit = field.find( '[class=od-item-unit]' ).val();
    const qty = field.find( '[class=od-item-qty]' ).val();
    const sellprice = field.find( '[class=od-item-sellprice]' ).val();
    const part_type = field.find( '[class=od-item-type]' ).val();

    $( '#edit_article-parts_id' ).val( parts_id );
    $( '#edit_article-description' ).val( desc );
    $( '#edit_article-part_type' ).val( part_type );
    $( '#edit_article-qty' ).val( qty );
    $( '#edit_article-sellprice' ).val( sellprice );

    crmEditArticleDlg( field );
}

function crmEditOrderChangeHundredPro( hundredpro, hundredproBtn){
    if( hundredpro.val() == 100 ){
        hundredpro.val( 0 );
        hundredproBtn.text( '0%' );
    }
    else{
        hundredpro.val( 100 );
        hundredproBtn.text( '100%' );
    }
}

function crmEditOrderHundredPro(){
    let hundredpro =  $( ':focus' ).parent().parent().find( '[class=od-ui-hundredpro]' );
    $( ':focus' ).parent().parent().find( '[class=od-item-discount]' ).val( hundredpro.val() );
    crmEditOrderChangeHundredPro( hundredpro, $( ':focus' ).parent().parent().find( '[class=od-ui-hundredpro-btn]' ) )
    crmCalcOrderPos();
    crmSaveOrder();
}

$( '#od-ui-discount-100-all-btn' ).click( function(){
    $( '#edit-order-table > tbody > tr').each( function( key, pos ){
        if( 'od-empty-item-id' !== $( pos ).attr( 'id' ) ){
            let hundredpro =  $( pos ).find( '[class=od-ui-hundredpro]' );
            hundredpro.val( $( '#od-ui-discount-100-all' ).val()  );
            $( pos ).find( '[class=od-item-discount]' ).val( hundredpro.val() );
            crmEditOrderChangeHundredPro( hundredpro, $( pos ).find( '[class=od-ui-hundredpro-btn]' ) )
        }
    });
    crmEditOrderChangeHundredPro( $( '#od-ui-discount-100-all' ) , $( '#od-ui-discount-100-all-btn' ) )
    crmCalcOrderPos();
    crmSaveOrder();
});

function crmAddOrderItem( dataRow ){
    /* Ist es nicht sinnvoller die workers einmalig beim erzeugen / holen des Auftrages anstatt mit jeder Zeile zu holen? */
    let tableRow;
    tableRow += '<tr ' + ( ( exists( dataRow.id ) )? ('id="' + dataRow.id + '"') : 'id = "od-empty-item-id" class="od-item-pin"') + '><td class="od-item-position"></td>' +
                '<td><img class="od-ui-hsort" src="image/updown.png" alt="umsortieren"' + ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '</td>' +
                '<td><img class="od-ui-del" src="image/close.png" alt="löschen"' + ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + 'onclick="crmDeleteOrderPos(this)"></td>' +
                '<td class="od-ui-edit-article"></td>' +
                '<td><span class="od-hidden-item-partnumber">' + ( ( exists( dataRow.partnumber ) )? dataRow.partnumber : '' ) + '</span>' +
                '<input class="od-item-parts_id" type="hidden" value="' + ( ( exists( dataRow.parts_id ) )? dataRow.parts_id : '' ) + '"></input></td>' +
                '<td>';
    let orderType = '';
    if( dataRow.instruction )  orderType = 'I';
    else if( 'part' === dataRow.part_type ) orderType = 'P';
    else if( 'service' === dataRow.part_type ) orderType = 'S';
    tableRow += '<input class="od-item-type" type="hidden" value="' + orderType + '"></input>';
    tableRow += '<span class="od-table-item-type">' + kivi.t8( orderType ) + '</span>';
    tableRow += '</td>' +
                '<td><input name="od-item-description" class="od-item-description" type="text" size="40" value="' + ( ( exists( dataRow.description ) )? dataRow.description : '' ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup(event)"></input></td>' +
                '<td><input class="od-item-longdescription" type="text" size="40" value="' + ( ( exists( dataRow.longdescription ) )? dataRow.longdescription : '' )  + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup(event)" ' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input>' +
                '</td><td><input class="od-item-qty" type="text" size="5" value="' + kivi.format_amount( ( exists( dataRow.qty ) )? dataRow.qty : '0' ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup(event)"' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input></td>';

    // Unit is readonly now:
    tableRow += '<td><input class="od-item-unit" type="text" size="5" readonly="readonly" value="' + ( ( exists( dataRow.unit ) )? dataRow.unit : '' ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup(event)" ' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input></td>';

    tableRow += '<td><input class="od-hidden-item-rate" type="hidden" value="' + ( ( exists( dataRow.rate ) )? dataRow.rate : '0' ) + '"></input>' +
                '<input class="od-item-sellprice" type="text" size="5" value="' + kivi.format_amount( ( exists( dataRow.sellprice ) )? dataRow.sellprice : '0', 2 ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup(event)" ' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input></td>' +
                '<td><input class="od-item-discount" type="text" size="5" value="' + kivi.format_amount( ( exists( dataRow.discount ) )? dataRow.discount : '0' ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup(event)" ' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input></td>' +
                '<td><input class="od-ui-hundredpro" type="hidden" value="100"></input><button class="od-ui-hundredpro-btn" onclick="crmEditOrderHundredPro()" ' + ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '>100%</button></td>' +
                '<td><input class="od-item-marge_total" type="text" size="5" readonly="readonly" value="' + kivi.format_amount( ( exists( dataRow.marge_total ) )? dataRow.marge_total : '0', 2 ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup(event)" ' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input></td>';

    tableRow += '<td><select class="od-item-u_id" type="select" onchange="crmEditOrderOnChange()" ' + ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '>';
    tableRow += '<option value=""></option>';
    //console.info( crmOrderItemLists.workers );//ToDo???
    if( crmOrderItemLists.workers === null ) alert( kivi.t8( 'No members of group "Werkstatt" ' ) );
    else{
        for( let worker of crmOrderItemLists.workers ){
            tableRow += '<option value="' + worker.name  + '"';
            if(dataRow.u_id === worker.name) tableRow += ' selected'
            tableRow += '>' + worker.name + '</option>';
        }
        tableRow += '</select></td>';
    }
    const statusList = [ 'gelesen', 'Bearbeitung', 'erledigt' ];
    tableRow += '<td><select class="od-item-status" type="select" onchange="crmEditOrderOnChange()" ' + ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '>';
    for( let status of statusList ){
        tableRow += '<option value="' + status  + '"';
        if(dataRow.status === status) tableRow += ' selected'
        tableRow += '>' + status  + '</option>';
    }
    tableRow += '</select></td>';

    $( '#edit-order-table > tbody' ).append(tableRow);

    $( '.od-item-description' ).catcomplete({
        source: "crm/ajax/crm.app.php?action=findPart",
        select: function( e, ui ){
            const row = $( ':focus' ).parent().parent();
            crmCompleteInsertOrderPos( row, ui.item );
        }
    });

    crmCalcOrderPos();
}

function crmCompleteInsertOrderPos( row, item ){
    row.find( '[class=od-hidden-item-partnumber]' ).text( item.partnumber );
    row.find( '[class=od-item-parts_id]' ).val( item.id );
    let orderType = '';
    if( item.instruction )  orderType = 'I';
    else if( 'part' === item.part_type || 'P' === item.part_type ) orderType = 'P';
    else if( 'service' === item.part_type || 'S' === item.part_type ) orderType = 'S';
    row.find( '[class=od-item-type]' ).val( orderType );
    row.find( '[class=od-table-item-type]' ).text( orderType );
    row.find( '[class=od-item-qty]' ).val( kivi.format_amount( item.qty, 2 ) );
    row.find( '[class=od-item-unit]' ).val( item.unit );
    row.find( '[class=od-item-sellprice]' ).val( kivi.format_amount( item.sellprice, 2 ) );
    row.find( '[class=od-hidden-item-rate]' ).val( item.rate );
    row.find( '[name=od-item-description]' ).val( item.description );
    row.find( '[class=od-ui-hsort]' ).show();
    row.find( '[class=od-ui-del]' ).show();
    row.find( '[class=od-item-longdescription]' ).show();
    row.find( '[class=od-item-qty]' ).show();
    row.find( '[class=od-item-unit]' ).show();
    row.find( '[class=od-item-sellprice]' ).show();
    row.find( '[class=od-item-discount]' ).show();
    row.find( '[class=od-ui-hundredpro-btn]' ).show();
    row.find( '[class=od-item-marge_total]' ).show();
    row.find( '[class=od-item-u_id]' ).show();
    row.find( '[class=od-item-status]' ).show();
    let itemPosition = row.find( '[class=od-item-position]' )[0].innerText;
    //Bug or feature, can't do otherwise:
    row[0].className = "";

    const list = $( '.od-item-description' );
    if( list[list.length - 1].value !== '' ){
        crmAddOrderItem( { } );
    }
    crmCalcOrderPos();
    crmInsertOrderPos( itemPosition, orderType, item, ( row[0].id !== 'od-empty-item-id' ) );

    row.css( "background-color", "" );
}

function crmNewOrderAndInsertPos( itemPosition, itemType, item ){
    let dbData = { }
    dbData['customer_id'] = $( '#od-customer-id' ).val();
    dbData['c_id'] = $( '#od-lxcars-c_id' ).val();

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'insertNewOrder', data: dbData },
        success: function( data ){
            $( '#od-oe-id' ).val( data.id );
            crmInsertOrderPos( itemPosition, itemType, item );
            if( !isEmpty( $( '#od-oe-id' ).val() ) ){
                $( '#od-ui-btn-printer1' ).show();
                $( '#od-ui-btn-printer2' ).show();
                $( '#od-ui-btn-pdf' ).show();
            }
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmInsertOrderPos()', xhr.responseText );
        }
    });
}

function crmInsertOrderPos( itemPosition, itemType, item, modified = false ){
    let pos = {};
    let dbTable = '';
    if( 'P' === itemType  ) dbTable = 'orderitems';
    if( 'S' === itemType  ) dbTable = 'orderitems';
    if( 'I' === itemType  ) dbTable = 'instructions';

    pos['record'] = {};
    pos['record'][dbTable] = {};
    pos['record'][dbTable]['trans_id'] = $( '#od-oe-id' ).val();
    pos['record'][dbTable]['position'] = itemPosition;
    pos['record'][dbTable]['parts_id'] = item.id;
    pos['record'][dbTable]['qty'] = ( item.qty === null )? 0 : item.qty;
    pos['record'][dbTable]['unit'] = item.unit;
    pos['record'][dbTable]['sellprice'] = item.sellprice;
    pos['record'][dbTable]['description'] = item.description;
    pos['sequence_name'] = 'orderitemsid';

    if( isEmpty( $( '#od-oe-id' ).val() ) ){
        crmNewOrderAndInsertPos( itemPosition, itemType, item );
        return;
    }

    if( modified ){
        crmSaveOrder();
        return;
    }

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'genericSingleInsert', data: pos },
        success: function( data ){
            $( '#od-empty-item-id' ).attr( 'id', data.id );
            crmSaveOrder();
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmInsertOrderPos()', xhr.responseText );
        }
    });
 }

crmDeleteOrderPos = function( e ) {
    var row = $( e ).parent().parent();

    let pos = {};
    let dbTable = '';
    let itemType = $( row ).find( '[class=od-item-type]' ).val();
    if( 'P' === itemType  ) dbTable = 'orderitems';
    if( 'S' === itemType  ) dbTable = 'orderitems';
    if( 'I' === itemType  ) dbTable = 'instructions';

    pos[dbTable] = {};
    pos[dbTable]['WHERE'] = 'id = ' + $( row ).attr( 'id' );

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'genericDelete', data: pos },
        success: function( data ){
            $( row ).remove();
            crmCalcOrderPos();
            crmSaveOrder();
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmDeleteOrderPos()', xhr.responseText );
        }
    });
 }

function crmSaveOrder(){
    if( isEmpty( $( '#od-oe-id' ).val() ) ) return;

    let dbUpdateData = { }
    dbUpdateData['oe'] = {};
    dbUpdateData['customer'] = {};
    dbUpdateData['lxc_cars'] = {};
    dbUpdateData['orderitems'] = [];
    dbUpdateData['instructions'] = [];

    $( '.od-common :input' ).each( function( key, pos ){
        dbUpdateData['oe'][pos.id.split( '-' )[2]] = ( 'checkbox' === pos.type )? $( pos ).prop( 'checked' ) : $( pos ).val();
    });

    dbUpdateData['customer']['notes'] = $( '#od-customer-notes' ).val();
    dbUpdateData['lxc_cars']['c_text'] = $( '#od-lxcars-c_text' ).val();
    dbUpdateData['oe']['intnotes'] = $( '#od-oe-intnotes' ).val();
    dbUpdateData['oe']['shippingpoint'] = $( '#od-lxcars-c_ln' ).html();// für Kilometerstand in Druckvorlage od-lxcars-c_ln
    dbUpdateData['oe']['shipvia'] = $( '#od-oe-km_stnd' ).val();// für Kennzeichen in Druckvorlage
    dbUpdateData['oe']['amount'] = kivi.parse_amount( $( '#od-amount' ).val() );
    dbUpdateData['oe']['netamount'] = kivi.parse_amount( $( '#od-netamount' ).val() );

    $( '#edit-order-table > tbody > tr').each( function( key, pos ){
        let itemType;
        let dataRow = { };
        $( pos ).find( '[class^=od-item]' ).each( function( i, item ){
            let columnName = item.className.split( ' ' )[0].split( '-' )[2];
            if( !exists( columnName ) ) return;
            if( 'type' === columnName) itemType = item.value;
            else if( exists( item.value ) ) dataRow[columnName] = item.value;
            else if( exists( item.innerText ) ) dataRow[columnName] = item.innerText;
        });
        dataRow.qty = ( dataRow.qty )? kivi.parse_amount( dataRow.qty ) : 0;
        dataRow.sellprice = kivi.parse_amount( dataRow.sellprice );
        dataRow.discount = kivi.parse_amount( dataRow.discount );
        dataRow.marge_total = kivi.parse_amount( dataRow.marge_total );
        if( exists( pos.id ) ){
            if( 'P' === itemType  ){
                dataRow['WHERE'] = {};
                dataRow['WHERE'] = 'id = ' +  pos.id;
                dbUpdateData['orderitems'].push( dataRow );
            }
            if( 'S' === itemType  ){
                dataRow['WHERE'] = {};
                dataRow['WHERE'] = 'id = ' + pos.id;
                dbUpdateData['orderitems'].push( dataRow );
            }
            if( 'I' === itemType  ){
                dataRow['WHERE'] = {};
                dataRow['WHERE'] = 'id = ' + pos.id;
                dbUpdateData['instructions'].push( dataRow );
            }
        }
    });

    dbUpdateData['oe']['WHERE'] = {};
    dbUpdateData['oe']['WHERE'] = 'id = ' + $( '#od-oe-id' ).val();

    dbUpdateData['customer']['WHERE'] = {};
    dbUpdateData['customer']['WHERE']= 'id = ' + $( '#od-customer-id' ).val();

    dbUpdateData['lxc_cars']['WHERE'] = {};
    dbUpdateData['lxc_cars']['WHERE'] = 'c_id = ' + $( '#od-lxcars-c_id' ).val();

    //console.info( 'dbUpdateData' );
    //console.info( dbUpdateData );

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'genericUpdateEx', data: dbUpdateData },
        success: function( data ){
            console.info( 'Order saved' );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmSaveOrder()', xhr.responseText );
        }
    });
}

function crmNewOrderForCar( c_id ){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getDataForNewLxcarsOrder', data: { 'id': c_id } },
        success: function( crmData ){
            crmEditOrderDlg( crmData );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewOrderForCar', xhr.responseText );
        }
    });
}

$( '#crm-edit-order-dialog :input' ).change( function(){
    crmCalcOrderPos();
    crmSaveOrder();
});
$( '#od-ui-items-workers' ).change( function(){
    $( '#edit-order-table > tbody > tr').each( function( key, pos ){
        if( 'od-empty-item-id' !== $( pos ).attr( 'id' ) ){
            $( pos ).find( '[class=od-item-u_id]' ).val( $( '#od-ui-items-workers' ).val() );
        }
    });
    crmCalcOrderPos();
    crmSaveOrder();
});
$( '#od-ui-items-status-all' ).change( function(){
    $( '#edit-order-table > tbody > tr').each( function( key, pos ){
        if( 'od-empty-item-id' !== $( pos ).attr( 'id' ) ){
            $( pos ).find( '[class=od-item-status]' ).val( $( '#od-ui-items-status-all' ).val() );
        }
    });
    crmCalcOrderPos();
    crmSaveOrder();
});


function crmEditOrderDlg( crmData ){
    crmOrderItemLists = { };
    crmOrderItemLists['workers'] = crmData.workers;
    $( '#edit-order-table > tbody' ).html( '' );
    if( exists( crmData.order ) && exists( crmData.order.orderitems ) ){
        for( let dataRow of crmData.order.orderitems ){
           crmAddOrderItem( dataRow );
        }
    }
    crmAddOrderItem( { } );
    $( '#edit-order-table > tbody' ).sortable({
        items: '> tr:not(.od-item-pin)',
        cancel: '.od-item-pin, .od-ui-del, input, select, button',
        update: function(){
            crmCalcOrderPos();
        }
    });

    if( exists( crmData.order ) ){
        $( '#od-customer-id' ).val( crmData.order.common.customer_id );
        $( '#od-lxcars-c_id' ).val( crmData.order.common.c_id );
        $( '#od-oe-id' ).val( crmData.order.common.id );
        $( '#od-customer-name' ).html( crmData.order.common.customer_name );
        $( '#od-oe-ordnumber' ).html( crmData.order.common.ordnumber );
        $( '#od-oe-finish_time' ).val( crmData.order.common.finish_time );
        $( '#od-oe-km_stnd' ).val( crmData.order.common.km_stnd );
        $( '#od-oe-employee_name' ).html( crmData.order.common.employee_name );
        $( '#od-oe-employee_id' ).val( crmData.order.common.employee_id );
        $( '#od-lxcars-c_ln' ).html( crmData.order.common.c_ln );
        $( '#od-oe-mtime' ).html( kivi.format_date( new Date( crmData.order.common.mtime ) ) );
        $( '#od-oe-internalorder' ).prop( 'checked', crmData.order.common.internalorder );
        $( '#od-oe-itime' ).html( kivi.format_date( new Date( crmData.order.common.itime ) ) );
        $( '#od-oe-car_status' ).val( crmData.order.common.car_status );
        $( '#od-oe-status' ).val( crmData.order.common.status );
        $( '#od-lxcars-c_text' ).val( crmData.order.common.int_car_notes );
        $( '#od-customer-notes' ).val( crmData.order.common.int_cu_notes );
        $( '#od-oe-intnotes' ).val( crmData.order.common.intnotes );
    }
    else{
        $( '#od-customer-id' ).val( crmData.common.customer_id );
        $( '#od-lxcars-c_id' ).val( crmData.common.c_id );
        $( '#od-oe-id' ).val( '' );
        $( '#od-customer-name' ).html( crmData.common.customer_name );
        $( '#od-oe-ordnumber' ).html( '' );
        $( '#od-oe-finish_time' ).val( '' );
        $( '#od-oe-km_stnd' ).val( '0' );
        $( '#od-oe-employee_name' ).html( crmData.common.employee_name );
        $( '#od-oe-employee_id' ).val( crmData.common.employee_id );
        $( '#od-lxcars-c_ln' ).html( crmData.common.c_ln  );
        $( '#od-oe-mtime' ).html( '' );
        $( '#od-oe-internalorder' ).prop( 'checked', false );
        $( '#od-oe-itime' ).html( '' );
        $( '#od-oe-car_status' ).val( '' );
        $( '#od-oe-status' ).val( '' );
        $( '#od-lxcars-c_text' ).val( crmData.common.int_car_notes );
        $( '#od-customer-notes' ).val( crmData.common.int_cu_notes  );
        $( '#od-oe-intnotes' ).val( '' );
     }

    $( '#od-ui-items-workers' ).html( '' );
    $( '#od-ui-items-workers' ).append(new Option( '', ''  ) );

    if( crmData.workers !== null ){
        for( let worker of crmData.workers ){
            $( '#od-ui-items-workers' ).append(new Option( worker.name, worker.name  ) );
        }
    }
    $( '#crm-edit-order-dialog' ).dialog({
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
        modal: true,
        title: kivi.t8( exists( crmData.order )? 'Edit order' : 'New order' ),
        position: { my: "top", at: "top+250" },
        open: function(){
            $( this ).css( 'maxWidth', window.innerWidth );
            if( isEmpty( $( '#od-oe-id' ).val() ) ){
                $( '#od-ui-btn-printer1' ).hide();
                $( '#od-ui-btn-printer2' ).hide();
                $( '#od-ui-btn-pdf' ).hide();
            }
        },
        close: function(){
            crmRefreshAppViewAction();
        },
        buttons:[{
            text: kivi.t8( 'Printer 1' ),
            id: 'od-ui-btn-printer1',
            click: function(){
                let printData = {};
                printData['orderId'] = $( '#od-oe-id' ).val();
                printData['print'] = 'printOrder1';
                printData['customerId'] = $( '#od-customer-id' ).val();
                $.ajax({
                    url: 'crm/ajax/crm.app.php',
                    type: 'POST',
                    data:  { action: 'printOrder', data: printData },
                    success: function( data ){
                        console.info( 'printed' );
                    },
                    error: function( xhr, status, error ){
                        $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'printOrder( printOrder1 )', xhr.responseText );
                    }
                });
            }
        },{
            text: kivi.t8( 'Printer 2' ),
            id: 'od-ui-btn-printer2',
            click: function(){
                let printData = {};
                printData['orderId'] = $( '#od-oe-id' ).val();
                printData['print'] = 'printOrder2';
                printData['customerId'] = $( '#od-customer-id' ).val();
                $.ajax({
                    url: 'crm/ajax/crm.app.php',
                    type: 'POST',
                    data:  { action: 'printOrder', data: printData },
                    error: function( xhr, status, error ){
                        $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'printOrder( printOrder2 )', xhr.responseText );
                    }
                });
             }
        },{
            text: kivi.t8( ' PDF ' ),
            id: 'od-ui-btn-pdf',
            click: function(){
                let printData = {};
                printData['orderId'] = $( '#od-oe-id' ).val();
                printData['print'] = 'pdfOrder';
                printData['customerId'] = $( '#od-customer-id' ).val();
                 $.ajax({
                    url: 'crm/ajax/crm.app.php',
                    type: 'GET',
                    data:  { action: 'printOrder', data: printData },
                    success: function( data ){
                       window.open( 'crm/out.pdf' );
                    },
                    error: function( xhr, status, error ){
                        $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'printOrder( pdfOrder )', xhr.responseText );
                    }
                });
             }
        },
        {
            text: kivi.t8( 'Close' ),
            click: function(){
                $( this ).dialog( "close" );
            }
        }]
    }).dialog( 'open' ).resize();
}
