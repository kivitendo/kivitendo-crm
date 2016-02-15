-- @tag: test2.1.2
-- @description: DEsc von test2.1.2

-- @version: 2.1.5

CREATE TABLE test2(
    id      serial NOT NULL PRIMARY KEY,
    label   text,
    color      char(7)
);

INSERT INTO test ( 'label', 'color' ) VALUES ( 'version 2.1.2', 'rot' );
-- @php: *
writelog( 'tets2.1.2' );
return true;
-- @exec: *
