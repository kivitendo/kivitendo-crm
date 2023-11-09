//Auftag mit Kalendar verknüpfen (order_id)
//Kunde (location) in Kundennummer umwandeln und speichern
//Vendor in Vendornummer umwandeln und speichern
//job_xyz ist deprecated siehe Auftrag
//umgang mit location ??

const currentDay = moment().format('YYYY-MM-DD');
const fourDaysLater = moment().add(4, 'days').format('YYYY-MM-DD');
var crmCalendarInstances = [];

var crmCalculateEnd = function(){
  $( "#crm-edit-event-repeat-end" ).val( moment( $( "#crm-edit-event-end" ).val(), "DD.MM.YYYY" ).add( $( "#crm-edit-event-repeat" ).val(), $( "#crm-edit-event-repeat-factor" ).val() * $( "#crm-edit-event-repeat-quantity" ).val() ).format( "DD.MM.YYYY" ) );
};

var crmCalculateRepeatQuantity = function() {
  var a = moment( $( "#crm-edit-event-end" ).val(), 'L' );
  var b = moment( $( "#crm-edit-event-repeat-end" ).val(), 'L' );
  var erg = Math.floor( ( b.diff( a, $( "#crm-edit-event-repeat" ).val() ) ) / $( "#crm-edit-event-repeat-factor" ).val() );
  $( "#crm-edit-event-repeat-quantity" ).val( erg < 0 ? 0 : erg );
};

