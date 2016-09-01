CREATE TABLE example(
  id serial,
  date_time timestamp without time zone,
  c_name text,
  c_age integer,
  c_comments text);

CREATE TABLE contact_events(
  id serial,
  cause text,
  caller_id integer,
  calldate timestamp without time zone,
  cause_long text,
  employee integer,
  contact_reference integer DEFAULT 0,
  "inout" character(1),
  calendar_event integer,
  type_of_contact integer);

CREATE TABLE telcall (
    id serial,
    termin_id integer,
    cause text,
    caller_id integer NOT NULL,
    calldate timestamp without time zone NOT NULL,
    c_long text,
    employee integer,
    kontakt character(1),
    inout char(1) DEFAULT 'i',
    bezug integer,
    dokument integer);

CREATE TABLE telcallhistory (
    id serial,
    orgid integer,
    cause text,
    caller_id integer NOT NULL,
    calldate timestamp without time zone NOT NULL,
    c_long text,
    employee integer,
    kontakt character(1),
    bezug integer,
    dokument integer,
    chgid integer,
    grund char(1),
    datum timestamp without time zone NOT NULL);

CREATE TABLE documents (
    filename text,
    descript text,
    datum date,
    zeit time,
    size integer,
    pfad text,
    kunde integer,
    lock integer DEFAULT 0,
    employee integer,
    id serial);

CREATE TABLE wiedervorlage (
    id serial,
    initdate timestamp without time zone NOT NULL,
    changedate timestamp without time zone,
    finishdate timestamp without time zone,
    cause text,
    descript text,
    document integer,
    status integer,
    kontakt character(1),
    employee integer,
    gruppe boolean DEFAULT false,
    initemployee integer,
    kontaktid integer,
    kontakttab character(1),
    tellid integer);

CREATE TABLE documenttotc (
    id serial,
    telcall integer,
    documents integer);

CREATE TABLE telnr (
    id integer,
    tabelle character(1),
    nummer character varying(20));

CREATE TABLE docvorlage (
    docid serial,
    vorlage character varying(60),
    beschreibung character varying(255),
    file character varying(40),
    applikation character(1));

CREATE TABLE docfelder (
    fid serial,
    docid   integer,
    feldname    character varying(20),
    platzhalter character varying(20),
    beschreibung character varying(200),
    laenge  integer,
    zeichen character varying(20),
    position    integer);

CREATE TABLE gruppenname (
    grpid  serial,
    grpname  character varying(40),
    rechte       char(1) DEFAULT 'w');

CREATE TABLE grpusr (
    gid  serial,
    grpid integer,
    usrid integer);


CREATE TABLE custmsg (
    id serial,
    fid integer,
    prio integer DEFAULT 3,
    msg char varying(60),
    uid integer,
    akt boolean);

CREATE TABLE crm (
    id serial,
    uid integer,
    datum timestamp without time zone,
    version char(5));

CREATE TABLE labels (
    id serial,
    name char varying(32),
    cust char(1),
    papersize char varying(10),
    metric char(2),
    marginleft double precision,
    margintop double precision,
    nx integer,
    ny integer,
    spacex double precision,
    spacey double precision,
    width double precision,
    height double precision,
    fontsize integer,
    employee integer);

INSERT INTO labels (name, cust, papersize, metric, marginleft, margintop, nx, ny, spacex, spacey, width, height, fontsize, employee)
VALUES ('Firma', 'C', 'A4', 'mm', 2, 2, 2, 3, 4, 2, 66, 38, 10, NULL);


CREATE TABLE labeltxt (
    id serial,
    lid integer,
    font integer,
    zeile text);

INSERT INTO labeltxt (lid, font, zeile) VALUES ((select id from labels limit 1), 6, '');
INSERT INTO labeltxt (lid, font, zeile) VALUES ((select id from labels limit 1), 8, 'Lx-System, Unser Weg 1, 12345 Woanders');
INSERT INTO labeltxt (lid, font, zeile) VALUES ((select id from labels limit 1), 6, '');
INSERT INTO labeltxt (lid, font, zeile) VALUES ((select id from labels limit 1), 10, '%ANREDE%');
INSERT INTO labeltxt (lid, font, zeile) VALUES ((select id from labels limit 1), 10, '%NAME1% %NAME2%');
INSERT INTO labeltxt (lid, font, zeile) VALUES ((select id from labels limit 1), 10, '!%KONTAKT%|%DEPARTMENT%');
INSERT INTO labeltxt (lid, font, zeile) VALUES ((select id from labels limit 1), 10, '%STRASSE%');
INSERT INTO labeltxt (lid, font, zeile) VALUES ((select id from labels limit 1), 8, '');
INSERT INTO labeltxt (lid, font, zeile) VALUES ((select id from labels limit 1), 10,'%PLZ% %ORT%');

