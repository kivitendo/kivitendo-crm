Create or replace function random_string(length integer) returns text as
$$
declare
  chars text[] := '{a,b,c,d,e,f}';
  result text := '';
  i integer := 0;
begin
  if length < 0 then
    raise exception 'Given length cannot be less than 0';
  end if;
  for i in 1..length loop
    result := result || chars[1+random()*(array_length(chars, 1)-1)];
  end loop;
  return result;
end;
$$ language plpgsql;

DROP FUNCTION ranCharInsert( TEXT, INT, TEXT );
Create or replace function ranCharInsert( tableName TEXT, n INT, columnname TEXT DEFAULT 'name' ) 
    returns text as    
    $$
declare
begin
    for i in 1..n loop
    EXECUTE 'INSERT INTO '|| tableName ||' ( '|| columnname || ' )  SELECT random_string(10)';
  end loop;
  return n;
end;
$$ language plpgsql;

DROP TABLE IF EXISTS kunde; 
CREATE TABLE kunde(
    id integer primary key generated always as identity,
    name TEXT
);

DROP TABLE IF EXISTS lieferant; 
CREATE TABLE lieferant(
    id integer primary key generated always as identity,
    name TEXT
);

DROP TABLE IF EXISTS person; 
CREATE TABLE person(
    id integer primary key generated always as identity,
    name TEXT
);

DROP TABLE IF EXISTS car; 
CREATE TABLE car(
    id integer primary key generated always as identity,
    licenseplate TEXT
);


--SELECT name AS label, 'kunde' AS categorie FROM kunde WHERE name ILIKE '%d%' UNION SELECT name AS label, 'lieferant' AS categorie FROM lieferant WHERE name ILIKE '%d%' ORDER BY categorie;
--SELECT random_string(10);
SELECT ranCarInsert( 'kunde', 80 );
SELECT ranCarInsert( 'lieferant', 40 );
SELECT ranCarInsert( 'person', 20 );
SELECT ranCarInsert( 'car', 30, 'licenseplate' );

SELECT name AS label, 'kunde' AS categorie FROM kunde WHERE name ILIKE '%da%' 
UNION SELECT name AS label, 'lieferant' AS categorie FROM lieferant WHERE name ILIKE '%da%'  
UNION SELECT name AS label, 'person' AS categorie FROM person WHERE name ILIKE '%da%' 
UNION SELECT licenseplate AS label, 'Auto' AS categorie FROM car WHERE licenseplate ILIKE '%da%' ORDER BY categorie;
