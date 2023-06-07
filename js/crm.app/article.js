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
        data: { action: 'newArticleNumber', data:{ 'part_type': $( '#edit_article-part_type' ).val() } },
        type: "POST",
        success: function( crmData ){
            console.info( crmData );
            $( '#edit_article-partnumber' ).val( crmData.newnumber )
        }
    });
});

function crmEditArticleDlg(){
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
                    text: kivi.t8( 'Save' ),
                    click: function(){
                        dbData = {};
                        for( let item of editArticleFormModel ){
                            let columnName = item.name.split( '-' );
                            let val = $( '#' + item.name ).val();
                            if( exists(val) && val !== '' ){
                                if( item.name !== 'edit_car-c_id' ) dbData[columnName[1]] = val;
                            }
                        }
                        console.info( dbData );

                        $( this ).dialog( "close" );
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
