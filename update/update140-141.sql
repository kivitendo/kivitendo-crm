--Starttag
ALTER TABLE termine ADD COLUMN stag date;
UPDATE termine SET stag = CAST(starttag as date);
ALTER TABLE termine DROP COLUMN starttag;
ALTER TABLE termine RENAME COLUMN stag TO starttag;
-- Stoptag
ALTER TABLE termine ADD COLUMN stag date;
UPDATE termine SET stag = CAST(stoptag as date);
ALTER TABLE termine DROP COLUMN stoptag;
ALTER TABLE termine RENAME COLUMN stag TO stoptag;

CREATE INDEX t_starttag_key ON termine USING btree (starttag);
CREATE INDEX t_stoptag_key ON termine USING btree (stoptag);

ALTER TABLE termdate ADD COLUMN idx integer;

CREATE TABLE sonderflag (
    svalue int,
    skey text,
    sorder int
);
INSERT INTO  sonderflag (svalue,skey,sorder) VALUES (1,'News',1);
INSERT INTO  sonderflag (svalue,skey,sorder) VALUES (2,'Test 1',2);

