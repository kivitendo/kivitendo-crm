-- $Id: $
CREATE TABLE  contmasch(
	mid integer,
	cid integer);
	
CREATE TABLE history (
	mid integer,
	datum date,
	art character varying(20),
	beschreibung text);
	
CREATE TABLE repauftrag (
	aid integer,
	mid integer,
	cause text,
	schaden text,
	reparatur text,
	bearbdate timestamp without time zone,
	employee integer,
	bearbeiter integer,
	anlagedatum timestamp without time zone,
	status integer,
	kdnr integer,
        counter bigint);
	
CREATE TABLE  maschmat (
	mid integer,
	aid integer,
	parts_id integer,
	betrag numeric(15,5),
	menge numeric(10,3));
	
CREATE TABLE contract (
	cid integer DEFAULT nextval('id'::text),
	contractnumber text,
	template text,
	bemerkung text,
	customer_id integer,
	anfangdatum date,
	betrag numeric(15,5),
	endedatum date );
	
CREATE TABLE maschine (
	id integer DEFAULT nextval('id'::text),
	parts_id integer,
	serialnumber text,
	standort text,
        inspdatum DATE,
        counter BIGINT);
	
--ALTER TABLE employee ADD COLUMN status integer;
ALTER TABLE employee ADD COLUMN termbegin integer;
ALTER TABLE employee ADD COLUMN termend integer;
ALTER TABLE defaults ADD COLUMN contnumber text;
CREATE INDEX mid_key ON contmasch USING btree (mid);
UPDATE defaults SET contnumber=1000;
insert into crm (uid,datum,version) values (0,now(),'1.2.0');
