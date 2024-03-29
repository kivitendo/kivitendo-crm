
-- @tag: App
-- @description: Upgrade script for crm_app
-- @version: 2.3.3
-- @php: *

$GLOBALS['dbh']->myquery( 'DROP TABLE IF EXISTS streets_germany' );
$GLOBALS['dbh']->myquery( 'CREATE TABLE streets_germany( id integer NOT NULL GENERATED ALWAYS AS IDENTITY, name text, postalcode text, locality text, regionalkey text )' );
$GLOBALS['dbh']->myquery( "COPY streets_germany( name, postalcode, locality, regionalkey ) FROM '".__DIR__."/../data/streets_germany.csv' DELIMITER ';' CSV HEADER" );

$GLOBALS['dbh']->myquery( 'DROP TABLE IF EXISTS firstnametogender' );
$GLOBALS['dbh']->myquery( 'CREATE TABLE firstnametogender( gender character(1) NOT NULL,firstname text NOT NULL PRIMARY KEY)' );
$GLOBALS['dbh']->myquery( "COPY firstnametogender( gender, firstname ) FROM '".__DIR__."/../data/firstnameToGender.csv' DELIMITER '|' CSV HEADER" );

$GLOBALS['dbh']->myquery( 'DROP TABLE IF EXISTS zipcode_to_location' );
$GLOBALS['dbh']->myquery( 'CREATE TABLE zipcode_to_location( id integer primary key generated always as identity, ort text NOT NULL, plz character(5) NOT NULL, landkreis text, bundesland text NOT NULL )' );
$GLOBALS['dbh']->myquery( "COPY zipcode_to_location( ort, plz, landkreis, bundesland ) FROM '".__DIR__."/../data/ort_plz_lk_bland.csv' DELIMITER ',' CSV HEADER" );

$GLOBALS['dbh']->myquery( 'ALTER TABLE customer ADD COLUMN phone3 TEXT' );
$GLOBALS['dbh']->myquery( 'ALTER TABLE customer ADD COLUMN note_fax TEXT' );
$GLOBALS['dbh']->myquery( 'ALTER TABLE customer ADD COLUMN note_phone TEXT' );
$GLOBALS['dbh']->myquery( 'ALTER TABLE customer ADD COLUMN note_phone3 TEXT' );

$GLOBALS['dbh']->myquery( 'ALTER TABLE vendor ADD COLUMN phone3 TEXT' );
$GLOBALS['dbh']->myquery( 'ALTER TABLE vendor ADD COLUMN note_fax TEXT' );
$GLOBALS['dbh']->myquery( 'ALTER TABLE vendor ADD COLUMN note_phone TEXT' );
$GLOBALS['dbh']->myquery( 'ALTER TABLE vendor ADD COLUMN note_phone3 TEXT' );

-- @exec: *
