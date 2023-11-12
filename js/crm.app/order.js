/*****************************************************
*
* @var crmOrderItemLists - contains the list of worker and units
*****************************************************/
var crmOrderItemLists;

function crmCalcOrderPos(){
    if( crmOrderTypeEnum.Order == crmOrderType ){
        crmPerfomanceIst =  0;
        crmPerfomanceSoll = 0;
    }

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
        if( $( pos ).find( '[class=od-item-type]' ).val() == 'I' ){
            $( pos ).css( "background-color", "#00BFFF" );
            $( pos ).find( ':input' ).css( "background-color", "#00BFFF" );
        }
        else if( key % 2 == 0 ){
            $( pos ).addClass( 'listrow0' );
            $( pos ).find( ':input' ).css( "background-color", "#FFFFFF" );
        }
        else{
            $( pos ).addClass( 'listrow1' );
            $( pos ).find( ':input' ).css( "background-color", "#D3D3D3" );
        }
        $( pos ).find( '[class=od-item-position]' )[0].innerText = key + 1;
        if( !isEmpty( $( pos ).find( '[class=od-hidden-item-partnumber]' ).text() ) ){
            $( $( pos ).find( '[class=od-ui-edit-article]' )[0] ).html('<button onclick="crmOrderEditArticle()">Edit</button>');
        }
        crmCalcOrderPrice( pos );
   });
}

var crmPerfomanceIst = 0;
var crmPerfomanceSoll = 0;

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

    const item_type = $( pos ).find( '[class=od-item-type]' )[0].value;
    if( 'I' !== item_type ){
        let netamount = kivi.parse_amount( $( '#od-netamount' ).val() ) + marge_total;
        $( '#od-netamount' ).val( kivi.format_amount( netamount, 2 ) );
        let amount = kivi.parse_amount( $( '#od-amount' ).val() ) + marge_total * ( parseFloat( $( pos ).find( '[class=od-hidden-item-rate]' )[0].value ) + 1 );
        $( '#od-amount' ).val( kivi.format_amount( amount, 2 ) );
    }

    //Brechnung der Performance für di GuV
    if( crmOrderTypeEnum.Order == crmOrderType ){
        const base_unit = $( pos ).find( '[class=od-hidden-item-base_unit]' )[0].value;
        const qty = kivi.parse_amount( $( pos ).find( '[class=od-item-qty]' )[0].value );
        const factor = kivi.parse_amount( $( pos ).find( '[class=od-hidden-item-factor]' )[0].value );
        if( 'I' == item_type && 'min' == base_unit ) crmPerfomanceIst += qty * factor;
        if( 'S' == item_type && 'min' == base_unit ) crmPerfomanceSoll += qty * factor;
        const performance = ( crmPerfomanceSoll - crmPerfomanceIst ) / 60;
        $( '#od-performance' ).val( kivi.format_amount( performance ) );
        if( performance < 0 ) $( '#od-performance' ).css( 'background-color', 'red' );
        else $( '#od-performance' ).css( 'background-color', 'green' );
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
        const part_id = field.find( '[class=od-item-parts_id]' ).val();
        if( !isEmpty( desc ) ){
           if( '' === part_id ){
                field.css("background-color","red");
                $( '#edit_article-description' ).val( desc );
                if( !isNaN( desc ) && null != crmEditOrderFirstFoundArticle){
                    const row = $( ':focus' ).parent().parent();
                    const res = crmCompleteInsertOrderPos( row, crmEditOrderFirstFoundArticle  );
                    if( res ) $( '[name=od-item-description]' ).filter( ':last' ).focus();
                }
                else{
                    crmEditArticleDlg( field );
                }
            }
            else{
                if( 'I' == field.find( '[class=od-item-type]' )[0].value ) field.css( "background-color", "00BFF" );
                else  field.css( "background-color", "" );
            }
        }
    }
}

