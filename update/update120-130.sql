CREATE TABLE telcallhistory (
	id integer DEFAULT nextval('id'::text) NOT NULL,
	orgid integer,
	cause text,
	caller_id integer NOT NULL,
	calldate timestamp without time zone NOT NULL,
	c_long text,
	employee integer,
	kontakt character(1),
	bezug integer,
	dokument integer,
        chgid integer,
        grund char(1),
        datum timestamp without time zone NOT NULL);

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

DROP TABLE tempcsvdata;
CREATE TABLE tempcsvdata (
	uid  integer,
	csvdaten text
);

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

CREATE TABLE postit (
	id integer DEFAULT nextval('id'::text) NOT NULL,
	cause character varying(100),
	notes text,
	employee integer,
	date timestamp without time zone NOT NULL
);

ALTER TABLE customer ADD COLUMN lead integer;
ALTER TABLE customer ADD COLUMN leadsrc character varying(15);
ALTER TABLE custmsg ADD COLUMN akt boolean;
ALTER TABLE employee ADD COLUMN kdview integer;
ALTER TABLE employee alter COLUMN kdview SET DEFAULT 1;
ALTER TABLE customer ADD COLUMN sonder int;
ALTER TABLE vendor ADD COLUMN sonder int;

UPDATE employee SET kdview = 1;

INSERT INTO crm (uid,datum,version) VALUES (0,now(),'1.3.0');
