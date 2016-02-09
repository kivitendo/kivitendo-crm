-- @tag: katalogsortpart
-- @description: Individuelle Sortierung nach Artikelnummern

CREATE OR REPLACE FUNCTION idx(anyarray, anyelement) RETURNS int AS $$ SELECT i FROM ( SELECT generate_series(array_lower($1,1),array_upper($1,1)) ) g(i) WHERE $1[i] = $2 LIMIT 1; $$ LANGUAGE sql IMMUTABLE;
