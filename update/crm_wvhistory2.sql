-- @tag: wvhistory2
-- @description: Beschreibung aufsplitten

ALTER TABLE history ADD COLUMN  bezug integer;
ALTER TABLE history ADD COLUMN  tmp2 text;
UPDATE history set bezug = int4(substring(beschreibung, '([0-9]+)'));
UPDATE history set tmp2 = substr(beschreibung,position('|' in beschreibung)+1);
UPDATE history set tmp2 = 'Vertrag zugeordnet' WHERE text(bezug) = tmp2;
UPDATE history set tmp2 = 'Aufnahme' WHERE art = 'neu';
ALTER TABLE history DROP beschreibung;
ALTER TABLE history RENAME tmp2 TO beschreibung;
