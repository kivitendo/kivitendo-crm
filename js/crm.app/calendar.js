
const currentDay = moment().format('YYYY-MM-DD');
const fourDaysLater = moment().add(4, 'days').format('YYYY-MM-DD');
var crmCalendarInstances = [];

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
          timeZone: 'UTC',
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
            console.info( 'eventClick', info.event._def.extendedProps );
            $( '#crm-edit-event-dialog' ).dialog( 'open' );
          },
          select: function( info ) {
            console.info( 'select', info );
            //console.info( 'tab', $("#crm-cal-tabs .ui-tabs-panel:visible").attr("id") )
            console.info( 'tab', crmCalendarInstances[ $( '#crm-cal-tabs' ).tabs( "option", "active" ) ] );
          },
          eventResize: function( info ) {
            console.info( 'eventResize', info );
          },
          eventDrop: function( info ) {
            console.info( 'eventDrop', info );
          }
        });

        calendar.render();
        crmCalendarInstances.push( calendar );
      }

      $( "#crm-cal-tabs" ).tabs().show();

    },
    error: function( xhr, status, error ){
        alert( 'Error: ' + error );
    }
  });

  $( '#crm-edit-event-dialog' ).dialog({
    autoOpen: false,
    height: 540,
    width: 740,
    modal: true,
    buttons: {
        "Save": function() {
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
