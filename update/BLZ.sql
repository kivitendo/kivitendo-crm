CREATE TABLE blz_data (
   blz character(8) default Null,
   fuehrend character(1) default Null,
   bezeichnung character varying(58) default Null,
   plz character(5) default Null,
   ort character varying(35) default Null,
   kurzbez character varying(27) default Null,
   pan character(5) default Null,
   bic character(11) default Null,
   pzbm character(2) default Null,
   nummer int default Null,
   aekz character(1) default Null,
   bl character(1) default Null,
   folgeblz character(8) default Null
);
CREATE INDEX blz_id ON blz_data  USING btree (blz);
CREATE INDEX blz_plz ON blz_data  USING btree (plz);
CREATE INDEX blz_ort ON blz_data  USING btree (ort);
