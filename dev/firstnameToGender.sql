DROP TABLE IF EXISTS firstnametogender;

CREATE TABLE firstnametogender
(
    gender character(1) NOT NULL,
    firstname text NOT NULL PRIMARY KEY
);

COPY firstnametogender( gender, firstname ) FROM '/var/www/dev/kivitendo-crm/firstnameToTitle.csv' DELIMITER '|' CSV HEADER;
