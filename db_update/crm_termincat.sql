-- @tag: termincat
-- @description: Kattegorien für Termine

CREATE TABLE termincat (
    catid int,
    catname text,
    sorder int
);

ALTER TABLE termincat ADD  primary key (catid);
ALTER TABLE termine ADD COLUMN kategorie int;
ALTER TABLE termine ALTER COLUMN kategorie SET DEFAULT 0;
UPDATE termine SET kategorie = 0;