function crmEditOrderKeyup2(e){
    crmEditOrderKeyup(e);
    if( e.which != 13 && e.which != 9 ){
        crmCalcOrderPos();
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

var crmEditOrderFirstFoundArticle = null;

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
                '</td><td><input class="od-item-qty" type="text" size="5" value="' + kivi.format_amount( ( exists( dataRow.qty ) )? dataRow.qty : '0' ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup2(event)"' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input></td>';

    // Unit is readonly now:
    tableRow += '<td><input class="od-hidden-item-base_unit" type="hidden" value="' + ( ( exists( dataRow.unit_type ) )? dataRow.unit_type.base_unit : '' ) + '"></input>' +
                '<input class="od-hidden-item-factor" type="hidden" value="' + ( ( exists( dataRow.unit_type ) )? dataRow.unit_type.factor : '0' ) + '"></input>' +
                '<input class="od-item-unit" type="text" size="5" readonly="readonly" value="' + ( ( exists( dataRow.unit ) )? dataRow.unit : '' ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup(event)" ' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input></td>';

    tableRow += '<td><input class="od-hidden-item-rate" type="hidden" value="' + ( ( exists( dataRow.buchungsziel ) )? dataRow.buchungsziel.rate : '0' ) + '"></input>' +
                '<input class="od-hidden-item-income_chart_id" type="hidden" value="' + ( ( exists( dataRow.buchungsziel ) )? dataRow.buchungsziel.income_chart_id : '0' ) + '"></input>' +
                '<input class="od-hidden-item-tax_id" type="hidden" value="' + ( ( exists( dataRow.buchungsziel ) )? dataRow.buchungsziel.tax_id : '0' ) + '"></input>' +
                '<input class="od-hidden-item-tax_chart_id" type="hidden" value="' + ( ( exists( dataRow.buchungsziel ) )? dataRow.buchungsziel.tax_chart_id : '0' ) + '"></input>' +
                '<input class="od-item-sellprice" type="text" size="5" value="' + kivi.format_amount( ( exists( dataRow.sellprice ) )? dataRow.sellprice : '0', 2 ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup2(event)" ' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input></td>' +
                '<td><input class="od-item-discount" type="text" size="5" value="' + kivi.format_amount( ( exists( dataRow.discount ) )? dataRow.discount * 100 : '0' ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup(event)" ' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input></td>' +
                '<td><input class="od-ui-hundredpro" type="hidden" value="100"></input><button class="od-ui-hundredpro-btn" onclick="crmEditOrderHundredPro()" ' + ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '>100%</button></td>' +
                '<td><input class="od-item-marge_total" type="text" size="5" readonly="readonly" value="' + kivi.format_amount( ( exists( dataRow.marge_total ) )? dataRow.marge_total : '0', 2 ) + '" onchange="crmEditOrderOnChange()" onkeyup="crmEditOrderKeyup(event)" ' +
                ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '></input></td>';

    if( exists( crmOrderItemLists.workers  )  ){
        tableRow += '<td><select class="od-item-u_id" type="select" onchange="crmEditOrderOnChange()" ' + ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '>';
        tableRow += '<option value=""></option>';
         for( let worker of crmOrderItemLists.workers ){
            tableRow += '<option value="' + worker.name  + '"';
            if(dataRow.u_id === worker.name) tableRow += ' selected'
            tableRow += '>' + worker.name + '</option>';
        }
        tableRow += '</select></td>';
    }
    if( crmOrderTypeEnum.Order == crmOrderType ){
        const statusList = [ 'gelesen', 'Bearbeitung', 'erledigt' ];
        tableRow += '<td><select class="od-item-status" type="select" onchange="crmEditOrderOnChange()" ' + ( ( exists( dataRow.id ) )? '' : 'style = "display:none"') + '>';
        for( let status of statusList ){
            tableRow += '<option value="' + status  + '"';
            if(dataRow.status === status) tableRow += ' selected'
            tableRow += '>' + status  + '</option>';
        }
        tableRow += '</select></td>';
    }

    $( '#edit-order-table > tbody' ).append(tableRow);

    function crmGetCatcompleteURL(){
        let url = "crm/ajax/crm.app.php?action=findPart";
        switch( crmOrderType ){
            case crmOrderTypeEnum.Order:
                url += "&filter=order";
                break;
            case crmOrderTypeEnum.Offer:
                url += "&filter=offer";
                break;
            case crmOrderTypeEnum.Delivery:
                //url += "&filter=delivery";
                break;
            case crmOrderTypeEnum.Invoice:
                url += "&filter=invoice";
                break;
        }

        return url;
    }

    $( '.od-item-description' ).catcomplete({
        delay: crmAcDelay,
        minLength: 3,
        //source: "crm/ajax/crm.app.php?action=findPart" + ( ( crmOrderTypeEnum.Invoice == crmOrderType )? '&filterI' : '' ),
        source: crmGetCatcompleteURL(),
        select: function( e, ui ){
            const row = $( ':focus' ).parent().parent();
            const res = crmCompleteInsertOrderPos( row, ui.item );
            if( !res ) return false;
            $( '[name=od-item-description]' ).filter( ':last' ).focus();
            return true;
        },
        response: function( e, ui ){
            crmEditOrderFirstFoundArticle = null;
            if( exists( ui.content ) && ui.content.length > 0 ){
                crmEditOrderFirstFoundArticle = ui.content[0];
            }
        }
    });

    crmCalcOrderPos();
}

function crmCompleteInsertOrderPos( row, item ){
    let itemType = row.find( '[class=od-item-type]' ).val();
    if( true == item.instruction ){
        if( 'P' == itemType || 'S' == itemType ){
            alert( kivi.t8( "Invalid type of article: It can't be a instruction!" ) );
            return false;
        }
    }
    else{
        if( 'I' == itemType ){
            alert( kivi.t8( "Invalid type of article: It must be a good or a service!" ) );
            return false;
        }
    }

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
    if( exists( item.unit_type) ){
        row.find( '[class=od-hidden-item-base_unit]' ).val( item.unit_type.base_unit );
        row.find( '[class=od-hidden-item-factor]' ).val( item.unit_type.factor );
    }
    row.find( '[class=od-item-sellprice]' ).val( kivi.format_amount( item.sellprice, 2 ) );
    row.find( '[class=od-hidden-item-rate]' ).val( exists( item.buchungsziel )? item.buchungsziel.rate  : 0 );
    row.find( '[class=od-hidden-item-income_chart_id]' ).val( exists( item.buchungsziel )? item.buchungsziel.income_chart_id  : 0 );
    row.find( '[class=od-hidden-item-tax_id]' ).val( exists( item.buchungsziel )? item.buchungsziel.tax_id  : 0 );
    row.find( '[class=od-hidden-item-tax_chart_id]' ).val( exists( item.buchungsziel )? item.buchungsziel.tax_chart_id  : 0 );
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

    return true;
}

function crmNewOrderAndInsertPos( itemPosition, itemType, item ){
    let dbData = { }

    let action;
    switch( crmOrderType ){
        case crmOrderTypeEnum.Order:
            dbData['customer_id'] = $( '#od-customer-id' ).val();
            dbData['c_id'] = $( '#od-lxcars-c_id' ).val();
            action = 'insertNewOrder';
            break;
        case crmOrderTypeEnum.Offer:
            dbData['customer_id'] = $( '#od-customer-id' ).val();
            if( '' != $( '#od-lxcars-c_id' ).val() ) dbData['c_id'] = $( '#od-lxcars-c_id' ).val();
            action = 'insertNewOffer';
            break;
        case crmOrderTypeEnum.Delivery:
            action = 'insertNewDelivery';
            break;
        case crmOrderTypeEnum.Invoice:
            action = 'insertNewInvoice';
            break;
    }

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: action, data: dbData },
        success: function( data ){
            switch( crmOrderType ){
                case crmOrderTypeEnum.Order:
                    $( '#od-oe-id' ).val( data.id );
                    $( '#od-oe-ordnumber' ).text( data.ordnumber );
                    break;
                case crmOrderTypeEnum.Offer:
                    $( '#od-off-id' ).val( data.id );
                    $( '#od-off-quonumber' ).text( data.quonumber );
                    $( '#od-off-itime' ).text( kivi.format_date( new Date( data.itime ) ) );
                    $( '#od-off-mtime' ).text( $( '#od-off-itime' ).text() );
                    break;
                case crmOrderTypeEnum.Delivery:
                    break;
                case crmOrderTypeEnum.Invoice:
                    $( '#od-inv-id' ).val( data.id );
                    break;
            }
            crmInsertOrderPos( itemPosition, itemType, item );
            if( !isEmpty( $( '#od-oe-id' ).val() ) ){
                $( '#od-ui-btn-printer1' ).show();
                $( '#od-ui-btn-printer2' ).show();
                $( '#od-ui-btn-pdf' ).show();
                $( '#od-ui-btn-coparts' ).show();
            }
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmNewOrderAndInsertPos()', xhr.responseText );
        }
    });
}

function crmInsertOrderPos( itemPosition, itemType, item, modified = false ){
    let pos = {};
    pos['record'] = {};
    let crmInsertOrderPos = 'genericSingleInsert';

    switch( crmOrderType ){
        case crmOrderTypeEnum.Order:
            let dbTable = '';
            if( 'P' === itemType  ) dbTable = 'orderitems';
            if( 'S' === itemType  ) dbTable = 'orderitems';
            if( 'I' === itemType  ) dbTable = 'instructions';

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
            break;
        case crmOrderTypeEnum.Offer:
            if( isEmpty( $( '#od-off-id' ).val() ) ){
                crmNewOrderAndInsertPos( itemPosition, itemType, item );
                return;
            }
            pos['record']['orderitems'] = {};
            pos['record']['orderitems']['trans_id'] = $( '#od-off-id' ).val();
            pos['record']['orderitems']['position'] = itemPosition;
            pos['record']['orderitems']['parts_id'] = item.id;
            pos['record']['orderitems']['qty'] = ( item.qty === null )? 0 : item.qty;
            pos['record']['orderitems']['unit'] = item.unit;
            pos['record']['orderitems']['sellprice'] = item.sellprice;
            pos['record']['orderitems']['description'] = item.description;
            pos['sequence_name'] = 'orderitemsid';
            break;
        case crmOrderTypeEnum.Delivery:
            return;
        case crmOrderTypeEnum.Invoice:
            pos['record']['invoice'] = {};
            pos['record']['invoice']['trans_id'] = $( '#od-inv-id' ).val();
            pos['record']['invoice']['position'] = itemPosition;
            pos['record']['invoice']['parts_id'] = item.id;
            pos['record']['invoice']['qty'] = ( item.qty === null )? 0 : item.qty;
            pos['record']['invoice']['unit'] = item.unit;
            pos['record']['invoice']['sellprice'] = item.sellprice;
            pos['record']['invoice']['description'] = item.description;
            pos['sequence_name'] = 'invoiceid';
            break;
    }

    if( modified ){
        crmSaveOrder();
        return;
    }


    if( crmOrderTypeEnum.Invoice == crmOrderType || crmOrderTypeEnum.Order == crmOrderType && !item.instruction && item.description.includes( 'Hauptuntersuchung' ) || item.description.includes( 'HU/AU' ) ){
        const c_id = $( '#od-lxcars-c_id' ).val();
        if( exists( c_id ) && '' != c_id ){
            pos['record']['huau'] = c_id;
            crmInsertOrderPos = 'insertOrderPosHuAu';
        }
    }

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: crmInsertOrderPos, data: pos },
        success: function( data ){
            $( '#od-empty-item-id' ).attr( 'id', data.id );
            crmSaveOrder();
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmInsertOrderPos()', xhr.responseText );
        }
    });
 }

