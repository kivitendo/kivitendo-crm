--LxCars


CREATE TABLE IF NOT EXISTS lxckba(
    hsn 			TEXT NOT NULL,
    tsn 			TEXT NOT NULL,
    hersteller 		TEXT NOT NULL,
    marke 			TEXT NOT NULL,
    name 			TEXT,
    datum 			TEXT,
    klasse 			TEXT,
    aufbau 			TEXT,
    kraftstoff 		TEXT,
    leistung 		TEXT,
    hubraum 		TEXT,
    achsen 			TEXT,
    antrieb 		TEXT,
    sitze 			TEXT,
    masse 			TEXT, --End KBA
	d3				TEXT, --Handelsbezeichnung
	j				TEXT, --Fahrzeugklasse
	field_4			TEXT, --Art des Aufbaus
	d1				TEXT, --Marke
	d2				TEXT, --Typ !!setzt sich aus d2_1 + d2_2 + d2_3 + d2_4 zusammen
	field_2			TEXT, --Hersteller-Kurzbezeichnung
	field_5			TEXT, --Bezeichnung der Fahrzeugklasse und des Aufbaus !! d_5_1 + d_5_2
	v9				TEXT, --Schadstoffklasse für die EG-Typgenehmigung
	field_14		TEXT, --Bezeichnug der nationalen Emmisionsklasse
	p3				TEXT, --Kraftstoffart oder Energiequelle
	field_10		TEXT, --Kraftstoff- bzw Energiecode
	field_14_1 		TEXT, --Code EG Schadstoffklasse
	p1				TEXT, --Hubraum
	l				TEXT, --Anzahl der Achsen
	field_9			TEXT, --Anzahl der Antriebsachsen
	p2_p4			TEXT, --maximale Leistung bei Drehzahl
	t				TEXT, --Höchstgeschwindigkeit
	field_18		TEXT, --Fahrzeuglänge
	field_19		TEXT, --Fahrzeugbreite
	field_20		TEXT, --Fahrzeughöhe
	g				TEXT, --Leermasse
	field_12		TEXT, --Tankvolumen
	field_13		TEXT, --Stützlast
	q				TEXT, --Leistungsgewicht
	v7				TEXT, --CO2 g/km
	f1				TEXT, --zulässige Gesamtmasse in kg
	f2				TEXT, --zulässige Gesamtmasse in kg im Zusassungsmitgliedstaat	
	field_7_1		TEXT, --maximale Achslast Achse 1
	field_7_2		TEXT, --maximale Achslast Achse 2
	field_7_3		TEXT, --maximale Achslast Achse 3
	field_8_1		TEXT, --maximale Achslast Achse 1 im Zusassungsmitgliedstaat
	field_8_2		TEXT, --maximale Achslast Achse 2 im Zusassungsmitgliedstaat
	field_8_3		TEXT, --maximale Achslast Achse 3 im Zusassungsmitgliedstaat
	u1				TEXT, --Standgeräusch
	u2				TEXT, --Drehzahl zum Standgeräusch
	u3 				TEXT, --Fahrgeräusch
	o1				TEXT, --zulässige Anhängelast in kg gebremst
	o2				TEXT, --zulässige Anhängelast in kg ungebremst
	s1				TEXT, --Anzahl der Sitzplätze
	s2				TEXT, --Anzahl der Stehplätze
	field_15_1		TEXT, --Bereifung Achse 1
	field_15_2		TEXT, --Bereifung Achse 2
	field_15_3		TEXT, --Bereifung Achse 3
	k				TEXT, --Nummer der EG Typgenehmigung
	field_6			TEXT, --Datum der EG Typgenehmigung
	field_17		TEXT, --Merkmak zur Betriebserlaubnis
	field_21		TEXT, --Sontige Vermerke
	PRIMARY KEY( hsn, tsn )
);



-- Kivitendo CRM
CREATE TABLE firstnameToGender( gender CHAR(1), firstname TEXT UNIQUE PRIMARY KEY );