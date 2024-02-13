CREATE OR REPLACE FUNCTION calculate_modulo_11(fin VARCHAR)
RETURNS CHAR AS $$
DECLARE
    sum INT := 0;
    check_digit INT;
    char_val INT;
    weight INT := 2;
    i INT;
BEGIN
    -- Berechne die Summe der Produkte für jede Stelle der FIN
    FOR i IN REVERSE 17..1 LOOP
        -- Ermittle den Ziffernteil des EBCDIC-Codes für Buchstaben, sonst direkt den Zahlenwert
        CASE SUBSTRING(fin FROM i FOR 1)
            WHEN 'A', 'J', 'Ä' THEN char_val := 1;
            WHEN 'B', 'K', 'S' THEN char_val := 2;
            WHEN 'C', 'L', 'T' THEN char_val := 3;
            WHEN 'D', 'M', 'U' THEN char_val := 4;
            WHEN 'E', 'N', 'V' THEN char_val := 5;
            WHEN 'F', 'O', 'W' THEN char_val := 6;
            WHEN 'G', 'P', 'X' THEN char_val := 7;
            WHEN 'H', 'Q', 'Y' THEN char_val := 8;
            WHEN 'I', 'R', 'Z' THEN char_val := 9;
            ELSE char_val := CAST(SUBSTRING(fin FROM i FOR 1) AS INT);
        END CASE;

        -- Addiere das Produkt aus Ziffernwert und Gewicht zur Summe
        sum := sum + char_val * weight;

        -- Update das Gewicht für die nächste Iteration
        IF weight = 10 THEN
            weight := 2;
        ELSE
            weight := weight + 1;
        END IF;
    END LOOP;

    -- Berechne die Prüfziffer
    check_digit := sum % 11;

    -- Gib die Prüfziffer zurück, wobei der Teilungsrest 10 als 'X' dargestellt wird
    IF check_digit = 10 THEN
        RETURN 'X';
    ELSE
        RETURN check_digit::CHAR;
    END IF;
END;
$$ LANGUAGE plpgsql;




UPDATE lxc_cars SET c_fin = UPPER(REPLACE(c_fin, '!', '1'));
UPDATE lxc_cars SET c_fin = '151588X' WHERE c_fin ='151588-' ;
UPDATE lxc_cars SET c_fin = REPLACE(c_fin, '-', '') ;
DELETE FROM lxc_cars WHERE c_id = 4410 OR c_id = 220;
UPDATE lxc_cars SET c_fin = LTRIM(c_fin) WHERE LENGTH(c_fin) = 17;

UPDATE lxc_cars SET c_finchk = calculate_modulo_11(c_fin) WHERE LENGTH(c_fin) = 17 AND c_finchk = '';
UPDATE lxc_cars SET c_finchk = '-' WHERE LENGTH(c_fin) < 17 AND c_fin != '';
