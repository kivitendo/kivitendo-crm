DROP TABLE IF EXISTS zipcode_to_location;

CREATE TABLE zipcode_to_location
(
    ort text NOT NULL,
    zipcode character(5) NOT NULL,
    landkreis text NOT NULL,
    bundesland text NOT NULL,
    CONSTRAINT zipcode_to_location_pkey PRIMARY KEY (ort, zipcode)
);
