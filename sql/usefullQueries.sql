--Rechnungsbuch
COPY(
SELECT ar.id, ar.invnumber AS Rechnungsnummer, to_char( ar.transdate, 'DD.MM.YYYY' ) AS datum, replace( round( ar.netamount, 2 )::TEXT, '.', ',') AS netto, replace( round( ar.amount, 2 )::TEXT, '.', ',') AS brutto, replace( round( ar.paid, 2 )::TEXT, '.', ',') AS bezahlt, ar.type, customer.name FROM ar join customer ON( ar.customer_id = customer.id )  WHERE EXTRACT( YEAR FROM transdate ) = 2020 AND EXTRACT( MONTH FROM transdate ) = 2 ORDER BY ar.id
)TO '/tmp/rechnungsausgangsbuch_02-2020.csv' DELIMITER ';' CSV HEADER;
COPY(
SELECT ap.id, ap.invnumber AS Rechnungsnummer, to_char( ap.transdate, 'DD.MM.YYYY' ) AS datum, replace( round( ap.netamount, 2 )::TEXT, '.', ',') AS netto, replace( round( ap.amount, 2 )::TEXT, '.', ',') AS brutto, replace( round( ap.paid, 2 )::TEXT, '.', ',') AS bezahlt, ap.type, vendor.name FROM ap join vendor ON( ap.vendor_id = vendor.id )  WHERE EXTRACT( YEAR FROM transdate ) = 2020 AND EXTRACT( MONTH FROM transdate ) = 2 ORDER BY ap.id
)TO '/tmp/rechnungseingangssbuch_02-2020.csv' DELIMITER ';' CSV HEADER;

COPY (
SELECT to_char( transdate, 'DD.MM.YYYY' ), to_char( gldate, 'DD.MM.YYYY' ), reference, description, source, link, ROUND( amount, 2), accno, notes,  (SELECT taxdescription FROM tax t WHERE t.taxkey = mytable.taxkey LIMIT 1) FROM ( WITH myconstants ( von, bis) as ( values ('2015-01-01'::DATE, '2016-12-31'::DATE ))
SELECT
   ac.acc_trans_id, g.id, 'gl' AS type, FALSE AS invoice, g.reference, ac.taxkey, c.link,
   g.description, ac.transdate, ac.gldate, ac.source, ac.trans_id,
   ac.amount, c.accno, g.notes, t.chart_id
FROM gl g,  myconstants, acc_trans ac , chart c
LEFT JOIN tax t ON (t.chart_id = c.id)
WHERE 1 = 1 AND ac.transdate >= von AND ac.transdate <= bis AND (ac.chart_id = c.id) AND (g.id = ac.trans_id)
UNION
SELECT
   ac.acc_trans_id, a.id, 'ar' AS type, a.invoice, a.invnumber, ac.taxkey, c.link,
   ct.name, ac.transdate, ac.gldate, ac.source, ac.trans_id,
   ac.amount, c.accno, a.notes, t.chart_id
FROM ar a, myconstants, acc_trans ac , customer ct, chart c
LEFT JOIN tax t ON (t.chart_id=c.id)
WHERE  ac.transdate >= von AND ac.transdate <= bis AND (ac.chart_id = c.id) AND (a.customer_id = ct.id) AND (a.id = ac.trans_id)
UNION
SELECT
   ac.acc_trans_id, a.id, 'ap' AS type, a.invoice, a.invnumber, ac.taxkey, c.link,
   ct.name, ac.transdate, ac.gldate, ac.source, ac.trans_id,
   ac.amount, c.accno, a.notes, t.chart_id
FROM ap a, myconstants, acc_trans ac , vendor ct, chart c
LEFT JOIN tax t ON (t.chart_id=c.id)
WHERE ac.transdate >= von AND ac.transdate <= bis AND (ac.chart_id = c.id) AND (a.vendor_id = ct.id) AND (a.id = ac.trans_id)
ORDER BY transdate ASC, id ASC, acc_trans_id ASC ) AS mytable
)TO '/tmp/buchungsjornal01_semicolon.csv' DELIMITER ';' CSV HEADER;

--Buchungen
SELECT  a.itime AS a_itime,  ac.itime AS ac_itime, a.id, a.reference, a.description, ac.transdate, ac.chart_id,   FALSE AS invoice, ac.amount, 'gl' as module, (SELECT accno||'--'||rate FROM tax LEFT JOIN chart ON (tax.chart_id=chart.id) WHERE tax.id = (SELECT tax_id FROM taxkeys WHERE taxkey_id = ac.taxkey AND taxkeys.startdate <= ac.transdate ORDER BY taxkeys.startdate DESC LIMIT 1)) AS taxinfo, ac.source || ' ' || ac.memo AS memo FROM acc_trans ac, gl a WHERE 1 = 1 AND ac.transdate >= '2020-05-29' AND ac.transdate <=  '2020-05-29' AND ac.chart_id = 257  AND ac.trans_id = a.id  AND (NOT ac.ob_transaction OR ac.ob_transaction IS NULL) UNION ALL SELECT a.itime AS a_itime,  ac.itime AS ac_itime, a.id, a.invnumber, c.name, ac.transdate, ac.chart_id,  a.invoice, ac.amount, 'ar' as module, (SELECT accno||'--'||rate FROM tax LEFT JOIN chart ON (tax.chart_id=chart.id) WHERE tax.id = (SELECT tax_id FROM taxkeys WHERE taxkey_id = ac.taxkey AND taxkeys.startdate <= ac.transdate ORDER BY taxkeys.startdate DESC LIMIT 1)) AS taxinfo, ac.source || ' ' || ac.memo AS memo  FROM acc_trans ac, customer c, ar a WHERE 1 = 1 AND ac.transdate >= '2020-05-29' AND ac.transdate <= '2020-05-29' AND ac.chart_id = 257  AND ac.trans_id = a.id  AND a.customer_id = c.id  AND (NOT ac.ob_transaction OR ac.ob_transaction IS NULL)UNION ALL SELECT  a.itime AS a_itime,  ac.itime AS ac_itime, a.id, a.invnumber, v.name, ac.transdate, ac.chart_id,   a.invoice, ac.amount, 'ap' as module, (SELECT accno||'--'||rate FROM tax LEFT JOIN chart ON (tax.chart_id=chart.id) WHERE tax.id = (SELECT tax_id FROM taxkeys WHERE taxkey_id = ac.taxkey AND taxkeys.startdate <= ac.transdate ORDER BY taxkeys.startdate DESC LIMIT 1)) AS taxinfo, ac.source || ' ' || ac.memo AS memo  FROM acc_trans ac, vendor v, ap a WHERE 1 = 1 AND ac.transdate >= '2020-05-29' AND ac.transdate <= '2020-05-29' AND ac.chart_id = 257  AND ac.trans_id = a.id  AND a.vendor_id = v.id  AND (NOT ac.ob_transaction OR ac.ob_transaction IS NULL) ORDER BY ac_itime;