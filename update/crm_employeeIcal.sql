-- @tag: employeeIcal
-- @description: Termine exportieren

ALTER TABLE employee ADD COLUMN icalart text;
ALTER TABLE employee ADD COLUMN icaldest text;
ALTER TABLE employee ADD COLUMN icalext text;
