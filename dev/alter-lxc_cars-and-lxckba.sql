ALTER TABLE IF EXISTS lxckba DROP CONSTRAINT IF EXISTS lxckba_pkey;
ALTER TABLE IF EXISTS lxckba DROP COLUMN IF EXISTS id;
ALTER TABLE IF EXISTS lxckba ADD COLUMN id integer NOT NULL GENERATED ALWAYS AS IDENTITY;
ALTER TABLE IF EXISTS lxckba ADD CONSTRAINT lxckba_pkey PRIMARY KEY (id);
ALTER TABLE IF EXISTS lxckba DROP CONSTRAINT IF EXISTS lxckba_unique;
ALTER TABLE IF EXISTS lxckba ADD CONSTRAINT lxckba_unique UNIQUE (hsn, tsn, d2);

ALTER TABLE IF EXISTS lxc_cars DROP COLUMN IF EXISTS kba_id;
ALTER TABLE IF EXISTS lxc_cars ADD COLUMN kba_id integer;
UPDATE lxc_cars SET kba_id = lxckba.id FROM lxckba WHERE lxc_cars.c_2 = lxckba.hsn AND SUBSTRING( lxc_cars.c_3, 0, 4 ) = lxckba.tsn;

ALTER TABLE IF EXISTS lxc_cars DROP COLUMN IF EXISTS scan_detail_id;
ALTER TABLE IF EXISTS lxc_cars ADD COLUMN scan_detail_id text;
ALTER TABLE IF EXISTS lxc_cars DROP CONSTRAINT IF EXISTS lxc_cars_scan_detail_id_unique;
ALTER TABLE IF EXISTS lxc_cars ADD CONSTRAINT lxc_cars_scan_detail_id_unique UNIQUE (scan_detail_id);

ALTER TABLE IF EXISTS lxc_cars DROP COLUMN IF EXISTS scan_id;
ALTER TABLE IF EXISTS lxc_cars ADD COLUMN scan_id text;
ALTER TABLE IF EXISTS lxc_cars DROP CONSTRAINT IF EXISTS lxc_cars_scan_id_unique;
ALTER TABLE IF EXISTS lxc_cars ADD CONSTRAINT lxc_cars_scan_id_unique UNIQUE (scan_id);

ALTER TABLE IF EXISTS lxc_cars DROP COLUMN IF EXISTS filename;
ALTER TABLE IF EXISTS lxc_cars ADD COLUMN filename text;
