-- @tag: UserFolder
-- @description: Odner + Port eingeben, postf nach mailuser transportieren

ALTER TABLE employee ADD COLUMN mailuser character varying(75);
UPDATE employee set mailuser = postf;
UPDATE employee set postf = 'INBOX';
ALTER TABLE employee ADD COLUMN port int4;
ALTER TABLE employee ADD COLUMN proto boolean;
ALTER TABLE employee ADD COLUMN ssl boolean;

