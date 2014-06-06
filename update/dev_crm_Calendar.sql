-- @tag: Termine02
-- @description: Tabelle Termine Start-Stop-Zeit in Timestamp 

--Starttag
CREATE SEQUENCE termine_id_seq;
ALTER TABLE termine ALTER id SET DEFAULT NEXTVAL('termine_id_seq');
ALTER TABLE termine ADD COLUMN allday bool;
ALTER TABLE termine ADD COLUMN prio char;
ALTER TABLE termine ADD COLUMN job bool;
ALTER TABLE termine ADD COLUMN color char(7);