crmDeleteOrderPos = function( e ){
    var row = $( e ).parent().parent();

    let pos = {};
    let dbTable = '';
    let itemType = $( row ).find( '[class=od-item-type]' ).val();
    switch( crmOrderType ){
        case crmOrderTypeEnum.Order:
            if( 'P' === itemType  ) dbTable = 'orderitems';
            if( 'S' === itemType  ) dbTable = 'orderitems';
            if( 'I' === itemType  ) dbTable = 'instructions';
            break;
        case crmOrderTypeEnum.Offer:
            dbTable = 'orderitems';
            break;
        case crmOrderTypeEnum.Delivery:
            return
            break;
        case crmOrderTypeEnum.Invoice:
            dbTable = 'invoice';
            break;
    }

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
    let dbUpdateData = { }
    let dbPSItemsTable = '';

    switch( crmOrderType ){
        case crmOrderTypeEnum.Order:
            if( isEmpty( $( '#od-oe-id' ).val() ) ) return;
            dbPSItemsTable = 'orderitems';
            crmSaveOrderType( dbUpdateData );
            break;
        case crmOrderTypeEnum.Offer:
            if( isEmpty( $( '#od-off-id' ).val() ) ) return;
            dbPSItemsTable = 'orderitems';
            crmSaveOfferType( dbUpdateData );
            break;
        case crmOrderTypeEnum.Delivery:
            return
            break;
        case crmOrderTypeEnum.Invoice:
            if( isEmpty( $( '#od-inv-id' ).val() ) ) return;
            dbPSItemsTable = 'invoice';
            crmSaveInvoiceType( dbUpdateData );
            break;
    }

    dbUpdateData[dbPSItemsTable] = [];
    if( crmOrderTypeEnum.Invoice == crmOrderType ){
        //Rechnung buchen, Konten aktualisieren, Zahlungseingänge buchen
        dbUpdateData['buchungsziel'] = { };
        dbUpdateData['buchungsziel']['id'] = $( '#od-inv-id' ).val();
        dbUpdateData['buchungsziel']['charts'] = { };
    }

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
        if( crmOrderTypeEnum.Invoice == crmOrderType ) dataRow['fxsellprice'] = dataRow.sellprice;
        dataRow.discount = kivi.parse_amount( dataRow.discount ) / 100;
        dataRow.marge_total = kivi.parse_amount( dataRow.marge_total );
        if( exists( pos.id ) && pos.id != 'od-empty-item-id' ){
            if( 'P' === itemType  ){
                dataRow['WHERE'] = {};
                dataRow['WHERE'] = 'id = ' +  pos.id;
                dbUpdateData[dbPSItemsTable].push( dataRow );
            }
            if( 'S' === itemType  ){
                dataRow['WHERE'] = {};
                dataRow['WHERE'] = 'id = ' + pos.id;
                dbUpdateData[dbPSItemsTable].push( dataRow );
            }
            if( 'I' === itemType  ){
                dataRow['WHERE'] = {};
                dataRow['WHERE'] = 'id = ' + pos.id;
                dbUpdateData['instructions'].push( dataRow );
            }
            if( crmOrderTypeEnum.Invoice == crmOrderType ){
                //Rechnung buchen
                //Aufsummieren von marge_total pro income_chart_id, wenn die tax_id nicht 0 ist werden die Steuerbeträge auf dem entsprechenden chart (tax_chart_id)
                //gebucht werden, die Gesamtsumme wird auf dem chart für Forderungen gebucht (#od-ar-id)
                const income_chart_id = $( pos ).find( '[class=od-hidden-item-income_chart_id]' ).val();
                const marge_total = kivi.parse_amount( $( pos ).find( '[class=od-item-marge_total]' ).val() );
                const tax_id = $( pos ).find( '[class=od-hidden-item-tax_id]' ).val();
                if( !exists( dbUpdateData['buchungsziel']['charts'][income_chart_id] ) ) dbUpdateData['buchungsziel']['charts'][income_chart_id] = [ { 'amount': 0, 'memo': '', 'source': '', 'tax_id': tax_id } ];
                dbUpdateData['buchungsziel']['charts'][income_chart_id][0]['amount'] += marge_total;
                if( tax_id > 0 ){
                    const tax_chart_id = $( pos ).find( '[class=od-hidden-item-tax_chart_id]' ).val();
                    if( 'null' != tax_chart_id ){
                        if( !exists( dbUpdateData['buchungsziel']['charts'][tax_chart_id] ) ) dbUpdateData['buchungsziel']['charts'][tax_chart_id] = [ { 'amount': 0, 'memo': '', 'source': '', 'tax_id': 0 } ];
                        dbUpdateData['buchungsziel']['charts'][tax_chart_id][0]['amount'] += kivi.parse_amount( '' + ( marge_total * ( parseFloat( $( pos ).find( '[class=od-hidden-item-rate]' )[0].value ) + 1 ) - marge_total ) );
                    }
                }
          }
        }
    });

    //Rechnung buchen, Zahlungseingänge buchen
    if( crmOrderTypeEnum.Invoice == crmOrderType ){
        const chart_ar_id = $( '#od-ar-id' ).val();
        let deficit = kivi.parse_amount( $( '#od-amount' ).val() );
        dbUpdateData['buchungsziel']['charts'][chart_ar_id] = [ { 'amount': '-' + kivi.parse_amount( $( '#od-amount' ).val() ), 'memo': '', 'source': '', 'tax_id': 0  } ];
        $( '#od-inv-payment-list > tbody' ).find( '[class=od_inv_paid-row]' ).each( function( key, pos ){
            if( $( pos ).find( '[class=od_inv_paid-flag]' ).val() == 'gebucht' ){
                const chart_id = $( pos ).find( '[class=od_inv_paid-chart_id]' ).val();
                const amount = kivi.parse_amount( $( pos ).find( '[class=od_inv_paid-amount]' ).val() );
                if( !exists( dbUpdateData['buchungsziel']['charts'][chart_id] ) ) dbUpdateData['buchungsziel']['charts'][chart_id] = [];
                dbUpdateData['buchungsziel']['charts'][chart_id].push( { 'amount': '-' + amount, 'memo': '', 'source': '', 'tax_id': 0 } );
                dbUpdateData['buchungsziel']['charts'][chart_ar_id].push( { 'amount': amount, 'memo': '', 'source': '', 'tax_id': 0  } );
                deficit = kivi.round_amount( deficit - amount, 2 );
            }
        });

        if( deficit > 0 ){
            let row_item = $( '.od_inv_paid-row' ).last();
            if( 'gebucht' == row_item.find( '[class=od_inv_paid-flag]' ).val() ) row_item = $( '.od_inv_paid-row' ).last().clone();
            row_item.find( '[class=od_inv_paid-flag]' ).val( 'ungebucht' );
            row_item.find( '.od_inv_paid-date' ).val( kivi.format_date( new Date() ) );
            row_item.find( '[class=od_inv_paid-amount]' ).val( kivi.format_amount( deficit ) );
            row_item.css( 'background-color', 'red' );
            row_item.appendTo( '#od-inv-payment-list > tbody' );
        }
    }

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'saveOrder', data: dbUpdateData },
        success: function( data ){
            if( $( '#od-inv-payment' ).is(':visible' ) ){
            }
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmSaveOrder()', xhr.responseText );
        }
    });
}

function crmSaveOfferType( dbUpdateData ){
    dbUpdateData['oe'] = {};
    dbUpdateData['customer'] = {};

    $( '.od-off-common :input' ).each( function( key, pos ){
        let columnName = pos.id.split( '-' )[2];
        if( !exists( columnName ) ) return;
         dbUpdateData['oe'][pos.id.split( '-' )[2]] = ( 'checkbox' === pos.type )? $( pos ).prop( 'checked' ) : $( pos ).val();
    });

    dbUpdateData['oe']['intnotes'] = $( '#od-oe-intnotes' ).val();
    dbUpdateData['oe']['amount'] = kivi.parse_amount( $( '#od-amount' ).val() );
    dbUpdateData['oe']['netamount'] = kivi.parse_amount( $( '#od-netamount' ).val() );
    dbUpdateData['oe']['WHERE'] = {};
    dbUpdateData['oe']['WHERE'] = 'id = ' + $( '#od-off-id' ).val();

    dbUpdateData['customer']['notes'] = $( '#od-customer-notes' ).val();
    dbUpdateData['customer']['WHERE'] = {};
    dbUpdateData['customer']['WHERE']= 'id = ' + $( '#od-customer-id' ).val();

}

function crmSaveOrderType( dbUpdateData ){
    if( '' == $( '#od-lxcars-c_id' ).val() ){
        alert( kivi.t8( 'There is no car assigned!' ) );
        return;
    }

    dbUpdateData['oe'] = {};
    dbUpdateData['customer'] = {};
    dbUpdateData['lxc_cars'] = {};
    dbUpdateData['instructions'] = [];

    $( '.od-oe-common :input' ).each( function( key, pos ){
        let columnName = pos.id.split( '-' )[2];
        if( !exists( columnName ) ) return;
         dbUpdateData['oe'][pos.id.split( '-' )[2]] = ( 'checkbox' === pos.type )? $( pos ).prop( 'checked' ) : $( pos ).val();
    });

    dbUpdateData['customer']['notes'] = $( '#od-customer-notes' ).val();
    dbUpdateData['lxc_cars']['c_text'] = $( '#od-lxcars-c_text' ).val();
    dbUpdateData['oe']['intnotes'] = $( '#od-oe-intnotes' ).val();
    dbUpdateData['oe']['notes'] = $( '#od-oe-notes' ).val();
    dbUpdateData['oe']['shippingpoint'] = $( '#od_lxcars_c_ln' ).val();// für Kilometerstand in Druckvorlage od-lxcars-c_ln
    dbUpdateData['oe']['shipvia'] = $( '#od-oe-km_stnd' ).val();// für Kennzeichen in Druckvorlage
    dbUpdateData['oe']['amount'] = kivi.parse_amount( $( '#od-amount' ).val() );
    dbUpdateData['oe']['netamount'] = kivi.parse_amount( $( '#od-netamount' ).val() );

    dbUpdateData['oe']['WHERE'] = {};
    dbUpdateData['oe']['WHERE'] = 'id = ' + $( '#od-oe-id' ).val();

    dbUpdateData['customer']['WHERE'] = {};
    dbUpdateData['customer']['WHERE']= 'id = ' + $( '#od-customer-id' ).val();

    dbUpdateData['lxc_cars']['WHERE'] = {};
    dbUpdateData['lxc_cars']['WHERE'] = 'c_id = ' + $( '#od-lxcars-c_id' ).val();
}

