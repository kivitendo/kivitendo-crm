-- @tag: extrafelder
-- @description: Owner aufsplitten in Tabelle und ID

ALTER TABLE extra_felder ADD COLUMN tab char(1);
UPDATE extra_felder SET tab = substring(owner,1,1);
ALTER TABLE extra_felder ADD COLUMN tmp integer;
UPDATE extra_felder SET tmp = CAST(substring(owner,2) as integer);
ALTER TABLE extra_felder DROP owner;
ALTER TABLE extra_felder RENAME tmp TO owner;
