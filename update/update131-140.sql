ALTER TABLE documents ADD COLUMN zeit time;
ALTER TABLE documents ADD COLUMN pfad text;
ALTER TABLE contacts  ADD COLUMN cp_salutation text;
ALTER TABLE customer ADD COLUMN konzern int4; 
ALTER TABLE vendor ADD COLUMN konzern int4; 
ALTER TABLE vendor ADD COLUMN lead integer;
ALTER TABLE vendor ADD COLUMN leadsrc character varying(25);
ALTER TABLE opportunity ADD COLUMN tab char(1);
ALTER TABLE opportunity ADD COLUMN salesman int;
ALTER TABLE opportunity ADD COLUMN next character varying(100);
CREATE SEQUENCE extraid INCREMENT BY 1 MAXVALUE 2147483647 CACHE 1;
CREATE TABLE extra_felder (
id       integer DEFAULT nextval('extraid'::text) NOT NULL,
owner    char(10),
fkey     text,
fval     text
);
CREATE INDEX extrafld_key ON extra_felder USING btree (owner);
insert into crm (uid,datum,version) values (0,now(),'1.4.0');
-- Geburtstage zusammenführen
update contacts set cp_birthday = coalesce(cp_gebdatum,cp_birthday);
alter table contacts drop column cp_gebdatum;
--
alter table tempcsvdata add column id integer;
