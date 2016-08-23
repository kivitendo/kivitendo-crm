--drop table if exists contact_events;

CREATE TABLE contact_events
(
  id serial,
  cause text,
  caller_id integer,
  calldate timestamp without time zone,
  cause_long text,
  employee integer,
  contact_reference integer default 0,
  inout character(1),
  calendar_event integer,
  type_of_contact integer
)