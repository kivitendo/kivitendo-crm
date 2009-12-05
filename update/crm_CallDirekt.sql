-- @tag: CallDirekt
-- @description: Richtung der Aktion markieren

ALTER TABLE telcall ADD COLUMN inout char(1);
ALTER TABLE telcall ALTER COLUMN inout SET DEFAULT 'i';

