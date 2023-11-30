const currentDay = moment().format('YYYY-MM-DD');
const fourDaysLater = moment().add(4, 'days').format('YYYY-MM-DD');
var crmCalendarInstances = [];
var crmCategoryTmpIndex = 0;

const RRule = rrule.RRule;
const crmRruleFreqMap = { 'daily': RRule.DAILY, 'weekly': RRule.WEEKLY, 'monthly': RRule.MONTHLY, 'yearly': RRule.YEARLY };
const crmMomentFreqMap = { 'daily': 'days', 'weekly': 'weeks', 'monthly': 'months', 'yearly': 'years' };

var crmCalculateEnd = function(){
  const rrule = new RRule({ 'count': parseInt( $( "#crm-edit-event-count" ).val() ), 'dtstart': moment( $( "#crm-edit-event-start" ).val(), "DD.MM.YYYY HH:mm" ).toDate(), 'freq': crmRruleFreqMap[$( "#crm-edit-event-freq" ).val().trim()], 'interval': parseInt( $( "#crm-edit-event-interval" ).val() ) });
  const all = rrule.all();
  if( all.length < 1 ) return;
  $( '#crm-edit-event-repeat-end' ).val( moment( new Date( all[all.length -1] ) ).format( "DD.MM.YYYY") );
};

var crmCalculateRepeatQuantity = function() {
  const a = moment( $( "#crm-edit-event-end" ).val(), 'DD.MM.YYYY HH:mm' );
  const b = moment( $( "#crm-edit-event-repeat-end" ).val(), 'DD.MM.YYYY HH:mm' );
  const erg = Math.floor( ( b.diff( a, crmMomentFreqMap[$( "#crm-edit-event-freq" ).val()] ) ) / parseFloat( $( "#crm-edit-event-interval" ).val() ) );
  $( "#crm-edit-event-count" ).val( erg < 0 ? 0 : erg );
};

function crmCalendarEventDuration( start, end ){
  const timeDiff = end.diff( start, 'minutes' );
  const hours = Math.floor( timeDiff / 60 );
  const minutes = timeDiff % 60;
  if( hours < 0 || minutes < 0 ) return 'Invalid date';
  let duration = '';
  if( hours < 10 ) duration += '0';
  duration += hours + ':';
  if( minutes < 10 ) duration += '0';
  duration += minutes;
  return duration;
}

