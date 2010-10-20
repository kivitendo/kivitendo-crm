-- @tag: UserPre
-- @description: Pre in Suchmasken per Voreinstellung festlegen

ALTER TABLE employee ADD COLUMN preon boolean;
ALTER TABLE employee ALTER COLUMN preon SET DEFAULT 'f';

