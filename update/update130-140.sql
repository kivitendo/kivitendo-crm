CREATE TABLE mailvorlage (
        id integer DEFAULT nextval('crmid'::text) NOT NULL,
        cause char varying(120),
        c_long text,
        employee integer
);
alter table contacts add column cp_salutation text;