document.addEventListener('DOMContentLoaded', function() {
  $.ajax({
    url: '../ajax/crm.app.php',
    type: 'POST',
    data:  { action: 'getCalendarEvents', data: { employee: crmEmployee, start: currentDay, end: fourDaysLater } },
    success: function( crmCalendarData ){
      console.info( 'crmCalendarData', crmCalendarData );

      for( let entry of crmCalendarData ){
        console.info( 'entry', entry );
        $( '#crm-cal-tab-list' ).append( '<li><a href="#crm-cal-' + entry.id + '">' +  entry.label + '</a></li>' );
        $( '#crm-cal-tabs' ).append( '<div id="crm-cal-' + entry.id + '" class="crm-cal-tab"></div>' );
        var calendar = new FullCalendar.Calendar( document.getElementById( 'crm-cal-' + entry.id ), {
          themeSystem: 'bootstrap5',
          locale: 'de',
          initialView: 'timeGridFourDay',
          initialDate: currentDay,
          slotMinTime: '07:00',
          slotMaxTime: '19:00',
          selectable: true,
          editable: true,
          eventDurationEditable: true,
          eventResizableFromStart: true,
          contentHeight: 'auto',
          headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'timeGridDay,timeGridFourDay,weekEvents'
          },
          events: entry.events,
          views: {
            timeGridFourDay: {
              type: 'timeGrid',
              duration: { days: 7 },
              buttonText: '7 days'
            },
            weekEvents: {
              type: 'listWeek',
              duration: { days: 7 },
              buttonText: 'Events'
            }
          },
          eventClick: function( info ) {
            //console.info( 'eventClick', info );
            //console.info( 'eventClick', info.event._def.extendedProps );
            //console.info( 'tab', crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ] );
            $( "#crm-edit-event-title" ).val( info.event.title );
            $( "#crm-edit-event-description" ).text( info.event._def.extendedProps.description );
            $( "#crm-edit-event-id" ).val( info.event.id );
            $( "#crm-edit-event-full-time" ).prop( 'checked', info.event.allDay );
            $( "#crm-edit-event-start" ).val( moment( info.event.start ).format( "DD.MM.YYYY") );
            $( "#crm-edit-event-start-time" ).val( moment( info.event.start ).format( "HH:mm") );
            $( "#crm-edit-event-end" ).val( moment( info.event.end ? info.event.end : info.event.start ).format( "DD.MM.YYYY") );
            $( "#crm-edit-event-end-time" ).val( moment( info.event.end ? info.event.end : info.event.start ).format( "HH:mm") );
            $( '#crm-edit-event-prio' ).val( info.event._def.extendedProps.prio );
            $( '#crm-edit-event-repeat' ).val( info.event._def.extendedProps.repeat.trim() );
            $( "#crm-edit-event-repeat-factor" ).val( info.event._def.extendedProps.repeat_factor );
            $( "#crm-edit-event-repeat-quantity" ).val( info.event._def.extendedProps.repeat_quantity );
            $( "#crm-edit-event-repeat-end" ).val( moment( info.event._def.extendedProps.repeat_end ).format( "DD.MM.YYYY") == 'Invalid date' ? '' : moment( info.event._def.extendedProps.repeat_end ).format( "DD.MM.YYYY")  );
            $( '#crm-edit-event-customer' ).val( info.event._def.extendedProps.cvp_name );
            $( '#crm-edit-event-location' ).val( info.event._def.extendedProps.location );
            $( '#crm-edit-event-category' ).val( info.event._def.extendedProps.category );
            $( '#crm-edit-event-visibility' ).val( info.event._def.extendedProps.visibility );
            $( '#crm-edit-event-dialog' ).dialog( 'open' );
          },
          select: function( info ) {
            //console.info( 'select', info );
            //console.info( 'tab', $("#crm-cal-tabs .ui-tabs-panel:visible").attr("id") )
            //console.info( 'tab', crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ] );
          },
          eventResize: function( info ) {
            //console.info( 'eventResize', info );
          },
          eventDrop: function( info ) {
            //console.info( 'eventDrop', info );
          }
        });

        calendar.render();
        crmCalendarInstances.push( calendar );

        $( '#crm-edit-event-category' ).append( '<option value="' + entry.id + '">' + entry.label + '</option>' );
      }

      $( "#crm-cal-tabs" ).tabs().show();
      for( let employee of crmEmployeeGroups ){
        $( '#crm-edit-event-visibility' ).append( '<option value="' + employee.value + '">' + employee.text + '</option>' );
      }
    },
    error: function( xhr, status, error ){
        alert( 'Error: ' + error );
    }
  });

  $( '#crm-edit-event-start, #crm-edit-event-end' ).datepicker({ dateFormat: 'dd.mm.yy' });
  $( '#crm-edit-event-start-time, #crm-edit-event-end-time' ).timepicker({
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

  $( '#crm-edit-event-colorpicker' ).colorPicker({
    //defaultColor: 1,
    columns: 13,     // number of columns (optional)
    color: ['#FF7400', '#CDEB8B','#6BBA70','#006E2E','#C3D9FF','#0101DF','#4096EE','#356AA0','#FF0096','#DF0101','#B02B2C','#112211','#000000'], // list of colors (optional)
    click: function(color){
        $( '#crm-edit-event-color' ).val(color);
    },
  });

  $( "#crm-edit-event-repeat, #crm-edit-event-repeat-factor, #crm-edit-event-repeat-quantity" ).change( crmCalculateEnd );
  $( "#crm-edit-event-repeat-end").change( crmCalculateRepeatQuantity );

  /*
  $( '#crm-edit-event-color' ).change( function(){
    $( '#crm-edit-event-colorpicker' ).select( $( this ).val() );
  });
  */

  $.widget("custom.catcomplete", $.ui.autocomplete, {
    _renderMenu: function(ul,items) {
        var that = this,
        currentCategory = "";
        $.each( items, function( index, item ) {
            if ( item.category != currentCategory ) {
                ul.append( "<li class=\'ui-autocomplete-category\'>" + item.category + "</li>" );
                currentCategory = item.category;
            }
            that._renderItemData(ul,item);
        });
    }
  });

  $( '#crm-edit-event-customer' ).catcomplete({
    source: "../ajax/crm.app.php?action=searchPersonsAndCars",
    select: function( e, ui ) {
      console.info( 'select', ui.item );
      if( 'C' == ui.item.src || 'A' == ui.item.src ){
        $.ajax({
          url: '../ajax/crm.app.php',
          type: 'POST',
          data:  { action: 'getCars', data: { id: ui.item.id } },
          success: function( data ){
            $( '#crm-edit-event-car' ).html( '' );
            $( '#crm-edit-event-car' ).append( '<option value=""></option>' );
            for( let car of data ){
              $( '#crm-edit-event-car' ).append( '<option value="' + car.c_id + '">' + car.c_ln + '</option>' );
            }
          },
          error: function( xhr, status, error ){
            $( '#crm-edit-event-car' ).html( '' );
          }
        });
      }
      else{
        $( '#crm-edit-event-car' ).html( '' );
      }
    }
  });

  $( '#crm-edit-event-dialog' ).dialog({
    autoOpen: false,
    height: 668,
    width: 666,
    modal: true,
    buttons: {
        "Save": function() {
          dbUpdateData = {};
          dbUpdateData['events'] = {};
          const start = moment($( "#crm-edit-event-start" ).val() + ' ' + $( "#crm-edit-event-start-time" ).val(), 'DD.MM.YYYY hh:mm:ss').format('YYYY-MM-DD hh:mm:ss');
          const end = moment($( "#crm-edit-event-end" ).val() + ' ' + $( "#crm-edit-event-end-time" ).val(), 'DD.MM.YYYY hh:mm:ss').format('YYYY-MM-DD HH:mm:ss');
          dbUpdateData['events']['duration'] = '[' + start + ',' + end + ')';
          if( $( "#crm-edit-event-cvp-id" ).val() != '' ) dbUpdateData['events']['cvp_id'] = $( "#crm-edit-event-cvp-id" ).val();
          if( $( "#crm-edit-event-cvp-type" ).val() != '' ) dbUpdateData['events']['cvp_type'] = $( "#crm-edit-event-cvp-type" ).val();
          if( $( "#crm-edit-event-customer" ).val() != '' ) dbUpdateData['events']['cvp_name'] = $( "#crm-edit-event-customer" ).val();
          if( $( "#crm-edit-event-car-id" ).val() != '' ) dbUpdateData['events']['car_id'] = $( "#crm-edit-event-car-id" ).val();
          if( $( "#crm-edit-event-order-id" ).val() != '' ) dbUpdateData['events']['order_id'] = $( "#crm-edit-event-order-id" ).val();
          dbUpdateData['events']['title'] = $( "#crm-edit-event-title" ).val();
          dbUpdateData['events']['description'] = $( "#crm-edit-event-description" ).val();
          dbUpdateData['events']['allDay'] = $( "#crm-edit-event-full-time" ).is( ":checked" );
          dbUpdateData['events']['uid'] = crmEmployee;
          dbUpdateData['events']['visibility'] = $( "#crm-edit-event-visibility option:selected" ).val();
          dbUpdateData['events']['category'] = $( "#crm-edit-event-category option:selected" ).val();
          dbUpdateData['events']['prio'] = $( "#crm-edit-event-prio option:selected" ).val();
          dbUpdateData['events']['color'] = $( "#crm-edit-event-color" ).val();
          dbUpdateData['events']['location'] = $( "#crm-edit-event-location" ).val();
          dbUpdateData['events']['repeat'] = $( "#crm-edit-event-repeat" ).val();
          dbUpdateData['events']['repeat_factor'] = $( "#crm-edit-event-repeat-factor" ).val();
          dbUpdateData['events']['repeat_quantity'] = $( "#crm-edit-event-repeat-quantity" ).val();
          dbUpdateData['events']['repeat_end'] =  moment( $( "#crm-edit-event-repeat-end" ).val() + ' 23:59:59', 'DD.MM.YYYY hh:mm:ss' ).format('YYYY-MM-DD HH:mm:ss');
          dbUpdateData['events']['WHERE'] = {};
          dbUpdateData['events']['WHERE'] = 'id = ' + $( '#crm-edit-event-id' ).val();
          console.info( 'dbUpdateData', dbUpdateData );
          $( this ).dialog( "close" );
        },
        Delete: function() {
            $( this ).dialog( "close" );
        },
        Cancel: function() {
            $( this ).dialog( "close" );
        }
    },
    close: function() {
        $( this ).dialog( "close" );
    }
  });
});
