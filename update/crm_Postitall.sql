-- @tag: Postitall
-- @description: Postits for kivitendo

--Starttag
CREATE TABLE postitall (
    id          SERIAL NOT NULL PRIMARY KEY,
    iduser      TEXT,
    idnote      TEXT,
    content     TEXT
);
-- @php: *
convertPostits();
-- @exec: *
