-- @tag: createTableExample
-- @description: creates table example
-- @version: 2.2.2


DROP TABLE IF EXISTS example;

CREATE TABLE example
(
  id serial,
  date_time timestamp without time zone,
  c_name text,
  c_age integer,
  c_comments text
);
