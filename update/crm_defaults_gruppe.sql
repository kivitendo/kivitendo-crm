-- @tag: defaults_gruppe
-- @description: Einstellungen zu Gruppen zusammenfassen

ALTER TABLE crmdefaults ADD COLUMN grp char(10);
UPDATE TABLE crmdefaults SET grp = 'mandant';
