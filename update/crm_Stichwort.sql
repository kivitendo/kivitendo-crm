-- @tag: Stichwort
-- @description: Spaltentyp ändern

ALTER TABLE customer ADD COLUMN tmp text;
UPDATE customer SET tmp = sw;
ALTER TABLE customer DROP COLUMN sw;
ALTER TABLE customer RENAME COLUMN tmp TO sw;

ALTER TABLE vendor ADD COLUMN tmp text;
UPDATE vendor SET tmp = sw;
ALTER TABLE vendor DROP COLUMN sw;
ALTER TABLE vendor RENAME COLUMN tmp TO sw;

ALTER TABLE contacts ADD COLUMN tmp text;
UPDATE contacts SET tmp = cp_stichwort1;
ALTER TABLE contacts DROP COLUMN cp_stichwort1;
ALTER TABLE contacts RENAME COLUMN tmp TO cp_stichwort1;
