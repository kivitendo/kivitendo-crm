-- @tag: app
-- @description: Upgrade script for KBA entries with null as TSN
-- @version: 2.3.5

INSERT INTO lxckba (hsn, hersteller, tsn, marke)
SELECT hsn, hersteller, '000' AS tsn, marke FROM ( SELECT DISTINCT( x.hsn ), ( SELECT tsn FROM lxckba WHERE x.hsn = hsn LIMIT 1) AS old_tsn, ( SELECT hersteller FROM lxckba WHERE x.hsn = hsn LIMIT 1) AS hersteller, ( SELECT marke FROM lxckba WHERE x.hsn = hsn LIMIT 1) AS marke FROM lxckba x ) AS kba WHERE NOT old_tsn ILIKE '000%' ORDER BY hsn;
