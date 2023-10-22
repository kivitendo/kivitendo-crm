-- telephone integration for kivitendo
DROP TABLE IF EXISTS crmti CASCADE;;
CREATE TABLE crmti(
    crmti_id            SERIAL PRIMARY KEY,
    crmti_init_time     timestamp with time zone DEFAULT NOW(), -- Zeitpunt des Anrufes
    crmti_end_time      timestamp with time zone DEFAULT NOW(), -- Zeitpunk Anrufende
    crmti_src           text,                                   -- Anrufquelle
    crmti_dst           text,                                   -- Anrufziel
    crmti_caller_id     int,                                    -- id des Telefonierenden
    crmti_caller_typ    char,                                   -- Kunde, Kieferant, Kontakt
    crmti_direction     text,                                   -- Richtung
    crmti_status        text,                                   -- Anrufstatus
    crmti_number        text,
    unique_call_id      text
);;
INSERT INTO crmti( crmti_src, crmti_dst ) VALUES ( 'INSTALL.TXT', 'LESEN' );;

ALTER TABLE customer DROP CONSTRAINT IF EXISTS check_tel;
ALTER TABLE customer DROP CONSTRAINT IF EXISTS check_fax;
ALTER TABLE vendor   DROP CONSTRAINT IF EXISTS check_tel;
ALTER TABLE vendor   DROP CONSTRAINT IF EXISTS check_fax;
ALTER TABLE contacts DROP CONSTRAINT IF EXISTS check_cp_phone1;
ALTER TABLE contacts DROP CONSTRAINT IF EXISTS check_cp_phone2;
ALTER TABLE customer ADD CONSTRAINT check_tel CHECK ( phone ~ '^(|(?=.*\d).*)$' );
ALTER TABLE customer ADD CONSTRAINT check_fax CHECK ( fax ~ '^(|(?=.*\d).*)$' );
ALTER TABLE vendor   ADD CONSTRAINT check_tel CHECK ( phone ~ '^(|(?=.*\d).*)$' );
ALTER TABLE vendor   ADD CONSTRAINT check_fax CHECK ( fax ~ '^(|(?=.*\d).*)$' );
ALTER TABLE contacts ADD CONSTRAINT check_cp_phone1 CHECK ( cp_phone1 ~ '^(|(?=.*\d).*)$' );
ALTER TABLE contacts ADD CONSTRAINT check_cp_phone2 CHECK ( cp_phone2 ~ '^(|(?=.*\d).*)$' );
-- Wenn es schief geht: SELECT id, phone, fax FROM customer WHERE fax !~ '^(|(?=.*\d).*)$';

DROP FUNCTION kuerze( INT, TEXT );
DROP FUNCTION SucheNummer( TEXT );
DROP FUNCTION CallOut( TEXT, TEXT, TEXT );
DROP FUNCTION CallIn( TEXT, TEXT, TEXT );


CREATE OR REPLACE FUNCTION kuerze( INT, TEXT )
    RETURNS text AS $$
    -- Kürzt die Telefonummer von rechts auf n Stellen
    -- (0049, +49, 0)-Problematik
    DECLARE
        laenge INT;
    BEGIN
        laenge = length( $2 );
        IF laenge <= $1 THEN
            RETURN $2;
        ELSE
            RETURN substring( $2, laenge  - $1 + 1 );
        END IF;
    END;
$$ LANGUAGE 'plpgsql';;;


