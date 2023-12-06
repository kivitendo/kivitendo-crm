
-- @tag: AppCalendar
-- @description: Upgrade script for calendar function in crm_app
-- @version: 2.3.4

CREATE TABLE IF NOT EXISTS calendar_events
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    title text,
    description text,
    dtstart timestamp without time zone,
    dtend timestamp without time zone,
    repeat_end timestamp without time zone,
    duration text,
    freq text,
    interval integer,
    count integer,
    byweekday text,
    location text,
    uid integer,
    prio integer,
    category integer,
    visibility integer,
    "allDay" boolean,
    color text,
    cvp_id integer,
    order_id integer,
    car_id integer,
    cvp_name text,
    cvp_type character(1)
);


INSERT INTO event_category (id, label, color, cat_order)
SELECT 3, 'Werkstatt-Plan', '#111', ( SELECT count( * ) FROM event_category )
WHERE NOT EXISTS (
    SELECT 1 FROM event_category WHERE id = 3
);



INSERT INTO calendar_events (title, description, dtstart, dtend, repeat_end, duration, freq, interval, count, uid, prio, category, visibility, "allDay", color)
SELECT title, description, lower( duration ) AS dtstart, upper( duration ) AS dtend, repeat_end, to_char( ( upper( duration ) - lower( duration ) ), 'HH24:MI' ) AS duration, REPLACE( REPLACE( REPLACE( REPLACE( repeat, 'year', 'yearly' ), 'month', 'monthly' ), 'week', 'weekly' ), 'day', 'daily' ) AS freq, repeat_factor AS interval, repeat_quantity AS count, uid, prio, category, visibility, "allDay", color FROM events WHERE repeat_end IS NOT NULL AND repeat_end >= current_date AND "allDay" = false;

INSERT INTO calendar_events (title, description, dtstart, dtend, repeat_end, duration, freq, interval, count, uid, prio, category, visibility, "allDay", color)
SELECT title, description, lower( duration ) AS dtstart, upper( duration ) AS dtend, repeat_end, '24:00' AS duration, REPLACE( REPLACE( REPLACE( REPLACE( repeat, 'year', 'yearly' ), 'month', 'monthly' ), 'week', 'weekly' ), 'day', 'daily' ) AS freq, repeat_factor AS interval, repeat_quantity AS count, uid, prio, category, visibility, "allDay", color FROM events WHERE repeat_end IS NOT NULL AND repeat_end >= current_date AND "allDay" = true;

