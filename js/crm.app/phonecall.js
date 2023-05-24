$( '#tel1_dialog_button, #tel2_dialog_button' ).click( function( data ){
    data.stopImmediatePropagation();
    //alert( "ClickToCall Dialog");
    var dialog_id = this.id.replace( '_button', '' );
    //console.info( dialog_id );
    $( '#' + dialog_id ).dialog({
        modal: true,
        title: kivi.t8('Dialog for ' + dialog_id.replace( '_dialog', '' ) ),//kivi.t8( 'Phone Dialog'), //ToDo
        width: 'auto',
        resizable: false,
        open: function( event, ui ){
            $.ajax({
                url: 'ajax/clickToCall.php?action=getPhones',
                type: 'GET',
                success: function ( data ){
                    var external_contexts_array = data['external_contexts'].split( ',');
                    var internal_phones_array = data['internal_phones'].split( ',');
                    var selected_context = typeof data['user_external_context'] !== 'undefined' ?  data['user_external_context'] : '';
                    var selected_phone = typeof data['user_internal_phone'] !== 'undefined' ?  data['user_internal_phone'] : '';
                    var selected = '';
                    var dynamic_html = '<table><tr><td>' + kivi.t8( 'External Context:' ) + '</td><td> <select id="user_external_context"  style="width:100%;">';
                    $.each( external_contexts_array, function( key, value ){
                        selected = value == selected_context ? 'selected' : '';
                        dynamic_html +=  '<option value="' + value + '"' + selected + '>' + value + '</option>'
                    })
                    dynamic_html += '</select></td></tr>';
                    dynamic_html += '<tr><td>' + kivi.t8( 'Internal Phone:' ) + '</td><td> <select id="user_internal_phone"  style="width:100%;">';
                    $.each( internal_phones_array, function( key, value ){
                        selected = value == selected_phone ? 'selected' : '';
                        dynamic_html +=  '<option value="' + value + '"' + selected + '>' + value + '</option>'
                    })
                    dynamic_html += '</select></td></tr></table>';
                    $( '#' + dialog_id ).html( dynamic_html );
                    //console.info(  dynamic_html );
                    $( '#user_external_context, #user_internal_phone' ).change( function( data ){
                        var dataObj = {};
                        dataObj[this.id] = $(this).val();
                        $.ajax({
                            url: 'ajax/clickToCall.php',
                            type: 'POST',
                            data: { action: 'saveClickToCall', data: dataObj },
                            success: function ( data ) {
                                //if( data ) alert( );
                            },
                            error: function () {
                                alert( 'Error: saveClickToCall!' );
                            }
                        });

                    })
                    //console.info( dialog_id );
                },
                error: function (){
                    alert( 'Error: ajax/clickToCall.php?action=getPhones' );
                }
            })
        },
        buttons: [{
            text: kivi.t8( 'cancel' ),
            click: function(){
                $( this ).dialog( "close" );
            }
        },{
            text: kivi.t8( 'call' ),
            click: function(){
                $( '#' + dialog_id.replace( '_dialog', '' ) ).click();
                $( this ).dialog( "close" );
            }
        }],

    })
}).button();