CREATE OR REPLACE FUNCTION SucheNummer( text )
    RETURNS record AS $$
    -- Sucht die Telefonnummer in den Kivitendo-Tabellen und gibt Namen (falls gefunden) sonst name=Nummer und id=0 zurück
    DECLARE
        telnum ALIAS FOR $1;
        myname text;
        result record;
        format text;
    BEGIN
        format := '99999999999999999';
        IF telnum !~ '[0-9]' THEN
            SELECT INTO result 0 AS id, telnum AS name, 'Y'::char AS typ;
            return result;
        END IF;
        SELECT INTO result id, name::text, 'C'::char AS typ FROM (SELECT id, name, to_number(phone, format)::char(16) AS p, to_number(phone, format)::char(16) AS f, char_length(to_number(phone, format)::char(16)) AS l, char_length(to_number(phone, format)::char(16)) AS l1, char_length(to_number(telnum, format)::char(16)) AS lt FROM customer WHERE phone !='') AS xyz WHERE kuerze(lt,xyz.p) LIKE kuerze(l,to_number(telnum, format)::char(16))||'%';
        IF result.name != '' THEN return result; END IF;
        SELECT INTO result id, name::text, 'C'::char AS typ FROM (SELECT id, name, to_number(fax, format)::char(16) AS p, to_number(fax, format)::char(16) AS f, char_length(to_number(fax, format)::char(16)) AS l, char_length(to_number(fax, format)::char(16)) AS l1, char_length(to_number(telnum, format)::char(16)) AS lt FROM customer WHERE fax !='') AS xyz WHERE kuerze(lt,xyz.p) LIKE kuerze(l,to_number(telnum, format)::char(16))||'%';
        IF result.name != '' THEN return result; END IF;
        SELECT INTO result id, name AS name, 'V'::char AS typ FROM (SELECT id, name, to_number(phone, format)::char(16) AS p, to_number(phone, format)::char(16) AS f, char_length(to_number(phone, format)::char(16)) AS l, char_length(to_number(phone, format)::char(16)) AS l1, char_length(to_number(telnum, format)::char(16)) AS lt FROM vendor WHERE phone !='') AS xyz WHERE kuerze(lt,xyz.p) LIKE kuerze(l,to_number(telnum, format)::char(16))||'%';
        IF result.name != '' THEN return result; END IF;
        SELECT INTO result id, name, 'V'::char AS typ FROM (SELECT id, name, to_number(fax, format)::char(16) AS p, to_number(fax, format)::char(16) AS f, char_length(to_number(fax, format)::char(16)) AS l, char_length(to_number(fax, format)::char(16)) AS l1, char_length(to_number(telnum, format)::char(16)) AS lt FROM vendor WHERE fax !='') AS xyz WHERE kuerze(lt,xyz.p) LIKE kuerze(l,to_number(telnum, format)::char(16))||'%';
        IF result.name != '' THEN return result; END IF;
        SELECT INTO result id, name, 'K'::char AS typ FROM (SELECT cp_id AS id, (cp_givenname||' '||cp_name)::text AS name, to_number(cp_phone1, format)::char(16) AS p, to_number(cp_phone1, format)::char(16) AS f, char_length(to_number(cp_phone1, format)::char(16)) AS l, char_length(to_number(cp_phone1, format)::char(16)) AS l1, char_length(to_number(telnum, format)::char(16)) AS lt FROM contacts WHERE cp_phone1 !='') AS xyz WHERE kuerze(lt,xyz.p) LIKE kuerze(l,to_number(telnum, format)::char(16))||'%';
        IF result.name != '' THEN return result; END IF;
        SELECT INTO result id, name, 'K'::char AS typ FROM (SELECT cp_id AS id, (cp_givenname||' '||cp_name)::text AS name, to_number(cp_phone2, format)::char(16) AS p, to_number(cp_phone2, format)::char(16) AS f, char_length(to_number(cp_phone2, format)::char(16)) AS l, char_length(to_number(cp_phone2, format)::char(16)) AS l1, char_length(to_number(telnum, format)::char(16)) AS lt FROM contacts WHERE cp_phone2 !='') AS xyz WHERE kuerze(lt,xyz.p) LIKE kuerze(l,to_number(telnum, format)::char(16))||'%';
        IF result.name != '' THEN return result; END IF;
        SELECT INTO result id, name, 'K'::char AS typ FROM (SELECT cp_id AS id, (cp_givenname||' '||cp_name)::text AS name, to_number(cp_mobile1, format)::char(16) AS p, to_number(cp_mobile1, format)::char(16) AS f, char_length(to_number(cp_mobile1, format)::char(16)) AS l, char_length(to_number(cp_mobile1, format)::char(16)) AS l1, char_length(to_number(telnum, format)::char(16)) AS lt FROM contacts WHERE cp_mobile1 !='') AS xyz WHERE kuerze(lt,xyz.p) LIKE kuerze(l,to_number(telnum, format)::char(16))||'%';
        IF result.name != '' THEN return result; END IF;
        SELECT INTO result id, name, 'K'::char AS typ FROM (SELECT cp_id AS id, (cp_givenname||' '||cp_name)::text AS name, to_number(cp_mobile2, format)::char(16) AS p, to_number(cp_mobile2, format)::char(16) AS f, char_length(to_number(cp_mobile2, format)::char(16)) AS l, char_length(to_number(cp_mobile2, format)::char(16)) AS l1, char_length(to_number(telnum, format)::char(16)) AS lt FROM contacts WHERE cp_mobile2 !='') AS xyz WHERE kuerze(lt,xyz.p) LIKE kuerze(l,to_number(telnum, format)::char(16))||'%';
        IF result.name != '' THEN return result; END IF;
        SELECT INTO result id, name, 'K'::char AS typ FROM (SELECT cp_id AS id, (cp_givenname||' '||cp_name)::text AS name, to_number(cp_privatphone, format)::char(16) AS p, to_number(cp_privatphone, format)::char(16) AS f, char_length(to_number(cp_privatphone, format)::char(16)) AS l, char_length(to_number(cp_privatphone, format)::char(16)) AS l1, char_length(to_number(telnum, format)::char(16)) AS lt FROM contacts WHERE cp_privatphone !='') AS xyz WHERE kuerze(lt,xyz.p) LIKE kuerze(l,to_number(telnum, format)::char(16))||'%';
        IF result.name != '' THEN return result; END IF;
        SELECT INTO result id, name, 'K'::char AS typ FROM (SELECT cp_id AS id, (cp_givenname||' '||cp_name)::text AS name, to_number(cp_fax, format)::char(16) AS p, to_number(cp_fax, format)::char(16) AS f, char_length(to_number(cp_fax, format)::char(16)) AS l, char_length(to_number(cp_fax, format)::char(16)) AS l1, char_length(to_number(telnum, format)::char(16)) AS lt FROM contacts WHERE cp_fax !='') AS xyz WHERE kuerze(lt,xyz.p) LIKE kuerze(l,to_number(telnum, format)::char(16))||'%';
        IF result.name != '' THEN return result; END IF;
        SELECT INTO result 0 AS id, telnum AS name, 'X'::char AS typ;
        return result;
    END;
