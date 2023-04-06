DROP  TABLE IF EXISTS personen CASCADE;
CREATE TABLE personen( id SERIAL, name TEXT );
INSERT INTO personen ( name ) VALUES ( 'Ronny Zimmermann' );
INSERT INTO personen ( name ) VALUES ( 'Dirk Schwatzer' );

DROP  TABLE IF EXISTS kunden CASCADE;
CREATE TABLE kunden( id SERIAL, name TEXT, person_id INT );
INSERT INTO kunden ( name ) VALUES ( 'Glaserei Falkenberg' );
INSERT INTO kunden ( name, person_id ) VALUES ( 'Bautech', 2 );

DROP  TABLE IF EXISTS liefe CASCADE;
CREATE TABLE liefe( id serial, name text,  person_id INT );
INSERT INTO liefe ( name ) VALUES ( 'HUGOCMS' );
INSERT INTO liefe ( name, person_id ) VALUES ( 'Inter-Data', 1 );

CREATE VIEW crm_cv AS SELECT id, name, person_id, 'C'  AS cv FROM kunden UNION SELECT id, name, person_id, 'V' AS cv FROM liefe ;
SELECT * FROM crm_cv;
SELECT crm_cv.cv, crm_cv.name, crm_cv.id, personen.name AS p_name, personen.id AS p_id FROM crm_cv JOIN personen ON ( crm_cv.person_id = personen.id ) WHERE personen.id = 2;