function crmSaveInvoiceType( dbUpdateData ){
    dbUpdateData['ar'] = {};
    dbUpdateData['customer'] = {};
    dbUpdateData['lxc_cars'] = {};

    $( '.od-inv-common :input' ).each( function( key, pos ){
        let columnName = pos.id.split( '-' )[2];
        if( !exists( columnName ) ) return;
        dbUpdateData['ar'][pos.id.split( '-' )[2]] = ( 'checkbox' === pos.type )? $( pos ).prop( 'checked' ) : $( pos ).val();
    });

    dbUpdateData['customer']['notes'] = $( '#od-customer-notes' ).val();
    dbUpdateData['lxc_cars']['c_text'] = $( '#od-lxcars-c_text' ).val();
    dbUpdateData['ar']['intnotes'] = $( '#od-oe-intnotes' ).val();
    dbUpdateData['ar']['shippingpoint'] = $( '#od-inv-shippingpoint' ).val();// für Kilometerstand in Druckvorlage od-lxcars-c_ln
    dbUpdateData['ar']['shipvia'] = $( '#od-inv-shipvia' ).val();// für Kennzeichen in Druckvorlage
    dbUpdateData['ar']['amount'] = kivi.parse_amount( $( '#od-amount' ).val() );
    dbUpdateData['ar']['netamount'] = kivi.parse_amount( $( '#od-netamount' ).val() );
    dbUpdateData['ar']['employee_id'] = kivi.parse_amount( $( '#od-inv-employee_id' ).val() );

    dbUpdateData['ar']['WHERE'] = {};
    dbUpdateData['ar']['WHERE'] = 'id = ' + $( '#od-inv-id' ).val();

    dbUpdateData['customer']['WHERE'] = {};
    dbUpdateData['customer']['WHERE']= 'id = ' + $( '#od-customer-id' ).val();

    dbUpdateData['lxc_cars']['WHERE'] = {};
    dbUpdateData['lxc_cars']['WHERE'] = 'c_id = ' + $( '#od-lxcars-c_id' ).val();
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

function crmNewOffer(){
    const src = $( '#crm-cvpa-src' ).val();
    if( 'C' != src ){
        alert( "This operation is not yet possible for vendors or cars!" );
        return;
    }
    const id = $( '#crm-cvpa-id' ).val();
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getDataForNewOffer', data: { 'src': src, 'id': id } },
        success: function( crmData ){
            crmEditOrderDlg( crmData, crmOrderTypeEnum.Offer );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Response Error in: ' ) + 'crmNewOffer', xhr.responseText );
        }
    });
}

// changeCustomer:
$( '#od_off_customer_name, #od_customer_name, #od_inv_customer_name' ).autocomplete({
    delay: crmAcDelay,
    source: "crm/ajax/crm.app.php?action=searchCustomer",
    select: function( e, ui ) {
        let tabName = '';
        let id = '';
        switch( crmOrderType ){
            case crmOrderTypeEnum.Order:
                tabName = "oe";
                id = 'od-oe-id';
                break;
            case crmOrderTypeEnum.Offer:
                tabName = "oe";
                id = 'od-off-id';
                break;
            case crmOrderTypeEnum.Delivery:
                break;
            case crmOrderTypeEnum.Invoice:
                tabName = "ar";
                id = 'od-inv-id';
                break;
        }
        dbUpdateData = {};
        dbUpdateData[tabName] = {};
        dbUpdateData[tabName]['customer_id'] = ui.item.id;
        dbUpdateData[tabName]['WHERE'] = {};
        dbUpdateData[tabName]['WHERE'] = 'id = ' + $( '#' + id ).val();

        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'genericUpdateEx', data: dbUpdateData },
            success: function( data ){
                $( '#od-customer-id' ).val( ui.item.id );
                crmRefreshAppView( 'C', ui.item.id );
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'changeCustomer', xhr.responseText );
            }
        });
    }
});

// changeCar:
$( '#od_lxcars_c_ln, #od_inv_shippingpoint' ).autocomplete({
        delay: crmAcDelay,
        source: function(request, response) {
            $.get('crm/ajax/crm.app.php?action=searchCarLicense', { term: request.term, customer: $( '#od-customer-id' ).val()  }, function(data) {
                response(data);
            });
        },
        select: function( e, ui ) {
        let tabName = '';
        let id = '';
        let tabCol = '';
        let c_ln = '';
        switch( crmOrderType ){
            case crmOrderTypeEnum.Order:
                tabName = "oe";
                id = 'od-oe-id';
                tabCol = 'c_id';
                c_ln = ui.item.id
                break;
            case crmOrderTypeEnum.Offer:
                tabName = "oe";
                id = 'od-off-id';
                tabCol = 'c_id';
                c_ln = ui.item.id
                break;
           case crmOrderTypeEnum.Invoice:
                tabName = "ar";
                id = 'od-inv-id';
                tabCol = 'shippingpoint';
                c_ln = ui.item.value;
                break;
        }
        dbUpdateData = {};
        dbUpdateData[tabName] = {};
        dbUpdateData[tabName][tabCol] = c_ln;
        dbUpdateData[tabName]['WHERE'] = {};
        dbUpdateData[tabName]['WHERE'] = 'id = ' + $( '#' + id ).val();

        $.ajax({
            url: 'crm/ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'genericUpdateEx', data: dbUpdateData },
            success: function( data ){
                $( '#od-lxcars-c_id' ).val( ui.item.id );
                $( '#od-inv-shippingpoint' ).val( ui.item.value );
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'changeCar', xhr.responseText );
            }
        });
    }
});

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

$( "#od-oe-finish_time" ).datetimepicker({
    beforeShow: function( input ){
        crmDateTimePickerAddButton( input );
    },
    onChangeMonthYear: function( year, month, inst ){
        crmDateTimePickerAddButton( inst.input );
        crmSaveOrder();
    },
    stepMinute: 5,
    hour: 16,
    hourMin: 8,
    hourMax: 17,
    timeSuffix: kivi.t8( " Uhr" ),
    timeText: kivi.t8(' Time'),
    hourText: 'Stunde',
    closeText: 'Fertig',
    currentText: 'Jetzt'
});

$( "#od_oe_event" ).datetimepicker({
    beforeShow: function( input ){
        crmDateTimePickerAddButton( input );
    },
    onChangeMonthYear: function( year, month, inst ){
        crmDateTimePickerAddButton( inst.input );
        crmSaveOrder();
    },
    stepMinute: 5,
    hour: 16,
    hourMin: 8,
    hourMax: 17,
    timeText: kivi.t8(' Time'),
    hourText: 'Stunde',
    closeText: 'Fertig',
    currentText: 'Jetzt'
});

function crmEditOrderAddEvent(){
    dbUpdateData = {};//jsonobj für die Datenbankupdate (genericUpdateEx)
    dbUpdateData['events'] = {};
    const start = moment($( "#od_oe_event" ).val(), 'DD.MM.YYYY HH:mm ');
    const end = start.clone().add( 2, 'hour' ); //beachte end    = start;    funktioniert nicht denn bei end.add( 2, 'hour' ); wird    auch start um 2 Stunden    erhöht

    dbUpdateData['events']['duration'] = '[' + start.format('YYYY-MM-DD HH:mm') + ',' + end.format('YYYY-MM-DD HH:mm:ss') + ')'; //'HH:mm:ss' ist wichtig sonst wird die Zeit nicht im 24h-Format angezeigt
    alert( dbUpdateData['events']['duration'] );
    if( $( "#od-customer-id" ).val() != '' ) dbUpdateData['events']['cvp_id'] = $( "#od-customer-id" ).val();
    if( $( "#crm-edit-event-cvp-type" ).val() != '' ) dbUpdateData['events']['cvp_type'] = 'C';
    if( $( "#od_customer_name" ).val() != '' ) dbUpdateData['events']['cvp_name'] = $( "#od_customer_name" ).val();
    if( $( "#od-lxcars-c_id" ).val() != '' ) dbUpdateData['events']['car_id'] = $( "#od-lxcars-c_id" ).val();
    if( $( "#od-oe-id" ).val() != '' ) dbUpdateData['events']['order_id'] = $( "#od-oe-id" ).val();
    dbUpdateData['events']['title'] = $( "#crm-edit-event-title" ).val();
    dbUpdateData['events']['description'] = $( "#crm-edit-event-description" ).val();
    dbUpdateData['events']['\"allDay\"'] = $( "#crm-edit-event-full-time" ).is( ":checked" );
    dbUpdateData['events']['uid'] = $( '#od-oe-employee_id' ).val();

    console.info( 'dbUpdateData', dbUpdateData );

}

