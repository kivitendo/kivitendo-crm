function crmNewCustomer(cov){
  var pos = {};
  pos['record'] = {};
  pos['record']['customer'] = {};
  crmInitFormEx( editNewCustomer, '#crm-tab-billaddr_customer');
  crmInitFormEx(extraFormModel, "#crm-tab-extras_customer");
  crmInitFormEx(editdeladdrCustomer, "#crm-tab-deladdr_customer");
  crmInitFormEx(banktaxFormModel, "#crm-tab-banktax_customer");

  var blandid;
  $('#edit_new_customer-gender').append(new Option(' ', ''));
  $('#edit_new_customer-gender').append(new Option('Herr', 'M'));
  $('#edit_new_customer-gender').append(new Option('Frau', 'F'));
  $('#edit_new_customer-type').val(cov);
  $('#billaddr-phone').empty();
  $('#crm-new_customer-dialog').dialog({
    title: 'Customermenu',
    autoOpen: false,
    resizable: true,
    width: 'auto',
    height: 'auto',
  }).dialog('open').resize();
  $('#edit_new_customer-name').focus();
  var deladdr_infos = {

  }
  $('#edit_new_customer-name').keyup(function(e){
    if(e.which == 32){
      $.ajax({
        url: '/kivitendo/crm/ajax/website_r.php',
        type: 'POST',
        data:  { action: 'firstnameToGender', data: { 'name': $('#edit_new_customer-name').val().replace(' ', '') } },
        success: function( data ){
          console.info( data );
          $("#edit_new_customer-gender").val(data["gender"]).change();
        },
      })
    }
    if(e.which == 13){
      $('#edit_new_customer-zipcode').focus();
    }
  })

  $( '#edit_new_customer-zipcode' ).keyup( function(e){
    $.ajax({
      url: '/kivitendo/crm/ajax/website_r.php',
      type: 'POST',
      data: { action: 'getZipCode', data: { 'zipcode': $('#edit_new_customer-zipcode').val() } },
      success: function( data ){
        $.each(data, function(index, value){
          var opt = document.createElement('option');
          opt.value = index;
          opt.innerHTML = JSON.stringify( value["ort"]).replace(new RegExp('"', 'g'),'');
          $("#edit_new_customer-location").append(opt);
              //$.cookie("name", $("#edit_new_customer-name"))
          })
        if(data.length > 1){
          //$('#edit_new_customer-location').attr('size', $('option').length);
          //$('#edit_new_customer-location').attr('size', data.length);
          //$('#edit_new_customer-location').on('click', function () { 
          //  $('#edit_new_customer-location').attr('size', 1);
          //})
          $('#edit_new_customer-location').prevAll('input.select-dropdown').trigger('open');
        }
        else if(data.length = 1){
          $('#edit_new_customer-street').focus()
        }
        $('#edit_new_customer-zipcode').on("input", function(){
          $('#edit_new_customer-location').empty();
        })
      },
      error: function( xhr, status, error ){
        console.info("ERROR", xhr, status)
      }

    });
  if($('#edit_new_customer-zipcode').val().length = 5){
      $.ajax({
        url: '/kivitendo/crm/ajax/website_r.php',
        type: 'POST',
        data: {action: 'f_state', data: { 'zipcode': $('#edit_new_customer-zipcode').val() } },
        success: function( data ){
          $('#edit_new_customer-federalstate').append($('<option />').text(data['federal_state']));
        },
        error: function( xhr, status, error ){
            //console.info("ERROR ", xhr, status, error)
        }

      });
    }

  $('#edit_new_customer-location').change(function(){
    $.ajax({
      url: "crm/ajax/crm.app.php",
      type: "POST",
      data: {action: "getblandid", data:{'bundesland': $('#edit_new_customer-federalstate option:selected').val()}},
      success: function(data){
        blandid = data['id']
      },
      error: function (xhr, error, status) { 
        console.info(xhr, status, error);
      }
    })
      $('#edit_new_customer-street').focus();

          })
      })

      $('#edit_new_customer-street').on("input", function(){
        $('#edit_new_customer-street').autocomplete({
          source:function(request, response){
            $.ajax({
              url: '/kivitendo/crm/ajax/website_r.php',
              type: 'POST',
              data: {action: 'street', data:{ 'street': $('#edit_new_customer-street').val(), 'locality': $('#edit_new_customer-location option:selected').text() }},
              success: function(data){
                response($.map(data, function(item){
                  return{
                    value: item.street
                  }
                }))
              },
              error: function(xhr, status, error){
                //console.error(xhr, error, status)
              },
            });
            autoFocus = true;
          }
        })
        $('#edit_new_customer-street').on('autocompleteselect', function(){
          //$('').focus();
        })

      })
      $('#edit_new_customer-street').keypress(function(e){
        if((e.which == 13) || (e.which==40) ){
          
          $('#edit_new_customer-phone_number').focus();

        }
      }) 
      $('#edit_new_customer-phone_number').keypress(function(e){
        if(e.which==13){
          $('#edit_new_customer-email').focus();
          
        }
      })
      $('#edit_new_customer-email').keypress(function(e){
        if(e.which==13){
          pos['record']['customer']['greeting'] = $('#edit_new_customer-gender option:selected').text();
          pos['record']['customer']['name'] = $('#edit_new_customer-name').val();
          pos['record']['customer']['zipcode'] = $('#edit_new_customer-zipcode').val();
          pos['record']['customer']['federal_state'] = $('#edit_new_customer-federalstate option:selected').text();
          pos['record']['customer']['city'] = $('#edit_new_customer-location option:selected').text();
          pos['record']['customer']['bland'] = blandid;
          pos['record']['customer']['street'] = $('#edit_new_customer-street').val();
          pos['record']['customer']['department_1'] = $('#deladdr_Customer-shiptodepartment_1').val();
          pos['record']['customer']['department_2'] = $('#deladdr_Customer-shiptodepartment_2').val();
          pos['record']['customer']['email'] = $('#edit_new_customer-email').val();
          pos['record']['customer']['phone'] = $('#edit_new_customer-phone_number').val()
          pos['record']['customer']['contact'] = $('#deladdr_Customer-shiptocontact').val();
          pos['record']['customer']['country'] = $('#deladdr_Customer-shiptocountry').val();
          pos['record']['customer']['taxzone_id'] = 4;
          pos['record']['customer']['currency_id'] = 1;
          pos['record']['customer']['phone'] = $('#edit_new_customer-phone_number').val();
          pos['record']['customer']['fax'] = $('#deladdr_Customer-shiptofax').val();
          //Bank Steuer
          pos['record']['customer']['ustId'] = $('#billaddr-ustid').val();
          pos['record']['customer']['taxnumber'] = $('#billaddr-taxnumber').val();
          pos['record']['customer']['bank'] = $('#billaddr-bank').val();
          pos['record']['customer']['Iban'] = $('#billaddr-iban').val();
          pos['record']['customer']['bic'] = $('#billaddr-bic').val()
          pos['record']['customer']['direct_debit'] = $('#billaddr-direct_debit').val();

          console.info(pos);
          console.info($('#deladdr-shiptocountry').val());
          $.ajax({
            url: "crm/ajax/crm.app.php",
            type: "POST",
            data: {action: "genericSingleInsert", data:pos},
            error: function(xhr, error, status){
              console.info(xhr, status, error);
            }
          })
        }
      })
      $('#deladdr_Customer-shiptocity').blur(function(){
        pos['record']['customer']
        console.info(pos);
        console.info("7744")
      })
      $('#deladdr_Customer-shiptoname').change(function () { 
        console.info("black hole sun, won't you come ?")
       })
}
