-- @tag: timetracker
-- @description: Zeiterfassung 

CREATE TABLE timetrack (
    id integer DEFAULT nextval('crmid'::text) NOT NULL,
    fid integer,
    tab char(1),
    ttname text NOT NULL,
    ttdescription text,
    startdate date,
    stopdate date,
    aim integer,
    active boolean DEFAULT 't',
    uid integer NOT NULL
);

CREATE TABLE tt_event (
    id integer DEFAULT nextval('crmid'::text) NOT NULL,
    ttid integer NOT NULL,
    uid integer NOT NULL,
    ttevent text NOT NULL,
    ttstart timestamp without time zone,
    ttstop timestamp without time zone,
    cleared int
);
    
