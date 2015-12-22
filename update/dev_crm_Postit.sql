-- @tag: Postit
-- @description: Adds color and position to postit   
--
-- Starttag


DROP TABLE IF EXISTS postit_tmp CASCADE;
CREATE TABLE postit_tmp(
    id              SERIAL NOT NULL PRIMARY KEY,
    cause           TEXT,
    notes           TEXT,
    employee        INT, 
    date  	    TIMESTAMP WITHOUT TIME ZONE,
    color	    TEXT DEFAULT 'yellow',
    position        TEXT		
);


INSERT INTO postit_tmp ( cause, notes, employee, date ) SELECT cause, notes, employee, date FROM postit;

DROP TABLE IF EXISTS postit CASCADE;
ALTER TABLE postit_tmp RENAME TO postit;
