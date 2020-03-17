
-- Rechnungsausgangsbuch
COPY(
SELECT ar.id, ar.invnumber AS Rechnungsnummer, to_char( ar.transdate, 'DD.MM.YYYY' ) AS datum, replace( round( ar.netamount, 2 )::TEXT, '.', ',') AS netto, replace( round( ar.amount, 2 )::TEXT, '.', ',') AS brutto, replace( round( ar.paid, 2 )::TEXT, '.', ',') AS bezahlt, ar.type, customer.name FROM ar join customer ON( ar.customer_id = customer.id )  WHERE EXTRACT( YEAR FROM transdate ) = 2020 AND EXTRACT( MONTH FROM transdate ) = 1 ORDER BY ar.id
)TO '/tmp/rechnungsausgangsbuch.csv' DELIMITER ';' CSV HEADER;
-- 