$$ LANGUAGE 'plpgsql';;;


CREATE OR REPLACE FUNCTION CallIn( text, text, text )
    RETURNS text AS $$
    -- Für eingehende Anrufe, Sucht in src und gibt den Namen zurück, speichert
    DECLARE
        src ALIAS FOR $1;
        dst ALIAS FOR $2;
        unique_call_id ALIAS FOR $3;
        result record;
        new_row crmti%rowtype;
    BEGIN
        result := SucheNummer( src );
        INSERT INTO crmti ( crmti_src, crmti_caller_id, crmti_caller_typ, crmti_dst, crmti_direction, crmti_number, unique_call_id  ) VALUES ( result.name, result.id, result.typ, dst, 'E', src, unique_call_id  ) RETURNING * INTO new_row;
        DELETE FROM crmti WHERE crmti_id  < new_row.crmti_id - 512;
        IF result.typ != 'X' THEN
            insert into telcall ( calldate, bezug, cause, caller_id, kontakt, inout ) values ( CURRENT_TIMESTAMP, 0, 'Eingehender Anruf zu[r|m] '||dst, result.id, 'T','i' );
        END IF;
        --PERFORM pg_notify( 'crmti_watcher', to_json( new_row  )::TEXT );
        IF result.id = 0 THEN  --not found in kivi database! Use https://github.com/Superslub/AGI_Reverse_Lookup_DACH/commits/master!!!
            return NULL;
        ELSE
            return result.name;
        END IF;
    END;
$$ LANGUAGE 'plpgsql';;;


CREATE OR REPLACE FUNCTION CallOut( text, text, text )
    RETURNS text AS $$
    -- Für ausgehende Anrufe
    DECLARE
        src ALIAS FOR $1;
        dst ALIAS FOR $2;
        unique_call_id ALIAS FOR $3;
        result record;
        new_row crmti%rowtype;
    BEGIN
        result := SucheNummer( dst );
        INSERT INTO crmti ( crmti_src, crmti_dst, crmti_caller_typ, crmti_caller_id, crmti_direction, crmti_number, unique_call_id ) VALUES ( src, result.name, result.typ, result.id, 'A', dst, unique_call_id ) RETURNING * INTO new_row;
        DELETE FROM crmti WHERE crmti_id  < new_row.crmti_id - 512;
        IF result.typ != 'X' THEN
            INSERT INTO telcall ( calldate, bezug, cause, caller_id, kontakt, inout ) values ( CURRENT_TIMESTAMP, 0, 'Ausgehender Anruf vo[n|m] '||src, result.id, 'T', 'o' );
        END IF;
        --PERFORM pg_notify( 'crmti_watcher', to_json( new_row )::TEXT );
        return '1';
    END;
$$ LANGUAGE 'plpgsql';;;

