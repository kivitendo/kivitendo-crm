

CREATE OR REPLACE FUNCTION random_string( length INT ) RETURNS TEXT AS
$$
DECLARE
  chars TEXT[] := '{a,b,c,d,e,f}';
  result TEXT := '';
  i INT := 0;
BEGIN
  IF length < 0 THEN
    raise exception 'Given length cannot be less than 0';
  END IF;
  FOR i IN 1..length LOOP
    result := result || chars[1 + random() * ( array_length( chars, 1 ) -1 )];
  END LOOP;
  RETURN result;
END;
$$ LANGUAGE PLPGSQL;

DROP FUNCTION IF EXISTS ranCharInsert( TEXT, INT, TEXT );
CREATE OR REPLACE FUNCTION ranCharInsert( tableName TEXT, n INT, columnname TEXT DEFAULT 'name' )
  RETURNS VOID AS
  $$
DECLARE
BEGIN
    FOR i IN 1..n LOOP
      EXECUTE 'INSERT INTO '|| tableName ||' ( '|| columnname || ' ) SELECT random_string(10)';
    END LOOP;
END;
$$ LANGUAGE PLPGSQL;

DROP TABLE IF EXISTS kunde;
CREATE TABLE kunde(
  id INT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
  name TEXT
);

DROP TABLE IF EXISTS lieferant;
CREATE TABLE lieferant(
  id INT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
  name TEXT
);

DROP TABLE IF EXISTS person;
CREATE TABLE person(
  id INT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
  name TEXT
);

DROP TABLE IF EXISTS car;
CREATE TABLE car(
  id INT PRIMARY KEY GENERATED ALWAYS AS IDENTITY,
  licenseplate TEXT
);


SELECT ranCharInsert( 'kunde', 80 );
SELECT ranCharInsert( 'lieferant', 40 );
SELECT ranCharInsert( 'person', 20 );
SELECT ranCharInsert( 'car', 30, 'licenseplate' );

SELECT * FROM ( SELECT * FROM ( ( SELECT name AS label, 'Kunde' AS categorie FROM kunde WHERE name ILIKE '%da%' )
UNION
( SELECT name AS label, 'Lieferant' AS categorie FROM lieferant WHERE name ILIKE '%da%' )
UNION
( SELECT name AS label, 'Person' AS categorie FROM person WHERE name ILIKE '%da%' )
UNION
( SELECT licenseplate AS label, 'Auto' AS categorie FROM car WHERE licenseplate ILIKE '%da%' )
) AS test ORDER BY random() LIMIT 20 ) AS gemischt ORDER BY categorie ;
