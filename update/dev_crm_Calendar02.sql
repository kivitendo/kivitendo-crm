DROP TABLE IF EXISTS events_tst CASCADE;
CREATE TABLE events_tst(
    id                         serial NOT NULL PRIMARY KEY,
    title               TEXT,
    start               TIMESTAMP WITHOUT TIME ZONE,
    stop               TIMESTAMP WITHOUT TIME ZONE,
    repeat              CHAR(5), --day, week, month, year
    repeat_factor     SMALLINT, -- alle n Tage, Wochen, ..
    repeat_quantity   SMALLINT, -- wie oft soll wiederholt werden bzw...
    repeat_end      TIMESTAMP WITHOUT TIME ZONE -- bis wann soll wiederholt werden
);
INSERT INTO events_tst ( title, start, stop, repeat, repeat_factor, repeat_quantity ) VALUES ( 'ein ganz normales Event ohne Wdhl', '2014-07-12 10:00:00'::TIMESTAMP, '2014-07-12 11:00:00'::TIMESTAMP, '',0 ,0);
INSERT INTO events_tst ( title, start, stop, repeat, repeat_factor, repeat_quantity ) VALUES ( 'Wiederholung alle 2 Tage 3 mal', '2014-06-03 10:00:00'::TIMESTAMP, '2014-06-03 11:00:00'::TIMESTAMP, 'day',2 ,100);
INSERT INTO events_tst ( title, start, stop, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( 'Wdhl alle 6 Wochen bis Weihnachten', '2014-05-28 10:00:00'::TIMESTAMP, '2014-05-28 11:00:00'::TIMESTAMP, 'week',6 ,6, '2015-12-25'::TIMESTAMP);
INSERT INTO events_tst ( title, start, stop, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( 'Wdhl alle 57 Tage , Blut spenden ', '2014-05-14 18:00:00'::TIMESTAMP, '2014-05-14 18:45:00'::TIMESTAMP, 'day',57 ,1, NULL);

--SELECT * FROM events_tst WHERE start <= '2014-07-14' AND stop >= '2014-07-07'OR repeat != '';
--DROP FUNCTION getEvents() ;
CREATE OR REPLACE FUNCTION getEvents( TIMESTAMP, TIMESTAMP ) RETURNS SETOF events_tst AS $$
DECLARE 
	start ALIAS FOR $1;
	stop ALIAS FOR $2;
	n INT := 0;
	tst BOOL;
	event events_tst%ROWTYPE;
BEGIN 
	FOR event IN SELECT * FROM events_tst
    WHERE id > 0
    LOOP
	-- Events ohne Wdhl zw. start und stop liefern
	IF event.start <= stop AND event.stop >= start THEN
		RETURN NEXT event; 
	-- Events mit Wdhl
	ELSEIF event.repeat_factor > 0 THEN
		-- Wdhl-Ende vor start 
		IF event.repeat_end < start THEN 
			RAISE NOTICE 'event.repeat_end < start';
			CONTINUE;
		END IF;
		-- Prüfen ob Wdhl zw. start und stop liegt
		FOR n IN 1 .. event.repeat_quantity  LOOP
			IF event.start + ( n * event.repeat_factor || ' ' || event.repeat )::INTERVAL <= stop AND event.stop + ( n * event.repeat_factor || ' ' || event.repeat )::INTERVAL >= start  THEN
				event.start := event.start + (event.repeat_factor * n || ' ' || event.repeat)::INTERVAL;
				event.stop := event.stop + (event.repeat_factor * n || ' ' || event.repeat)::INTERVAL;
				RAISE NOTICE 'NEW EVENT';
				RETURN NEXT event;
			END IF;
			--RAISE NOTICE 'n = %', n;
			--RAISE NOTICE 'repeat_quantity = %', event.repeat_quantity;
			--RAISE NOTICE 'n <= kleiner gleich repeat_quantity?? %', n <= event.repeat_quantity;
		END LOOP;
		
		--event.start := event.start + (event.repeat_factor || ' ' || event.repeat)::INTERVAL;
		--RETURN NEXT event;
	END IF;

        
    END LOOP;


END;
$$ LANGUAGE 'plpgsql';

SELECT * FROM getEvents('2014-07-07','2014-07-14');