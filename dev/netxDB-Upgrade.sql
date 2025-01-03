--LxCars


CREATE TABLE IF NOT EXISTS lxckba(
    hsn             TEXT NOT NULL,
    tsn             TEXT NOT NULL,
    hersteller         TEXT NOT NULL,
    marke             TEXT NOT NULL,
    name             TEXT,
    datum             TEXT,
    klasse             TEXT,
    aufbau             TEXT,
    kraftstoff         TEXT,
    leistung         TEXT,
    hubraum         TEXT,
    achsen             TEXT,
    antrieb         TEXT,
    sitze             TEXT,
    masse             TEXT, --End KBA
    d3                TEXT, --Handelsbezeichnung
    j                TEXT, --Fahrzeugklasse
    field_4            TEXT, --Art des Aufbaus
    d1                TEXT, --Marke
    d2                TEXT, --Typ !!setzt sich aus d2_1 + d2_2 + d2_3 + d2_4 zusammen
    field_2            TEXT, --Hersteller-Kurzbezeichnung
    field_5            TEXT, --Bezeichnung der Fahrzeugklasse und des Aufbaus !! d_5_1 + d_5_2
    v9                TEXT, --Schadstoffklasse für die EG-Typgenehmigung
    field_14        TEXT, --Bezeichnug der nationalen Emmisionsklasse
    p3                TEXT, --Kraftstoffart oder Energiequelle
    field_10        TEXT, --Kraftstoff- bzw Energiecode
    field_14_1         TEXT, --Code EG Schadstoffklasse
    p1                TEXT, --Hubraum
    l                TEXT, --Anzahl der Achsen
    field_9            TEXT, --Anzahl der Antriebsachsen
    p2_p4            TEXT, --maximale Leistung bei Drehzahl
    t                TEXT, --Höchstgeschwindigkeit
    field_18        TEXT, --Fahrzeuglänge
    field_19        TEXT, --Fahrzeugbreite
    field_20        TEXT, --Fahrzeughöhe
    g                TEXT, --Leermasse
    field_12        TEXT, --Tankvolumen
    field_13        TEXT, --Stützlast
    q                TEXT, --Leistungsgewicht
    v7                TEXT, --CO2 g/km
    f1                TEXT, --zulässige Gesamtmasse in kg
    f2                TEXT, --zulässige Gesamtmasse in kg im Zusassungsmitgliedstaat
    field_7_1        TEXT, --maximale Achslast Achse 1
    field_7_2        TEXT, --maximale Achslast Achse 2
    field_7_3        TEXT, --maximale Achslast Achse 3
    field_8_1        TEXT, --maximale Achslast Achse 1 im Zusassungsmitgliedstaat
    field_8_2        TEXT, --maximale Achslast Achse 2 im Zusassungsmitgliedstaat
    field_8_3        TEXT, --maximale Achslast Achse 3 im Zusassungsmitgliedstaat
    u1                TEXT, --Standgeräusch
    u2                TEXT, --Drehzahl zum Standgeräusch
    u3                 TEXT, --Fahrgeräusch
    o1                TEXT, --zulässige Anhängelast in kg gebremst
    o2                TEXT, --zulässige Anhängelast in kg ungebremst
    s1                TEXT, --Anzahl der Sitzplätze
    s2                TEXT, --Anzahl der Stehplätze
    field_15_1        TEXT, --Bereifung Achse 1
    field_15_2        TEXT, --Bereifung Achse 2
    field_15_3        TEXT, --Bereifung Achse 3
    k                TEXT, --Nummer der EG Typgenehmigung
    field_6            TEXT, --Datum der EG Typgenehmigung
    field_17        TEXT, --Merkmak zur Betriebserlaubnis
    field_21        TEXT, --Sontige Vermerke
    PRIMARY KEY( hsn, tsn )
);



-- Kivitendo CRM
CREATE TABLE firstnametogender
(
    gender character(1) NOT NULL,
    firstname text NOT NULL PRIMARY KEY
);

