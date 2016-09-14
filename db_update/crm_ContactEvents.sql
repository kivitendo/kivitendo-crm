-- @tag: ContactEvents
-- @description: save customer vendor contacts
-- @version: 2.2.3

DROP TABLE IF EXISTS tmp;

CREATE TABLE tmp(
    id serial,
    cause text,
    caller_id integer,
    calldate timestamp without time zone,
    cause_long text,
    employee integer,
    contact_reference integer DEFAULT 0,
    "inout" character(1),
    calendar_event integer,
    type_of_contact integer
);

INSERT INTO tmp SELECT * FROM contact_events;

DROP TABLE contact_events;

CREATE TABLE contact_events(
    id serial,
    cause text,
    caller_id integer,
    calldate timestamp without time zone,
  cause_long text,
  employee integer,
  contact_reference integer DEFAULT 0,
  "inout" character(1),
  calendar_event integer,
  type_of_contact integer);

INSERT INTO contact_events SELECT * FROM tmp;
DROP TABLE tmp;

-- @exec