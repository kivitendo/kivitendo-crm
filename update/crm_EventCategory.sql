-- @tag: EventCategory
-- @description: Event Category with order   

--Starttag
ALTER TABLE event_category ADD COLUMN cat_order INT DEFAULT 1;
DELETE FROM event_category WHERE label = '' OR label = NULL;