function crmConfirmInsertInvoiceFromOrder(){
    if( '' == $( '#od-oe-id' ).val() ){
        alert( kivi.t8( 'Please insert a part!' ) );
        return;
    }

    $( '#crm-confirm-order-to-invoice-dialog' ).dialog({
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
        modal: true,
        title: kivi.t8( 'New invoice' ),
        position: { my: "top", at: "top+250" },
        open: function(){
            $( this ).css( 'maxWidth', window.innerWidth );
        }
    }).dialog( 'open' ).resize();
}

function crmInsertInvoiceFromOrder(){
    let data = {};
    data['ordnumber'] = $( '#od-oe-ordnumber' ).text();
    data['employee_id'] = $( '#od-inv-employee_id' ).val();
    data['oe_id'] = $( '#od-oe-id' ).val();

    $( '#crm-confirm-order-to-invoice-dialog' ).dialog( "close" );

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'insertInvoiceFromOrder', data: data },
        success: function( crmData ){
            if( exists( crmData['flag'] ) ){
                alert( kivi.t8( 'The invoice already exists and will be displayed.' ) );
            }
            crmEditOrderDlg( crmData, crmOrderTypeEnum.Invoice );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmInsertInvoiceFromOrder()', xhr.responseText );
        }
    });
}

function crmConfirmInsertOfferFromOrder(){
    if( '' == $( '#od-oe-id' ).val() ){
        alert( kivi.t8( 'Please insert a part!' ) );
        return;
    }

    $( '#crm-confirm-order-to-offer-dialog' ).dialog({
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
        modal: true,
        title: kivi.t8( 'New invoice' ),
        position: { my: "top", at: "top+250" },
        open: function(){
            $( this ).css( 'maxWidth', window.innerWidth );
        }
    }).dialog( 'open' ).resize();
}

function crmInsertOfferFromOrder(){
    let data = {};
    data['ordnumber'] = $( '#od-oe-ordnumber' ).text();
    data['employee_id'] = $( '#od-inv-employee_id' ).val();
    data['oe_id'] = $( '#od-oe-id' ).val();

    $( '#crm-confirm-order-to-offer-dialog' ).dialog( 'close' );

    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'insertOfferFromOrder', data: data },
        success: function( crmData ){
            crmEditOrderDlg( crmData, crmOrderTypeEnum.Offer );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmInsertOfferFromOrder()', xhr.responseText );
        }
    });
}

function crmEmailOrder( ){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'getGenericTranslations', data: { id: $( '#od-customer-id' ).val() } },
        success: function( crmData ){
            crmInitFormEx( orderEmailFormModel, '#order-email-form' );
            $( '#order_email-recipient' ).val( $( '#crm_inv_contact_email' ).text() );
            //Todo: Internationalisierung
            let subject = '';
            let docNumber = 0;
            let presetText = '';
            if( crmOrderTypeEnum.Offer == crmOrderType ){
                subject = 'Angebot';
                docNumber = $( '#od-off-quonumber' ).text();
                presetText = crmData['preset_text_sales_quotation'];
            }
            else if( crmOrderTypeEnum.Invoice == crmOrderType ){
                subject = 'Rechnung';
                docNumber = $( '#od-inv-invnumber' ).text();
                presetText = crmData['preset_text_invoice'];
            }
            $( '#order_email-subject' ).val( subject + ' ' + docNumber );

            // Ev. natürliche Person prüfen??
            let salutation = crmData['salutation_general'];
            if( 'Frau' == crmData['greeting'] ) salutation = crmData['salutation_female'];
            else if( 'Herr' == crmData['greeting'] ) salutation = crmData['salutation_male']; //ToDo: else Firma??

            $( '#order_email-message' ).val( salutation + ' ' + $( '#crm-contact-name' ).html() + crmData['salutation_punctuation_mark'] + presetText );
            tinymce.init({
                selector: '#order_email-message',
                menubar: '',
                language: 'de'
            });
            $( '#order_email-attachment' ).val( subject + '_' + docNumber + '.pdf' );

            $( '#crm-order-email-dialog' ).dialog({
                autoOpen: false,
                resizable: true,
                width: 'auto',
                height: 'auto',
                modal: true,
                title: kivi.t8( 'Send e-mail' ),
                position: { my: "top", at: "top+250" },
                open: function(){
                    $( this ).css( 'maxWidth', window.innerWidth );
                }
            }).dialog( 'open' ).resize();
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'crmEmailOrder()', xhr.responseText );
        }
    });
}

const crmOrderPrintTargetEnum = { Printer: 0, Email: 1, Screen: 2 };

