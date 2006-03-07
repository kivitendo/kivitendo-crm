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

