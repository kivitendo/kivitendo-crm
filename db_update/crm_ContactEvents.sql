-- @tag: ContactEvents
-- @description: save customer vendor contacts
-- @version: 2.2.3

DROP TABLE  IF EXISTS contact_events;
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
    type_of_contact character(1),
    document integer
);

INSERT INTO contact_events (id, cause, caller_id, calldate, cause_long, employee, contact_reference, "inout", type_of_contact, document)
    SELECT id, cause, caller_id, calldate, c_long, employee, bezug, "inout", kontakt, dokument FROM telcall;

-- @exec