CREATE TABLE  contmasch(
    mid integer,
    cid integer);

CREATE TABLE history (
    mid integer,
    itime timestamp without time zone default now(),
    art character varying(20),
    beschreibung text);

CREATE TABLE repauftrag (
    aid integer,
    mid integer,
    cause text,
    schaden text,
    reparatur text,
    bearbdate timestamp without time zone,
    employee integer,
    bearbeiter integer,
    anlagedatum timestamp without time zone,
    status integer,
    kdnr integer,
    counter bigint);

CREATE TABLE  maschmat (
    mid integer,
    aid integer,
    parts_id integer,
    betrag numeric(15,5),
    menge numeric(10,3));

CREATE TABLE contract (
    cid serial,
    contractnumber text,
    template text,
    bemerkung text,
    customer_id integer,
    anfangdatum date,
    betrag numeric(15,5),
    endedatum date);

CREATE TABLE maschine (
    id serial,
    parts_id integer,
    serialnumber text,
    standort text,
    inspdatum DATE,
    counter BIGINT);


CREATE TABLE wissencategorie(
    id serial NOT NULL,
    name character varying(60),
    hauptgruppe integer,
    kdhelp boolean
);

CREATE TABLE wissencontent(
    id serial,
    initdate timestamp without time zone NOT NULL,
    content text,
    employee integer,
    owener integer,
    version integer,
    categorie integer
);

CREATE TABLE opportunity(
    id serial,
    oppid integer DEFAULT 0 NOT NULL,
    fid integer,
    tab char(1),
    title character varying(100),
    betrag numeric (15,5),
    zieldatum date,
    chance integer,
    status integer,
    salesman int,
    next character varying(100),
    notiz text,
    auftrag integer DEFAULT 0,
    itime timestamp DEFAULT now(),
    iemployee integer,
    memployee integer
);
CREATE TABLE opport_status (
    id serial,
    statusname character varying(50),
    sort integer
);

INSERT INTO  opport_status (statusname,sort) VALUES ('Neu',1);
INSERT INTO  opport_status (statusname,sort) VALUES ('Wert-Angebot',2);
INSERT INTO  opport_status (statusname,sort) VALUES ('Entscheidungsfindung',3);
INSERT INTO  opport_status (statusname,sort) VALUES ('bedarf Analyse',4);
INSERT INTO  opport_status (statusname,sort) VALUES ('Gewonnen',5);
INSERT INTO  opport_status (statusname,sort) VALUES ('Aufgeschoben',6);
INSERT INTO  opport_status (statusname,sort) VALUES ('wieder offen',7);
INSERT INTO  opport_status (statusname,sort) VALUES ('Verloren',8);



CREATE TABLE tempcsvdata (
    uid  integer,
    csvdaten text,
    id  integer
);

CREATE TABLE mailvorlage (
        id serial,
        cause char varying(120),
        c_long text,
        employee integer
);

CREATE TABLE timetrack (
    id serial,
    fid integer,
    tab char(1),
    ttname text NOT NULL,
    budget numeric(15,5),
    ttdescription text,
    startdate date,
    stopdate date,
    aim integer,
    active boolean DEFAULT 't',
    uid integer NOT NULL
);
CREATE TABLE tt_parts(
    eid int4,
    qty numeric(10,3),
    parts_id int4,
    parts_txt text
);
CREATE TABLE tt_event (
    id serial,
    ttid integer NOT NULL,
    uid integer NOT NULL,
    ttevent text NOT NULL,
    ttstart timestamp without time zone,
    ttstop timestamp without time zone,
    cleared int
);

