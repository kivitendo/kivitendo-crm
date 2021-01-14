DROP TABLE IF EXISTS buildings CASCADE;
CREATE TABLE buildings(
    id SERIAL UNIQUE PRIMARY KEY,
    itime TIMESTAMP DEFAULT NOW(),
    mtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT,
    street TEXT,
    zipcode TEXT,
    city TEXT
);
DROP TABLE IF EXISTS flats CASCADE;
CREATE TABLE flats(
    id SERIAL UNIQUE PRIMARY KEY,
    itime TIMESTAMP DEFAULT NOW(),
    mtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description text,
    building_id int REFERENCES buildings( id ) NOT NULL
);
DROP TABLE IF EXISTS tenants CASCADE;
CREATE TABLE tenants(
    id SERIAL UNIQUE PRIMARY KEY,
    itime TIMESTAMP DEFAULT NOW(),
    mtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    startdate DATE,
    enddate DATE,
    flat_id INT REFERENCES flats( id ),
    customer_id INT REFERENCES customer( id )
);
DROP TABLE IF EXISTS meter_types CASCADE;
CREATE TABLE meter_types(
    id SERIAL UNIQUE PRIMARY KEY,
    itime TIMESTAMP DEFAULT NOW(),
    mtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    description TEXT,
    default_unit_id INT REFERENCES units( id ) NOT NULL
);
DROP TABLE IF EXISTS meters CASCADE;
CREATE TABLE meters(
    id SERIAL UNIQUE PRIMARY KEY,
    itime TIMESTAMP DEFAULT NOW(),
    mtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    meter_type_id INT REFERENCES meter_types( id ) NOT NULL,
    unit_id int REFERENCES units( id ),
    flat_id int REFERENCES flats( id ),
    building_id int REFERENCES buildings( id ),
    constraint at_least_one_id check( num_nonnulls( flat_id, building_id ) = 1 )
);
DROP TABLE IF EXISTS  meter_readings;
CREATE TABLE meter_readings(
    id SERIAL,
    itime TIMESTAMP DEFAULT NOW(),
    mtime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    counter int,
    meter_id int REFERENCES meters( id )
);
--SELECT * FROM meter;
--SELECT * FROM units;
INSERT INTO buildings ( description, street, zipcode, city ) VALUES ( 'test_descr', 'Dorfstr. 23A', '15345', 'Garzin' );
SELECT * FROM buildings;
INSERT INTO meter_types ( description, default_unit_id ) VALUES ( 'testdesc ', 1 );
INSERT INTO flats ( description, building_id )VALUES ( 'test', 1 );
INSERT INTO meters ( flat_id, meter_type_id ) VALUES ( 1, 1 );
--INSERT INTO meters ( flat_id, building_id ) VALUES ( 1, 1 );
INSERT INTO meters ( building_id, meter_type_id ) VALUES ( 1, 1 );
SELECT * FROM meters;