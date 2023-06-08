crmInitFormEx( editArticleFormModel, '#edit-article-form'  );
$( '#edit_article-part_type' ).append( new Option( 'Anweisung', 'I' ) );
$( '#edit_article-part_type' ).append( new Option( 'Ware', 'P' ) );
$( '#edit_article-part_type' ).append( new Option( 'Dienstleistung', 'S' ) );
$( '#edit_article-part_type' ).change( function(){
    $( '#edit_article-unit' ).val( $( '#edit_article-part_type' ).val() );
    $( '#edit_article-unit' ).change();
});

$( '#edit_article-part_type' ).change( function(){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        data: { action: 'newArticleNumber', data:{ 'part_type': $( '#edit_article-part_type' ).val(), 'description': $( '#edit_article-description' ).val() } },
        type: "POST",
        success: function( crmData ){
            console.info( crmData );
            $( '#edit_article-partnumber' ).val( crmData.newnumber )
        }
    });
});

$( '#edit_article-partnumber' ).keyup(function(e){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        data: { action: 'checkArticleNumber', data:{ 'partnumber': $( '#edit_article-partnumber' ).val() } },
        type: "POST",
        success: function( crmData ){
            if( crmData.exists ){
                $( '#edit_article-save-btn' ).hide();
            }
            else{
                $( '#edit_article-save-btn' ).show();
            }
        }
    });
});

function crmEditArticleDlg( field ){
    $.ajax({
        url: 'crm/ajax/crm.app.php',
        data: { action: 'dataForNewArticle', data:{ 'part_type': $( '#edit_article-part_type' ).val() } },
        type: "POST",
        success: function( crmData ){
            console.info( crmData );

            $( '#edit_article-unit' ).html( '' );
            for(let unit of crmData.common.units){
                $( '#edit_article-unit' ).append( new Option( unit.name, unit.name ) );
            }

            $( '#edit_article-buchungsgruppen_id' ).html( '' );
            for(let buchungsgruppe of crmData.common.buchungsgruppen){
                $( '#edit_article-buchungsgruppen_id' ).append( new Option( buchungsgruppe.description, buchungsgruppe.id ) );
            }

            $( '#edit_article-listprice' ).val( kivi.format_amount( '0.00' ) );
            $( '#edit_article-sellprice' ).val( kivi.format_amount( crmData.defaults.customer_hourly_rate ) );

            $( '#crm-edit-article-dialog' ).dialog({
                autoOpen: false,
                resizable: true,
                width: 'auto',
                height: 'auto',
                modal: true,
                title: kivi.t8( 'Edit article' ),
                position: { my: "top", at: "top+250" },
                open: function(){
                    $( this ).css( 'maxWidth', window.innerWidth );
                },
                buttons:[{
                    id: 'edit_article-save-btn',
                    text: kivi.t8( 'Save' ),
                        click: function(){
                            $.ajax({
                                url: 'crm/ajax/crm.app.php',
                                data: { action: 'checkArticleNumber', data:{ 'partnumber': $( '#edit_article-partnumber' ).val() } },
                                type: "POST",
                                success: function( crmData ){
                                if( crmData.exists ){
                                    $( '#edit_article-save-btn' ).hide();
                                    alert( kivi.t8( 'Part number already exists!' ) );
                                 }
                                else{
                                    dbData = {};
                                    for( let item of editArticleFormModel ){
                                        let columnName = item.name.split( '-' );
                                        let val = $( '#' + item.name ).val();
                                        if( exists(val) ){
                                            if( columnName[1] !== 'qty' ) dbData[columnName[1]] = val;
                                        }
                                    }
                                    switch( dbData['part_type'] ){
                                    case 'P':
                                        dbData['part_type'] = 'part';
                                        dbData['instruction'] = false;
                                        break;
                                    case 'S':
                                        dbData['part_type'] = 'service';
                                        dbData['instruction'] = false;
                                        break;
                                    case 'I':
                                        dbData['part_type'] = 'service';
                                        dbData['instruction'] = true;
                                        break;
                                    }
                                    if( dbData['sellprice'] === '' ){
                                            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Error' ), kivi.t8( 'Please set a sell price' ) );
                                    }
                                    if( dbData['listprice'] === '' ){
                                            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Error' ), kivi.t8( 'Please set a list price' ) );
                                    }
                                    dbData['sellprice'] = kivi.parse_amount( dbData['sellprice'] );
                                    dbData['listprice'] = kivi.parse_amount( dbData['listprice'] );

                                    $.ajax({
                                        url: 'crm/ajax/crm.app.php',
                                        data: { action: 'insertNewArticle', data: dbData },
                                        type: "POST",
                                        success: function( crmData ){
                                            dbData['id'] = crmData.id;
                                            dbData['qty'] = ( $( '#edit_article-qty' ).val() == '' )? 0 : $( '#edit_article-qty' ).val();
                                            crmCompleteInsertOrderPos( field, dbData );
                                            $( '#crm-edit-article-dialog' ).dialog( "close" );
                                       },
                                        error: function(xhr, status, error){
                                            $( '#message-dialog' ).showMessageDialog( 'error', kivi.t8( 'Connection to the server' ), kivi.t8( 'Request Error in: ' ) + 'getHistory()', xhr.responseText );
                                        }
                                    });
                                }
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

            $( '#edit_article-partnumber' ).val( crmData.defaults.newnumber );
        }
    });
}
