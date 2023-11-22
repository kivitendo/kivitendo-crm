document.getElementById('calculateButton').addEventListener('click', function() {
    // Definieren der Wiederholungsregel
    /*
    var rule = new RRule({
        freq: RRule.WEEKLY,
        interval: 1,
        byweekday: [RRule.MO],
        count: 10,
        dtstart: new Date(2023, 0, 1, 10, 0)
    });

    // Berechnen aller Vorkommen
    var allOccurrences = rule.all();

    // Das Enddatum ist das letzte Element in allOccurrences
    var endDate = allOccurrences[allOccurrences.length - 1];

    // Ergebnis im HTML-Dokument anzeigen
    document.getElementById('result').textContent = "Enddatum der Wiederholung: " + endDate.toISOString();
    */
    //console.info(rrule.rrulestr('DTSTART:20120201T023000Z\nRRULE:FREQ=MONTHLY;COUNT=5'));

    const RRule = rrule.RRule;

    const rule = new RRule({
        count: 10,
        freq: RRule.WEEKLY,
        interval: 5,
        byweekday: [RRule.MO, RRule.FR],
        dtstart: new Date(Date.UTC(2015, 1, 1, 10, 30)),
        //until: new Date(Date.UTC(2020, 12, 31))
    });

    const all = rule.all(); 
    console.info('rrule test', all[all.length - 1]);
});
