document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      themeSystem: 'bootstrap5',
      locale: 'de',
      timeZone: 'UTC',
      initialView: 'timeGridFourDay',
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
          duration: { days: 7 },
          buttonText: 'Events'
        }
      }
    });

    calendar.render();
});
