-- @tag: employeeFeldLaenge
-- @description: Einige Felder vergrößert

ALTER TABLE employee ADD COLUMN tmp character varying(75);
UPDATE employee set tmp = msrv;
ALTER TABLE employee DROP COLUMN msrv;
ALTER TABLE employee RENAME COLUMN tmp to msrv;
 
ALTER TABLE employee ADD COLUMN tmp character varying(75);
UPDATE employee set tmp = postf;
ALTER TABLE employee DROP COLUMN postf;
ALTER TABLE employee RENAME COLUMN tmp to postf;

ALTER TABLE employee ADD COLUMN tmp character varying(20);
UPDATE employee set tmp = kennw;
ALTER TABLE employee DROP COLUMN kennw;
ALTER TABLE employee RENAME COLUMN tmp to kennw;
 
ALTER TABLE employee ADD COLUMN tmp character varying(75);
UPDATE employee set tmp = abteilung;
ALTER TABLE employee DROP COLUMN abteilung;
ALTER TABLE employee RENAME COLUMN tmp to abteilung;
 
ALTER TABLE employee ADD COLUMN tmp character varying(75);
UPDATE employee set tmp = position;
ALTER TABLE employee DROP COLUMN position;
ALTER TABLE employee RENAME COLUMN tmp to position;
 