function crmPrintOrder( target ){
    let data = {};
    if( crmOrderTypeEnum.Invoice == crmOrderType ){
        data['id'] = ''+  $( '#od-inv-id' ).val();
        data['type'] = 'invoice';
    }
    else if( crmOrderTypeEnum.Offer == crmOrderType ){
        data['id'] = ''+  $( '#od-off-id' ).val();
        data['type'] = 'sales_quotation';
    }
    data['vc'] = 'customer';
    data['taxaccounts'] = '1776 ';
    data['show_details'] = '0';
    data['1776_rate'] = '0.19000';
    data['1776_description'] = 'Umsatzsteuer';
    data['1776_taxnumber'] = '1776';
    data['1776_tax_id'] = '777';
    if( crmOrderTypeEnum.Invoice == crmOrderType ) data['follow_up_trans_id_1'] = '' +  $( '#od-inv-id' ).val();
    else if( crmOrderTypeEnum.Offer == crmOrderType ) data['follow_up_trans_id_1'] = '' +  $( '#od-off-id' ).val();
    data['follow_up_trans_type_1'] = 'sales_invoice';
    if( crmOrderTypeEnum.Invoice == crmOrderType ) data['follow_up_trans_type_1'] = 'sales_invoice';
    else if( crmOrderTypeEnum.Offer == crmOrderType ) data['follow_up_trans_type_1'] = 'sales_quotation';
    data['already_printed_flag'] = '0';
    data['has_qr_reference'] = '0';
    data['customer_id'] = '' + $( '#od-customer-id' ).val();
    data['previous_customer_id'] = '' + $( '#od-customer-id' ).val();
    data['customer_pricegroup_id'] = '';
    if( crmOrderTypeEnum.Invoice == crmOrderType ) data['taxzone_id'] = '5';
    else if( crmOrderTypeEnum.Offer == crmOrderType ) data['taxzone_id'] = '4';
    data['language_id'] = '';
    data['department_id'] = '';
    data['currency'] = 'EUR';
    data['shippingpoint'] = '' + $( '#od-inv-shippingpoint' ).val();
    data['shipvia'] = '' + $( '#od-inv-shipvia' ).val();
    data['transaction_description'] = '';
    if( crmOrderTypeEnum.Invoice == crmOrderType ){
        data['employee_id'] = '' + $( '#od-inv-employee_id' ).val();
        data['salesman_id'] = '' + $( '#od-inv-employee_id' ).val();
    }
    else if( crmOrderTypeEnum.Offer == crmOrderType ){
        data['employee_id'] = '' + $( '#od-off-employee_id' ).val();
        data['salesman_id'] = '' + $( '#od-off-employee_id' ).val();
    }
     if( crmOrderTypeEnum.Invoice == crmOrderType ){
        data['invnumber'] = '' + $( '#od-inv-invnumber' ).text();
        data['invdate'] = ''+ $( '#od-inv-itime' ).text();
        data['duedate'] = ''+ $( '#od-inv-itime' ).text();
        data['ordnumber'] = '' + $( '#od-inv-ordnumber' ).text();
        data['orddate'] = '' + $( '#od-inv-itime' ).text();
    }
    else if( crmOrderTypeEnum.Offer == crmOrderType ){
        data['quonumber'] = '' + $( '#od-off-quonumber' ).text()
        data['transdate'] = '' + $( '#od-off-itime' ).text()
    }
    let runningnumber = 0;
    $( '#edit-order-table > tbody > tr').each( function( key, pos ){
        runningnumber = $( pos ).find( '[class=od-item-position]' ).text();
        data['runningnumber_' + runningnumber] = '' + runningnumber;
        data['partnumber_' + runningnumber] = '' + $( pos ).find( '[class=od-hidden-item-partnumber]' ).text();
        data['description_' + runningnumber] = '' + $( pos ).find( '[name=od-item-description]' ).val();
        data['qty_' + runningnumber] = '' + $( pos ).find( '[class=od-item-qty]' ).val();
        data['unit_' + runningnumber] = '' + $( pos ).find( '[class=od-item-unit]' ).val();
        data['sellprice_' + runningnumber] = '' + $( pos ).find( '[class=od-item-sellprice]' ).val();
        data['discount_' + runningnumber] = '' + kivi.parse_amount( $( pos ).find( '[class=od-item-discount]' ).val() );
        data['unit_old_' + runningnumber] = '' + $( pos ).find( '[class=od-item-unit]' ).val();
        data['id_' + runningnumber] = '' + $( pos ).find( '[class=od-item-parts_id]' ).val();
        data['bin_' + runningnumber] = '';
        if( crmOrderTypeEnum.Offer == crmOrderType ){
            data['active_price_source_' + runningnumber] = '';
            data['active_discount_source_' + runningnumber] = '';
        }
        data['part_type_' + runningnumber] = '' + ( ( 'P' == $( pos ).find( '[class=od-item-type]' ).val() )? 'part' : 'service' );
        if( $( pos ).find( '[class=od-hidden-item-rate]' ).val() > 0 ) data['taxaccounts_' + runningnumber] = '1776';
        data['marge_absolut_' + runningnumber] = '' + $( pos ).find( '[class=od-item-marge_total]' ).val();
        data['marge_percent_' + runningnumber] = '100,00';
        data['marge_price_factor_' + runningnumber] = '1.00000';

        if( crmOrderTypeEnum.Invoice == crmOrderType ) data['invoice_id_' + runningnumber] = '' + pos.id;
        else if( crmOrderTypeEnum.Offer == crmOrderType ) data['orderitems_id_' + runningnumber] = '' + pos.id;
        data['lastcost_' + runningnumber] = '0,00';
    });
    data['rowcount'] = runningnumber;
    if( crmOrderTypeEnum.Invoice == crmOrderType ) data['formname'] = 'invoice';
    else if( crmOrderTypeEnum.Offer == crmOrderType ) data['formname'] = 'sales_quotation';
    data['format'] = 'pdf';
    if( crmOrderPrintTargetEnum.Screen == target ){
            data['media'] = 'screen';
            data['printer_id'] = '';
    }
    else{
            data['media'] = 'printer';
            if( crmOrderPrintTargetEnum.Email != target ) data['printer_id'] = '' + ( ( crmOrderTypeEnum.Offer == crmOrderType )? $( '#od-off-current-printer' ).attr( 'value' ) : $( '#od-inv-current-printer' ).attr( 'value' ) );
    }
    data['copies'] = '1';
    data['action'] = 'print';

    let formId = '';
    if( crmOrderTypeEnum.Invoice == crmOrderType ) formId = 'od-inv-print-form';
    else if( crmOrderTypeEnum.Offer == crmOrderType ) formId = 'od-off-print-form';

    $( '#' + formId ).html( '' );
    $.each(data, function( key, value ){
        $( '#' + formId ).append('<input type="hidden" name="' + key + '" value="' + value + '"></input>');
    });

    if( crmOrderPrintTargetEnum.Screen == target ){
        $( '#' + formId ).submit();
    }
    else{
        if( crmOrderPrintTargetEnum.Email == target ){
            data['action'] = 'send_sales_purchase_email';
            data['email_form.to'] = $( '#order_email-recipient' ).val();
            data['email_form.subject'] = $( '#order_email-subject' ).val();
            data['email_form.attachment_filename'] = $( '#order_email-attachment' ).val();
            data['email_form.message'] = $( '#order_email-message' ).val();
        }

        let url = '';
        if( crmOrderTypeEnum.Invoice == crmOrderType ) url = 'is.pl';
        else if( crmOrderTypeEnum.Offer == crmOrderType ) url = 'oe.pl';
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function( data ){
                if( exists( data ) && exists( data.error ) ){
                    $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Print or send e-mail' ), kivi.t8( 'Error:' ) + ' ' + kivi.t8( 'The following error is occurred: ' ) + data.error );
                }
            },
            error: function( xhr, status, error ){
                $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'printOrder()', xhr.responseText );
            }
        });
    }

    if( crmOrderTypeEnum.Invoice == crmOrderType ){
        $.ajax({
          url: 'crm/ajax/ecTerminalData.php',
          type: "POST",
          data: { 'action': 'getTerminalCustomerData', 'data': $( '#crm-cvpa-id' ).val() },
          success: function( res ){
            var ip = res['ec_terminal_ip-adress'];
            var port = res['ec_terminal_port'];
            var passwd = res['ec_terminal_passwd'];
            var name = res['name'];
            $.ajax({
              url: 'crm/ajax/ecTerminal.py',
              type: "post",
              timeout: 100,
              data: { 'action':'pay', 'ip': ip,'port': port, 'passwd': passwd, 'amount': $( '#od-amount' ).val().replace( '.', '' ).replace( ',', '' ), 'name': name }
            });
          }
        })
    }
}

const crmOrderTypeEnum = { Order: 0, Invoice: 1, Offer: 2, Delivery: 3 };
var crmOrderType = crmOrderTypeEnum.Order;
var coparts = {};

