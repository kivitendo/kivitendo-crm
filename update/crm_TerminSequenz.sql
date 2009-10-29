-- @tag: TerminSequenz
-- @description: Zeitunterteilung

--Starttag
ALTER TABLE employee ADD COLUMN termseq int;
ALTER TABLE employee ALTER COLUMN termseq SET DEFAULT 30;
UPDATE employee SET termseq = 30;

