-- @tag: UserMailssl
-- @description: 3. Möglichkeit für ssl (notls)

ALTER TABLE employee ADD COLUMN ssltmp char;
UPDATE employee SET ssltmp = substr(cast(ssl as text),1,1);
ALTER TABLE employee DROP COLUMN ssl;
ALTER TABLE employee RENAME COLUMN ssltmp TO ssl;