function crmEditOrderDlg( crmData,  type = crmOrderTypeEnum.Order ){
    crmOrderType = type;

    crmOrderItemLists = { };
    if( exists( crmData.workers ) ) crmOrderItemLists['workers'] = crmData.workers;
    $( '#edit-order-table > tbody' ).html( '' );
    if( exists( crmData.order ) && exists( crmData.order.orderitems ) ){
        for( let dataRow of crmData.order.orderitems ){
           crmAddOrderItem( dataRow );
        }
    }
    else if( exists( crmData.offer ) && exists( crmData.offer.orderitems ) ){
        for( let dataRow of crmData.offer.orderitems ){
           crmAddOrderItem( dataRow );
        }
    }
    else if( exists( crmData.bill ) && exists( crmData.bill.invoice ) ){
        for( let dataRow of crmData.bill.invoice ){
           crmAddOrderItem( dataRow );
        }
    }

    crmAddOrderItem( { } );
    $( '#edit-order-table > tbody' ).sortable({
        items: '> tr:not(.od-item-pin)',
        cancel: '.od-item-pin, .od-ui-del, input, select, button',
        update: function(){
            crmCalcOrderPos();
            crmSaveOrder();
        }
    });

    let title;

    $( '#od-customer-id' ).val( '' );
    $( '#od-lxcars-c_id' ).val( '' );
    $( '#od-oe-id' ).val( '' );
    $( '#od-off-id' ).val( '' );
    $( '#od-inv-id' ).val( '' );

    if( crmOrderTypeEnum.Order == crmOrderType ){
        $( '#od-inv-menus' ).hide();
        $( '#od-off-menus' ).hide();
        $( '#od-inv-common-table' ).hide();
        $( '#od-oe-workflow' ).show();
        $( '#od-oe-common-table' ).show();
        $( '#od-off-common-table' ).hide();
        $( '#od-listheading-workers' ).show();
        $( '#od-listheading-status' ).show();
        $( '#od-lxcars-c_text-label' ).show();
        $( '#od-lxcars-c_text' ).show();
        $( '#od-inv-payment' ).hide();
        $( '.od-perform' ).show();

        if( exists( crmData.order ) ){
            title = kivi.t8( 'Edit order' );
            $( '#od-customer-id' ).val( crmData.order.common.customer_id );
            $( '#od-lxcars-c_id' ).val( crmData.order.common.c_id );
            $( '#od-oe-id' ).val( crmData.order.common.id );
            $( '#od_customer_name' ).val( crmData.order.common.customer_name );
            $( '#od-oe-ordnumber' ).html( crmData.order.common.ordnumber );
            $( '#od-oe-finish_time' ).val( crmData.order.common.finish_time );
            $( '#od-oe-km_stnd' ).val( crmData.order.common.km_stnd );
            $( '#od-oe-employee_name' ).html( crmData.order.common.employee_name );
            $( '#od-oe-employee_id' ).val( crmData.order.common.employee_id );
            $( '#od_lxcars_c_ln' ).val( crmData.order.common.c_ln );
            $( '#od-oe-mtime' ).html( kivi.format_date( new Date( crmData.order.common.mtime ) ) );
            $( '#od-oe-internalorder' ).prop( 'checked', crmData.order.common.internalorder );
            $( '#od-oe-itime' ).html( kivi.format_date( new Date( crmData.order.common.itime ) ) );
            $( '#od-oe-car_status' ).val( crmData.order.common.car_status );
            $( '#od-oe-status' ).val( crmData.order.common.status );
            $( '#od-lxcars-c_text' ).val( crmData.order.common.int_car_notes );
            $( '#od-customer-notes' ).val( crmData.order.common.int_cu_notes );
            $( '#od-oe-intnotes' ).val( crmData.order.common.intnotes );
            $( '#od-oe-notes' ).val( crmData.order.common.notes );
            coparts['c_hsn'] = crmData.order.common.c_2;
            coparts['c_tsn'] = crmData.order.common.c_3;
            coparts['c_fin'] = crmData.order.common.c_fin;
            coparts['c_d_de'] = crmData.order.common.c_d_de;
            coparts['c_mkb'] = crmData.order.common.c_mkb;
            coparts['customer_name'] = crmData.order.common.customer_name;
            coparts['customer_street'] = crmData.order.common.customer_street;
            coparts['customer_zipcode'] = crmData.order.common.customer_zipcode;
            coparts['customer_city'] = crmData.order.common.customer_city;
            $( '#show_car_data-c_ln' ).val( crmData.order.common.c_ln );
            $( '#show_car_data-hersteller' ).val( crmData.order.common.hersteller );
            $( '#show_car_data-typ' ).val( crmData.order.common.kba_typ );
            $( '#show_car_data-hsn' ).val( crmData.order.common.c_2 );
            $( '#show_car_data-tsn' ).val( crmData.order.common.c_3 );
            $( '#show_car_data-fin' ).val( crmData.order.common.c_fin );
            $( '#show_car_data-hubraum' ).val( crmData.order.common.hubraum );
            $( '#show_car_data-leistung' ).val( crmData.order.common.leistung );
        }
        else{
            title = kivi.t8( 'New order' );
            $( '#od-customer-id' ).val( crmData.common.customer_id );
            $( '#od-lxcars-c_id' ).val( crmData.common.c_id );
            $( '#od_customer_name' ).val( crmData.common.customer_name );
            $( '#od-oe-ordnumber' ).html( '' );
            $( '#od-oe-finish_time' ).val( '' );
            $( '#od-oe-km_stnd' ).val( '0' );
            $( '#od-oe-employee_name' ).html( crmData.common.employee_name );
            $( '#od-oe-employee_id' ).val( crmData.common.employee_id );
            $( '#od_lxcars_c_ln' ).val( crmData.common.c_ln  );
            $( '#od-oe-mtime' ).html( '' );
            $( '#od-oe-internalorder' ).prop( 'checked', false );
            $( '#od-oe-itime' ).html( '' );
            $( '#od-oe-car_status' ).val( '' );
            $( '#od-oe-status' ).val( '' );
            $( '#od-lxcars-c_text' ).val( crmData.common.int_car_notes );
            $( '#od-customer-notes' ).val( crmData.common.int_cu_notes  );
            $( '#od-oe-intnotes' ).val( '' );
        }
    }
    else if( crmOrderTypeEnum.Invoice == crmOrderType ){
        $( '#od-oe-workflow' ).hide();
        $( '#od-inv-menus' ).show();
        $( '#od-off-menus' ).hide();
        $( '#od-oe-common-table' ).hide();
        $( '#od-inv-common-table' ).show();
        $( '#od-off-common-table' ).hide();
        $( '#od-listheading-workers' ).hide();
        $( '#od-listheading-status' ).hide();
        $( '#od-lxcars-c_text-label' ).show();
        $( '#od-lxcars-c_text' ).show();
        $( '#od-inv-payment' ).show();
        $( '.od-perform' ).hide();

        if( exists( crmData.bill ) ){
            title = kivi.t8( 'Edit invoice' );
            $( '#od-inv-id' ).val( crmData.bill.common.id );
            $( '#od-lxcars-c_id' ).val( crmData.bill.common.c_id );
            $( '#od-customer-id' ).val( crmData.bill.common.customer_id );
            $( '#od_inv_customer_name' ).val( crmData.bill.common.customer_name );
            $( '#od-inv-shippingpoint' ).val( crmData.bill.common.shippingpoint );
            $( '#od_inv_shippingpoint' ).val( crmData.bill.common.shippingpoint );
            $( '#od-inv-shipvia' ).val( crmData.bill.common.shipvia );
            $( '#od-inv-invnumber' ).html( crmData.bill.common.invnumber );
            $( '#od-inv-ordnumber' ).html( crmData.bill.common.ordnumber );
            $( '#od-inv-mtime' ).html( kivi.format_date( new Date( crmData.bill.common.mtime ) ) );
            $( '#od-inv-itime' ).html( kivi.format_date( new Date( crmData.bill.common.itime ) ) );
            $( '#od-inv-employee_name' ).html( crmData.bill.common.employee_name );
            $( '#od-inv-employee_id' ).val( crmData.bill.common.employee_id );
            $( '#od-lxcars-c_text' ).val( crmData.bill.common.int_car_notes );
            $( '#od-customer-notes' ).val( crmData.bill.common.int_cu_notes );
            $( '#od-oe-intnotes' ).val( crmData.bill.common.intnotes );
        }
        else{
            title = kivi.t8( 'New invoice' );
            $( '#od-lxcars-c_id' ).val( '' );
            $( '#od_inv_customer_name' ).val( crmData.common.customer_name );
            $( '#od-customer-id' ).val( crmData.common.customer_id );
            $( '#od-inv-shippingpoint' ).val( '' );
            $( '#od_inv_shippingpoint' ).val( '' );
            $( '#od-inv-shipvia' ).val( '' );
            $( '#od-inv-invnumber' ).html( '' );
            $( '#od-inv-ordnumber' ).html( '' );
            $( '#od-inv-mtime' ).html( '' );
            $( '#od-inv-itime' ).html( '' );
            $( '#od-inv-employee_name' ).html( crmData.common.employee_name );
            $( '#od-inv-employee_id' ).val( crmData.common.employee_id );
            $( '#od-lxcars-c_text' ).val( crmData.common.int_car_notes );
            $( '#od-customer-notes' ).val( crmData.common.int_cu_notes );
            $( '#od-oe-intnotes' ).val( crmData.common.intnotes );
        }
        crmEditOrderListPayment( crmData );
        crmEditOrderPrinterList( crmOrderTypeEnum.Invoice, crmData );
    }
    else if( crmOrderTypeEnum.Offer == crmOrderType ){
        $( '#od-oe-workflow' ).hide();
        $( '#od-inv-menus' ).hide();
        $( '#od-off-menus' ).show();
        $( '#od-oe-common-table' ).hide();
        $( '#od-inv-common-table' ).hide();
        $( '#od-off-common-table' ).show();
        $( '#od-listheading-workers' ).hide();
        $( '#od-listheading-status' ).hide();
        $( '#od-lxcars-c_text-label' ).hide();
        $( '#od-lxcars-c_text' ).hide();
        $( '#od-inv-payment' ).hide();
        $( '.od-perform' ).hide();

        if( exists( crmData.offer ) ){
            title = kivi.t8( 'Edit offer' );
            $( '#od-customer-id' ).val( crmData.offer.common.customer_id );
            $( '#od-off-id' ).val( crmData.offer.common.id );
            $( '#od_off_customer_name' ).val( crmData.offer.common.customer_name );
            $( '#od-off-quonumber' ).html( crmData.offer.common.quonumber );
            $( '#od-off-finish_time' ).val( crmData.offer.common.finish_time );
            $( '#od-off-employee_name' ).html( crmData.offer.common.employee_name );
            $( '#od-off-employee_id' ).val( crmData.offer.common.employee_id );
            $( '#od-off-mtime' ).html( kivi.format_date( new Date( crmData.offer.common.mtime ) ) );
            $( '#od-off-itime' ).html( kivi.format_date( new Date( crmData.offer.common.itime ) ) );
            $( '#od-off-closed' ).prop( 'checked', crmData.offer.common.closed );
            $( '#od-customer-notes' ).val( crmData.offer.common.int_cu_notes );
            $( '#od-oe-intnotes' ).val( crmData.offer.common.intnotes );
        }
        else{
            title = kivi.t8( 'New offer' );
            $( '#od-customer-id' ).val( crmData.common.customer_id );
            $( '#od_off_customer_name' ).val( crmData.common.customer_name );
            $( '#od-off-quonumber' ).html( '' );
            $( '#od-off-finish_time' ).val( '' );
            $( '#od-off-employee_name' ).html( crmData.common.employee_name );
            $( '#od-off-employee_id' ).val( crmData.common.employee_id );
            $( '#od-off-mtime' ).html( '' );
            $( '#od-off-itime' ).html( '' );
            $( '#od-off-internalorder' ).prop( 'checked', false );
            $( '#od-customer-notes' ).val( crmData.common.int_cu_notes  );
            $( '#od-oe-intnotes' ).val( '' );
        }

        crmEditOrderPrinterList( crmOrderTypeEnum.Offer, crmData );
     }

    $( '#od-ui-items-workers' ).html( '' );
    $( '#od-ui-items-workers' ).append(new Option( '', ''  ) );

    if( exists( crmData.workers )  ){
        for( let worker of crmData.workers ){
            $( '#od-ui-items-workers' ).append(new Option( worker.name, worker.name  ) );
        }
    }

    crmOpenView( 'crm-edit-order-dialog', null, ' - ' + title );
}

