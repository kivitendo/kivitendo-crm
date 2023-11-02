document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      themeSystem: 'bootstrap5',
      locale: 'de',
      timeZone: 'UTC',
      initialView: 'timeGridFourDay',
      selectable: true,
      headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: 'timeGridDay,timeGridFourDay,weekEvents'
      },
      views: {
        timeGridFourDay: {
          type: 'timeGrid',
          duration: { days: 5 },
          buttonText: '5 days'
        },
        weekEvents: {
          type: 'listWeek',
          duration: { days: 5 },
          buttonText: 'Events'
        }
      },
      dateClick: function(info) {
        alert('clicked ' + moment(info.dateStr));
      },
      select: function(info) {
        alert('selected ' + moment(info.startStr + ' to ' + info.endStr));
      }
    });

    calendar.render();
});

console.info( 'calendar.js loaded', crmEmployee );
const currentDay = moment().format('YYYY-MM-DD');
const fourDaysLater = moment().add(4, 'days').format('YYYY-MM-DD');
console.info( 'currentDay', currentDay );
console.info( 'fourDaysLater', fourDaysLater );

$.ajax({
    url: '../ajax/crm.app.php',
    type: 'POST',
    data:  { action: 'getCalendarEvents', data: { employee: crmEmployee, start: currentDay, end: fiveDaysLater } },
    success: function( data ){
        console.log( data );
    },
    error: function( xhr, status, error ){
        alert( 'Error: ' + error );
    }
});
