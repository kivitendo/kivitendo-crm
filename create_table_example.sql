--drop table if exists example;

CREATE TABLE example
(
  id serial,
  date_time timestamp without time zone,
  c_name text,
  c_age integer,
  c_comments text
)