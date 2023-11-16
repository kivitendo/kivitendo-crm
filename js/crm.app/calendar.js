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
        $( '#crm-cal-tab-list' ).append( '<li><a href="#crm-cal-' + entry.id + '">' +  entry.label + '</a></li>' );
        $( '#crm-cal-tabs' ).append( '<div id="crm-cal-' + entry.id + '" class="crm-cal-tab"></div>' );
        var calendar = new FullCalendar.Calendar( document.getElementById( 'crm-cal-' + entry.id ), {
          themeSystem: 'bootstrap5',
          locale: 'de',
          timeZone: 'local',
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

          /*
          events: [
            {
            "id": 8888,
            "groupid": 8888,
            "start": "2023-11-16T11:00:00",
            "end": "2023-11-16T12:00:00",
            "duration": '26:00',
            "title": "Test ???",
            "repeat": "day  ",
            "repeat_factor": 0,
            "repeat_quantity": 0,
            "repeat_end": null,
            "description": "",
            "location": "",
            "uid": 861,
            "visibility": -1,
            "prio": 0,
            "category": 0,
            "allDay": false,
            "color": "#FF7400",
            "cvp_id": null,
            "cvp_name": null,
            "cvp_type": null,
            "car_id": null,
            "order_id": null,
            "rrule": {
                "dtstart": "2023-11-16T11:00:00",
                "interval": 1,
                "freq": "daily",
                "until": "2023-11-16T12:00:00"
            }
          }
          ],
          */

          /*
          events: [
            {
              title: 'rrule event',
              rrule: {
                dtstart: '2023-11-09T13:00:00',
                freq: 'weekly'
              },
              duration: '02:00'
            },
            {
              title: 'testbed event',
              id: 8297,
              groupId: 8297,
              start: '2023-11-16 13:00:00',
              end: '2023-11-16 13:30:00',
              rrule: {
                dtstart: '2023-11-16 13:00:00',
                interval: 1,
                freq: 'daily',
                until: '2023-11-16 13:30:00'
              }
            }
          ],
          */

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
            console.info( 'info', info );
            $( "#crm-edit-event-cvp-id" ).val( info.event._def.extendedProps.cvp_id );
            $( "#crm-edit-event-cvp-type" ).val( info.event._def.extendedProps.cvp_type );
            $( "#crm-edit-event-car-id" ).val( info.event._def.extendedProps.car_id );
            $( "#crm-edit-event-order-id" ).val( info.event._def.extendedProps.order_id );
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
            $( '#crm-edit-event-car' ).html( '' );
            $( '#crm-edit-event-car' ).append( '<option value=""></option>' );
            $( '#crm-edit-event-dialog' ).dialog( 'open' );
          },
          select: function( info ) {
            //console.info( 'select', info );
            //console.info( 'tab', $("#crm-cal-tabs .ui-tabs-panel:visible").attr("id") )
            //console.info( 'tab', crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ] );
            $( "#crm-edit-event-cvp-id" ).val( '' );
            $( "#crm-edit-event-cvp-type" ).val( '' );
            $( "#crm-edit-event-car-id" ).val( '' );
            $( "#crm-edit-event-order-id" ).val( '' );
            $( "#crm-edit-event-title" ).val( '' );
            $( "#crm-edit-event-description" ).text( '' );
            $( "#crm-edit-event-id" ).val( '' );
            $( "#crm-edit-event-full-time" ).prop( 'checked', false );
            $( "#crm-edit-event-start" ).val( moment( info.start ).format( "DD.MM.YYYY") );
            $( "#crm-edit-event-start-time" ).val( moment( info.start ).format( "HH:mm") );
            $( "#crm-edit-event-end" ).val( moment( info.end ? info.end : info.start ).format( "DD.MM.YYYY") );
            $( "#crm-edit-event-end-time" ).val( moment( info.end ? info.end : info.start ).format( "HH:mm") );
            $( '#crm-edit-event-prio' ).val( '0' );
            $( '#crm-edit-event-repeat' ).val( '0' );
            $( "#crm-edit-event-repeat-factor" ).val( '0' );
            $( "#crm-edit-event-repeat-quantity" ).val( '0' );
            $( "#crm-edit-event-repeat-end" ).val( ''  );
            $( '#crm-edit-event-customer' ).val( '' );
            $( '#crm-edit-event-location' ).val( '' );
            $( '#crm-edit-event-category' ).val( $("#crm-cal-tabs .ui-tabs-panel:visible").attr("id").replace( 'crm-cal-', '' ) );
            $( '#crm-edit-event-visibility' ).val( '-1' );
            $( '#crm-edit-event-car' ).html( '' );
            $( '#crm-edit-event-car' ).append( '<option value=""></option>' );
            $( '#crm-edit-event-dialog' ).dialog( 'open' );
          },
          eventResize: function( info ) {
          },
          eventDrop: function( info ) {
          },
          eventChange: function( info ) {
            //console.info( 'info', info );
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
      alert( 'Error: ' + xhr.responseText );
    }
  });

  $( '#crm-edit-event-car' ).append( '<option value=""></option>' );

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

  $( '#crm-edit-event-car' ).change( function(){
    $( "#crm-edit-event-car-id" ).val( $( this ).val() );
    if( $( "#crm-edit-event-car-id" ).val() != '' ) $( '#crm-edit-event-to-car' ).show();
    else $( '#crm-edit-event-to-car' ).hide();
  });

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
      //console.info( 'select', ui.item );
      $( "#crm-edit-event-cvp-id" ).val( ui.item.id );
      $( '#crm-edit-event-to-cvp' ).show();

      if( 'C' == ui.item.src || 'A' == ui.item.src ){
        $( "#crm-edit-event-cvp-type" ).val( ui.item.src );
        $.ajax({
          url: '../ajax/crm.app.php',
          type: 'POST',
          data:  { action: 'getCarsForCalendar', data: { id: ui.item.id } },
          success: function( data ){
            $( "#crm-edit-event-car-id" ).val();
            $( '#crm-edit-event-car' ).html( '' );
            $( '#crm-edit-event-car' ).append( '<option value=""></option>' );
            for( let car of data ){
              $( '#crm-edit-event-car' ).append( '<option value="' + car.c_id + '">' + car.label + '</option>' );
            }
          },
          error: function( xhr, status, error ){
            $( "#crm-edit-event-car-id" ).val();
            $( '#crm-edit-event-car' ).html( '' );
            $( '#crm-edit-event-car' ).append( '<option value=""></option>' );
          }
        });
      }
      else{
        $( '#crm-edit-event-car' ).html( '' );
        $( "#crm-edit-event-cvp-type" ).val( 'C' );
      }
    }
  });

  $( '#crm-edit-event-dialog' ).dialog({
    autoOpen: false,
    height: 735,
    width: 660,
    modal: true,
    open: function() {
      if( $( "#crm-edit-event-cvp-id" ).val() != '' ) $( '#crm-edit-event-to-cvp' ).show();
      else $( '#crm-edit-event-to-cvp' ).hide();
      if( $( "#crm-edit-event-order-id" ).val() != '' ) $( '#crm-edit-event-to-order' ).show();
      else $( '#crm-edit-event-to-order' ).hide();
      if( $( "#crm-edit-event-car-id" ).val() != '' ) $( '#crm-edit-event-to-car' ).show();
      else $( '#crm-edit-event-to-car' ).hide();
    },
    buttons: {
        "Save": function() {
          dbUpdateData = {};//jsonobj für die Datenbankupdate (genericUpdateEx)
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
          dbUpdateData['events']['\"allDay\"'] = $( "#crm-edit-event-full-time" ).is( ":checked" );
          dbUpdateData['events']['uid'] = crmEmployee;
          dbUpdateData['events']['visibility'] = $( "#crm-edit-event-visibility option:selected" ).val();
          dbUpdateData['events']['category'] = $( "#crm-edit-event-category option:selected" ).val();
          dbUpdateData['events']['prio'] = $( "#crm-edit-event-prio option:selected" ).val();
          dbUpdateData['events']['color'] = $( "#crm-edit-event-color" ).val();
          dbUpdateData['events']['location'] = $( "#crm-edit-event-location" ).val();
          dbUpdateData['events']['repeat'] = $( "#crm-edit-event-repeat" ).val();
          dbUpdateData['events']['repeat_factor'] = $( "#crm-edit-event-repeat-factor" ).val();
          dbUpdateData['events']['repeat_quantity'] = $( "#crm-edit-event-repeat-quantity" ).val();
          const repeatEnd = moment( $( "#crm-edit-event-repeat-end" ).val() + ' 23:59:59', 'DD.MM.YYYY hh:mm:ss' ).format('YYYY-MM-DD HH:mm:ss');
          if( 'Invalid date' != repeatEnd ) dbUpdateData['events']['repeat_end'] =  repeatEnd;

          let functionName = 'insertCalendarEvent';

          if( '' != $( '#crm-edit-event-id' ).val() ){
            dbUpdateData['events']['WHERE'] = {};
            dbUpdateData['events']['WHERE'] = 'id = ' + $( '#crm-edit-event-id' ).val();
            functionName = 'updateCalendarEvent';
          }
          else{
            let tmpData = {};
            tmpData['record'] = {};
            tmpData['record']['events'] = dbUpdateData['events'];
            dbUpdateData = tmpData;
          }

          $.ajax({
            url: '../ajax/crm.app.php',
            type: 'POST',
            data:  { action: functionName, data: dbUpdateData },
            success: function( data ){
              //console.info( 'tab', crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ] );
              crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ].refetchEvents();
              $( '#crm-edit-event-dialog' ).dialog( "close" );
            },
            error: function( xhr, status, error ){
              alert( 'Error: ' + xhr.responseText );
            }
          });
        },
        Delete: function() {
          if( '' == $( '#crm-edit-event-id' ).val() ){
            $( this ).dialog( "close" );
            return;
          }

          let pos = {};
          pos['events'] = {};
          pos['events']['WHERE'] = 'id = ' + $( '#crm-edit-event-id' ).val();

          $.ajax({
              url: '../ajax/crm.app.php',
              type: 'POST',
              data:  { action: 'deleteCalendarEvent', data: pos },
              success: function( data ){
                console.info( 'tab', crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ] );
                alert( crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ] );
                //crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ].refetchEvents();
                //crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ].render();
                $( '#crm-edit-event-dialog' ).dialog( "close" );
              },
              error: function( xhr, status, error ){
                alert( 'Error: ' + xhr.responseText );
              }
          });
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
