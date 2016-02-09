-- @tag: wvhistory
-- @description: Timestamp statt Datum

ALTER TABLE history ADD COLUMN  tmp timestamp without time zone default now();
UPDATE history set tmp = datum;
ALTER TABLE history DROP datum;
ALTER TABLE history RENAME tmp TO itime;
