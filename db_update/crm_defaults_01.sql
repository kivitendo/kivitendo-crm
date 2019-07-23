-- @tag: crm_defaults_01
-- @description: CRM Defaults Data for mandant ( employee = -1 ) and for user
-- @version: 2.3.2

UPDATE crmdefaults SET employee = -1;
CREATE INDEX ON crmdefaults (key);

-- @exec
