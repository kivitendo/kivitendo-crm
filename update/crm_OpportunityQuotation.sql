-- @tag: OpportunityQuotation
-- @description: Historie zu Auftragschancen + Auftragsnummer zuordnen

ALTER TABLE opportunity ADD COLUMN oppid INT;
UPDATE opportunity SET oppid = id;
ALTER TABLE opportunity ALTER COLUMN oppid SET not null;
ALTER TABLE opportunity ADD COLUMN auftrag INT;
ALTER TABLE opportunity ALTER COLUMN auftrag SET DEFAULT 0;
ALTER TABLE opportunity DROP COLUMN mtime;

