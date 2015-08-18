-- @tag: add_crm_master_rights
-- @description: Rechte für CRM in die Datenbank migrieren
-- @charset: utf-8
-- @locales: CRM
-- @locales: Searchmask
-- @locales: Add new Addresses
-- @locales: Sales: Opporunity,Catalog, Packlist, ebay
-- @locales: Other: Follow Up,E-Mail,Appointment
-- @locales: Documens
-- @locales: Finance
-- @locales: Parts: Edit, Warehouse
-- @locales: Service: Contract, Machine
-- @locales: Master: Client, Groups
-- @locales: Admin: Label, Category, Messages, Status, KnowHow write
-- @locales: User

INSERT INTO auth.master_rights (position, name, description, category) VALUES (200, 'crm',                   'CRM', TRUE);
INSERT INTO auth.master_rights (position, name, description) VALUES (201, 'crm_search',                      'Searchmask');
INSERT INTO auth.master_rights (position, name, description) VALUES (202, 'crm_new',                         'Add new Addresses');
INSERT INTO auth.master_rights (position, name, description) VALUES (203, 'crm_sales',                       'Sales: Opporunity,Catalog, Packlist, ebay');
INSERT INTO auth.master_rights (position, name, description) VALUES (204, 'crm_other',                       'Other: Follow Up,E-Mail,Appointment');
INSERT INTO auth.master_rights (position, name, description) VALUES (205, 'crm_document',                    'Documens');
INSERT INTO auth.master_rights (position, name, description) VALUES (206, 'crm_finance',                     'Finance');
INSERT INTO auth.master_rights (position, name, description) VALUES (207, 'crm_parts',                       'Parts: Edit, Warehouse');
INSERT INTO auth.master_rights (position, name, description) VALUES (208, 'crm_service',                     'Service: Contract, Machine');
INSERT INTO auth.master_rights (position, name, description) VALUES (209, 'crm_master',                      'Master: Client, Groups');
INSERT INTO auth.master_rights (position, name, description) VALUES (210, 'crm_admin',                       'Admin: Label, Category, Messages, Status, KnowHow write');
INSERT INTO auth.master_rights (position, name, description) VALUES (211, 'crm_user',                        'User');
