# kivitendo-crm

> **Hinweis: Dieses Repository wird nicht mehr weiterentwickelt.**
> Das CRM lebt als fester Bestandteil von **OSERP** weiter, dem Nachfolger von kivitendo.

## Was ist passiert?

kivitendo-crm wurde von der Firma **Inter-Data** als CRM-Erweiterung für kivitendo
entwickelt: Kundenverwaltung, Kontakthistorie, Telefonie-Anbindung und Kommunikation
rund um die kivitendo-Stammdaten.

Inter-Data hat das CRM von Grund auf neu entwickelt und in **OSERP** integriert.
OSERP ist ein vollständiges Open-Source-ERP, das **kivitendo ablöst**: Es nutzt
weiterhin die kivitendo-Datenbankstruktur, bringt aber eine moderne Oberfläche,
eine eigene Buchhaltung, Bankanbindung und viele weitere Module mit. Ein
paralleler Betrieb von kivitendo ist nicht mehr nötig.

👉 **https://github.com/Ciatronical/opensource-erp**

## Was bedeutet das für dich?

- **Diese Version nicht mehr einsetzen.** Sie erhält keine Updates,
  Fehlerbehebungen oder Sicherheitskorrekturen mehr. Für Neuinstallationen ist
  sie nicht geeignet.
- **Auf OSERP umsteigen.** OSERP ist der offizielle Nachfolger von kivitendo
  und kivitendo-crm und wird aktiv weiterentwickelt. Die Datenstruktur bleibt
  kivitendo-kompatibel, bestehende Daten können übernommen werden.
- **Kein Zusatzmodul nötig.** Das CRM ist in OSERP Teil des Kernsystems und
  sofort nach der Installation verfügbar.

## Was OSERP kann

### CRM (Nachfolger von kivitendo-crm)

- Kunden und Lieferanten mit beliebig vielen Telefonnummern, E-Mail-Adressen
  und URLs, Ansprechpartnern, Lieferadressen und Bankverbindungen
- Kundenspezifische Preisregeln und Preisgruppen
- Kontakthistorie: Anrufe, E-Mails, WhatsApp-Nachrichten und Briefe
  chronologisch an einem Ort
- Anrufhistorie mit automatischer Anruferkennung, Mitschnitt und Transkription
  (CTI über Asterisk)
- Infoleiste mit Live-Hinweisen zu Anrufen, E-Mails und Nachrichten
- Globale Suche über Kunden, Lieferanten, Ansprechpartner, Telefonnummern
  und E-Mail-Adressen
- Dateimanager je Kontakt, Wiedervorlagen, Aufgaben, Datenschutzfunktionen

### Finanzen

- **Banking**: FinTS-Anbindung, Kontoumsätze, SEPA-Überweisungen und
  Lastschriften, Daueraufträge, Vorlagen, automatischer Abgleich mit offenen
  Posten, Sammelbuchung, Kartenabrechnungen (SumUp, Flatpay, Rapyd),
  Liquiditätsvorschau
- **Buchhaltung**: Belegerfassung, Buchungen, offene Posten, Mahnwesen,
  „Magisch Buchen“ aus Postfach und Ordnern, Kontenrahmen, DATEV-Export
- **Kasse / Kassenbuch** mit Belegnummern
- **UStVA**: Umsatzsteuer-Voranmeldung aus den echten Buchungen
- **GoBD**: revisionssichere Belegablage und Verfahrensdokumentation

### Verkauf und Lager

- **Faktura**: Angebote, Aufträge, Rechnungen, Lieferscheine, Gutschriften,
  E-Rechnung (XRechnung / ZUGFeRD), Belegsuche, Versandstatus je Beleg
- **Wiederkehrende Rechnungen**: Abos, Mieten und Wartungsverträge mit
  Vorschau, Kündigungsfristen und Preisanpassung
- **Artikel und Preise**
- **Lager**: Bestandsführung, Scanner-Modus, geführte Inventur,
  Bewegungsjournal
- **Vorlageneditor**: Druckvorlagen per Drag & Drop gestalten
- **Shop**: HugoShop-Webseite, Verkaufskanäle, PayPal, Widerruf, eBay

### Kommunikation

- **E-Mail**: IMAP/SMTP-Client mit automatischer Kundenzuordnung
- **WhatsApp Business**: Nachrichten senden und empfangen
- **Briefe**: Hybridbrief per Post über PIN AG oder LetterXpress
- **Telegram**: Sprachnotizen, Nachrichten, Anschlagtafel
- **Telefonie (CRMTI)**: Anruferkennung in Echtzeit über Asterisk
- **DHL**: Versandintegration mit Label-Druck

### Organisation und Wissen

- **Kalender** mit Echtzeit-Updates und Wall-Display
- **Wiki** mit Kategorien und Versionierung
- Wiedervorlage, Aufgaben, Projektmanagement, Anschlagtafel
- **Personal (HR)**

### KI

- **Weroni**: KI-Assistentin für Datenanalyse, Belegerkennung und Aufgaben
- Spracheingabe, lokales LLM oder Cloud-KI nach Wahl
- KI-Liquiditätsprognose

### Kamera

- **Videoüberwachung**: IP-Kameras, Objekterkennung, Ereignisse und Regeln
- **ANPR**: automatische Kennzeichenerkennung

### System

- Mandanten (Multi-Tenant), Benutzer und Berechtigungsgruppen
- Mehrsprachigkeit (20 Sprachen), Echtzeit-Updates per SSE
- Setup-Assistent ohne kivitendo, integriertes Update, Einstellungssuche
- Developer- und Admin-Werkzeuge, Log-Viewer

### Erweiterung LxCars (Kfz-Handel und Werkstatt)

Fahrzeugstammdaten, KBA-Datenbank, Fahrzeugschein-Scan, Werkstattaufträge,
Mechaniker-Modus, Mängelerfassung, HU-Benachrichtigungen, Fahrzeugverkauf
und -zulassung, Etikettendruck, Spezialwerkzeug-Verleih.

Die vollständige Feature-Liste: https://opensource-erp.dev

## Unterstützung bei der Umstellung

Inter-Data begleitet den Wechsel von kivitendo und kivitendo-crm auf OSERP,
von der Datenübernahme bis zur Einrichtung. Bei Fragen zur Migration, zum
Betrieb oder zum Funktionsumfang:

**info@lxcars.de**
