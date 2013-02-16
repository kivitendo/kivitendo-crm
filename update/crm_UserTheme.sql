-- @tag: UserTheme
-- @description: jquery Theme auswahl

ALTER TABLE employee ADD COLUMN theme text;
ALTER TABLE employee ALTER COLUMN theme SET DEFAULT 'base';

