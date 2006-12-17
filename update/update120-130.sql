--CREATE SEQUENCE "crmid" start 1 increment 1 maxvalue 9223372036854775807 minvalue 1 cache 1;

CREATE TABLE telcallhistory (
	id integer DEFAULT nextval('crmid'::text) NOT NULL,
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
	id integer DEFAULT nextval('crmid'::text) NOT NULL,
	name character varying(60),
	hauptgruppe integer,
        kdhelp boolean
);

CREATE TABLE wissencontent(
	id integer DEFAULT nextval('crmid'::text) NOT NULL,
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
	id integer DEFAULT nextval('crmid'::text) NOT NULL,
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
CREATE TABLE opport_status (
        id integer DEFAULT nextval('crmid'::text) NOT NULL,
	statusname character varying(50),
        sort integer
);

INSERT INTO  opport_status (statusname,sort) VALUES ('Neu',1);
INSERT INTO  opport_status (statusname,sort) VALUES ('Wert-Angebot',2);
INSERT INTO  opport_status (statusname,sort) VALUES ('Entscheidungsfindung',3);
INSERT INTO  opport_status (statusname,sort) VALUES ('bedarf Analyse',4);
INSERT INTO  opport_status (statusname,sort) VALUES ('Gewonnen',5);
INSERT INTO  opport_status (statusname,sort) VALUES ('Aufgeschoben',6);
INSERT INTO  opport_status (statusname,sort) VALUES ('wieder offen',7);
INSERT INTO  opport_status (statusname,sort) VALUES ('Verloren',8);

CREATE TABLE postit (
	id integer DEFAULT nextval('crmid'::text) NOT NULL,
	cause character varying(100),
	notes text,
	employee integer,
	date timestamp without time zone NOT NULL
);

CREATE TABLE bundesland (
	id integer DEFAULT nextval('crmid'::text) NOT NULL,
	country character (3),
	bundesland character varying(50)
);
INSERT INTO bundesland (country,bundesland) VALUES ('D','Baden-W&uuml;ttemberg');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Bayern');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Berlin');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Brandenburg');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Bremen');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Hamburg');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Hessen');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Mecklenburg-Vorpommern');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Niedersachsen');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Nordrhein-Westfalen');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Rheinland-Pfalz');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Saarland');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Sachsen');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Sachen-Anhalt');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Schleswig-Holstein');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Th&uuml;ingen');

INSERT INTO bundesland (country,bundesland) VALUES ('CH','Aargau');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Appenzell Ausserrhoden');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Appenzell Innerrhoden');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Basel-Landschaft');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Basel-Stadt');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Bern');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Freiburg');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Genf');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Glarus');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Graub&uuml;nden');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Jura');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Luzern');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Neuenburg');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Nidwalden');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Obwalden');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Schaffhausen');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Schwyz');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Solothurn');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','St. Gallen');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Tessin');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Thurgau');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Uri');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Waadt');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Wallis');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Zug');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Z&uuml;rich');

INSERT INTO bundesland (country,bundesland) VALUES ('A','Burgenland');
INSERT INTO bundesland (country,bundesland) VALUES ('A','K&auml;rnten');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Nieder&ouml;sterreich');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Ober&ouml;sterreich');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Salzburg');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Steiermark');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Tirol');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Vorarlberg');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Wien');

ALTER TABLE customer ADD COLUMN lead integer;
ALTER TABLE customer ADD COLUMN leadsrc character varying(25);
ALTER TABLE customer ADD COLUMN bland int4;
ALTER TABLE custmsg ADD COLUMN akt boolean;
ALTER TABLE employee ADD COLUMN kdview integer;
ALTER TABLE employee alter COLUMN kdview SET DEFAULT 1;
ALTER TABLE customer ADD COLUMN sonder int;
ALTER TABLE vendor ADD COLUMN sonder int;
ALTER TABLE vendor ADD COLUMN bland int4;
ALTER TABLE shipto ADD COLUMN shiptobland int4;
ALTER TABLE termine RENAME COLUMN cause TO tmp;
ALTER TABLE termine ADD COLUMN cause character varying(45);
UPDATE termine SET cause=tmp;
ALTER TABLE termine DROP COLUMN tmp;

UPDATE employee SET kdview = 1;

ALTER TABLE telcall ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE wiedervorlage ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE documenttotc  ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE termine ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE termdate ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE custmsg ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE crm ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE labels ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE labeltxt ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE maschine ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE documents ALTER COLUMN id SET DEFAULT nextval('crmid'::text);
ALTER TABLE contract ALTER COLUMN cid SET DEFAULT nextval('crmid'::text);
ALTER TABLE docvorlage ALTER COLUMN docid SET DEFAULT nextval('crmid'::text);
ALTER TABLE docfelder ALTER COLUMN fid SET DEFAULT nextval('crmid'::text);
ALTER TABLE gruppenname ALTER COLUMN grpid SET DEFAULT nextval('crmid'::text);
ALTER TABLE grpusr ALTER COLUMN gid SET DEFAULT nextval('crmid'::text);

INSERT INTO crm (uid,datum,version) VALUES (0,now(),'1.3.0');
