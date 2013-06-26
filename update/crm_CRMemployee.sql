-- @tag: CRMemployee
-- @description: Zusätzliche Attribute für den User in eine eigene Tabelle auslagern

CREATE TABLE crmemployee (
    ceid integer DEFAULT nextval('crmid'::text) NOT NULL,
    uid int,
    key text,
    val text,
    typ char(1) DEFAULT 't'
);

