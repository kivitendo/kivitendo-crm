-- @tag: WiedervorlageGrp
-- @description: Wiedervorlage auch einer Gruppe zuweisen, Typwandlung cause + status

ALTER TABLE wiedervorlage ADD COLUMN gruppe boolean;
ALTER TABLE wiedervorlage ALTER COLUMN gruppe SET DEFAULT false;
ALTER TABLE wiedervorlage ADD COLUMN temp1 text;
ALTER TABLE wiedervorlage ADD COLUMN temp2 int;
UPDATE wiedervorlage SET temp1  = cause, temp2 = cast(status as integer);
ALTER TABLE wiedervorlage DROP cause;
ALTER TABLE wiedervorlage DROP status;
ALTER TABLE wiedervorlage RENAME temp1 TO cause;
ALTER TABLE wiedervorlage RENAME temp2 TO status;


