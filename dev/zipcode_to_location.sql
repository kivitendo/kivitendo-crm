CREATE TABLE zipcode_to_location
(
    id integer primary key generated always as identity,
    osm_id text,
    ags text,
    ort text NOT NULL,
    plz character(5) NOT NULL,
    landkreis text,
    bundesland text NOT NULL
);

COPY zipcode_to_location( osm_id, ags, ort, plz, landkreis, bundesland ) FROM '/var/www/dev/kivitendo-crm/dev/zuordnung_plz_ort.csv' DELIMITER ',' CSV HEADER;

ALTER TABLE zipcode_to_location DROP COLUMN ags;
ALTER TABLE zipcode_to_location DROP COLUMN osm_id;