function crmAddCalendar( id, label ){
  $( '#crm-cal-tab-list' ).append( '<li value="' + id + '"><a href="#crm-cal-' + id + '" class="crm-cal-' + id + '">' +  label + '</a>' + ( ( id != 0 )? '<button style="border: 0" onclick="crmEditCalendarTitle(' + id + ', \'' + label + '\')"><img src="../image/edit.png"></img></button>' : '' ) + '</li>' );
  $( '#crm-cal-tabs' ).append( '<div id="crm-cal-' + id + '" class="crm-cal-tab"></div>' );
  var calendar = new FullCalendar.Calendar( document.getElementById( 'crm-cal-' + id ), {
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
    navLinks: true,
    headerToolbar: {
      left: 'prevYear,prev,today,next,nextYear',
      center: 'title',
      right: 'timeGridDay,timeGridFourDay,weekEvents'
    },
    events: { url: '../ajax/crm.app.php?action=getCalendarEvents&employee=' + crmEmployee + '&category=' + id, method: 'GET' },
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
      $( "#crm-edit-event-cvp-id" ).val( info.event._def.extendedProps.cvp_id );
      $( "#crm-edit-event-cvp-type" ).val( info.event._def.extendedProps.cvp_type );
      $( "#crm-edit-event-car-id" ).val( info.event._def.extendedProps.car_id );
      $( "#crm-edit-event-order-id" ).val( info.event._def.extendedProps.order_id );
      $( "#crm-edit-event-title" ).val( info.event.title );
      $( "#crm-edit-event-description" ).text( info.event._def.extendedProps.description );
      $( "#crm-edit-event-id" ).val( info.event.id );
      $( "#crm-edit-event-full-time" ).prop( 'checked', info.event.allDay );
      $( "#crm-edit-event-start" ).val( moment( info.event.start ).format( "DD.MM.YYYY HH:mm") );
      $( "#crm-edit-event-end" ).val( moment( info.event.end ? info.event.end : info.event.start ).format( "DD.MM.YYYY HH:mm") );
      $( '#crm-edit-event-prio' ).val( info.event._def.extendedProps.prio );
      $( '#crm-edit-event-freq' ).val( info.event._def.extendedProps.freq.trim() );
      $( "#crm-edit-event-interval" ).val( info.event._def.extendedProps.interval );
      $( "#crm-edit-event-count" ).val( info.event._def.extendedProps.count );
      $( "#crm-edit-event-repeat-end" ).val( moment( info.event._def.extendedProps.until ).format( "DD.MM.YYYY")  );
      $( '#crm-edit-event-customer' ).val( info.event._def.extendedProps.cvp_name );
      $( '#crm-edit-event-location' ).val( info.event._def.extendedProps.location );
      $( '#crm-edit-event-category' ).val( info.event._def.extendedProps.category );
      $( '#crm-edit-event-visibility' ).val( info.event._def.extendedProps.visibility );
      $( '#crm-edit-event-car' ).html( '' );
      $( '#crm-edit-event-car' ).append( '<option value=""></option>' );
      if( '' != $( "#crm-edit-event-car-id" ).val() ) crmGetCarsForCalendar( $( "#crm-edit-event-cvp-type" ).val(), $( "#crm-edit-event-cvp-id" ).val(), function(){
          $( '#crm-edit-event-car' ).val( $( "#crm-edit-event-car-id" ).val() ); $( '#crm-edit-event-dialog' ).dialog( 'open' );
      });
      else $( '#crm-edit-event-dialog' ).dialog( 'open' );
    },
    select: function( info ) {
      console.info( 'info', info );
      $( "#crm-edit-event-cvp-id" ).val( '' );
      $( "#crm-edit-event-cvp-type" ).val( '' );
      $( "#crm-edit-event-car-id" ).val( '' );
      $( "#crm-edit-event-order-id" ).val( '' );
      $( "#crm-edit-event-title" ).val( '' );
      $( "#crm-edit-event-description" ).text( '' );
      $( "#crm-edit-event-id" ).val( '' );
      $( "#crm-edit-event-full-time" ).prop( 'checked', false );
      $( "#crm-edit-event-start" ).val( moment( info.event.start ).format( "DD.MM.YYYY HH:mm") );
      $( "#crm-edit-event-end" ).val( moment( info.event.end ? info.event.end : info.event.start ).format( "DD.MM.YYYY HH:mm") );
      $( '#crm-edit-event-prio' ).val( '0' );
      $( '#crm-edit-event-freq' ).val( '0' );
      $( "#crm-edit-event-count" ).val( '1' );
      $( "#crm-edit-event-interval" ).val( '1' );
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
      const start = moment( info.event.start );
      const end = moment( info.event.end );
      dbUpdateData = {};//jsonobj für die Datenbankupdate (genericUpdateEx)
      dbUpdateData['calendar_events'] = {};
      dbUpdateData['calendar_events']['dtstart'] = start.format( "YYYY-MM-DD HH:mm:ss" );
      dbUpdateData['calendar_events']['dtend'] = end.format( "YYYY-MM-DD HH:mm:ss" );
      dbUpdateData['calendar_events']['duration'] = crmCalendarEventDuration( start, end );
      dbUpdateData['calendar_events']['WHERE'] = {};
      dbUpdateData['calendar_events']['WHERE'] = 'id = ' + info.event.id;

      $.ajax({
        url: '../ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'genericUpdateEx', data: dbUpdateData },
        error: function( xhr, status, error ){
          alert( 'Error: ' + xhr.responseText );
        }
      });
    },
    eventDrop: function( info ) {
      const start = moment( info.event.start );
      const end = moment( info.event.end );
      dbUpdateData = {};//jsonobj für die Datenbankupdate (genericUpdateEx)
      dbUpdateData['calendar_events'] = {};
      dbUpdateData['calendar_events']['dtstart'] = start.format( "YYYY-MM-DD HH:mm:ss" );
      dbUpdateData['calendar_events']['dtend'] = end.format( "YYYY-MM-DD HH:mm:ss" );
      dbUpdateData['calendar_events']['duration'] = crmCalendarEventDuration( start, end );
      dbUpdateData['calendar_events']['WHERE'] = {};
      dbUpdateData['calendar_events']['WHERE'] = 'id = ' + info.event.id;

      $.ajax({
        url: '../ajax/crm.app.php',
        type: 'POST',
        data:  { action: 'genericUpdateEx', data: dbUpdateData },
        error: function( xhr, status, error ){
          alert( 'Error: ' + xhr.responseText );
        }
      });
    }
  });

  calendar.render();
  crmCalendarInstances.push( { 'id': id, 'calendar': calendar } );

  $( '#crm-edit-event-category' ).append( '<option value="' + id + '">' + label + '</option>' );
}

