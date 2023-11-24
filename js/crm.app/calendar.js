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
  $( '#crm-edit-event-repeat-end' ).val( (new Date( all[all.length -1] )).toLocaleString() );
};

var crmCalculateRepeatQuantity = function() {
  const a = moment( $( "#crm-edit-event-end" ).val(), 'DD.MM.YYYY HH:mm' );
  const b = moment( $( "#crm-edit-event-repeat-end" ).val(), 'DD.MM.YYYY HH:mm' );
  const erg = Math.floor( ( b.diff( a, crmMomentFreqMap[$( "#crm-edit-event-freq" ).val()] ) ) / parseFloat( $( "#crm-edit-event-interval" ).val() ) );
  $( "#crm-edit-event-count" ).val( erg < 0 ? 0 : erg );
};

document.addEventListener('DOMContentLoaded', function() {
  $.ajax({
    url: '../ajax/crm.app.php',
    type: 'POST',
    data:  { action: 'getCalendarEvents', data: { employee: crmEmployee, start: currentDay, end: fourDaysLater } },
    success: function( crmCalendarData ){

      crmCalendarData.push( { 'id': 'new', 'color': '', 'label': kivi.t8( 'New' ), 'events': []  } );
      //console.info( 'crmCalendarData', crmCalendarData );

      for( let entry of crmCalendarData ){
        $( '#crm-cal-tab-list' ).append( '<li value="' + entry.id + '"><a href="#crm-cal-' + entry.id + '">' +  entry.label + '</a></li>' );
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
            $( '#crm-edit-event-dialog' ).dialog( 'open' );
          },
          select: function( info ) {
            //console.info( 'select', info );
            //console.info( 'tab', $("#crm-cal-tabs .ui-tabs-panel:visible").attr("id") )
            //console.info( 'tab', crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ].calendar );
            $( "#crm-edit-event-cvp-id" ).val( '' );
            $( "#crm-edit-event-cvp-type" ).val( '' );
            $( "#crm-edit-event-car-id" ).val( '' );
            $( "#crm-edit-event-order-id" ).val( '' );
            $( "#crm-edit-event-title" ).val( '' );
            $( "#crm-edit-event-description" ).text( '' );
            $( "#crm-edit-event-id" ).val( '' );
            $( "#crm-edit-event-full-time" ).prop( 'checked', false );
            $( "#crm-edit-event-start" ).val( moment( info.start ).format( "DD.MM.YYYY HH:mm") );
            $( "#crm-edit-event-end" ).val( moment( info.end ? info.end : info.start ).format( "DD.MM.YYYY HH:mm") );
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
        crmCalendarInstances.push( { 'id': entry.id, 'calendar': calendar } );

        $( '#crm-edit-event-category' ).append( '<option value="' + entry.id + '">' + entry.label + '</option>' );
      }

      $( '#crm-cal-tab-list' ).sortable( {
        cancel: 'li:last-child, li:first-child',
        start: function( event, ui ){
          crmCategoryTmpIndex = ui.item.index();
        },
        update: function( event, ui ){

          const tmp = crmCalendarInstances.splice( crmCategoryTmpIndex, 1 );
          crmCalendarInstances.splice( ui.item.index(), 0, tmp[0] );

          dbUpdateData = [];//jsonobj für die Datenbankupdate (genericUpdateEx);
          for( let i = 0; i < crmCalendarInstances.length; i++ ){
            const dupel = [];
            dupel.push( crmCalendarInstances[i].id );
            dupel.push( i );
            dbUpdateData.push( dupel );;
          }
          dbUpdateData.shift();
          dbUpdateData.splice( -1 );

          $.ajax({
            url: '../ajax/crm.app.php',
            type: 'POST',
            data:  { action: 'updateEventCategoriesOrder', data: dbUpdateData },
            error: function( xhr, status, error ){
              alert( 'Error: ' + xhr.responseText );
            }
          });
        }
      } );

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

  $( "#crm-edit-event-start, #crm-edit-event-end, #crm-edit-event-repeat-end" ).datetimepicker({
    lang: 'de',
    minTime: '08:00',
    maxTime: '17:00',
    format:'d.m.Y H:i',
  });

  $( '#crm-edit-event-colorpicker' ).colorPicker({
    //defaultColor: 1,
    columns: 13,     // number of columns (optional)
    color: ['#FF7400', '#CDEB8B','#6BBA70','#006E2E','#C3D9FF','#0101DF','#4096EE','#356AA0','#FF0096','#DF0101','#B02B2C','#112211','#000000'], // list of colors (optional)
    click: function(color){
        $( '#crm-edit-event-color' ).val(color);
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

  var crmEventTmp = {};

  function crmCalendarEventDuration( start, end ){
    const timeDiff = end.diff( start, 'minutes' );
    const hours = Math.floor( timeDiff / 60 );
    const minutes = timeDiff % 60;
    return moment( { hour: hours, minute: minutes } ).format( 'HH:mm' );
  }

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
    },
    buttons: {
        "Save": function() {
          dbUpdateData = {};//jsonobj für die Datenbankupdate (genericUpdateEx)
          dbUpdateData['calendar_events'] = {};

          let start = moment($( "#crm-edit-event-start" ).val(), 'DD.MM.YYYY hh:mm:ss');
          let end = moment($( "#crm-edit-event-end" ).val(), 'DD.MM.YYYY hh:mm:ss');

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

          if( start.format('HH:mm') < '07:00' || start.format('HH:mm') > '19:00' || end.format('HH:mm') < '07:00' || end.format('HH:mm') > '19:00'){
            alert( kivi.t8( 'Start date must be between 8:00 a.m. and 5:00 p.m' ) );
            return;
          }

          dbUpdateData['calendar_events']['dtstart'] = start.format('YYYY-MM-DD HH:mm:ss');
          dbUpdateData['calendar_events']['dtend'] = end.format('YYYY-MM-DD HH:mm:ss');
          dbUpdateData['calendar_events']['duration'] = duration;
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
              const calendarInstance = crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ].calendar;
              if( crmEventTmp.id !== null && crmEventTmp.id !== undefined ) calendarInstance.getEventById( crmEventTmp.id ).remove();
              if( data.id !== null && data.id !== undefined ) crmEventTmp['id'] = data.id;
              calendarInstance.addEvent( crmEventTmp );
              calendarInstance.refetchEvents();
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

function crmCalendarToOrder(){
  parent.postMessage( { 'openOrder': $( '#crm-edit-event-order-id' ).val() }, '*' );
}

function crmCalendarToCar(){
  parent.postMessage( { 'openCar': $( '#crm-edit-event-car-id' ).val() }, '*' );
}

function crmCalendarToCustomer(){
  parent.postMessage( { 'openCustomer': { 'src': $( '#crm-edit-event-cvp-type' ).val(), 'id': $( '#crm-edit-event-cvp-id' ).val() } }, '*' );
}

function crmOpenEventCategoryDlg(){
  $( '#crm-event-category-dialog' ).dialog({
    autoOpen: false,
    height: 790,
    width: 630,
    modal: true
  }).dialog( 'open' );

  var colorInput = '';
  $.ajax({
    url: '../ajax/event_category.php',
    data: { action: 'getCategories' },
    type: "POST",
    success: function( json ) {
      var max = 0;
      var html = '';
      $.each( json, function( i, val ){
        html  +="<li class='ui-state-default'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span>"
              + "  <input type='text' class='ui-widget-content ui-corner-all left' autocomplete='off' value='" + val.label + "' name='cat'></input>"
              + "  <input type='text' class='ui-widget-content ui-corner-all middle' autocomplete='off' value='" + val.color + "' name='color' maxlength='7'></input>"
              + "  <input type='hidden'  value='" + val.id + "' name='id' ></input>"
              + "  <button class='right delete' title='" + kivi.t8( 'Attention! Delete category!' ) + "' tabIndex='-1'>" + kivi.t8( 'D' ) + "</button>"
              + "</li>";
      });
      $( "#sortable" ).prepend( html );
      $( ".right" ).tooltip({ position: { my: "center bottom-10", at: "center top" } } );
      $( '#colorPick' ).colorPicker({
        columns: 13, //number of columns
        color: ['#FF7400', '#CDEB8B','#6BBA70','#006E2E','#C3D9FF','#0101DF','#4096EE','#356AA0','#FF0096','#DF0101','#B02B2C','#112211','#000000'], //list of colors
        click: function( color ){
          colorInput.value = color;
        }
      });
      $( ".middle" ).click( function(){
        colorInput = this;
        var pos =  $( this ).position();
        $( "#colorPick" ).css({
          position: 'absolute',
          //left: pos.left - 230,
          //top: pos.top
          left: pos.left - 70,
          top: pos.top - 15
        }).toggle();
      });

      $( '.delete' ).click( function(){
        const delId = $(this).prev('input')[0].value;
        const delLine = $(this).parent();
        $.ajax({
          url: '../ajax/event_category.php',
          data: { action:  'deleteCategory', data:delId },
          type: "POST",
          success: function(){
            delLine.remove();
          },
          error: function(){
              alert( 'Error: deleteCategory()!' );
          }
        });
        return false;
      })
    },
    error: function () {
      alert('Error getCategories()!!');
    }
  });

  $( "#save" ).button({
    label: kivi.t8( 'save' )
  }).click( function(){
    var dataArr  = $( "#myform" ).serializeArray();
    $( '#colorPick' ).css( "display" , "none" );

    //;
    //remove name from array
    var onlyValueArr = [];
    var order = 0;
    $.each( dataArr, function( index, value ){
      onlyValueArr[index] = value.value;
    });

    // we have 3 comlumns. category, color, id (hidden)
    var twoDimValueArr = [];
    while( onlyValueArr.length ) twoDimValueArr.push( onlyValueArr.splice( 0, 3 ) );
    //add order
    $.each( twoDimValueArr, function( index, value ){
      twoDimValueArr[index].push( (index + 1) );
    })
    const last = twoDimValueArr.length - 1;
    if( twoDimValueArr[last][0] ){ // last line category is not empty,
      $.ajax({
        url: 'ajax/event_category.php',
        data: { action: 'newCategory', data:[twoDimValueArr[last][0], twoDimValueArr[last][1], twoDimValueArr[last][3] ] },
        type: "POST",
        success: function( lastId ){
          // add id to last hidden input named id
          $( '#sortable li:last input[name=id]' ).attr( 'value', lastId );
          $( '#sortable li:last' ).append( "<span class='ui-icon ui-icon-arrowthick-2-n-s'></span><button class='right delete' tabIndex='-1'>" + kivi.t8( 'D' ) + "</button>" );
          var newLine = "<li class='ui-state-default'>"
                  + "   <input type='text' class='ui-widget-content ui-corner-all left' autocomplete='off' name='cat'></input>"
                  + "   <input type='text' class='ui-widget-content ui-corner-all middle' autocomplete='off' name='color' maxlength='7'></input>"
                  + "   <input type='hidden' name='id' ></input>"
                  + "</li>";
          $( "#sortable" ).append( newLine );
          //$( '#sortable' ).sortable( "refresh" );
          $( '.delete' ).click( function(){
            const delId = lastId;
            const delLine = $(this).parent();
            $.ajax({
              url: 'ajax/event_category.php',
              data: { action:  'deleteCategory', data:delId },
              type: "POST",
              success: function(){
                delLine.remove();
              },
              error: function(){
                  alert( 'Error: deleteCategory()!' );
              }
            });
            return false;
          })

        },
      });
    }
    twoDimValueArr.pop();// remove last array, (it's not in db)
    //console.log( twoDimValueArr );
    $.ajax({
      url: '../ajax/event_category.php',
      data: { action:  'updateCategories', data: twoDimValueArr },
      type: "POST",
    });
  });// end save CLICK

  $( "#calendar" ).button({
    label: kivi.t8( 'Calendar' )
  }).click( function(){
    window.location.href = "calendar.phtml";
  });

  $( '#sortable' ).sortable({
    items: 'li:not(:last-child)',
    update: function(){
      $( "#save" ).click();
    }
  });

  $( '#headline' ).text( kivi.t8( 'Evemt category' ) );
  $( '#head_category' ).text( kivi.t8( 'Category' ) );
  $( '#head_color' ).text( kivi.t8( 'Color' ) );

}