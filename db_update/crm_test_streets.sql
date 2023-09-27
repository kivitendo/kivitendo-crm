-- @tag: crm_test_streets
-- @description: Test for DB Table 
-- @version: 2.3.3
-- @php: *
$rs = $GLOBALS['dbh']->query( 'CREATE TABLE streets_germany( id integer NOT NULL GENERATED ALWAYS AS IDENTITY, name text, postalcode text, locality text, regionalkey text )' );
$sql = "COPY streets_germany( name, postalcode, locality, regionalkey ) FROM '".__DIR__."/../data/streets_germany.csv' DELIMITER ';' CSV HEADER";
writeLogR( $sql );
$rs = $GLOBALS['dbh']->query( $sql );

-- @exec: *
