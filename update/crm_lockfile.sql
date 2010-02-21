-- @tag: lockfile
-- @description: Ein Dokument sperren

ALTER TABLE documents ADD COLUMN lock int;
ALTER TABLE documents ALTER COLUMN lock SET DEFAULT 0;

UPDATE documents SET lock = 0;
