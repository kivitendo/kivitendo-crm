-- @tag: headcount 
-- @description: Anzahl der Mitarbeiter

ALTER TABLE customer ADD COLUMN headcount int;
ALTER TABLE vendor ADD COLUMN headcount int;
