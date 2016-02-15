-- @tag: test2.1.2
-- @description: DEsc von test2.1.3
-- @require: test9
-- @version: 2.1.6

CREATE TABLE test3(
    id      serial NOT NULL PRIMARY KEY,
    label   text,
    color      char(7)
);

INSERT INTO test ( 'label', 'color' ) VALUES ( 'version 2.1.3', 'rot' );

-- @php: *
writelog( 'tets2.1.3' );
return true;
-- @exec: *
