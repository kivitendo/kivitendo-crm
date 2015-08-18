-- @tag: Calendar
-- @description: Table events, event_category for Calendar imported from termine 

--Starttag
CREATE TABLE event_category( 
    id      serial NOT NULL PRIMARY KEY,
    label   text,
	   color 	 char(7)
);

CREATE TABLE events(
    id              serial NOT NULL PRIMARY KEY,
    title 		        text,
    description     text,
    location        text,
    start 		        timestamp without time zone,
    stop 			        timestamp without time zone,
    repeat		        char(16),
    repeat_factor  	smallint,
    repeat_quantity	smallint,
    repeat_end		    timestamp without time zone,
    uid             int,
    prio            smallint,
    category 		     smallint,
    visibility		    smallint,
    allday          boolean,
    color 		        character(7),
    job             boolean,
    done            boolean,
    job_planned_end timestamp without time zone,
    cust_vend_pers  text
);
INSERT INTO event_category ( label, color ) VALUEs ( 'Default-Category', '' );
INSERT INTO event_category ( label, color ) SELECT catname, '#'||ccolor FROM termincat;
INSERT INTO events ( title, description, location, start, stop, repeat, repeat_factor, repeat_end, uid, category, visibility )( SELECT cause AS title, c_cause AS description, location, ( SELECT CASE WHEN ( startZeit IS NULL OR startZeit = '' ) THEN start ELSE start + startZeit::INTERVAL END AS start ),( SELECT CASE WHEN ( stopZeit IS NULL OR stopZeit = '' ) THEN stop ELSE stop + stopZeit::INTERVAL END AS stop ), ( SELECT CASE WHEN repeat=1   THEN 'day' WHEN repeat=2   THEN 'day' WHEN repeat=7   THEN 'week' WHEN repeat=14  THEN 'week' WHEN repeat=30  THEN 'month' WHEN repeat=365 THEN 'year' END ) AS repeat, ( SELECT CASE WHEN repeat=1   THEN 1 WHEN repeat=2   THEN 2 WHEN repeat=7   THEN 1 WHEN repeat=14  THEN 2 WHEN repeat=30  THEN 1 WHEN repeat=365 THEN 1 END ) AS repeat_quantity, ( start + ( ( SELECT CASE WHEN repeat=1   THEN 1 WHEN repeat=2   THEN 2 WHEN repeat=7   THEN 1 WHEN repeat=14  THEN 2 WHEN repeat=30  THEN 1 WHEN repeat=365 THEN 1 END )||( SELECT CASE WHEN repeat=1   THEN 'day' WHEN repeat=2   THEN 'day' WHEN repeat=7   THEN 'week' WHEN repeat=14  THEN 'week' WHEN repeat=30  THEN 'month' WHEN repeat=365 THEN 'year' END ) )::INterVAL ) AS repeat_stop, uid AS uid, kategorie + 1 AS category,  ( CASE WHEN privat=true THEN 2 ELSE 0 END ) AS visibility FROM termine );

ALTER TABLE events ADD  FOREIGN KEY( category ) REFERENCES event_category( id );
DELETE FROM event_category WHERE label = '';



