-- @tag: CleanContact
-- @description: leere Felder auf NULL setzen da sonst COALESE nicht funktioniert, savePerson entspr. geändert

UPDATE contacts set cp_title = null where cp_title = '';
UPDATE contacts set cp_zipcode = null where cp_zipcode = '';
UPDATE contacts set cp_street = null where cp_street = '';
UPDATE contacts set cp_city = null where cp_city = '';
UPDATE contacts set cp_country = null where cp_country = '';
UPDATE contacts set cp_title = null where cp_title = '';
UPDATE contacts set cp_phone1 = null where cp_phone1 = '';
UPDATE contacts set cp_phone2 = null where cp_phone2 = '';
UPDATE contacts set cp_email = null where cp_email = '';

