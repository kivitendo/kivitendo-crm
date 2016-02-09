-- @tag: PrivatTermin
-- @description: Termine als Privat markieren

ALTER TABLE termine ADD COLUMN privat boolean;
ALTER TABLE termine ALTER COLUMN privat SET DEFAULT 'f';
