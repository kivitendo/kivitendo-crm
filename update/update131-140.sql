alter table contacts add column cp_salutation text;
alter table documents add column pfad text;
ALTER TABLE vendor ADD COLUMN lead integer;
ALTER TABLE vendor ADD COLUMN leadsrc character varying(25);
CREATE SEQUENCE extraid INCREMENT BY 1 MAXVALUE 2147483647 CACHE 1;
create table extra_felder (
id       integer DEFAULT nextval('extraid'::text) NOT NULL,
owner    char(10),
fkey     text,
fval     text
);
CREATE INDEX extrafld_key ON extra_felder USING btree (owner);
insert into crm (uid,datum,version) values (0,now(),'1.4.0');