COPY firstnametogender( gender, firstname ) FROM 'data/firstnameToGender.csv' DELIMITER '|' CSV HEADER;

CREATE TABLE zipcode_to_location
(
    id integer primary key generated always as identity,
    osm_id text,
    ags text,
    ort text NOT NULL,
    plz character(5) NOT NULL,
    landkreis text,
    bundesland text NOT NULL
);

COPY zipcode_to_location( osm_id, ags, ort, plz, landkreis, bundesland ) FROM 'data/zuordnung_plz_ort.csv' DELIMITER ',' CSV HEADER;

ALTER TABLE zipcode_to_location DROP COLUMN ags;
ALTER TABLE zipcode_to_location DROP COLUMN osm_id;

ALTER TABLE IF EXISTS oe ADD COLUMN delivery_time text;

CREATE TABLE IF NOT EXISTS calendar_events
(
    id integer NOT NULL GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    title text,
    description text,
    dtstart timestamp without time zone,
    dtend timestamp without time zone,
    repeat_end timestamp without time zone,
    duration text,
    freq text,
    interval integer,
    count integer,
    byweekday text,
    location text,
    uid integer,
    prio integer,
    category integer,
    visibility integer,
    "allDay" boolean,
    color text,
    cvp_id integer,
    order_id integer,
    car_id integer,
    cvp_name text,
    cvp_type character(1)
);

INSERT INTO event_category (id, label, color, cat_order)
SELECT 3, 'Werkstatt-Plan', '#111', ( SELECT count( * ) FROM event_category )
WHERE NOT EXISTS (
    SELECT 1 FROM event_category WHERE id = 3
);

-- Migration Kalendar (Dauerereignisse)
INSERT INTO calendar_events (title, description, dtstart, dtend, repeat_end, duration, freq, interval, count, uid, prio, category, visibility, "allDay", color)
SELECT title, description, lower( duration ) AS dtstart, upper( duration ) AS dtend, repeat_end, to_char( ( upper( duration ) - lower( duration ) ), 'HH24:MI' ) AS duration, REPLACE( REPLACE( REPLACE( REPLACE( repeat, 'year', 'yearly' ), 'month', 'monthly' ), 'week', 'weekly' ), 'day', 'daily' ) AS freq, repeat_factor AS interval, repeat_quantity AS count, uid, prio, category, visibility, "allDay", color FROM events WHERE repeat_end IS NOT NULL AND repeat_end >= current_date AND "allDay" = false;

INSERT INTO calendar_events (title, description, dtstart, dtend, repeat_end, duration, freq, interval, count, uid, prio, category, visibility, "allDay", color)
SELECT title, description, lower( duration ) AS dtstart, upper( duration ) AS dtend, repeat_end, '24:00' AS duration, REPLACE( REPLACE( REPLACE( REPLACE( repeat, 'year', 'yearly' ), 'month', 'monthly' ), 'week', 'weekly' ), 'day', 'daily' ) AS freq, repeat_factor AS interval, repeat_quantity AS count, uid, prio, category, visibility, "allDay", color FROM events WHERE repeat_end IS NOT NULL AND repeat_end >= current_date AND "allDay" = true;

-- Upgrade für Gnullte TSN in kba
INSERT INTO lxckba (hsn, hersteller, tsn, marke)
SELECT hsn, hersteller, '000' AS tsn, marke FROM ( SELECT DISTINCT( x.hsn ), ( SELECT tsn FROM lxckba WHERE x.hsn = hsn LIMIT 1) AS old_tsn, ( SELECT hersteller FROM lxckba WHERE x.hsn = hsn LIMIT 1) AS hersteller, ( SELECT marke FROM lxckba WHERE x.hsn = hsn LIMIT 1) AS marke FROM lxckba x ) AS kba WHERE NOT old_tsn ILIKE '000%' ORDER BY hsn;