function crmEditOrderPrinterList( type, crmData ){
    let sel = '';
    let list = '';
    let printers = undefined;

    if( crmOrderTypeEnum.Offer == type ){
        sel = '#od-off-current-printer';
        list = '#od-off-printers';
    }
    else if( crmOrderTypeEnum.Invoice == type ){
        sel = '#od-inv-current-printer';
        list = '#od-inv-printers';
    }
    else return;

    $( list ).html( '' );
    if( ( exists( crmData.offer ) && exists( printers = crmData.offer.printers ) ) || exists( printers = crmData.printers ) || ( exists( crmData.bill ) && exists( printers = crmData.bill.printers ) ) ){
        for( let printer of printers ){
            if( $( '#crm-userconf-defprn' ).val() == printer.id ) $( sel ).text( printer.printer_description.substring( 0, 27 ) );
            $( list ).append( '<div class="layout-actionbar-action layout-actionbar-submit" value="' + printer.id  + '" onclick="crmEditOrderSelectPrinter( this );">' + printer.printer_description + '</div>' );
        }
        $( sel ).attr( 'value', $( '#crm-userconf-defprn' ).val() );
    }
}

function crmEditOrderSelectPrinter( e ){
    $( '#od-off-current-printer' ).text( $( e ).text().substring( 0, 27 ) );
    $( '#od-off-current-printer' ).attr( 'value', $( e ).attr( 'value' ) );
}

function crmEditOrderCallPrinter1(){
    let printData = {};
    printData['orderId'] = $( '#od-oe-id' ).val();
    printData['print'] = 'printOrder1';
    printData['customerId'] = $( '#od-customer-id' ).val();
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'printOrder', data: printData },
        success: function( data ){
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'printOrder( printOrder1 )', xhr.responseText );
        }
    });
}

function crmEditOrderCallPrinter2(){
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

function crmEditOrderCallPDF(){
    let printData = {};
    printData['orderId'] = $( '#od-oe-id' ).val();
    printData['print'] = 'pdfOrder';
    printData['customerId'] = $( '#od-customer-id' ).val();
     $.ajax({
        url: 'crm/ajax/crm.app.php',
        type: 'GET',
        data:  { action: 'printOrder', data: printData },
        success: function( printFileName ){
            window.open( 'crm/printedFiles/' + printFileName );
        },
        error: function( xhr, status, error ){
            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'printOrder( pdfOrder )', xhr.responseText );
        }
    });
}

function crmEditOrderCallCoparts(){
    window.location ='lxcars://AAGOnlinekba___' + coparts['c_hsn'] + '___' + coparts['c_tsn'] + '___' + $( '#od-lxcars-c_ln' ).html() + '___' + coparts['c_fin'] + '___' + coparts['c_d_de'] + '___' + coparts['c_mkb'] + '___' + $( '#od-oe-km_stnd' ).val() + '___' + $( '#od-oe-ordnumber' ).html() + '___' + coparts['customer_name'] + '___' + coparts['customer_street'] + '___' + coparts['customer_zipcode'] + '___' + coparts['customer_city'] + '___7___nodebug';
}

function crmEditOrderCloseView(){
    const fx = function(){
        assert( 'close', crmPreView )
        if( 'crm-edit-order-dialog' == crmPreView ) crmPreView = 'crm-wx-base-data';
        crmCloseView( 'crm-edit-order-dialog' , crmPreView, kivi.t8( 'Search order' ) );
    }
    if( 'crm-search-order-view' == crmPreView ) crmSearchOrder( fx );
    else fx();
    if( 'crm-wx-base-data' != crmPreView ) crmRefreshAppViewAction();
}

function crmEditOrderGotoCustomer(){
    crmCloseView( 'crm-edit-order-dialog' , 'crm-wx-base-data' );
}

crmInitFormEx( showCarDataFormModel, '#show-car-data-form', 0, '#show-car-data-hidden' );
function crmEditOrderShowCarData(){
    if( $( '#show_car_data-c_ln' ).val() != $( '#od_lxcars_c_ln' ).val() ) crmInitFormEx( showCarDataFormModel, '#show-car-data-form', 0, '#show-car-data-hidden');

    $( '#crm-show-car-data-dialog' ).dialog({
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
        modal: true,
        title: kivi.t8( 'Show car data' ),
        position: { my: "top", at: "top+250" },
        open: function(){
            $( this ).css( 'maxWidth', window.innerWidth );
        }
    }).dialog( 'open' ).resize();
}

$( "#od-oe-workflow, #od-inv-workflow, #od-off-workflow" ).menu({
   icons: { submenu: "ui-selectmenu-icon ui-icon ui-icon-triangle-1-s"},
   position: { my: "right top", at: "right+3 top+28" }
});

$( "#od-inv-printers-menu, #od-off-printers-menu" ).menu({
   icons: { submenu: "ui-selectmenu-icon ui-icon ui-icon-triangle-1-s"},
   position: { my: "right top", at: "right+3 top+28" }
});

function crmEditOrderListPayment( data ){
    $( '#od-inv-payment-list > tbody' ).html( '' );
    $( '#od_inv_book_deficit').show();

    let row = '<tr class="od_inv_paid-row" style="background-color: red;">' +
                                            '<td><img src="image/close.png" alt="löschen" onclick="crmDeletePaymentPos(this)"></td>' +
                                            '<td><input class="od_inv_paid-flag" type="hidden" value="ungebucht"></input><input class="od_inv_paid-date" size="10"></input></td>' +
                                            '<td><input class="od_inv_paid-source" size="10"></input></td>' +
                                            '<td><input class="od_inv_paid-memo" size="10"></input></td>' +
                                            '<td><input class="od_inv_paid-amount" size="10"></input></td>' +
                                            '<td><select class="od_inv_paid-chart_id">';
    if( exists( data.bill ) && exists( data.bill.payment_acc ) ){
        let i = 0;
        for( let acc of data.bill.payment_acc ){
            row += '<option value="' + acc.id + '" ' + ((++i == 2)? 'selected' : '') + '>' + acc.accno + '--' + acc.description + '</option>';
        }
    }
    row += '</select></td></tr>';
    $( '#od-inv-payment-list' ).append( row );

    let deficit = kivi.parse_amount( $( '#od-amount' ).val() );
    if( exists( data.bill ) && exists( data.bill.payment ) ){
        for( let payment of data.bill.payment ){
            let row_item = $( '.od_inv_paid-row' ).last().clone();
            row_item.find( '.od_inv_paid-flag' ).val( 'gebucht' );
            row_item.find( '.od_inv_paid-date' ).val( kivi.format_date( new Date( payment.transdate ) ) );
            row_item.find( '.od_inv_paid-source' ).val( payment.source );
            row_item.find( '.od_inv_paid-memo' ).val( payment.memo );
            row_item.find( '.od_inv_paid-amount' ).val( kivi.format_amount( payment.amount * -1 ) );
            row_item.find( '.od_inv_paid-chart_id' ).val( payment.chart_id );
            row_item.css( 'background-color', '');
            row_item.prependTo( '#od-inv-payment-list' );
            deficit += payment.amount;
        }
    }
    if( deficit > 0 ){
        $( '.od_inv_paid-row' ).last().find( '.od_inv_paid-date' ).val( kivi.format_date( new Date() ) );
        $( '.od_inv_paid-row' ).last().find( '.od_inv_paid-amount' ).val( deficit );
    }
    else{
        $( '.od_inv_paid-row' ).last().remove();
        $( '#od_inv_book_deficit').hide();
    }
}

$( '#od_inv_book_deficit' ).click( function(){
    $( '#od-inv-payment-list > tbody' ).find( '[class=od_inv_paid-row]' ).each( function( key, pos ){
        if( $( pos ).find( '.od_inv_paid-flag' ).val() == 'ungebucht' ){
            $( pos ).find( '.od_inv_paid-flag' ).val( 'gebucht' );
            $( pos ).css( 'background-color', '' );
        }
    });
    crmSaveOrder();
});

crmDeletePaymentPos = function( e ){
    $( e ).parent().parent().remove();
    $( '#od_inv_book_deficit').show();
}