CREATE TABLE bundesland (
    id serial,
    country character (3),
    bundesland character varying(50)
);


CREATE table extra_felder (
    id       serial,
    owner    integer,
    tab      char(1),
    fkey     text,
    fval     text
);
CREATE INDEX extrafld_key ON extra_felder USING btree (owner);

CREATE TABLE crmdefaults (
    id serial,
    employee integer NOT NULL DEFAULT -1,
    key text,
    val text,
    grp char(10),
    modify timestamp without time zone DEFAULT NOW()
);
CREATE TABLE crmemployee (
    manid serial,
    uid int,
    key text,
    val text,
    typ char(1) DEFAULT 't'
);

CREATE TABLE event_category(
    id      serial NOT NULL PRIMARY KEY,
    label   text,
    color      char(7),
    cat_order INT DEFAULT 1
);

INSERT INTO event_category ( label, color ) VALUEs ( 'Default-Category', '' );

CREATE TABLE events(
    id              SERIAL NOT NULL PRIMARY KEY,
    title           TEXT,
    duration        TSRANGE,
    repeat          CHAR(5),
    repeat_factor   SMALLINT,
    repeat_quantity SMALLINT,
    repeat_end      TIMESTAMP WITHOUT TIME ZONE,
    description     TEXT,
    location        TEXT,
    uid             INT,
    prio            SMALLINT,
    category         SMALLINT,
    visibility        SMALLINT,
    "allDay"        BOOLEAN,
    color             CHAR(7),
    job             BOOLEAN,
    done            BOOLEAN,
    job_planned_end TIMESTAMP WITHOUT TIME ZONE,
    cust_vend_pers  TEXT
);

CREATE TABLE postitall (
    id          SERIAL NOT NULL PRIMARY KEY,
    iduser      TEXT,
    idnote      TEXT,
    content     TEXT
);

CREATE TABLE knowledge_category (
    id       SERIAL,
    labeltext text,
    maingroup int,
    help    bool
);
INSERT INTO knowledge_category ( labeltext, maingroup ) VALUES ( 'kivitendo', 0 );

CREATE TABLE knowledge_content (
    id          SERIAL,
    modifydate  TIMESTAMP,
    content     TEXT,
    employee    INT,
    version     INT,
    category    INT,
    owner       INT,
    rights      TEXT
);
INSERT INTO knowledge_content ( modifydate, content, employee, version, category, owner ) VALUES ( now(), 'right click for new category', 0, 1, 1, 0 );

INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('ttpart','','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('tttime','60','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('ttround','15','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('ttclearown','','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('GEODB','','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('BLZDB','','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('CallDel','','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('CallEdit','','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('Expunge','','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('MailFlag','Flagged','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('logmail','t','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('dir_group','users','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('dir_mode','0755','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('sep_cust_vendor','t','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('listLimit','500','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('showErr','','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('logfile','','mandant',-1);
INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('http://maps.google.de/maps?f=d&hl=de&saddr=Alexanderplatz+7,10178+Berlin&daddr=%TOSTREET%,%TOZIPCODE%+%TOCITY%','','mandant',-1);

INSERT INTO bundesland (country,bundesland) VALUES ('D','Baden-Württemberg');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Bayern');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Berlin');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Brandenburg');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Bremen');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Hamburg');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Hessen');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Mecklenburg-Vorpommern');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Niedersachsen');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Nordrhein-Westfalen');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Rheinland-Pfalz');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Saarland');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Sachsen');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Sachsen-Anhalt');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Schleswig-Holstein');
INSERT INTO bundesland (country,bundesland) VALUES ('D','Thüringen');

INSERT INTO bundesland (country,bundesland) VALUES ('CH','Aargau');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Appenzell Ausserrhoden');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Appenzell Innerrhoden');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Basel-Landschaft');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Basel-Stadt');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Bern');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Freiburg');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Genf');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Glarus');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Graubünden');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Jura');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Luzern');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Neuenburg');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Nidwalden');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Obwalden');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Schaffhausen');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Schwyz');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Solothurn');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','St. Gallen');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Tessin');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Thurgau');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Uri');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Waadt');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Wallis');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Zug');
INSERT INTO bundesland (country,bundesland) VALUES ('CH','Zürich');

