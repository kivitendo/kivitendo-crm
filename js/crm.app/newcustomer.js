
function crmNewCustomer(){
    crmInitFormEx( editNewCustomer, '#crm-tab-billaddr_Costumer');

    $('#crm-tab-billaddr_Costumer-gender').append(new Option('Male', 'M'));
    $('#crm-tab-billaddr_Costumer-gender').append(new Option('Female', 'F'));
    console.info("funktionstüchtig");
    $('#crm-new_Costumer-dialog').dialog({
        title: 'Customermenu',
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
    }).dialog('open').resize();

    $('#crm-nav-extras_Costumer').click(function(){
      crmInitFormEx(extraFormModel, "#crm-tab-extras_Costumer");
      $('crm-tab-extras_Costumer').dialog({
        title: 'Sonstiges',
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
      }).dialog('open').resize();
    })


    $('#crm-nav-deladdr_Costumer').click(function(){
      crmInitFormEx(deladdrFormModel, "#crm-tab-deladdr_Costumer");
      $('crm-tab-deladdr_Costumer').dialog({
        title: 'Lieferanschrift',
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
      }).dialog('open').resize();
    })

     $('#crm-nav-banktax_Costumer').click(function(){
      crmInitFormEx(banktaxFormModel, "#crm-tab-banktax_Costumer");
      $('crm-tab-banktax_Costumer').dialog({
        title: 'Lieferanschrift',
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
      }).dialog('open').resize();
    })
    // hier noch Variablenmenü erstellen
    $('#crm-nav-vars_Costumer').click(function(){
      crmInitFormEx(deladdrFormModel, "#crm-tab-vars_Costumer");
      $('crm-tab-vars_Costumer').dialog({
        title: 'Variablen',
        autoOpen: false,
        resizable: true,
        width: 'auto',
        height: 'auto',
      }).dialog('open').resize();
    })

    $('#edit_new_costumer-name').keyup(function(e){
        if(e.which == 32){
          $.ajax({
            url: '/kivitendo/crm/ajax/website_r.php',
            type: 'POST',
            data:  { action: 'firstnameToGender', data: { 'name': $('#edit_new_costumer-name').val().replace(' ', '') } },
            success: function( data ){
              console.info( data );
              $("#edit_new_costumer-gender").val(data["gender"]).change();
            },
            error: function( xhr, status, error ){
                console.error("ERROR: ", "Name not found" );
            }
        })
        }
        if(e.which == 13){
          $('#edit_new_costumer-zipcode').focus();
        }
      })

      $( '#edit_new_costumer-zipcode' ).keyup( function(){
        $.ajax({
          url: '/kivitendo/crm/ajax/website_r.php',
          type: 'POST',
          data: { action: 'getZipCode', data: { 'zipcode': $('#edit_new_costumer-zipcode').val() } },
          success: function( data ){
            $.each(data, function(index, value){
              var opt = document.createElement('option');
              opt.value = index;
              opt.innerHTML = JSON.stringify( value["ort"]).replace(new RegExp('"', 'g'),'');
              $("#edit_new_costumer-location").append(opt);
            })
            $('#edit_new_costumer-street').focus()
            $('#edit_new_costumer-zipcode').on("input", function(){
              $('#edit_new_costumer-location').empty();
            })
          },
          error: function( xhr, status, error ){
            console.info("ERROR", xhr, status)
          }

        });
        if($('#edit_new_costumer-zipcode').val().length = 5){
          $.ajax({
          url: '/kivitendo/crm/ajax/website_r.php',
          type: 'POST',
          data: {action: 'f_state', data: { 'zipcode': $('#edit_new_costumer-zipcode').val() } },
          success: function( data ){
            alert("Funktion")
            $('#edit_new_costumer-federalstate').append($('<option />').text(data['federal_state']));
          },
          error: function( xhr, status, error ){
            console.info("ERROR ", xhr, status, error)
          }

        });
        }

        $('#edit_new_costumer-location').change(function(){
          $('#edit_new_costumer-street').focus(); })
      })

      $('#edit_new_costumer-street').on("input", function(){
        $('#edit_new_costumer-street').autocomplete({
          source:function(request, response){
            $.ajax({
              url: '/kivitendo/crm/ajax/website_r.php',
              type: 'POST',
              data: {action: 'street', data:{ 'street': $('#edit_new_costumer-street').val(), 'locality': $('#edit_new_costumer-location option:selected').text() }},
              success: function(data){
                response($.map(data, function(item){
                  return{
                    value: item.street
                  }
                }))
              },
              error: function(xhr, status, error){
                console.error(xhr, error, status)
              },
            });
            autoFocus = true;
          }
        })
        $('#edit_new_costumer-street').on('autocompleteselect', function(){
          $('').focus();
        })
      })
}
