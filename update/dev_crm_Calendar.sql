-- @tag: Termine02
-- @description: Tabelle Termine Start-Stop-Zeit in Timestamp 

--Starttag
CREATE SEQUENCE termine_id_seq;
ALTER TABLE termine ALTER id SET DEFAULT NEXTVAL('termine_id_seq');
ALTER TABLE termine ADD COLUMN allday bool;
ALTER TABLE termine ADD COLUMN prio char;
ALTER TABLE termine ADD COLUMN job bool;
ALTER TABLE termine ADD COLUMN color char(7);
ALTER TABLE termine ADD COLUMN done bool;
ALTER TABLE termine ADD COLUMN cust_vend_pers char(32);
/* Neue Tabelle termin_repeat erstellen. Inhalt: id (fremdschlüssel), anz-wdhl, end-date::TIMESTAMP 

