DROP TABLE IF EXISTS cars;
CREATE TABLE cars(
    id serial,
    vin text
);

INSERT INTO cars ( vin ) VALUES ( 'WDB6381221A1234560' );
INSERT INTO cars ( vin ) VALUES ( 'WAUZZZ0001W3456780' );
INSERT INTO cars ( vin ) VALUES ( 'TRABANT' );
SELECT left( vin, 17 ) AS vin, substring( vin, 18, 1 ) AS ckeck FROM cars;
ALTER TABLE cars ADD COLUMN vincheck CHAR;
UPDATE cars SET  vin = left( vin, 17 ), vincheck  = substring( vin, 18, 1 );
SELECT * FROM cars;
