CREATE TABLE wissencategorie(
id integer DEFAULT nextval('id'::text) NOT NULL,
name character varying(60),
hauptgruppe integer
);
CREATE TABLE leads(
id integer DEFAULT nextval('id'::text) NOT NULL,
lead character varying(50)
);
CREATE TABLE wissencontent(
id integer DEFAULT nextval('id'::text) NOT NULL,
initdate timestamp without time zone NOT NULL,
content text,
employee integer,
version integer,
categorie integer
);
ALTER TABLE customer ADD COLUMN lead integer;
ALTER TABLE customer ADD COLUMN leadsrc character varying(15);
CREATE TABLE opportunity(
id integer DEFAULT nextval('id'::text) NOT NULL,
fid integer,
title character varying(100),
betrag numeric (15,5),
zieldatum date,
chance integer,
status integer,
notiz text,
itime timestamp DEFAULT now(),
mtime timestamp,
iemployee integer,
memployee integer
);
ALTER TABLE employee ADD COLUMN kdview integer DEFAULT 1;
UPDATE employee SET kdview = 1;
create table postit (
id integer DEFAULT nextval('id'::text) NOT NULL,
cause character varying(100),
notes text,
employee integer,
date timestamp without time zone NOT NULL);
