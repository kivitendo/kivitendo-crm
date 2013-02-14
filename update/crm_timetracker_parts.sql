-- @tag: timetracker_parts
-- @description: Verbrauchte Artikel speichern.

CREATE TABLE tt_parts(
    eid int4,
    qty numeric(10,3),
    parts_id int4,
    parts_txt text
);