INSERT INTO bundesland (country,bundesland) VALUES ('A','Burgenland');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Kärnten');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Niederösterreich');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Oberösterreich');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Salzburg');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Steiermark');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Tirol');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Vorarlberg');
INSERT INTO bundesland (country,bundesland) VALUES ('A','Wien');


ALTER TABLE customer ADD COLUMN owener int4;
ALTER TABLE customer ADD COLUMN employee int4;
ALTER TABLE customer ADD COLUMN sw text;
ALTER TABLE customer ADD COLUMN branche character varying(45);
ALTER TABLE customer ADD COLUMN grafik character varying(4);
ALTER TABLE customer ADD COLUMN sonder int;
ALTER TABLE customer ADD COLUMN lead integer;
ALTER TABLE customer ADD COLUMN leadsrc character varying(25);
ALTER TABLE customer ADD COLUMN bland int4;
ALTER TABLE customer ADD COLUMN konzern int4;
ALTER TABLE customer ADD COLUMN headcount int;
ALTER TABLE vendor ADD COLUMN owener int4;
ALTER TABLE vendor ADD COLUMN employee int4;
ALTER TABLE vendor ADD COLUMN kundennummer character varying(20);
ALTER TABLE vendor ADD COLUMN sw text;
ALTER TABLE vendor ADD COLUMN branche character varying(45);
ALTER TABLE vendor ADD COLUMN grafik character varying(5);
ALTER TABLE vendor ADD COLUMN sonder int;
ALTER TABLE vendor ADD COLUMN bland int4;
ALTER TABLE vendor ADD COLUMN lead integer;
ALTER TABLE vendor ADD COLUMN leadsrc character varying(25);
ALTER TABLE vendor ADD COLUMN konzern int4;
ALTER TABLE vendor ADD COLUMN headcount int;
ALTER TABLE shipto ADD COLUMN shiptoowener int4;
ALTER TABLE shipto ADD COLUMN shiptoemployee int4;
ALTER TABLE shipto ADD COLUMN shiptobland int4;
ALTER TABLE contacts ADD COLUMN cp_homepage text;
ALTER TABLE contacts ADD COLUMN cp_notes text;
ALTER TABLE contacts ADD COLUMN cp_beziehung integer;
ALTER TABLE contacts ADD COLUMN cp_sonder integer;
ALTER TABLE contacts ADD COLUMN cp_stichwort1 text;
ALTER TABLE contacts ADD COLUMN cp_owener integer;
ALTER TABLE contacts ADD COLUMN cp_employee integer;
ALTER TABLE contacts ADD COLUMN cp_grafik character varying(5);
ALTER TABLE contacts ADD COLUMN cp_country character varying(3);
ALTER TABLE contacts ADD COLUMN cp_salutation text;
ALTER TABLE defaults ADD COLUMN contnumber text;


CREATE INDEX contacts_id_key ON contacts USING btree (cp_id);
CREATE INDEX contacts_name_key ON contacts USING btree (cp_name);
CREATE INDEX telcall_id_key ON telcall USING btree (id);
CREATE INDEX telcall_bezug_key ON telcall USING btree (bezug);
CREATE INDEX mid_key ON contmasch USING btree (mid);

INSERT INTO schema_info (tag,login) VALUES ('crm_defaults','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_wvhistory2','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_id2login','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_katalogsortpart','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_defaults_gruppe','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_bundeslaender','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_CleanContact','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_employeeFeldLaenge','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_PrivatTermin','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_sonderflag','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_sonderflag2','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_bundeslaenderutf','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_CallDirekt','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_employeeIcal','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_extrafelder','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_headcount','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_lockfile','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_OpportunityQuotation','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_Stichwort','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_streetview','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_TerminSequenz','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_TerminDate','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_TelCallTermin','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_termincat','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_TerminCatCol','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_TerminLocation','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_timetracker','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_timetracker_budget','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_timetracker_parts','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_wissen_own','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_WiedervorlageGrp','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_wvhistory','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_CRMemployee','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_CRMemployeeMID','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_UserFolder','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_UserMailssl','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_Calendar','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_Calendar02','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_EventCategory','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_Postitall','install');
INSERT INTO schema_info (tag,login) VALUES ('crm_Knowledge','install');