document.addEventListener('DOMContentLoaded', function() {
  $.ajax({
    url: '../ajax/crm.app.php',
    type: 'POST',
    data:  { action: 'getCalenderCategories' },
    success: function( crmCalendarData ){

      //console.info( 'crmCalendarData', crmCalendarData );
      for( let entry of crmCalendarData ){
        crmAddCalendar( entry.id, entry.label );
      }

      $( '#crm-cal-tab-list' ).sortable({
        cancel: 'li:first-child',
        start: function( event, ui ){
          crmCategoryTmpIndex = ui.item.index() - 1;
        },
        update: function( event, ui ){
          const tmp = crmCalendarInstances.splice( crmCategoryTmpIndex, 1 );
          crmCalendarInstances.splice( ui.item.index() - 1, 0, tmp[0] );

          dbUpdateData = [];//jsonobj für die Datenbankupdate (genericUpdateEx);
          for( let i = 0; i < crmCalendarInstances.length; i++ ){
            const dupel = [];
            dupel.push( crmCalendarInstances[i].id );
            dupel.push( i );
            dbUpdateData.push( dupel );;
          }

          $.ajax({
            url: '../ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'updateEventCategoriesOrder', data: dbUpdateData },
            error: function( xhr, status, error ){
              alert( 'Error: ' + xhr.responseText );
            }
          });
        }
      });

    $( "#crm-cal-tabs" ).tabs({
      activate: function( event, ui ){
          //den angeklickten Calendar rendern
          crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ].calendar.render();
       }
    }).show();
      for( let employee of crmEmployeeGroups ){
        $( '#crm-edit-event-visibility' ).append( '<option value="' + employee.value + '">' + employee.text + '</option>' );
      }
    },
    error: function( xhr, status, error ){
      alert( 'Error: ' + xhr.responseText );
    }
  });

  $( '#crm-edit-event-car' ).append( '<option value=""></option>' );

  $( "#crm-edit-event-start1, #crm-edit-event-end, #crm-edit-event-repeat-end" ).datetimepicker({
    //onChangeDateTime: function( current_time, $input ){
    //  crmCalculateEnd();
    //},
    lang: 'de',
    minTime: '08:00',
    maxTime: '17:00',
    format:'d.m.Y H:i',
    timepicker: true
  });

  $( "#crm-edit-event-start" ).datetimepicker({
    lang: 'de',
    minTime: '08:00',
    maxTime: '17:00',
    format:'d.m.Y H:i',
    timepicker: true
  });

  $( "#crm-edit-event-repeat-end" ).datetimepicker({
    lang: 'de',
    format:'d.m.Y',
    timepicker: false
  });

  $( '#crm-edit-event-colorpicker' ).colorPicker({
    columns: 13,     // number of columns (optional)
    color: ['#FF7400', '#CDEB8B','#6BBA70','#006E2E','#C3D9FF','#0101DF','#4096EE','#356AA0','#FF0096','#DF0101','#B02B2C','#112211','#000000'], // list of colors (optional)
    click: function( color ){
        $( '#crm-edit-event-color' ).val( color );
    },
  });

  $( '#crm-new-color-colorpicker' ).colorPicker({
    columns: 13,     // number of columns (optional)
    color: ['#FF7400', '#CDEB8B','#6BBA70','#006E2E','#C3D9FF','#0101DF','#4096EE','#356AA0','#FF0096','#DF0101','#B02B2C','#112211','#000000'], // list of colors (optional)
    click: function( color ){
        $( '#crm-new-calendar-color' ).val( color );
    },
  });

  $( "#crm-edit-event-freq, #crm-edit-event-interval, #crm-edit-event-count" ).change( crmCalculateEnd );
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
      $( "#crm-edit-event-cvp-id" ).val( ui.item.id );
      $( '#crm-edit-event-to-cvp' ).show();

      crmGetCarsForCalendar( ui.item.src, ui.item.id );
    }
  });

  var crmEventTmp = {};

  $( '#crm-edit-event-dialog' ).dialog({
    autoOpen: false,
    height: 790,
    width: 630,
    modal: true,
    open: function() {
      if( $( "#crm-edit-event-cvp-id" ).val() != '' ) $( '#crm-edit-event-to-cvp' ).show();
      else $( '#crm-edit-event-to-cvp' ).hide();
      if( $( "#crm-edit-event-order-id" ).val() != '' ) $( '#crm-edit-event-to-order' ).show();
      else $( '#crm-edit-event-to-order' ).hide();
      if( $( "#crm-edit-event-car-id" ).val() != '' ) $( '#crm-edit-event-to-car' ).show();
      else $( '#crm-edit-event-to-car' ).hide();
      crmCalculateEnd();
    },
    buttons: {
        "Save": function() {
          dbUpdateData = {};//jsonobj für die Datenbankupdate (genericUpdateEx)
          dbUpdateData['calendar_events'] = {};

          let start = moment($( "#crm-edit-event-start" ).val(), 'DD.MM.YYYY HH:mm');
          let end = moment($( "#crm-edit-event-end" ).val(), 'DD.MM.YYYY HH:mm');
          let repeat_end = moment($( "#crm-edit-event-repeat-end" ).val(), 'DD.MM.YYYY HH:mm');

          let duration;
          if( $( "#crm-edit-event-full-time" ).is( ":checked" ) ){
            duration = '24:00';
          }
          else{
            duration = crmCalendarEventDuration( start, end );
            if( 'Invalid date' == duration ){
              alert( kivi.t8( 'Invalid date' ) );
              return;
            }
          }

          //if( start.format('HH:mm') < '07:00' || start.format('HH:mm') > '19:00' || end.format('HH:mm') < '07:00' || end.format('HH:mm') > '19:00'){
          //  alert( kivi.t8( 'Start date must be between 8:00 a.m. and 5:00 p.m' ) );
          //  return;
          //}

          dbUpdateData['calendar_events']['dtstart'] = start.format('YYYY-MM-DD HH:mm:ss');
          dbUpdateData['calendar_events']['dtend'] = end.format('YYYY-MM-DD HH:mm:ss');
          dbUpdateData['calendar_events']['duration'] = duration;
          dbUpdateData['calendar_events']['repeat_end'] = repeat_end.format('YYYY-MM-DD HH:mm:ss');
          dbUpdateData['calendar_events']['title'] = $( "#crm-edit-event-title" ).val();
          dbUpdateData['calendar_events']['description'] = $( "#crm-edit-event-description" ).val();
          dbUpdateData['calendar_events']['\"allDay\"'] = $( "#crm-edit-event-full-time" ).is( ":checked" );
          dbUpdateData['calendar_events']['uid'] = crmEmployee;
          dbUpdateData['calendar_events']['visibility'] = $( "#crm-edit-event-visibility option:selected" ).val();
          dbUpdateData['calendar_events']['category'] = $( "#crm-edit-event-category option:selected" ).val();
          dbUpdateData['calendar_events']['prio'] = $( "#crm-edit-event-prio option:selected" ).val();
          dbUpdateData['calendar_events']['color'] = $( "#crm-edit-event-color" ).val();
          dbUpdateData['calendar_events']['location'] = $( "#crm-edit-event-location" ).val();
          dbUpdateData['calendar_events']['freq'] = $( "#crm-edit-event-freq" ).val();
          dbUpdateData['calendar_events']['interval'] = $( "#crm-edit-event-interval" ).val();
          if( 0 >= dbUpdateData['calendar_events']['interval'] ) dbUpdateData['calendar_events']['interval'] = 1;
          dbUpdateData['calendar_events']['count'] = $( "#crm-edit-event-count" ).val();
          if( 0 >= dbUpdateData['calendar_events']['count'] ) dbUpdateData['calendar_events']['count'] = 1;
          if( $( "#crm-edit-event-cvp-id" ).val() != '' ) dbUpdateData['calendar_events']['cvp_id'] = $( "#crm-edit-event-cvp-id" ).val();
          if( $( "#crm-edit-event-cvp-type" ).val() != '' ) dbUpdateData['calendar_events']['cvp_type'] = $( "#crm-edit-event-cvp-type" ).val();
          if( $( "#crm-edit-event-customer" ).val() != '' ) dbUpdateData['calendar_events']['cvp_name'] = $( "#crm-edit-event-customer" ).val();
          if( $( "#crm-edit-event-car-id" ).val() != '' ) dbUpdateData['calendar_events']['car_id'] = $( "#crm-edit-event-car-id" ).val();
          if( $( "#crm-edit-event-order-id" ).val() != '' ) dbUpdateData['calendar_events']['order_id'] = $( "#crm-edit-event-order-id" ).val();

          crmEventTmp = { ...dbUpdateData['calendar_events'] }; // Objekt wird später der Kalendarinstanz direkt übergeben um die Darstellung zu aktualisieren
          crmEventTmp['allDay'] = crmEventTmp['\"allDay\"'];
          delete crmEventTmp['\"allDay\"'];
          crmEventTmp['rrule'] = { 'count': parseInt( dbUpdateData['calendar_events']['count'] ),
                                'dtstart': dbUpdateData['calendar_events']['dtstart'],
                                'interval': parseInt( dbUpdateData['calendar_events']['interval'] ),
                                'freq': dbUpdateData['calendar_events']['freq'] };

          let functionName = 'genericSingleInsertGetId';

          if( '' != $( '#crm-edit-event-id' ).val() ){
            dbUpdateData['calendar_events']['WHERE'] = {};
            dbUpdateData['calendar_events']['WHERE'] = 'id = ' + $( '#crm-edit-event-id' ).val();
            functionName = 'genericUpdateEx';
            crmEventTmp['id'] = $( '#crm-edit-event-id' ).val();
          }
          else{
            let tmpData = {};
            tmpData['record'] = {};
            tmpData['record']['calendar_events'] = dbUpdateData['calendar_events'];
            dbUpdateData = tmpData;
          }

          $.ajax({
            url: '../ajax/crm.app.php',
            type: 'POST',
            data:  { action: functionName, data: dbUpdateData },
            success: function( data ){
              for( let calendarInstance of crmCalendarInstances ){
                calendarInstance.calendar.refetchEvents();
              }
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
          pos['calendar_events'] = {};
          pos['calendar_events']['WHERE'] = 'id = ' + $( '#crm-edit-event-id' ).val();

          $.ajax({
              url: '../ajax/crm.app.php',
              type: 'POST',
              data:  { action: 'genericDelete', data: pos },
              success: function( data ){
                crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ].calendar.getEventById( $( '#crm-edit-event-id' ).val() ).remove();
                for( let calendarInstance of crmCalendarInstances ){
                  calendarInstance.calendar.refetchEvents();
                }
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

  $( "#crm-calendar-goto" ).datetimepicker({
    lang: 'de',
    format:'d.m.Y',
    timepicker: false
  });

  $( "#crm-calendar-goto" ).change( function(){
    const date = moment( $( this ).val(), 'DD.MM.YYYY' );
    for( let calendarInstance of crmCalendarInstances ){
      calendarInstance.calendar.gotoDate( date.format( 'YYYY-MM-DD' ) );
    }
  });

  $( '#crm-calendar-goto' ).val( moment().format( 'DD.MM.YYYY' ) );
});

function crmGetCarsForCalendar( src, id, fx ){
  if( 'C' == src || 'A' == src ){
    $( "#crm-edit-event-cvp-type" ).val( src );
    $.ajax({
      url: '../ajax/crm.app.php',
      type: 'POST',
      data:  { action: 'getCarsForCalendar', data: { id: id } },
      success: function( data ){
        $( "#crm-edit-event-car-id" ).val();
        $( '#crm-edit-event-car' ).html( '' );
        $( '#crm-edit-event-car' ).append( '<option value=""></option>' );
        for( let car of data ){
          $( '#crm-edit-event-car' ).append( '<option value="' + car.c_id + '">' + car.label + '</option>' );
        }
        fx();
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

function crmCalendarToOrder(){
  parent.postMessage( { 'openOrder': $( '#crm-edit-event-order-id' ).val() }, '*' );
}

function crmCalendarToCar(){
  parent.postMessage( { 'openCar': $( '#crm-edit-event-car-id' ).val() }, '*' );
}

function crmCalendarToCustomer(){
  parent.postMessage( { 'openCustomer': { 'src': $( '#crm-edit-event-cvp-type' ).val(), 'id': $( '#crm-edit-event-cvp-id' ).val() } }, '*' );
}

function crmEditCalendarTitle( id, label ){
  $( '#crm-edit-calendar-title-id' ).val( id );
  $( '#crm-edit-calendar-title' ).val( label );
  $( '#crm-edit-calendar-title-dialog' ).dialog({
    title: kivi.t8( 'Edit calendar title' ),
    autoOpen: false,
    height: 190,
    width: 350,
    modal: true,
    buttons: {
      "Save": function() {
        dbUpdateData = {};//jsonobj für die Datenbankupdate (genericUpdateEx)
        dbUpdateData['event_category'] = {};
        dbUpdateData['event_category']['label'] = $( '#crm-edit-calendar-title' ).val();
        dbUpdateData['event_category']['WHERE'] = {};
        dbUpdateData['event_category']['WHERE'] = 'id = ' + $( '#crm-edit-calendar-title-id' ).val();

        $.ajax({
          url: '../ajax/crm.app.php',
          type: 'POST',
          data:  { action: 'genericUpdateEx', data: dbUpdateData },
          success: function( data ){
            $( '.crm-cal-' + $( '#crm-edit-calendar-title-id' ).val() ).text( $( '#crm-edit-calendar-title' ).val() );
            $( '#crm-edit-calendar-title-dialog' ).dialog( "close" );
          },
          error: function( xhr, status, error ){
            alert( 'Error: ' + xhr.responseText );
          }
        });
      },
      "Delete": function() {
        let pos = {};
        pos['event_category'] = {};
        pos['event_category']['WHERE'] = 'id = ' + $( '#crm-edit-calendar-title-id' ).val();

        $.ajax({
            url: '../ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'genericDelete', data: pos },
            success: function( data ){
              const tab = $( '#crm-cal-tabs' ).tabs( "option", "active" );
              crmCalendarInstances.splice( tab, 1 );
              $( '#crm-cal-tabs' ).tabs('remove', tab);
              console.info( 'crmCalendarInstances', crmCalendarInstances );
              $( '#crm-edit-event-dialog' ).dialog( "close" );
            },
            error: function( xhr, status, error ){
              alert( 'Error: ' + xhr.responseText );
            }
        });
        $( this ).dialog( "close" );
      },
      "Close": function() {
        $( this ).dialog( "close" );
      }
    }
  }).dialog( 'open' );
}

function crmNewCalender(){
  $( '#crm-new-calendar-dialog' ).dialog({
    title: kivi.t8( 'New calendar' ),
    autoOpen: false,
    height: 250,
    width: 300,
    modal: true,
    buttons: {
      "Save": function() {
        $.ajax({
          url: '../ajax/crm.app.php',
          type: 'POST',
          data:  { action: 'insertNewCalendar', data: { label: $( '#crm-new-calendar-title' ).val(), color: $( '#crm-new-calendar-color' ).val() } },
          success: function( data ){
            crmAddCalendar( data.id, $( '#crm-new-calendar-title' ).val() );
            $( '#crm-cal-tabs' ).tabs("refresh");
            $( '#crm-new-calendar-dialog' ).dialog( "close" );
          },
          error: function( xhr, status, error ){
            alert( 'Error: ' + xhr.responseText );
          }
        });
      },
      "Close": function() {
        $( this ).dialog( "close" );
      }
    }
  }).dialog( 'open' );
};